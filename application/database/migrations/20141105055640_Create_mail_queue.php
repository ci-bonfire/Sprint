<?php

/**
 * Migration: Create Mail Queue
 *
 * Created by: SprintPHP
 * Created on: 2014-11-05 05:56:40 am
 */
class Migration_create_mail_queue extends CI_Migration {

    public function up ()
    {
        $fields = [
            'id'    => [
                'type'  => 'int',
                'constraint' => 11,
                'unsigned' => true,
                'auto_increment' => true
            ],
            'mailer' => [
                'type'  => 'varchar',
                'constraint' => 255
            ],
            'params'    => [
                'type'  => 'text',
                'null' => true
            ],
            'options'   => [
                'type'  => 'text',
                'null' => true
            ],
            'sent' => [
                'type'  => 'tinyint',
                'constraint' => 1,
                'default' => 0
            ],
            'sent_on' => [
                'type'  => 'datetime',
                'null'  => true,
                'default' => null
            ],
        ];

        $this->dbforge->add_field($fields);
        $this->dbforge->add_key('id', true);
        $this->dbforge->add_key('sent');
        $this->dbforge->create_table('mail_queue', FALSE, array('ENGINE' => 'InnoDB'));
    }

    //--------------------------------------------------------------------

    public function down ()
    {
        $this->dbforge->drop_table('mail_queue');
    }

    //--------------------------------------------------------------------

}
