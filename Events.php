<?php

namespace humhub\modules\firewall;

use Yii;
use yii\base\Event;
use yii\helpers\Url;
use humhub\modules\ui\menu\MenuLink;
use humhub\modules\ui\icon\widgets\Icon;
use humhub\modules\admin\widgets\AdminMenu;
use humhub\modules\firewall\models\FirewallLog;
use humhub\modules\admin\permissions\ManageModules;

class Events
{
    /**
     * Handles the AdminMenu init event
     * 
     * @param Event $event
     */
    public static function onAdminMenuInit($event)
    {
        if (!Yii::$app->user->can(ManageModules::class)) {
            return;
        }

        /** @var AdminMenu $menu */
        $menu = $event->sender;

        $menu->addEntry(new MenuLink([
            'label' => Yii::t('FirewallModule.base', 'Firewall'),
            'url' => Url::toRoute('/firewall/admin/index'),
            'icon' => Icon::get('shield'),
            'isActive' => Yii::$app->controller->module && Yii::$app->controller->module->id == 'firewall' && Yii::$app->controller->id == 'admin',
            'sortOrder' => 650,
            'isVisible' => true,
        ]));
    }

    /**
     * Handles the Application's beforeRequest event.
     * Performs firewall checks before serving the request
     *
     * @param Event $event
     */
    public static function onBeforeRequest($event)
    {
        /** @var \humhub\components\Application $app */
        $app = $event->sender;
        
        /** @var Module $module */
        $module = Yii::$app->getModule('firewall');
        
        if (!$module->enableFirewall) {
            return;
        }
        
        // Skip check on console requests
        if ($app instanceof \yii\console\Application) {
            return;
        }
        
        $isAllowed = $module->checkAccess();
        
        if (!$isAllowed) {
            // Log the blocked request
            $log = new FirewallLog();
            $log->ip = Yii::$app->request->userIP;
            $log->url = Yii::$app->request->url;
            $log->user_agent = Yii::$app->request->userAgent;
            $log->save();
            
            // Render access denied page
            echo Yii::$app->view->renderFile(
                $module->denyView . '.php', 
                ['ip' => Yii::$app->request->userIP]
            );
            
            Yii::$app->end();
        }
    }

    /**
     * Handles hourly cron events
     * Used for cleaning up old log entries and other maintenance tasks
     * 
     * @param Event $event
     */
    public static function onHourlyCron($event)
    {
        // Clean up old log entries (older than 30 days)
        FirewallLog::deleteAll(['<', 'created_at', new \yii\db\Expression('DATE_SUB(NOW(), INTERVAL 30 DAY)')]);
    }
}