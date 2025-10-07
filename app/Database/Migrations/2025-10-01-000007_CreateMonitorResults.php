<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateMonitorResults extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id' => [
                'type'           => 'BIGINT',
                'constraint'     => 20,
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'monitor_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
            ],
            'status' => [
                'type'       => 'ENUM',
                'constraint' => ['up', 'down'],
            ],
            'response_time' => [
                'type'       => 'INT',
                'constraint' => 11,
                'null'       => true,
                'comment'    => 'Response time in milliseconds',
            ],
            'status_code' => [
                'type'       => 'INT',
                'constraint' => 11,
                'null'       => true,
            ],
            'error_message' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'checked_at' => [
                'type' => 'DATETIME',
            ],
        ]);

        $this->forge->addKey('id', true);
        $this->forge->addKey('monitor_id');
        $this->forge->addKey('checked_at');
        $this->forge->addForeignKey('monitor_id', 'monitors', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('monitor_results');
    }

    public function down()
    {
        $this->forge->dropTable('monitor_results');
    }
}
