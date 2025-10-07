<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateMonitors extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id' => [
                'type'           => 'INT',
                'constraint'     => 11,
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'component_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
            ],
            'name' => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
            ],
            'type' => [
                'type'       => 'ENUM',
                'constraint' => ['http', 'https', 'tcp', 'ping', 'ssl'],
                'default'    => 'https',
            ],
            'target' => [
                'type'       => 'VARCHAR',
                'constraint' => 500,
                'comment'    => 'URL, IP:PORT, or hostname',
            ],
            'interval' => [
                'type'       => 'INT',
                'constraint' => 11,
                'default'    => 300,
                'comment'    => 'Check interval in seconds',
            ],
            'timeout' => [
                'type'       => 'INT',
                'constraint' => 11,
                'default'    => 10,
                'comment'    => 'Timeout in seconds',
            ],
            'method' => [
                'type'       => 'VARCHAR',
                'constraint' => 10,
                'default'    => 'GET',
            ],
            'expected_status_code' => [
                'type'       => 'INT',
                'constraint' => 11,
                'default'    => 200,
            ],
            'keyword_match' => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
                'null'       => true,
            ],
            'retry_count' => [
                'type'       => 'INT',
                'constraint' => 11,
                'default'    => 3,
            ],
            'failure_threshold' => [
                'type'       => 'INT',
                'constraint' => 11,
                'default'    => 3,
                'comment'    => 'Number of failed checks before marking as down',
            ],
            'consecutive_failures' => [
                'type'       => 'INT',
                'constraint' => 11,
                'default'    => 0,
            ],
            'last_check_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'last_status' => [
                'type'       => 'ENUM',
                'constraint' => ['up', 'down', 'unknown'],
                'default'    => 'unknown',
            ],
            'is_active' => [
                'type'       => 'TINYINT',
                'constraint' => 1,
                'default'    => 1,
            ],
            'created_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'updated_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
        ]);

        $this->forge->addKey('id', true);
        $this->forge->addKey('component_id');
        $this->forge->addKey(['is_active', 'last_check_at']);
        $this->forge->addForeignKey('component_id', 'components', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('monitors');
    }

    public function down()
    {
        $this->forge->dropTable('monitors');
    }
}
