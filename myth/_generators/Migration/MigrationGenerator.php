<?php

use Myth\CLI as CLI;

class MigrationGenerator extends \Myth\Forge\BaseGenerator {

	// The auto-determined action
	protected $action = null;

	// The table we're using, if any
	protected $table = null;

	// The fields to create for making new tables.
	protected $fields = [];

	// The field name to be used as a primary key
	protected $primary_key  = null;

	protected $use_exist_table = false;

	protected $defaultSizes = [
		'tinyint'   => 1,
		'int'       => 9,
		'bigint'    => 20,
		'char'      => 20,
		'varchar'   => 255,
	];

	protected $defaultValues = [
		'tinyint'   => 0,
		'int'       => 0,
		'bigint'    => 0,
		'float'     => 0.0,
		'double'    => 0.0,
		'char'      => 'NULL',
		'varchar'   => 'NULL',
		'text'      => 'NULL',
		'date'      => 'NULL',
		'datetime'  => 'NULL'
	];

	protected $defaultNulls = [
		'char'      => true,
		'varchar'   => true,
		'text'      => true,
		'date'      => true,
		'datetime'  => true
	];

	protected $map = [
		'string'    => 'varchar',
		'number'    => 'int'
	];

	protected $allowedActions = [
		'create', 'add', 'remove'
	];

	protected $actionMap = [
		'make'      => 'create',
		'insert'    => 'add',
		'drop'      => 'remove',
		'delete'    => 'remove'
	];

	//--------------------------------------------------------------------

	public function run($segments=[], $quiet=false)
	{
		$name = array_shift( $segments );

		if ( empty( $name ) )
		{
			$name = CLI::prompt( 'Migration name' );
		}

		// Format to CI Standards
		$name = str_replace('.php', '', strtolower( $name ) );

		$this->detectAction($name);

		if ( $quiet === false )
		{
			$this->collectOptions( $name );
		}

		$data = [
			'name'              => $name,
			'clean_name'        => ucwords(str_replace('_', ' ', $name)),
			'today'             => date( 'Y-m-d H:ia' ),
			'fields'            => trim( $this->stringify( $this->fields ), ', '),
			'action'            => $this->action,
			'table'             => $this->table,
			'primary_key'       => $this->primary_key
		];

		$this->load->library('migration');

		// todo Allow different migration "types"
		$destination = $this->migration->determine_migration_path('app');

		$file = $this->migration->make_name($name);

		$destination = rtrim($destination, '/') .'/'. $file;

		if (! $this->copyTemplate( 'migration', $destination, $data, true) )
		{
			CLI::error('Error creating seed file.');
		}

		return true;
	}

	//--------------------------------------------------------------------

	/**
	 * Examines the name of the migration and attempts to determine
	 * the correct action to build the migration around, based on the first
	 * word of the name.
	 *
	 * Examples:
	 *  'create_user_table'         action = 'create', table = 'user'
	 *  'add_name_to_user_table     action = 'add', table = 'user'
	 *
	 * @param $name
	 */
	public function detectAction($name)
	{
	    $segments = explode('_', $name);

		$action = trim(strtolower($segments[0]));

		// Is the action a convenience mapping?
		if (array_key_exists($action, $this->actionMap))
		{
			$action = $this->actionMap[$action];
		}

		if (! in_array($action, $this->allowedActions))
		{
			return;
		}

		$this->action = $action;

		// Are we referencing a table?
		if (! $index = array_search('table', $segments))
		{
			return;
		}

		// The name of the table is assumed to be the one
		// prior to the $index found.
		$this->table = $segments[$index - 1];
	}

	//--------------------------------------------------------------------

	public function collectOptions($name)
	{
		$options = CLI::getOptions();

		// Use existing db table?
		if (! empty($options['dbtable']) )
		{
			$this->readTable($options['dbtable']);
		}
		// Otherwise try to use any fields from the CLI
		else
		{
			$fields = empty( $options['fields'] ) ?
				CLI::prompt( 'Fields? (name:type)' ) :
				$options['fields'];
			$this->fields = $this->parseFields( $fields );
		}

		// Use existing db table?
//		die(var_dump($this->fields));
	}

	//--------------------------------------------------------------------

	/**
	 * Parses a string containing 'name:type' segments into an array of
	 * fields ready for $dbforge;
	 *
	 * @param $str
	 *
	 * @return array
	 */
	public function parseFields($str)
	{
        if (empty($str))
        {
	        return;
        }

		$fields = [];
		$segments = explode(' ', $str);

		if (! count($segments))
		{
			return $fields;
		}

		foreach ($segments as $segment)
		{
			$pop = [null, null, null];
			list($field, $type, $size) = array_merge( explode(':', $segment), $pop);
			$type = strtolower($type);

			// Is type one of our convenience mapped items?
			if (array_key_exists($type, $this->map))
			{
				$type = $this->map[$type];
			}

			$f = [ 'type' => $type ];

			// Creating a primary key?
			if ($type == 'id')
			{
				$f['type'] = 'int';
				$size = empty($size) ? 9 : $size;
				$f['unsigned'] = true;
				$f['auto_increment'] = true;

				$this->primary_key = $field;
			}

			// Constraint?
			if (! empty($size))
			{
				$f['constraint'] = (int)$size;
			}
			else if (array_key_exists($type, $this->defaultSizes))
			{
				$f['constraint'] = $this->defaultSizes[$type];
			}

			// NULL?
			if (array_key_exists($type, $this->defaultNulls))
			{
				$f['null'] = true;
			}

			// Default Value?
			if (array_key_exists($type, $this->defaultValues))
			{
				$f['default'] = $this->defaultValues[$type];
			}

			$fields[$field] = $f;
		}

		return $fields;
	}

	//--------------------------------------------------------------------


}