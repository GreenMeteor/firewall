<?php

namespace humhub\modules\firewall\widgets;

use Yii;
use humhub\components\Widget;
use humhub\modules\user\models\User;
use humhub\modules\admin\permissions\ManageUsers;
use humhub\modules\admin\permissions\ManageSettings;

class IPMonitor extends Widget
{
    /**
     * Maximum number of IPs to store
     */
    const MAX_IPS = 200;

    /**
     * Cache duration in seconds (24 hours)
     */
    const CACHE_DURATION = 86400;

    /**
     * Cache key for storing all IPs
     */
    const CACHE_KEY = 'firewall_all_ips_list';
    
    /**
     * Cache key prefix for rate limiting
     */
    const RATE_LIMIT_KEY_PREFIX = 'firewall_rate_limit_';
    
    /**
     * Maximum requests allowed in the time window
     */
    const RATE_LIMIT_MAX_REQUESTS = 100;
    
    /**
     * Rate limit time window in seconds (5 minutes)
     */
    const RATE_LIMIT_WINDOW = 300;

    public function run(): string
    {
        $user = Yii::$app->user;
        $isGuest = Yii::$app->user->isGuest;

        $isAdmin = false;
        if (!$isGuest) {
            $currentUser = User::findOne(['id' => $user->id]);

            $isAdmin = $currentUser->isSystemAdmin() || 
                      $currentUser->can(ManageUsers::class) || 
                      $currentUser->can(ManageSettings::class);
        }

        $clientIP = $this->getClientIP();
        $isSuspicious = $this->isSuspiciousIP($clientIP);
        $isRateLimited = $this->checkRateLimit($clientIP);

        $accessData = [
            'ip' => $clientIP,
            'isGuest' => $isGuest,
            'username' => !$isGuest ? User::findOne($user->id)->username : 'Guest',
            'suspicious' => $isSuspicious,
            'rateLimited' => $isRateLimited
        ];

        if (!$isRateLimited) {
            $this->recordAccess($clientIP, $isGuest, $accessData['username'], $isSuspicious);
        }

        // Only fetch all IPs if user has admin permissions
        $allIps = $isAdmin ? $this->getAllIps() : [];
        $uniqueIpsCount = count(array_unique(array_column($allIps, 'ip')));

        $guestIps = [];
        $loggedInIps = [];
        $suspiciousIps = [];

        foreach ($allIps as $ipData) {
            if ($ipData['isGuest']) {
                $guestIps[] = $ipData;
            } else {
                $loggedInIps[] = $ipData;
            }

            if (isset($ipData['suspicious']) && $ipData['suspicious']) {
                $suspiciousIps[] = $ipData;
            }
        }

        return $this->render('monitor', [
            'accessData' => $accessData,
            'isAdmin' => $isAdmin,
            'guestIps' => $guestIps,
            'loggedInIps' => $loggedInIps,
            'suspiciousIps' => $suspiciousIps,
            'uniqueIpsCount' => $uniqueIpsCount,
            'totalAccessesCount' => count($allIps)
        ]);
    }

    /**
     * Get the real client IP by checking various headers
     * 
     * @return string The client IP address
     */
    protected function getClientIP(): string
    {
        $ipHeaders = [
            'HTTP_CLIENT_IP',
            'HTTP_X_FORWARDED_FOR',
            'HTTP_X_FORWARDED',
            'HTTP_X_CLUSTER_CLIENT_IP',
            'HTTP_FORWARDED_FOR',
            'HTTP_FORWARDED',
            'REMOTE_ADDR'
        ];

        foreach ($ipHeaders as $header) {
            if (isset($_SERVER[$header]) && filter_var($_SERVER[$header], FILTER_VALIDATE_IP)) {
                if ($header === 'HTTP_X_FORWARDED_FOR') {
                    $ips = explode(',', $_SERVER[$header]);
                    $ip = trim($ips[0]);
                    if (filter_var($ip, FILTER_VALIDATE_IP)) {
                        return $ip;
                    }
                } else {
                    return $_SERVER[$header];
                }
            }
        }

        return Yii::$app->request->userIP;
    }

    /**
     * Check if the IP might be a proxy/VPN
     * 
     * @param string $ip IP address to check
     * @return bool Whether the IP is suspicious
     */
    protected function isSuspiciousIP(string $ip): bool
    {
        if (filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE) === false) {
            return true;
        }

        $userAgent = Yii::$app->request->userAgent;
        $proxyPatterns = ['proxy', 'vpn', 'tor', 'anonymous'];
        foreach ($proxyPatterns as $pattern) {
            if (stripos($userAgent, $pattern) !== false) {
                return true;
            }
        }

        return false;
    }
    
    /**
     * Implement basic rate limiting
     * 
     * @param string $ip The IP to check
     * @return bool Whether the IP is rate limited
     */
    protected function checkRateLimit(string $ip): bool
    {
        $cache = Yii::$app->cache;
        $key = self::RATE_LIMIT_KEY_PREFIX . md5($ip);

        $requestData = $cache->get($key);
        $now = time();

        if (!$requestData) {
            $requestData = [
                'count' => 1,
                'first_request' => $now
            ];
            $cache->set($key, $requestData, self::RATE_LIMIT_WINDOW);
            return false;
        }

        if (($now - $requestData['first_request']) > self::RATE_LIMIT_WINDOW) {
            $requestData = [
                'count' => 1,
                'first_request' => $now
            ];
            $cache->set($key, $requestData, self::RATE_LIMIT_WINDOW);
            return false;
        }

        $requestData['count']++;
        $cache->set($key, $requestData, self::RATE_LIMIT_WINDOW);

        return $requestData['count'] > self::RATE_LIMIT_MAX_REQUESTS;
    }

    /**
     * Record an access (guest or logged-in user)
     * 
     * @param string $ip The user's IP address
     * @param bool $isGuest Whether the user is a guest
     * @param string $username The username (or 'Guest')
     * @param bool $isSuspicious Whether the IP is suspicious
     */
    protected function recordAccess(string $ip, bool $isGuest, string $username, bool $isSuspicious = false): void
    {
        $cache = Yii::$app->cache;
        $allIpsData = $cache->get(self::CACHE_KEY) ?: [];
        $now = time();

        $recentlyLogged = false;
        
        if (!$isGuest) {
            foreach ($allIpsData as $entry) {
                if ($entry['ip'] === $ip && $entry['username'] === $username && 
                    ($now - $entry['timestamp']) < 300) {
                    $recentlyLogged = true;
                    break;
                }
            }
        }

        if (!$recentlyLogged || $isGuest) {
            $allIpsData[] = [
                'ip' => $ip,
                'isGuest' => $isGuest,
                'username' => $username,
                'timestamp' => $now,
                'suspicious' => $isSuspicious,
                'user_agent' => Yii::$app->request->userAgent,
                'request_uri' => Yii::$app->request->url
            ];

            usort($allIpsData, function($a, $b) {
                return $b['timestamp'] - $a['timestamp'];
            });

            if (count($allIpsData) > self::MAX_IPS) {
                $allIpsData = array_slice($allIpsData, 0, self::MAX_IPS);
            }

            $cache->set(self::CACHE_KEY, $allIpsData, self::CACHE_DURATION);
        }
    }
    
    /**
     * Get all IPs (both guests and logged-in users)
     * 
     * @return array List of all IP accesses with details
     */
    protected function getAllIps(): array
    {
        return Yii::$app->cache->get(self::CACHE_KEY) ?: [];
    }
    
    /**
     * Clear all IP records from cache
     * Can be called from a controller action to reset the log
     */
    public static function clearAllIps(): void
    {
        Yii::$app->cache->delete(self::CACHE_KEY);
    }
    
    /**
     * Reset rate limit for a specific IP
     * 
     * @param string $ip The IP address to reset
     */
    public static function resetRateLimit(string $ip): void
    {
        Yii::$app->cache->delete(self::RATE_LIMIT_KEY_PREFIX . md5($ip));
    }
    
    /**
     * Get rate limit information for an IP
     * 
     * @param string $ip The IP to check
     * @return array|null Rate limit data or null if not found
     */
    public static function getRateLimitInfo(string $ip): ?array
    {
        return Yii::$app->cache->get(self::RATE_LIMIT_KEY_PREFIX . md5($ip));
    }
}
