<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateIncidents extends Migration
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
            'status_page_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
            ],
            'title' => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
            ],
            'status' => [
                'type'       => 'ENUM',
                'constraint' => ['investigating', 'identified', 'monitoring', 'resolved', 'scheduled'],
                'default'    => 'investigating',
            ],
            'impact' => [
                'type'       => 'ENUM',
                'constraint' => ['none', 'minor', 'major', 'critical'],
                'default'    => 'minor',
            ],
            'scheduled_start' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'scheduled_end' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'resolved_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'is_visible' => [
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
        $this->forge->addKey('status_page_id');
        $this->forge->addKey(['status', 'created_at']);
        $this->forge->addForeignKey('status_page_id', 'status_pages', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('incidents');
    }

    public function down()
    {
        $this->forge->dropTable('incidents');
    }
}
