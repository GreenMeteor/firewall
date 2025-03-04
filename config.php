<?php

use humhub\components\Application;
use humhub\commands\CronController;
use humhub\modules\firewall\Module;
use humhub\modules\firewall\Events;
use humhub\modules\admin\widgets\AdminMenu;

return [
    'id' => 'firewall',
    'class' => Module::class,
    'namespace' => 'humhub\modules\firewall',
    'isCoreModule' => true,
    'events' => [
        ['class' => AdminMenu::class, 'event' => AdminMenu::EVENT_INIT, 'callback' => [Events::class, 'onAdminMenuInit']],
        ['class' => Application::class, 'event' => Application::EVENT_BEFORE_REQUEST, 'callback' => [Events::class, 'onBeforeRequest']],
        ['class' => CronController::class, 'event' => CronController::EVENT_ON_HOURLY_RUN, 'callback' => [Events::class, 'onHourlyCron']],
    ],
    'urlManagerRules' => [
        'admin/firewall' => 'firewall/admin/index',
        'admin/firewall/create' => 'firewall/admin/create',
        'admin/firewall/update' => 'firewall/admin/update',
        'admin/firewall/delete' => 'firewall/admin/delete',
        'admin/firewall/settings' => 'firewall/admin/settings',
        'admin/firewall/logs' => 'firewall/admin/logs',
    ],
];
