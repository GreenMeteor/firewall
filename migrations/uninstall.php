<?php

use yii\db\Migration;
use humhub\modules\admin\Events;
use humhub\modules\dashboard\widgets\Sidebar;

/**
 * Uninstall script for the firewall module
 */
class uninstall extends Migration
{
    public function up()
    {
        // Drop the tables
        $this->dropTable('firewall_log');
        $this->dropTable('firewall_rule');

        // Remove module event listeners
        Yii::$app->events->off(Events::EVENT_ADMIN_MENU_INIT, ['humhub\modules\firewall\Events', 'onAdminMenuInit']);
        Yii::$app->events->off(Sidebar::EVENT_INIT, ['humhub\modules\firewall\Events', 'onSidebarInit']);

        // Clear any cache
        Yii::$app->cache->flush();

        return true;
    }

    public function down()
    {
        echo "Uninstall script cannot be reversed.\n";
        return false;
    }
}
