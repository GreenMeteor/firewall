<?php

namespace humhub\modules\firewall\widgets;

use Yii;
use humhub\components\Widget;
use humhub\modules\user\models\User;
use yii\caching\Cache;

class IPMonitor extends Widget
{
    public function run(): string
    {
        $request = Yii::$app->request;
        $user = Yii::$app->user;
        $guest = Yii::$app->user->isGuest;

        $accessData = [
            'ip' => $request->userIP,
            'isGuest' => $guest,
            'username' => !$user->isGuest ? User::findOne($user->id)->username : 'Guest',
        ];

        $cacheKey = 'guest_ips_list';

        $guestIps = Yii::$app->cache->get($cacheKey) ?: [];

        if ($guest) {
            $guestIps[] = $request->userIP;

            Yii::$app->cache->set($cacheKey, $guestIps, 86400);
        }

        $loggedInData = !$guest ? [
            'ip' => $request->userIP,
            'isGuest' => false,
            'username' => $user->identity->username,
        ] : null;

        return $this->render('accessing-ips', [
            'accessData' => $accessData,
            'loggedInData' => $loggedInData,
            'guestIps' => $guestIps,
        ]);
    }
}
