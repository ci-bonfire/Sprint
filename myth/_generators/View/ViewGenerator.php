<?php

use Myth\CLI;

class ViewGenerator extends \Myth\Forge\BaseGenerator {

	public function run($segments=[], $quiet=false)
	{
		$name = array_shift( $segments );

		if ( empty( $name ) )
		{
			$name = CLI::prompt( 'View name' );
		}

		// Format to CI Standards
		$name = str_replace('.php', '', strtolower( $name ) );

		$destination = $this->determineOutputPath( 'views' ) . $name . '.php';

		if (! $this->createFile($destination, "The {$name}.php view file.") )
		{
			CLI::error('Error creating view file.');
		}

		return true;
	}

	//--------------------------------------------------------------------


}