<?php

use Myth\CLI as CLI;

class ScaffoldGenerator extends \Myth\Forge\BaseGenerator {

	protected $fields = '';

	protected $model_name = '';

	//--------------------------------------------------------------------

	public function run($segments = [ ], $quiet = false)
	{
		$name = array_shift( $segments );

		// If we didn't attach any fields, then we
		// need to generate the barebones here.
		$this->fields = CLI::option('fields');
		if (empty($this->fields))
		{
			$this->fields = "id:int id:id title:string created_on:datetime modified_on:datetime";
		}

		// Perform the steps.
		$this->makeMigration($name);
		$this->makeSeed($name);
		$this->makeModel($name);
		$this->makeController($name);
	}

	//--------------------------------------------------------------------

	//--------------------------------------------------------------------
	// Step Methods
	//--------------------------------------------------------------------

	protected function makeMigration($name)
	{
		$name = strtolower($name);

		$mig_name = "create_{$name}_table";

		$this->generate("migration {$mig_name}", "-fields '{$this->fields}'", true);
	}

	//--------------------------------------------------------------------

	public function makeSeed($name)
	{
	    $this->generate("seed {$name}", null, true);
	}

	//--------------------------------------------------------------------


	public function makeModel($name)
	{
	    $this->load->helper('inflector');

		$name = singular($name);
		$this->model_name = $name;

		$this->generate("model {$name}", "-table $name -primary_key id", true);
	}

	//--------------------------------------------------------------------

	public function makeController($name)
	{
		$this->generate("controller {$name}", "-model {$this->model_name} -create_views -fields '{$this->fields}'", true);
	}

	//--------------------------------------------------------------------



}