<?php

$up     = '';
$down   = '';

//--------------------------------------------------------------------
// Actions
//--------------------------------------------------------------------

/*
 * Create
 */
if ($action == 'create')
{
	$up = "\$fields = {$fields};

		\$this->dbforge->add_field(\$fields);
";

	if (! empty($primary_key))
	{
		$up .= "        \$this->dbforge->add_key('{$primary_key}', true);
";
	}

	$up .="	    \$this->dbforge->create_table('{$table}');
	";

	$down = "\$this->dbforge->drop_table('{$table}');";
}

/*
 * Add
 */

/*
 * Remove
 */

//--------------------------------------------------------------------
// The Template
//--------------------------------------------------------------------

echo "<?php

/**
 * Migration: {$clean_name}
 *
 * Created by: SprintPHP
 * Created on: {$today}
 */
class Migration_{$name} extends CI_Migration {

    public function up ()
    {
		{$up}
    }

    //--------------------------------------------------------------------

    public function down ()
    {
		{$down}
    }

    //--------------------------------------------------------------------

}";