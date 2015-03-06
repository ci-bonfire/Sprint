<?php

/**
 * Migration: Create User Tables
 *
 * Created by: SprintPHP
 * Created on: 2014-10-10 06:39:11 am
 */
class Migration_create_user_tables extends CI_Migration {

    public function up ()
    {
        // Users
        $fields = [
            'id'    => [
                'type'  => 'int',
                'constraint' => 11,
                'unsigned'  => true,
                'auto_increment' => true
            ],
            'email' => [
                'type' => 'varchar',
                'constraint' => 255
            ],
            'username'  => [
                'type'  => 'varchar',
                'constraint'    => 30,
                'null'          => true
            ],
            'password_hash' => [
                'type' => 'varchar',
                'constraint' => 255
            ],
            'reset_hash' => [
                'type' => 'varchar',
                'constraint' => 40,
                'null'      => true
            ],
            'activate_hash' => [
                'type' => 'varchar',
                'constraint' => 40,
                'null'  => true
            ],
            'created_on' => [
                'type' => 'datetime',
                'default' => '0000-00-00 00:00:00'
            ],
            'status' => [
                'type' => 'varchar',
                'constraint'    => 255,
                'null'      => true
            ],
            'status_message' => [
                'type' => 'varchar',
                'constraint'    => 255,
                'null'      => true
            ],
            'active'  => [
                'type'          => 'tinyint',
                'constraint'    => 1,
                'null'          => 0,
                'default'       => 0
            ],
            'deleted'  => [
                'type'          => 'tinyint',
                'constraint'    => 1,
                'null'          => 0,
                'default'       => 0
            ],
            'force_pass_reset'  => [
                'type'          => 'tinyint',
                'constraint'    => 1,
                'null'          => 0,
                'default'       => 0
            ],
        ];

        $this->dbforge->add_field($fields);
        $this->dbforge->add_key('id', true);
        $this->dbforge->add_key('email');

        $this->dbforge->create_table('users');

        // User Meta
        $fields = [
            'user_id'   => [
                'type'          => 'int',
                'constraint'    => 11,
                'unsigned'      => true
            ],
            'meta_key'    => [
                'type'          => 'varchar',
                'constraint'    => 255
            ],
            'meta_value'    => [
                'type'          => 'text',
                'null'       => true
            ],
        ];

        $this->dbforge->add_field($fields);
        $this->dbforge->add_key(['user_id', 'meta_key']);

        $this->dbforge->create_table('user_meta');

    }

    //--------------------------------------------------------------------

    public function down ()
    {
        $this->dbforge->drop_table('users');
        $this->dbforge->drop_table('user_meta');
    }

    //--------------------------------------------------------------------

}
