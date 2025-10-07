<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateSubscribers extends Migration
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
            'email' => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
            ],
            'verification_token' => [
                'type'       => 'VARCHAR',
                'constraint' => 64,
                'null'       => true,
            ],
            'is_verified' => [
                'type'       => 'TINYINT',
                'constraint' => 1,
                'default'    => 0,
            ],
            'verified_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'unsubscribe_token' => [
                'type'       => 'VARCHAR',
                'constraint' => 64,
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
        $this->forge->addKey('status_page_id');
        $this->forge->addKey(['email', 'status_page_id']);
        $this->forge->addKey('verification_token');
        $this->forge->addKey('unsubscribe_token');
        $this->forge->addForeignKey('status_page_id', 'status_pages', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('subscribers');
    }

    public function down()
    {
        $this->forge->dropTable('subscribers');
    }
}
