<?php

use Myth\CLI;

class SeedGenerator extends \Myth\Forge\BaseGenerator {

	public function run($segments=[], $quiet=false)
	{
		$name = array_shift( $segments );

		if ( empty( $name ) )
		{
			$name = CLI::prompt( 'Seed name' );
		}

		// Format to CI Standards
		$name = str_replace('.php', '', strtolower( $name ) );
		if (substr( $name, -4) == 'seed')
		{
			$name = substr($name, 0, strlen($name) - 4);
		}
		$name = ucfirst($name) .'Seeder';

		$data = [
			'seed_name' => $name,
			'today'     => date( 'Y-m-d H:ia' )
		];

		$destination = $this->determineOutputPath( 'database/seeds' ) . $name . '.php';

		if (strpos($destination, 'modules') !== false)
		{
			$destination = str_replace('database/', '', $destination);
		}

		if (! $this->copyTemplate( 'seed', $destination, $data, $this->overwrite) )
		{
			CLI::error('Error creating seed file.');
		}

		return true;
	}

	//--------------------------------------------------------------------


}