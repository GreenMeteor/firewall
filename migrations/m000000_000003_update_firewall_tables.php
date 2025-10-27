<?php

use humhub\components\Migration;

/**
 * Updates firewall tables to match new standards
 */
class m000000_000003_update_firewall_tables extends Migration
{
    public function safeUp()
    {
        $firewallRuleTable = 'firewall_rule';

        if ($this->db->getTableSchema($firewallRuleTable, true)) {
            $this->safeCreateIndex('idx-firewall_rule-priority', $firewallRuleTable, 'priority');
            $this->safeCreateIndex('idx-firewall_rule-status', $firewallRuleTable, 'status');

            $this->safeAddForeignKey(
                'fk-firewall_rule-created_by',
                $firewallRuleTable,
                'created_by',
                '{{%user}}',
                'id',
                'SET NULL',
                'CASCADE'
            );

            $this->safeAddForeignKey(
                'fk-firewall_rule-updated_by',
                $firewallRuleTable,
                'updated_by',
                'user',
                'id',
                'SET NULL',
                'CASCADE'
            );
        }

        $firewallLogTable = 'firewall_log';

        if ($this->db->getTableSchema($firewallLogTable, true)) {
            $this->safeCreateIndex('idx-firewall_log-ip', $firewallLogTable, 'ip');
            $this->safeCreateIndex('idx-firewall_log-created_at', $firewallLogTable, 'created_at');
        }
    }

    public function safeDown()
    {
        echo "m000000_000003_update_firewall_tables cannot be reverted.\n";
        return false;
    }
}
