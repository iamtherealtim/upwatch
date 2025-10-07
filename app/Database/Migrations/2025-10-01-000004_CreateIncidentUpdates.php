<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateIncidentUpdates extends Migration
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
            'incident_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
            ],
            'status' => [
                'type'       => 'ENUM',
                'constraint' => ['investigating', 'identified', 'monitoring', 'resolved', 'scheduled', 'in_progress', 'completed'],
                'default'    => 'investigating',
            ],
            'message' => [
                'type' => 'TEXT',
            ],
            'notify_subscribers' => [
                'type'       => 'TINYINT',
                'constraint' => 1,
                'default'    => 0,
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
        $this->forge->addKey('incident_id');
        $this->forge->addForeignKey('incident_id', 'incidents', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('incident_updates');
    }

    public function down()
    {
        $this->forge->dropTable('incident_updates');
    }
}
