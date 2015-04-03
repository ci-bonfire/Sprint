<?php

echo "<?php

/**
 * Migration: Create Localization Table
 *
 * Created by: SprintPHP
 * Created on: {$today}
 */
class Migration_Create_localize_table extends CI_Migration {

    public function up ()
    {
		\$fields = [
		    'id' => [
		        'type'      => 'int',
                'constraint' => 11,
                'unsigned'  => true,
                'auto_increment' => true
		    ],
		    'object_id' => [
		        'type'       => 'int',
		        'constraint' => 11,
		        'unsigned'   => true,
		        'null'       => false
		    ],
		    'object_table' => [
		        'type'       => 'varchar',
		        'constraint' => 255,
		        'null'       => false
		    ],
		    'field' => [
		        'type'       => 'varchar',
		        'constraint' => 255,
		        'null'       => false
		    ],
		    'language' => [
		        'type'       => 'varchar',
		        'constraint' => 255,
		        'null'       => false
		    ],
		    'body' => [
		        'type'       => 'text',
		        'null'       => true
		    ],
		    'published' => [
		        'type'       => 'tinyint',
		        'constraint' => 1,
		        'null'       => false,
		        'default'    => 0
		    ],
		    'created_on' => [
		        'type'  => 'datetime'
		    ],
		    'modified_on' => [
		        'type'  => 'datetime'
		    ],
		    'deleted' => [
		        'type'       => 'tinyint',
		        'constraint' => 1,
                'null'       => false,
                'default'    => 0
		    ]
		];

		\$this->dbforge->add_field(\$fields);
		\$this->dbforge->add_key('id', true);
		\$this->dbforge->add_key('object_id');
		\$this->dbforge->create_table('translations');
    }

    //--------------------------------------------------------------------

    public function down ()
    {
		\$this->dbforge->drop_table('translations');
    }

    //--------------------------------------------------------------------

}";
