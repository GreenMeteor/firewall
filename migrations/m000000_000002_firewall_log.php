<?php

use humhub\components\Migration;

/**
 * Creates the firewall_log table
 */
class m000000_000002_firewall_log extends Migration
{
    public function safeUp()
    {
        $this->createTable('firewall_log', [
            'id' => $this->primaryKey(),
            'ip' => $this->string()->notNull(),
            'url' => $this->text(),
            'user_agent' => $this->text(),
            'created_at' => $this->dateTime(),
        ]);
        
        $this->createIndex('idx-firewall_log-ip', 'firewall_log', 'ip');
        $this->createIndex('idx-firewall_log-created_at', 'firewall_log', 'created_at');
    }
}