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
		'return_insert_id'  => true
	];

	//--------------------------------------------------------------------

	public function run($segments=[], $quiet=false)
	{
		$name = array_shift($segments);

		if (empty($name))
		{
			$name = CLI::prompt('Model name');
		}

		// Format to CI Standards
		if (substr($name, -6) !== '_model')
		{
			$name .= '_model';
		}
		$name = ucfirst($name);

		if (! $quiet) {
			$this->collectOptions( $name );
		}
		else {
			$this->quietSetOptions( $name );
		}

		$data = [
			'model_name'    => $name,
			'today'         => date('Y-m-d H:ia')
		];

		$data = array_merge($data, $this->options);

		$destination = $this->determineOutputPath('models') . $name .'.php';

		if (! $this->copyTemplate('model', $destination, $data, true) )
		{
			CLI::error('damn');
		}

		return true;
	}

	//--------------------------------------------------------------------

	/*
	 * Customizes our settings
	 */
	public function collectOptions($model_name)
	{
	    $this->load->helper('inflector');

		$options = CLI::getOptions();

		// Table Name?
		$this->options['table_name'] = empty($options['table']) ?
			CLI::prompt('Table name', plural( strtolower(str_replace('_model', '', $model_name))) ) :
			$options['table'];

		// Primary Key
		$this->options['key'] = empty($options['primary_key']) ?
			CLI::prompt('Primary Key', 'id') :
			$options['key'];

		$this->options['protected'] = [ $this->options['primary_key'] ];

		// Set Created?
		if (empty($options['set_created']))
		{
			$ans = CLI::prompt('Set Created date?', ['y', 'n']);
			if ($ans == 'n') $this->options['set_created'] = false;
		}

		// Set Modified?
		if (empty($options['set_modified']))
		{
			$ans = CLI::prompt('Set Modified date?', ['y', 'n']);
			if ($ans == 'n') $this->options['set_modified'] = false;
		}

		// Date Format
		$this->options['date_format'] = empty($options['date_format']) ?
			CLI::prompt('Date Format?', ['datetime', 'date', 'int']) :
			$options['date_format'];

		// Log User?
		if (empty($options['log_user']))
		{
			$ans = CLI::prompt('Log User actions?', ['y', 'n']);
			if ($ans == 'y') $this->options['log_user'] = true;
		}

		// Soft Deletes
		if (empty($options['soft_delete']))
		{
			$ans = CLI::prompt('Use Soft Deletes?', ['y', 'n']);
			if ($ans == 'n') $this->options['soft_delete'] = false;
		}

	}

	//--------------------------------------------------------------------

	public function quietSetOptions($model_name)
	{
		$this->load->helper('inflector');

		$this->options['table_name'] = plural( strtolower(str_replace('_model', '', $model_name)));

		$this->options['primary_key'] = 'id';
		$this->options['protected'] = ['id'];
	}

	//--------------------------------------------------------------------


}