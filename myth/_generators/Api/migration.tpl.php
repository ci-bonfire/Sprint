<?php

$today = date('Y-m-d H:ia');

//--------------------------------------------------------------------
// The Template
//--------------------------------------------------------------------

echo "<?php

/**
 * Migration: Add api_key to user table.
 *
 * Created by: SprintPHP
 * Created on: {$today}
 */
class Migration_Add_api_key_to_users extends CI_Migration {

    public function up ()
    {
		\$field = [
			'api_key' => [
				'type' => 'VARCHAR',
				'constraint' => '255',
				'null' => true
			]
		];
		\$this->dbforge->add_column('users', \$field);
    }

    //--------------------------------------------------------------------

    public function down ()
    {
		\$this->dbforge->drop_column('users', 'api_key');
    }

    //--------------------------------------------------------------------

}";