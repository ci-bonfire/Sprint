<?php

use Myth\CLI;

class ControllerGenerator extends \Myth\Forge\BaseGenerator {

	protected $options = [
		'cache_type'   => 'null',
		'backup_cache' => 'null',
		'ajax_notices' => 'true',
		'lang_file'    => 'null',
		'model'        => NULL,
		'themed'       => FALSE,
		'base_class'   => 'BaseController',
		'base_path'    => 'Myth\Controllers\\'
	];

	//--------------------------------------------------------------------

	public function run( $segments = [ ], $quiet = FALSE )
	{
		$name = array_shift( $segments );

		if ( empty( $name ) )
		{
			$name = CLI::prompt( 'Controller name' );
		}

		// Format to CI Standards
		$name = ucfirst( $name );

		if ( $quiet === FALSE )
		{
			$this->collectOptions( $name );
		}
		else
		{
			$this->quietSetOptions( $name );
		}

		$data = [
			'controller_name' => $name,
			'today'           => date( 'Y-m-d H:ia' )
		];

		$data = array_merge( $data, $this->options );

		if ($data['themed'] == 'y' || $data['themed'] == true)
		{
			$data['base_class'] = 'ThemedController';
		}

		$destination = $this->determineOutputPath( 'controllers' ) . $name . '.php';

		if ( ! $this->copyTemplate( 'controller', $destination, $data, $this->overwrite ) )
		{
			CLI::error( 'Error creating new files' );
		}

		if ( CLI::option( 'create_views' ) && $this->options['themed'] == 'y' )
		{
			$this->createViews( $name );
		}

		return TRUE;
	}

	//--------------------------------------------------------------------

	protected function quietSetOptions( $name )
	{
		$options = CLI::getOptions();

		if ( ! empty( $options['model'] ) )
		{
			$this->options['model'] = $options['model'];

			// Format per CI
			if ( ! empty( $this->options['model'] ) && substr( $this->options['model'], - 6 ) !== '_model' )
			{
				$this->options['model'] .= '_model';
			}
			$this->options['model'] = ! empty( $this->options['model'] ) ? ucfirst( $this->options['model'] ) : NULL;

			$this->options['themed'] = 'y';
		}


	}

	//--------------------------------------------------------------------

	protected function collectOptions( $name )
	{
		$options = CLI::getOptions();

		// Model?
		$this->options['model'] = empty( $options['model'] ) ?
			CLI::prompt( 'Model Name? (empty is fine)' ) :
			$options['model'];

		// Format per CI
		if ( ! empty( $this->options['model'] ) && substr( $this->options['model'], - 6 ) !== '_model' )
		{
			$this->options['model'] .= '_model';
		}
		$this->options['model'] = ! empty( $this->options['model'] ) ? ucfirst( $this->options['model'] ) : NULL;

		// If we're using a model, then force the use of a themed controller.
		if ( ! empty( $this->options['model'] ) )
		{
			$options['themed'] = 'y';
		}

		// Themed Controller?
		$this->options['themed'] = empty( $options['themed'] ) ?
			CLI::prompt( 'Is a Themed Controller?', [ 'y', 'n' ] ) :
			$options['themed'];

		$this->options['themed'] = $this->options['themed'] == 'y' ? TRUE : FALSE;

		if ( $this->options['themed'] )
		{
			$this->options['base_class'] = 'ThemedController';
		}
	}

	//--------------------------------------------------------------------

	/**
	 * Generates the standard views for our CRUD methods.
	 */
	protected function createViews( $name )
	{
		$this->load->helper( 'inflector' );

		$data = [
			'name'        => $name,
			'lower_name'  => strtolower($name),
			'single_name' => singular( $name ),
			'plural_name' => plural( $name ),
			'fields'      => $this->prepareFields()
		];

		$subfolder = empty( $this->module ) ? '/' . $name : '';

		// Index
		$destination = $this->determineOutputPath( 'views' . $subfolder ) . 'index.php';
		$this->copyTemplate( 'view_index', $destination, $data, $this->overwrite );

		// Create
		$destination = $this->determineOutputPath( 'views' . $subfolder ) . 'create.php';
		$this->copyTemplate( 'view_create', $destination, $data, $this->overwrite );

		// Show
		$destination = $this->determineOutputPath( 'views' . $subfolder ) . 'show.php';
		$this->copyTemplate( 'view_show', $destination, $data, $this->overwrite );

		// Index
		$destination = $this->determineOutputPath( 'views' . $subfolder ) . 'update.php';
		$this->copyTemplate( 'view_update', $destination, $data, $this->overwrite );
	}

	//--------------------------------------------------------------------

	/**
	 * Grabs the fields from the CLI options and gets them ready for
	 * use within the views.
	 */
	protected function prepareFields()
	{
		$fields = CLI::option( 'fields' );

		if ( empty( $fields ) )
		{
			return NULL;
		}

		$fields = explode( ' ', $fields );

		$new_fields = [ ];

		foreach ( $fields as $field )
		{
			$pop = [ NULL, NULL, NULL ];
			list( $field, $type, $size ) = array_merge( explode( ':', $field ), $pop );
			$type = strtolower( $type );

			// Ignore list
			if (in_array($field, ['created_on', 'modified_on']))
			{
				continue;
			}

			// Strings
			if (in_array($type, ['char', 'varchar', 'string']))
			{
				$new_fields[] = [
					'name'  => $field,
					'type'  => 'text'
				];
			}

			// Textarea
			else if ($type == 'text')
			{
				$new_fields[] = [
					'name'  => $field,
					'type'  => 'textarea'
				];
			}

			// Number
			else if (in_array($type, ['tinyint', 'int', 'bigint', 'mediumint', 'float', 'double', 'number']))
			{
				$new_fields[] = [
					'name'  => $field,
					'type'  => 'number'
				];
			}

			// Date
			else if (in_array($type, ['date', 'datetime', 'time']))
			{
				$new_fields[] = [
					'name'  => $field,
					'type'  => $type
				];
			}
		}

		return $new_fields;
	}

	//--------------------------------------------------------------------

}