<?php

use humhub\modules\tour\TourConfig;
use humhub\modules\firewall\controllers\AdminController;
use yii\helpers\Url;

return [
    TourConfig::KEY_TOUR_ID => 'firewall-admin-tour',
    TourConfig::KEY_IS_VISIBLE => Yii::$app->user->isAdmin(),
    TourConfig::KEY_TOUR_ON_CONTROLLER_CLASS => AdminController::class,
    TourConfig::KEY_TITLE => Yii::t('FirewallModule.base', '<strong>Guide:</strong> Firewall Module Administration'),
    TourConfig::KEY_START_URL => Url::to(['/firewall/admin/index', 'tour' => true]),
    TourConfig::KEY_NEXT_TOUR_ID => null,
    TourConfig::KEY_DRIVER_JS => [
        'steps' => [
            [
                'popover' => [
                    'title' => Yii::t('FirewallModule.base', '<strong>Welcome</strong>'),
                    'description' => Yii::t(
                        'FirewallModule.base',
                        'This tour will guide you through the Firewall module administration interface, including rules, settings, logs, and IP actions.'
                    ),
                ],
            ],
            [
                'element' => '#firewall-create-btn',
                'popover' => [
                    'title' => Yii::t('FirewallModule.base', '<strong>Create Rule</strong>'),
                    'description' => Yii::t(
                        'FirewallModule.base',
                        'Click this button to create a new firewall rule. A modal will open where you can define IP ranges, action type, description, and priority.'
                    ),
                ],
            ],
            [
                'element' => '#firewall-rules-panel',
                'popover' => [
                    'title' => Yii::t('FirewallModule.base', '<strong>Firewall Rules</strong>'),
                    'description' => Yii::t(
                        'FirewallModule.base',
                        'All firewall rules are listed here. You can edit, delete, or toggle the status of each rule using the buttons on each card.'
                    ),
                ],
            ],
            [
                'element' => '#firewall-settings-btn',
                'popover' => [
                    'title' => Yii::t('FirewallModule.base', '<strong>Settings</strong>'),
                    'description' => Yii::t(
                        'FirewallModule.base',
                        'Click this button to configure global firewall settings, including enabling/disabling the module, logging, notifications, default action, and deny messages.'
                    ),
                ],
            ],
            [
                'element' => '#firewall-logs-btn',
                'popover' => [
                    'title' => Yii::t('FirewallModule.base', '<strong>Access Logs</strong>'),
                    'description' => Yii::t(
                        'FirewallModule.base',
                        'Click here to view firewall logs. You can clear all logs using the button at the bottom of the modal.'
                    ),
                ],
            ],
            [
                'element' => '#firewall-ip-actions',
                'popover' => [
                    'title' => Yii::t('FirewallModule.base', '<strong>IP & Rate Limit Actions</strong>'),
                    'description' => Yii::t(
                        'FirewallModule.base',
                        'This panel shows active IPs and their rate limits. You can reset rate limits or clear IP logs from here.'
                    ),
                ],
            ],
            [
                'popover' => [
                    'title' => Yii::t('FirewallModule.base', '<strong>All Set!</strong>'),
                    'description' => Yii::t(
                        'FirewallModule.base',
                        'You have now learned how to manage firewall rules, settings, logs, and IP actions. You are ready to secure your platform!'
                    ),
                ],
            ],
        ],
    ],
];
