<?php

/**
 * Migration: Create Settings Table
 *
 * Created by: SprintPHP
 * Created on: 2014-10-20 18:49:26 pm
 */
class Migration_create_settings_table extends CI_Migration {

    public function up ()
    {
        $fields = [
            'name'  => [
                'type'  => 'varchar',
                'constraint' => 255
            ],
            'value' => [
                'type' => 'varchar',
                'constraint' => 255,
                'null'  => true
            ],
            'group' => [
                'type' => 'varchar',
                'constraint' => 255
            ]
        ];
        $this->dbforge->add_field($fields);
        $this->dbforge->add_key(['name', 'group']);
        $this->dbforge->create_table('settings', FALSE, array('ENGINE' => 'InnoDB'));
    }

    //--------------------------------------------------------------------

    public function down ()
    {
        $this->dbforge->drop_table('settings');
    }

    //--------------------------------------------------------------------

}
