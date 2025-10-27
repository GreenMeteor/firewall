<?php

use humhub\components\Migration;

/**
 * Creates the firewall_log table
 */
class m000000_000002_firewall_log extends Migration
{
    protected string $table = 'firewall_log';

    public function safeUp()
    {
        $this->safeCreateTable($this->table, [
            'id' => $this->primaryKey(),
            'ip' => $this->string()->notNull(),
            'url' => $this->text(),
            'user_agent' => $this->text(),
            'created_at' => $this->timestampWithoutAutoUpdate(),
        ]);

        $this->safeCreateIndex('idx-firewall_log-ip', $this->table, 'ip');
        $this->safeCreateIndex('idx-firewall_log-created_at', $this->table, 'created_at');
    }

    public function safeDown()
    {
        $this->safeDropTable($this->table);
    }
}
