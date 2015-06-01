<?php

/**
 * Migration: Create Authorization Tables
 *
 * Created by: SprintPHP
 * Created on: 2014-12-11 05:41:40 am
 *
 * @property $dbforge
 */
class Migration_create_authorization_tables extends CI_Migration {

    public function up ()
    {
		/**
		 * Groups Table
		 */
	    $fields = [
		    'id'    => [
			    'type'  => 'int',
			    'constraint' => 11,
			    'unsigned' => true,
			    'auto_increment' => true
		    ],
		    'name'  => [
			    'type' => 'varchar',
			    'constraint' => 255
		    ],
		    'description' => [
			    'type' => 'varchar',
			    'constraint' => 255
		    ]
	    ];

	    $this->dbforge->add_field($fields);
	    $this->dbforge->add_key('id', true);
	    $this->dbforge->create_table('auth_groups', true, config_item('migration_create_table_attr'));
	    /**
	     * Permissions Table
	     */
	    $fields = [
		    'id'    => [
			    'type'  => 'int',
			    'constraint' => 11,
			    'unsigned' => true,
			    'auto_increment' => true
		    ],
		    'name'  => [
			    'type' => 'varchar',
			    'constraint' => 255
		    ],
		    'description' => [
			    'type' => 'varchar',
			    'constraint' => 255
		    ]
	    ];

	    $this->dbforge->add_field($fields);
	    $this->dbforge->add_key('id', true);
	    $this->dbforge->create_table('auth_permissions', true, config_item('migration_create_table_attr'));

	    /**
	     * Groups/Permissions Table
	     */
	    $fields = [
		    'group_id'    => [
			    'type'  => 'int',
			    'constraint' => 11,
			    'unsigned' => true,
			    'default' => 0
		    ],
		    'permission_id'    => [
			    'type'  => 'int',
			    'constraint' => 11,
			    'unsigned' => true,
			    'default' => 0
		    ],
	    ];

	    $this->dbforge->add_field($fields);
	    $this->dbforge->add_key(['group_id', 'permission_id']);
	    $this->dbforge->create_table('auth_groups_permissions', true, config_item('migration_create_table_attr'));

	    /**
	     * Users/Groups Table
	     */
	    $fields = [
		    'group_id'    => [
			    'type'  => 'int',
			    'constraint' => 11,
			    'unsigned' => true,
			    'default' => 0
		    ],
		    'user_id'    => [
			    'type'  => 'int',
			    'constraint' => 11,
			    'unsigned' => true,
			    'default' => 0
		    ],
	    ];

	    $this->dbforge->add_field($fields);
	    $this->dbforge->add_key(['group_id', 'user_id']);
	    $this->dbforge->create_table('auth_groups_users', true, config_item('migration_create_table_attr'));
    }

    //--------------------------------------------------------------------

    public function down ()
    {
		$this->dbforge->drop_table('auth_groups');
		$this->dbforge->drop_table('auth_permissions');
		$this->dbforge->drop_table('auth_groups_permissions');
		$this->dbforge->drop_table('auth_groups_users');
    }

    //--------------------------------------------------------------------

}
