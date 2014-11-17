<?php

use Myth\CLI;

class ModelGenerator extends \Myth\Forge\BaseGenerator {

	protected $options = [
		'table_name'        => '',
		'primary_key'       => '',
		'set_created'       => true,
		'set_modified'      => true,
		'created_field'     => 'created_on',
		'modified_field'    => 'modified_on',
		'date_format'       => 'datetime',
		'log_user'          => false,
		'created_by_field'  => 'created_by',
		'modified_by_field' => 'modified_by',
		'deleted_by_field'  => 'deleted_by',
		'use_soft_deletes'  => true,
		'soft_delete_key'   => 'deleted',
		'protected'         => '',
		'return_type'       => 'object',
		'return_insert_id'  => true,
		'rules'             => '[]'
	];

	//--------------------------------------------------------------------

	public function run( $segments = [ ], $quiet = false )
	{
		die(print_r($segments));
		$name = array_shift( $segments );

		$this->options['table_name'] = array_shift( $segments );

		if ( empty( $name ) )
		{
			$name = CLI::prompt( 'Model name' );
		}

		// Format to CI Standards
		if ( substr( $name, - 6 ) !== '_model' )
		{
			$name .= '_model';
		}
		$name = ucfirst( $name );

		if ( $quiet !== false )
		{
			$this->collectOptions( $name );
		}
		else
		{
			$this->quietSetOptions( $name );
		}

		$data = [
			'model_name' => $name,
			'today'      => date( 'Y-m-d H:ia' )
		];

		$data = array_merge( $data, $this->options );

		$destination = $this->determineOutputPath( 'models' ) . $name . '.php';

		if ( ! $this->copyTemplate( 'model', $destination, $data, true ) )
		{
			CLI::error( 'Error creating new files' );
		}

		return TRUE;
	}

	//--------------------------------------------------------------------

	/*
	 * Customizes our settings
	 */
	protected function collectOptions( $model_name )
	{
		$this->load->helper( 'inflector' );

		$options = CLI::getOptions();

		// Table Name?
		if ( empty( $this->options['table_name'] ) )
		{
			$this->options['table_name'] = empty( $options['table'] ) ?
				CLI::prompt( 'Table name', plural( strtolower( str_replace( '_model', '', $model_name ) ) ) ) :
				$options['table'];
		}

		$this->options['fields'] = $this->table_info( $this->options['table_name'] );

		// Primary Key
		if (empty($this->options['primary_key']))
		{
			$this->options['primary_key'] = empty( $options['primary_key'] ) ?
				CLI::prompt( 'Primary Key', 'id' ) :
				$options['primary_key'];
		}

		$this->options['protected'] = [ $this->options['primary_key'] ];

		// Set Created?
		if ( empty( $options['set_created'] ) )
		{
			$ans = CLI::prompt( 'Set Created date?', [ 'y', 'n' ] );
			if ( $ans == 'n' )
			{
				$this->options['set_created'] = FALSE;
			}
		}

		// Set Modified?
		if ( empty( $options['set_modified'] ) )
		{
			$ans = CLI::prompt( 'Set Modified date?', [ 'y', 'n' ] );
			if ( $ans == 'n' )
			{
				$this->options['set_modified'] = FALSE;
			}
		}

		// Date Format
		$this->options['date_format'] = empty( $options['date_format'] ) ?
			CLI::prompt( 'Date Format?', [ 'datetime', 'date', 'int' ] ) :
			$options['date_format'];

		// Log User?
		if ( empty( $options['log_user'] ) )
		{
			$ans = CLI::prompt( 'Log User actions?', [ 'y', 'n' ] );
			if ( $ans == 'y' )
			{
				$this->options['log_user'] = TRUE;
			}
		}

		// Soft Deletes
		if ( empty( $options['soft_delete'] ) )
		{
			$ans = CLI::prompt( 'Use Soft Deletes?', [ 'y', 'n' ] );
			if ( $ans == 'n' )
			{
				$this->options['soft_delete'] = false;
			}
		}

	}

	//--------------------------------------------------------------------

	protected function quietSetOptions( $model_name )
	{
		$this->load->helper( 'inflector' );

		if (empty($this->options['table_name']))
		{
			$this->options['table_name'] = plural( strtolower( str_replace( '_model', '', $model_name ) ) );
		}

		$this->options['fields'] = $this->table_info( $this->options['table_name'] );

		$this->options['primary_key'] = ! empty( $this->options['primary_key'] ) ? $this->options['primary_key'] : 'id';
		$this->options['protected']   = [ $this->options['primary_key'] ];
	}

	//--------------------------------------------------------------------

	/**
	 * Get the structure and details for the fields in the specified DB table
	 *
	 * @param string $table_name Name of the table to check
	 *
	 * @return mixed An array of fields or false if the table does not exist
	 */
	protected function table_info( $table_name )
	{
		$this->load->database();

		// Check whether the table exists in this database
		if ( ! $this->db->table_exists( $table_name ) )
		{
			return FALSE;
		}

		$fields = $this->db->field_data( $table_name );

		// There may be something wrong or the database driver may not return
		// field data
		if ( empty( $fields ) )
		{
			return FALSE;
		}

		// Use the primary key if the table has one already set.
		foreach ( $fields as $field )
		{
			if ( ! empty( $field->primary_key ) && $field->primary_key == 1 )
			{
				$this->options['primary_key'] = $field->name;
				break;
			}
		}

		// Set our validation rules based on these fields
		$this->options['rules'] = $this->buildValidationRules( $fields );

		return $fields;
	}

	//--------------------------------------------------------------------

	/**
	 * Takes the fields from field_data() and creates the basic validation
	 * rules for those fields.
	 *
	 * @param array $fields
	 *
	 * @return array
	 */
	public function buildValidationrules($fields=[])
	{
		if (empty($fields) || ! is_array($fields) || ! count($fields))
		{
			return null;
		}

		$rules = [];

		foreach ($fields as $field)
		{
			$rule = [];

			switch ($field->type)
			{
				// Numeric Types
				case 'tinyint':
				case 'smallint':
				case 'mediumint':
				case 'int':
				case 'integer':
				case 'bigint':
					$rule[] = 'integer';
					break;
				case 'decimal':
				case 'dec':
				case 'numeric':
				case 'fixed':
					$rule[] = 'decimal';
					break;
				case 'float':
				case 'double':
					$rule[] = 'numeric';
					break;

				// Date types don't have many defaults we can go off of...

				// Text Types
				case 'char':
				case 'varchar':
				case 'text':
					$rule[] = 'alpha_numeric_spaces';
					$rule[] = 'xss_clean';
					break;
			}

			if (! empty($field->max_length))
			{
				$rule[] = "max_length[{$field->max_length}]";
			}

			$rules[] = [
				'field' => $field->name,
				'label' => ucwords(str_replace('_', ' ', $field->name)),
				'rules' => implode('|', $rule)
			];
		}

		$str = $this->stringify($rules);

		// Clean up the resulting array a bit.
		$str = substr_replace($str, "\n]", -3);

		return $str;
	}

	//--------------------------------------------------------------------

	/**
	 * Converts an array to a string representation.
	 *
	 * @param $array
	 */
	protected function stringify($array, $depth=0)
	{
		if (! is_array($array))
		{
			return '';
		}



		$str = '';

		if ($depth > 1)
		{
			$str .= str_repeat("\t", $depth);
		}

		$depth++;

	    $str .= "[\n";

		foreach ($array as $key => $value)
		{
			$str .= str_repeat("\t", $depth +1);

			if (! is_numeric($key))
			{
				$str .= "'{$key}' => ";
			}

			if (is_array($value))
			{
				$str .= $this->stringify($value, $depth);
			}
			else if (is_bool($value))
			{
				$b = $value === true ? 'true' : 'false';
				$str .= "{$b},\n";
			}
			else
			{
				$str .= "'{$value}',\n";
			}
		}

		$str .= str_repeat("\t", $depth) ."],";

		return $str;
	}

	//--------------------------------------------------------------------


}