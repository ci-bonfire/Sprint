<?php

/**
 * Migration: Update Login Attempts Table
 *
 * Created by: SprintPHP
 * Created on: 2016-01-11 12:11pm
 *
 * @property $dbforge
 */
class Migration_UpdateLoginAttemptsTable extends CI_Migration {

    public function up()
    {
        $fields = array(
            'type' => array(
                'type'       => 'varchar',
                'constraint' => 64,
                'null'       => false,
                'default'    => 'app',
                'after'      => 'id'
            ),
            'ip_address' => array(
                'type'       => 'varchar',
                'constraint' => 255,
                'null'       => true,
                'after'      => 'type'
            ),
            'user_id' => array(
                'type'       => 'int',
                'constraint' => 11,
                'unsigned'   => true,
                'null'       => true,
                'after'      => 'ip_address'
            )
        );

        $this->dbforge->add_column('auth_login_attempts', $fields);

        $this->db->query('ALTER TABLE `auth_login_attempts` ADD KEY (`user_id`)');

        $this->dbforge->drop_column('auth_login_attempts', 'email');
    }

    //--------------------------------------------------------------------

    public function down()
    {
        $this->dbforge->drop_column('auth_login_attempts', 'type');
        $this->dbforge->drop_column('auth_login_attempts', 'ip_address');
        $this->dbforge->drop_column('auth_login_attempts', 'user_id');

        $column = ['email' => [
            'type'       => 'varchar',
            'constraint' => 255,
            'after'      => 'id'
        ]];

        $this->dbforge->add_column('auth_login_attempts', $column);

        $this->db->query('ALTER TABLE `auth_login_attempts` ADD KEY (`email`)');
    }

    //--------------------------------------------------------------------

}