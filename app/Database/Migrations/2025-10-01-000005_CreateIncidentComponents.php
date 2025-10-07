<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateIncidentComponents extends Migration
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
            'component_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
            ],
        ]);

        $this->forge->addKey('id', true);
        $this->forge->addKey(['incident_id', 'component_id']);
        $this->forge->addForeignKey('incident_id', 'incidents', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('component_id', 'components', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('incident_components');
    }

    public function down()
    {
        $this->forge->dropTable('incident_components');
    }
}
