<?php

use Myth\CLI;

class ControllerGenerator extends \Myth\Forge\BaseGenerator {

	protected $options = [
		'cache_type'    => 'null',
		'backup_cache'  => 'null',
		'ajax_notices'  => 'true',
		'lang_file'     => 'null',
		'model'         => null,
		'themed'        => false,
		'base_class'    => 'BaseController',
		'base_path'     => 'Myth\Controllers\\'
	];

	//--------------------------------------------------------------------

	public function run($segments=[], $quiet=false)
	{
		$name = array_shift( $segments );

		if ( empty( $name ) )
		{
			$name = CLI::prompt( 'Controller name' );
		}

		// Format to CI Standards
		$name = ucfirst( $name );

		if ( $quiet === false )
		{
			$this->collectOptions( $name );
		}

		$data = [
			'controller_name'   => $name,
			'today'             => date( 'Y-m-d H:ia' )
		];

		$data = array_merge( $data, $this->options );

		$destination = $this->determineOutputPath( 'controllers' ) . $name . '.php';

		if ( ! $this->copyTemplate( 'controller', $destination, $data, true ) )
		{
			CLI::error( 'Error creating new files' );
		}

		return TRUE;
	}

	//--------------------------------------------------------------------

	protected function collectOptions($name)
	{
		$options = CLI::getOptions();

		// Model?
		$this->options['model'] = empty( $options['model'] ) ?
			CLI::prompt( 'Model Name? (empty is fine)') :
			$options['model'];

		// Format per CI
		if (!empty($this->options['model']) && substr( $this->options['model'], - 6 ) !== '_model' )
		{
			$this->options['model'] .= '_model';
		}
		$this->options['model'] = !empty($this->options['model']) ? ucfirst( $this->options['model'] ) : null;

		// If we're using a model, then force the use of a themed controller.
		if (! empty($this->options['model']))
		{
			$options['themed'] = 'y';
		}

		// Themed Controller?
		$this->options['themed'] = empty( $options['themed'] ) ?
			CLI::prompt( 'Is a Themed Controller?', ['y', 'n']) :
			$options['themed'];

		$this->options['themed'] = $this->options['themed'] == 'y' ? true : false;

		if ($this->options['themed'])
		{
			$this->options['base_class'] = 'ThemedController';
		}
	}

	//--------------------------------------------------------------------


}