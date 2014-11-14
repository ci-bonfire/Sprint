<?php

use Myth\CLI;

class ModelGenerator extends \Myth\Forge\BaseGenerator {

	protected $default_options = [
		'table_name'    => '',
		'primary_key'   => '',
		'set_created'   => true,
		'set_modified'  => true,
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

	public function run($segments=[])
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

		$data = [
			'model_name'    => $name,
			'date'          => date('Y-m-d H:ia')
		];

		$data = array_merge($data, $this->default_options);

		$destination = $this->determineOutputPath('models') . $name .'.php';

		if (! $this->copyTemplate('model', $destination, $data, true) )
		{
			CLI::error('damn');
		}

		return true;
	}

	//--------------------------------------------------------------------

}