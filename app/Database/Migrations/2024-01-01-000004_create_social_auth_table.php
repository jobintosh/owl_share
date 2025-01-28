<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateSocialAuthTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'auto_increment' => true,
            ],
            'user_id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
            ],
            'provider' => [
                'type' => 'VARCHAR',
                'constraint' => 50,
            ],
            'social_id' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
            ],
            'access_token' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'refresh_token' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'token_expires_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'profile_data' => [
                'type' => 'JSON',
                'null' => true,
            ],
            'created_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'updated_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ]
        ]);

        $this->forge->addKey('id', true);
        $this->forge->addKey(['user_id', 'provider']);
        $this->forge->addUniqueKey(['provider', 'social_id']);
        $this->forge->addForeignKey('user_id', 'users', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('social_auth');
    }

    public function down()
    {
        $this->forge->dropTable('social_auth');
    }
}