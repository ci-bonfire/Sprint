<?php

/**
 * Migration: CreateSessionTable
 *
 * Created by: SprintPHP
 * Created on: 2014-10-01 05:22:49 am
 */
class Migration_CreateSessionTable extends CI_Migration {

    public function up ()
    {
        $fields = array(
            'session_id' => array(
                'type'          => 'varchar',
                'constraint'    => 40,
                'default'       => '0',
                'null'          => false
            ),
            'ip_address' => array(
                'type'          => 'varchar',
                'constraint'    => 45,
                'default'       => '0',
                'null'          => false
            ),
            'user_agent' => array(
                'type'          => 'varchar',
                'constraint'    => 120,
                'null'          => false,
            ),
            'last_activity' => array(
                'type'          => 'bigint',
                'constraint'    => 20,
                'unsigned'      => true,
                'default'       => '0',
                'null'          => false
            ),
            'user_data' => array(
                'type'          => 'text',
                'null'          => false
            )
        );

        $this->dbforge->add_field($fields);

        $this->dbforge->add_key('session_id', true);
        $this->dbforge->add_key('last_activity');

        $this->dbforge->create_table('ci_sessions');
    }

    //--------------------------------------------------------------------

    public function down ()
    {
        $this->dbforge->drop_table('ci_sessions');
    }

    //--------------------------------------------------------------------

}