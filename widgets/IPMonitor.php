<?php

namespace humhub\modules\firewall\widgets;

use Yii;
use humhub\components\Widget;
use humhub\modules\user\models\User;

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

    public function run(): string
    {
        $request = Yii::$app->request;
        $user = Yii::$app->user;
        $isGuest = $user->isGuest;
        $isAdmin = !$isGuest && $user->identity->isSystemAdmin();
        
        // Current user's access data
        $accessData = [
            'ip' => $request->userIP,
            'isGuest' => $isGuest,
            'username' => !$isGuest ? User::findOne($user->id)->username : 'Guest',
        ];
        
        // Record this access
        $this->recordAccess($request->userIP, $isGuest, $accessData['username']);
        
        // Get all IPs (both guests and logged-in users)
        $allIps = $isAdmin ? $this->getAllIps() : [];
        
        // Count of unique IPs
        $uniqueIpsCount = count(array_unique(array_column($allIps, 'ip')));
        
        // Separate guest and logged-in user IPs for display
        $guestIps = [];
        $loggedInIps = [];
        
        foreach ($allIps as $ipData) {
            if ($ipData['isGuest']) {
                $guestIps[] = $ipData;
            } else {
                $loggedInIps[] = $ipData;
            }
        }
        
        return $this->render('monitor', [
            'accessData' => $accessData,
            'isAdmin' => $isAdmin,
            'guestIps' => $guestIps,
            'loggedInIps' => $loggedInIps,
            'uniqueIpsCount' => $uniqueIpsCount,
            'totalAccessesCount' => count($allIps)
        ]);
    }

    /**
     * Record an access (guest or logged-in user)
     * 
     * @param string $ip The user's IP address
     * @param bool $isGuest Whether the user is a guest
     * @param string $username The username (or 'Guest')
     */
    protected function recordAccess(string $ip, bool $isGuest, string $username): void
    {
        $cache = Yii::$app->cache;
        $allIpsData = $cache->get(self::CACHE_KEY) ?: [];
        $now = time();
        
        // Add this access to the list
        $allIpsData[] = [
            'ip' => $ip,
            'isGuest' => $isGuest,
            'username' => $username,
            'timestamp' => $now
        ];
        
        // Sort by timestamp (most recent first)
        usort($allIpsData, function($a, $b) {
            return $b['timestamp'] - $a['timestamp'];
        });
        
        // Limit to maximum number of IPs
        if (count($allIpsData) > self::MAX_IPS) {
            $allIpsData = array_slice($allIpsData, 0, self::MAX_IPS);
        }
        
        // Save back to cache
        $cache->set(self::CACHE_KEY, $allIpsData, self::CACHE_DURATION);
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
}
