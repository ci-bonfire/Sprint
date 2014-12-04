<?php

use Myth\CLI as CLI;

class Forge extends \Myth\Controllers\CLIController {

    public function __construct()
    {
        parent::__construct();

        $this->load->config('forge');
    }

    //--------------------------------------------------------------------


    public function _remap($method, $params)
    {
        if (method_exists($this, $method))
        {
            call_user_func_array( [$this, $method], $params);
        }
        else
        {
	        $params = array_merge([CLI::cli_string()], $params);

            call_user_func_array( [$this, 'run'], $params);
        }
    }
    
    //--------------------------------------------------------------------

    /**
     * Overrides to implement dynamic description building based on
     * scanning the collections and grabbing the information from
     * 'forge.php' files.
     */
    public function index()
    {
        $collections = config_item('forge.collections');

        if (! is_array($collections) || ! count($collections) )
        {
            return CLI::error('No generator collections found.');
        }

        // We loop through each collection scanning
        // for any generator folders that have a
        // 'forge.php' file. For each one found
        // we build out another section in our help commands
        foreach ($collections as $alias => $path)
        {
            $path = rtrim($path, '/ ') .'/';
            $folders = scandir($path);

            $_descriptions = [];

            foreach ($folders as $dir)
            {
                if ($dir == '.' || $dir == '..' || ! is_file($path . $dir .'/forge.php'))
                {
                    continue;
                }

                include $path . $dir .'/forge.php';

                // Don't have valid arrays to work with? Move along...
                if (! isset($descriptions))
                {
                    log_message('debug', '[Forge] Invalid forge.php file at: '. $path . $dir .'/forge.php');
                    continue;
                }

                $_descriptions = array_merge($descriptions, $_descriptions);
            }

	        ksort($_descriptions);

            CLI::new_line();
            CLI::write(ucwords( str_replace('_', ' ', $alias)) .' Collection');
            $this->sayDescriptions($_descriptions);
        }
    }

    //--------------------------------------------------------------------

    /**
     * The primary method that calls the correct generator and
     * makes it run.
     */
    public function run($command)
    {
	    $quiet = false;

	    $segments = explode(" ", $command);

	    // Get rid of the 'forge' command
	    if ($segments[0] == 'forge') {
		    array_shift( $segments );
	    }

	    $command = trim(str_ireplace("forge", '', array_shift($segments)));

		$dir = $this->locateGenerator($command);

		$class_name = ucfirst($command) .'Generator';

	    if (! file_exists($dir . $class_name .'.php'))
	    {
		    return CLI::error("Generator file not found for: {$class_name}");
	    }

		require_once $dir . $class_name .'.php';

	    if (! class_exists($class_name, false))
	    {
		    return CLI::error("No class `{$class_name}` found in generator file.");
	    }

	    // Should we run the process quietly?
	    if ( (CLI::option('q') || CLI::option('quiet')))
	    {
		    $quiet = true;
	    }

	    CLI::write('Invoked '. CLI::color($class_name, 'yellow'));

		$class = new $class_name();

	    $class->run( $segments, $quiet );
    }

    //--------------------------------------------------------------------

	/**
	 * Displays the readme file for a generator if it exists.
	 *
	 * @param $command
	 */
	public function readme($command)
	{
		$dir = $this->locateGenerator($command);

		if (! is_file($dir .'readme.txt'))
		{
			return CLI::error('Unable to locate the readme.txt file.');
		}

		$lines = file($dir .'readme.txt');

		if (! is_array($lines) || ! count($lines))
		{
			return CLI::error('The readme file does not have anything to display.');
		}

		$line_count = 0; // Total we're currently viewing.
		$max_rows   = CLI::getHeight() -3;

		foreach ($lines as $line)
		{
			$line_count++;

			if ($line_count >= $max_rows)
			{
				CLI::prompt("\nContinue...");
				$line_count = 0;
			}

			echo CLI::wrap($line, 125);
		}

		CLI::new_line(2);
	}

	//--------------------------------------------------------------------


    /**
     * Overrides CLIController's version to support searching our
     * collections for the help description.
     *
     * @param null $method
     */
    public function longDescribeMethod($method=null)
    {
	    $collections = config_item('forge.collections');

	    if (! is_array($collections) || ! count($collections) )
	    {
		    return CLI::error('No generator collections found.');
	    }

	    // We loop through each collection scanning
	    // for any generator folders that have a
	    // 'forge.php' file. For each one found
	    // we build out another section in our help commands
	    foreach ($collections as $alias => $path)
	    {

		    $path = rtrim($path, '/ ') .'/';
		    $folders = scandir($path);

		    if (! $i = array_search(ucfirst($method), $folders))
		    {
			    continue;
		    }

		    $dir = $path . $folders[$i] .'/';

		    if (! is_file($dir .'/forge.php'))
		    {
			    CLI::error("The {$method} command does not have any cli help available.");
		    }

		    include $dir .'/forge.php';

		    // Don't have valid arrays to work with? Move along...
		    if (! isset($long_description))
		    {
			    log_message('debug', '[Forge] Invalid forge.php file at: '. $dir .'/forge.php');
			    continue;
		    }

		    if (empty($long_description))
		    {
			    return CLI::error("The {$method} command does not have an cli help available.");
		    }

		    CLI::new_line();
		    CLI::write( CLI::color(ucfirst($method) .' Help', 'yellow') );
		    return CLI::write( CLI::wrap($long_description, CLI::getWidth()) );
	    }

	    // Still here?
	    CLI::error("No help found for command: {$method}");
    }

    //--------------------------------------------------------------------

	/**
	 * Scans through the collections for the folder for this generator.
	 *
	 * @param $name
	 *
	 * @return null|string
	 */
	protected function locateGenerator($name)
	{
		$collections = config_item('forge.collections');

		if (! is_array($collections) || ! count($collections) )
		{
			return CLI::error('No generator collections found.');
		}

		foreach ($collections as $alias => $path)
		{
			$path = rtrim($path, '/ ') .'/';
			$folders = scandir($path);

			if (! $i = array_search(ucfirst($name), $folders))
			{
				continue;
			}

			return $path . $folders[$i] .'/';
		}

		return null;
	}

	//--------------------------------------------------------------------
}