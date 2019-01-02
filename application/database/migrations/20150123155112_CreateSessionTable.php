<?php

/**
 * Migration: CreateSessionTable
 *
 * Created by: SprintPHP
 * Created on: 2014-01-23 15:51:12 am
 *
 * @property $dbforge
 */
class Migration_Createsessiontable extends CI_Migration {

    public function up ()
    {
        $fields = array(
            'id' => array(
                'type'          => 'varchar',
                'constraint'    => 40,
                'null'          => false
            ),
            'ip_address' => array(
                'type'          => 'varchar',
                'constraint'    => 45,
                'null'          => false
            ),
            'timestamp' => array(
                'type'          => 'int',
                'constraint'    => 10,
                'unsigned'      => true,
                'default'       => '0',
                'null'          => false,
            ),
            'data' => array(
                'type'          => 'blob',
                'null'          => false
            )
        );

        $this->dbforge->add_field($fields);

        $this->dbforge->add_key('id', TRUE);
        $this->dbforge->add_key('ip_address', TRUE);
        $this->dbforge->add_key('ci_sessions_timestamp');

        $this->dbforge->create_table('ci_sessions', true, config_item('migration_create_table_attr'));
    }

    //--------------------------------------------------------------------

    public function down ()
    {
        $this->dbforge->drop_table('ci_sessions');
    }

    //--------------------------------------------------------------------

}
