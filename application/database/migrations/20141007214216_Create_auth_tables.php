<?php

/**
 * Migration: Create Auth Tables
 *
 * Created by: SprintPHP
 * Created on: 2014-10-07 21:42:16 pm
 *
 * @property $dbforge
 */
class Migration_create_auth_tables extends CI_Migration {

    public function up ()
    {
        // Auth Login Attempts
        $fields = [
            'id'    => [
                'type' => 'int',
                'constraint' => 11,
                'unsigned' => true,
                'auto_increment' => true
            ],
            'email' => [
                'type' => 'varchar',
                'constraint' => 255,
            ],
            'datetime' => [
                'type' => 'datetime',
            ]
        ];
        $this->dbforge->add_field($fields);
        $this->dbforge->add_key('id', true);
        $this->dbforge->add_key('email');

        $this->dbforge->create_table('auth_login_attempts', true, config_item('migration_create_table_attr'));

        // Auth Logins
        $fields = [
            'id'    => [
                'type' => 'int',
                'constraint' => 11,
                'unsigned' => true,
                'auto_increment' => true
            ],
            'user_id' => [
                'type' => 'int',
                'constraint' => 11,
            ],
            'ip_address' => [
                'type'  => 'varchar',
                'constraint'    => 40,
                'null'          => true
            ],
            'datetime' => [
                'type' => 'datetime',
            ]
        ];
        $this->dbforge->add_field($fields);
        $this->dbforge->add_key('id', true);
        $this->dbforge->add_key('email');

        $this->dbforge->create_table('auth_logins', true, config_item('migration_create_table_attr'));

        // Auth Tokens
        $fields = [
            'email' => [
                'type' => 'varchar',
                'constraint' => 255,
            ],
            'hash' => [
                'type' => 'char',
                'constraint' => 40
            ],
            'created' => [
                'type' => 'datetime',
            ]
        ];
        $this->dbforge->add_field($fields);
        $this->dbforge->add_key( ['email', 'hash'] );

        $this->dbforge->create_table('auth_tokens', true, config_item('migration_create_table_attr'));
    }

    //--------------------------------------------------------------------

    public function down ()
    {
        $this->dbforge->drop_table('auth_tokens');
        $this->dbforge->drop_table('auth_logins');
        $this->dbforge->drop_table('auth_login_attempts');
    }

    //--------------------------------------------------------------------

}
