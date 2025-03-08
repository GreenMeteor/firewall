<?php

namespace humhub\modules\firewall\widgets;

use Yii;
use yii\caching\Cache;
use humhub\components\Widget;
use humhub\modules\user\models\User;

class IPMonitor extends Widget
{
    /**
     * Maximum number of guest IPs to store
     */
    const MAX_GUEST_IPS = 100;
    
    /**
     * Cache duration in seconds (24 hours)
     */
    const CACHE_DURATION = 86400;

    public function run(): string
    {
        $request = Yii::$app->request;
        $user = Yii::$app->user;
        $guest = $user->isGuest;

        $accessData = [
            'ip' => $request->userIP,
            'isGuest' => $guest,
            'username' => !$guest ? User::findOne($user->id)->username : 'Guest',
        ];

        $cacheKey = 'guest_ips_list';
        $guestIps = $this->getGuestIps($cacheKey, $request->userIP, $guest);

        $loggedInData = !$guest ? [
            'ip' => $request->userIP,
            'isGuest' => false,
            'username' => $user->identity->username,
        ] : null;

        return $this->render('monitor', [
            'accessData' => $accessData,
            'loggedInData' => $loggedInData,
            'guestIps' => $guestIps,
        ]);
    }

    /**
     * Get the list of guest IPs, add current IP if needed
     * 
     * @param string $cacheKey The cache key to use
     * @param string $currentIp The current user's IP
     * @param bool $isGuest Whether the current user is a guest
     * @return array List of guest IPs with timestamps
     */
    protected function getGuestIps(string $cacheKey, string $currentIp, bool $isGuest): array
    {
        $guestIpsData = Yii::$app->cache->get($cacheKey) ?: [];
        
        if ($isGuest) {
            $now = time();

            $ipExists = false;
            foreach ($guestIpsData as $key => $data) {
                if ($data['ip'] === $currentIp) {
                    $guestIpsData[$key]['timestamp'] = $now;
                    $ipExists = true;
                    break;
                }
            }

            if (!$ipExists) {
                $guestIpsData[] = [
                    'ip' => $currentIp,
                    'timestamp' => $now
                ];
            }

            usort($guestIpsData, function($a, $b) {
                return $b['timestamp'] - $a['timestamp'];
            });

            if (count($guestIpsData) > self::MAX_GUEST_IPS) {
                $guestIpsData = array_slice($guestIpsData, 0, self::MAX_GUEST_IPS);
            }
            
            Yii::$app->cache->set($cacheKey, $guestIpsData, self::CACHE_DURATION);
        }

        return $guestIpsData;
    }
}
