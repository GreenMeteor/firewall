<?php
use humhub\components\Migration;
/**
 * Creates the firewall_rule table
 */
class m000000_000001_firewall_rule extends Migration
{
    public function safeUp()
    {
        $this->createTable('firewall_rule', [
            'id' => $this->primaryKey(),
            'ip_range' => $this->string()->notNull(),
            'action' => $this->string(10)->notNull(),
            'description' => $this->text(),
            'priority' => $this->integer()->defaultValue(100),
            'status' => $this->boolean()->defaultValue(true),
            'created_at' => $this->dateTime(),
            'created_by' => $this->integer(),
            'updated_at' => $this->dateTime(),
            'updated_by' => $this->integer(),
        ]);
        
        $this->createIndex('idx-firewall_rule-priority', 'firewall_rule', 'priority');
        $this->createIndex('idx-firewall_rule-status', 'firewall_rule', 'status');
        
        // Add foreign key for created_by and updated_by
        $this->addForeignKey(
            'fk-firewall_rule-created_by',
            'firewall_rule',
            'created_by',
            '{{%user}}',
            'id',
            'SET NULL',
            'CASCADE'
        );
        
        $this->addForeignKey(
            'fk-firewall_rule-updated_by',
            'firewall_rule',
            'updated_by',
            '{{%user}}',
            'id',
            'SET NULL',
            'CASCADE'
        );
        
        // Add some default rules
        $this->insert('firewall_rule', [
            'ip_range' => '127.0.0.1',
            'action' => 'allow',
            'description' => 'Allow localhost',
            'priority' => 10,
            'status' => true,
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s')
        ]);
        
        $this->insert('firewall_rule', [
            'ip_range' => '::1',
            'action' => 'allow',
            'description' => 'Allow localhost IPv6',
            'priority' => 10,
            'status' => true,
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s')
        ]);
        
        $this->insert('firewall_rule', [
            'ip_range' => '192.168.0.0/16',
            'action' => 'allow',
            'description' => 'Allow local network',
            'priority' => 20,
            'status' => true,
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s')
        ]);
    }
}