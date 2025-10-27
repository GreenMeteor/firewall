<?php

use humhub\components\Migration;

/**
 * Creates the firewall_rule table
 */
class m000000_000001_firewall_rule extends Migration
{
    protected string $table = 'firewall_rule';

    public function safeUp()
    {
        $this->safeCreateTable($this->table, [
            'id' => $this->primaryKey(),
            'ip_range' => $this->string()->notNull(),
            'action' => $this->string(10)->notNull(),
            'description' => $this->text(),
            'priority' => $this->integer()->defaultValue(100),
            'status' => $this->boolean()->defaultValue(true),
            'created_at' => $this->timestampWithoutAutoUpdate(),
            'created_by' => $this->integer(),
            'updated_at' => $this->timestampWithoutAutoUpdate(),
            'updated_by' => $this->integer(),
        ]);

        $this->safeCreateIndex('idx-firewall_rule-priority', $this->table, 'priority');
        $this->safeCreateIndex('idx-firewall_rule-status', $this->table, 'status');

        $this->safeAddForeignKeyCreatedBy();
        $this->safeAddForeignKeyUpdatedBy();

        $this->insertSilent($this->table, [
            'ip_range' => '127.0.0.1',
            'action' => 'allow',
            'description' => 'Allow localhost',
            'priority' => 10,
            'status' => true,
        ]);

        $this->insertSilent($this->table, [
            'ip_range' => '::1',
            'action' => 'allow',
            'description' => 'Allow localhost IPv6',
            'priority' => 10,
            'status' => true,
        ]);

        $this->insertSilent($this->table, [
            'ip_range' => '192.168.0.0/16',
            'action' => 'allow',
            'description' => 'Allow local network',
            'priority' => 20,
            'status' => true,
        ]);
    }

    public function safeDown()
    {
        $this->safeDropTable($this->table);
    }
}
