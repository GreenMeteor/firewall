<?php

namespace humhub\modules\firewall\components;

use Yii;
use yii\helpers\Ip;
use yii\base\Component;
use yii\web\IdentityInterface;
use yii\caching\TagDependency;
use humhub\modules\firewall\Module;
use humhub\modules\firewall\models\FirewallRule;

/**
 * FirewallManager handles the checking of IP addresses against rules
 */
class FirewallManager extends Component
{
    /**
     * @var array Cached rules
     */
    private $_rules = [];
    
    /**
     * @var array Cached whitelist IPs
     */
    private $_whitelistIps = [];
    
    /**
     * @var array Cached blacklist IPs
     */
    private $_blacklistIps = [];
    
    /**
     * @inheritdoc
     */
    public function init()
    {
        parent::init();
        $this->loadRules();
    }
    
    /**
     * Loads rules from the database
     */
    protected function loadRules()
    {
        // Try to get rules from cache
        $cacheKey = 'firewall_rules';
        $this->_rules = Yii::$app->cache->get($cacheKey);
        
        if ($this->_rules === false) {
            $this->_rules = FirewallRule::find()
                ->where(['status' => true])
                ->orderBy(['priority' => SORT_ASC])
                ->all();
                
            // Cache rules for 5 minutes
            Yii::$app->cache->set(
                $cacheKey, 
                $this->_rules,
                300,
                new TagDependency(['tags' => ['firewall']])
            );
        }
        
        // Pre-process rules into whitelist and blacklist
        foreach ($this->_rules as $rule) {
            if ($rule->action === FirewallRule::ACTION_ALLOW) {
                $this->_whitelistIps[] = $rule->ip_range;
            } else {
                $this->_blacklistIps[] = $rule->ip_range;
            }
        }
    }
    
    /**
     * Check if the given IP is allowed according to firewall rules
     *
     * @param string $ip IP address to check
     * @return bool whether the IP is allowed
     */
    public function checkAccess($ip)
    {
        // First check whitelisted IPs - these always pass
        foreach ($this->_whitelistIps as $allowedIp) {
            if ($this->ipMatches($ip, $allowedIp)) {
                return true;
            }
        }
        
        // Then check blacklisted IPs - these always fail
        foreach ($this->_blacklistIps as $blockedIp) {
            if ($this->ipMatches($ip, $blockedIp)) {
                return false;
            }
        }
        
        // If no rules match, use the default action
        /** @var Module $module */
        $module = Yii::$app->getModule('firewall');
        return $module->defaultAction === 'allow';
    }
    
    /**
     * Checks if an IP matches a rule pattern
     * 
     * @param string $ip The IP to check
     * @param string $pattern The pattern to match against
     * @return bool whether the IP matches the pattern
     */
    protected function ipMatches($ip, $pattern)
    {
        if (strpos($pattern, '/') !== false) {
            // CIDR notation
            return $this->ipMatchesCidr($ip, $pattern);
        } else if (strpos($pattern, '-') !== false) {
            // IP range
            return $this->ipMatchesRange($ip, $pattern);
        } else if (strpos($pattern, '*') !== false) {
            // Wildcard pattern
            return $this->ipMatchesWildcard($ip, $pattern);
        }
        
        // Single IP
        return $ip === $pattern;
    }
    
    /**
     * Checks if an IP matches a CIDR pattern
     * 
     * @param string $ip The IP to check
     * @param string $cidr The CIDR pattern
     * @return bool whether the IP matches the CIDR
     */
    protected function ipMatchesCidr($ip, $cidr)
    {
        list($subnet, $bits) = explode('/', $cidr);
        $ip = ip2long($ip);
        $subnet = ip2long($subnet);
        $mask = -1 << (32 - $bits);
        $subnet &= $mask;
        
        return ($ip & $mask) === $subnet;
    }
    
    /**
     * Checks if an IP matches a range pattern (e.g. 192.168.1.1-192.168.1.100)
     * 
     * @param string $ip The IP to check
     * @param string $range The range pattern
     * @return bool whether the IP is in the range
     */
    protected function ipMatchesRange($ip, $range)
    {
        list($start, $end) = explode('-', $range);
        $ip = ip2long($ip);
        
        return $ip >= ip2long($start) && $ip <= ip2long($end);
    }
    
    /**
     * Checks if an IP matches a wildcard pattern (e.g. 192.168.1.*)
     * 
     * @param string $ip The IP to check
     * @param string $wildcard The wildcard pattern
     * @return bool whether the IP matches the wildcard
     */
    protected function ipMatchesWildcard($ip, $wildcard)
    {
        $pattern = '/^' . str_replace(['.', '*'], ['\.', '.*'], $wildcard) . '$/';
        return (bool) preg_match($pattern, $ip);
    }
    
    /**
     * Clears the firewall rules cache
     */
    public function clearCache()
    {
        TagDependency::invalidate(Yii::$app->cache, ['firewall']);
    }
}