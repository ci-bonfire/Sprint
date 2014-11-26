<?php namespace Myth\Forge;

use Myth\Controllers\CLIController;

/**
 * Class BaseGenerator
 * 
 * Builds on top of the features of the CLIController to provide
 * handy methods used for generating boilerplate code.
 *
 * todo Enforce a sandbox around the app that restricts where files are written or modified.
 *
 * @package Myth\Forge
 */
abstract class BaseGenerator extends CLIController {

    /**
     * Instance of the active themer.
     * @var null
     */
    protected $themer = null;

    protected $gen_path = null;

    //--------------------------------------------------------------------

    public function __construct()
    {
        parent::__construct();

        $this->load->config('forge');
    }

    //--------------------------------------------------------------------


    /**
     * The method called by the main generator script. This must be
     * overridden by child classes to implement the actual logic used.
     *
     * todo Return a 'Done' when the generator has ran
     * todo Spit out running script of actions ('Invoked ControllerGenerator', 'created /path/to/file.php')
     *
     * @param array $segments
     * @param bool  $quiet      If true, models should accept default values.
     * @return mixed
     */
    abstract function run($segments=[]);

    //--------------------------------------------------------------------


    /**
     * Creates a file at the specified path with the given contents.
     *
     * @param $path
     * @param null $contents
     *
     * @return bool
     */
    public function createFile($path, $contents=null, $overwrite=false, $perms=0644)
    {
        // Does file already exist?
        if (is_file($path))
        {
	        if (! $overwrite) {
		        throw new \RuntimeException( 'Cannot createFile. File already exists: ' . $path );
	        }

	        unlink($path);
        }

	    // Do we need to create the directory?
	    $segments = explode('/', $path);
		array_pop($segments);
		$folder = implode('/', $segments);

	    if (! is_dir($folder))
	    {
		    $this->createDirectory($folder);
	    }

        get_instance()->load->helper('file');

        if (! write_file($path, $contents))
        {
            throw new \RuntimeException('Unknown error writing file: '. $path);
        }

        chmod($path, $perms);

        return $this;
    }
    
    //--------------------------------------------------------------------

    /**
     * Creates a new directory at the specified path.
     *
     * @param $path
     * @param int|string $perms
     *
     * @return bool
     */
    public function createDirectory($path, $perms=0755)
    {
        if (is_dir($path))
        {
            return $this;
        }

        if (! mkdir($path, $perms, true) )
        {
            throw new \RuntimeException('Unknown error creating directory: '. $path);
        }

        return $this;
    }

    //--------------------------------------------------------------------

    /**
     * Copies a file from the current template group to the destination.
     *
     * @param $source
     * @param $destination
     * @param bool $overwrite
     *
     * @return bool
     */
    public function copyFile($source, $destination, $overwrite=false)
    {

    }

    //--------------------------------------------------------------------

    /**
     * Attempts to locate a template within the current template group,
     * parses it with the passed in data, and writes to the new location.
     *
     * @param $template
     * @param $destination
     * @param array $data
     * @param bool $overwrite
     *
     * @return $this
     */
    public function copyTemplate($template, $destination, $data=[], $overwrite=false)
    {
        $content = $this->render($template, $data);

        return $this->createFile($destination, $content, $overwrite);
    }

    //--------------------------------------------------------------------


    /**
     * Injects a block of code into an existing file. Using options
     * you can specify where the code should be inserted. Available options
     * are:
     *      prepend         - Place at beginning of file
     *      append          - Place at end of file
     *      before  => ''   - Insert just prior to this line of text (don't forget the line ending "\n")
     *      after   => ''   - Insert just after this line of text (don't forget the line ending "\n")
     *      replace => ''   - a simple string to be replaced. All locations will be replaced.
     *      regex   => ''   - a pregex pattern to use to replace all locations.
     *
     * @param $path
     * @param $content
     * @param array $options
     *
     * @return $this
     */
    public function injectIntoFile($path, $content, $options)
    {
        $kit = new FileKit();

        if (is_string($options))
        {
            $action = $options;
        }
        else if (is_array($options) && count($options))
        {
            $param = $options[0];
            $action = array_shift( array_keys($options) );
        }

        switch ( strtolower($action) )
        {
            case 'prepend':
                $kit->prepend($path, $content);
                break;
            case 'before':
                $kit->before($path, $param, $content);
                break;
            case 'after':
                $kit->after($path, $param, $content);
                break;
            case 'replace':
                $kit->replaceIn($path, $param, $content);
                break;
            case 'regex':
                $kit->replaceWithRegex($path, $param, $content);
                break;
            case 'append':
            default:
                $kit->append($path, $content);
                break;
        }

        return $this;
    }
    
    //--------------------------------------------------------------------

    /**
     * Runs another generator. The first parameter is the name of the
     * generator to run. All remaining arguments will be passed directly
     * to the generator.
     */
    public function generate()
    {

    }

    //--------------------------------------------------------------------

    /**
     * Adds a new route to the application's route file.
     *
     * @param $left
     * @param $right
     *
     * @return \Myth\Forge\BaseGenerator
     */
    public function route($left, $right, $options=[], $method='any')
    {
        $option_str = '[';

        foreach ($options as $key => $value)
        {
            $option_str .= "";
        }

        $option_str .= ']';

        $content = "\$routes->{$method}('{$left}', '{$right}', {$option_str});\n";

        return $this->injectIntoFile(APPPATH .'config/routes.php', $content, ['after' => "// Auto-generated routes go here\n"]);
    }
    
    //--------------------------------------------------------------------

    /**
     * Outputs the contents of the file in the template's source path.
     */
    public function readme($file='readme.txt')
    {
	    $name = str_replace('Generator', '', get_class($this));

	    $path = $this->locateGenerator($name);

	    die($path);
    }

    //--------------------------------------------------------------------

    /**
     * Renders a single generator template. The file must be in a folder
     * under the template group named the same as $this->generator_name.
     * The file must have a '.tpl.php' file extension.
     *
     * @param $template_name
     * @param array $data
     *
     * @return string The rendered template
     */
    public function render($template_name, $data=[], $folder=null)
    {
        if (empty($this->themer))
        {
            $this->setupThemer();
        }

        $output = null;

	    $view = $template_name .'.tpl';

        $groups = config_item('forge.collections');

	    $name = str_replace('Generator', '', get_class($this) );

        foreach ($groups as $group => $path)
        {
	        $path = rtrim($path, '/ ') .'/';
	        $folders = scandir($path);

	        if (! $i = array_search(ucfirst($name), $folders))
	        {
		        continue;
	        }

	        $view = $folders[$i] . '/'. $view;

            if (realpath($path . $view .'.php'))
            {
                $output = $this->themer->display($group .':'. $view, $data);
                break;
            }
        }

        return $output;
    }

    //--------------------------------------------------------------------


    //--------------------------------------------------------------------
    // Private Methods
    //--------------------------------------------------------------------

    protected function setupThemer()
    {
        $themer_name = config_item('forge.themer');

        if (! $themer_name)
        {
            throw new \RuntimeException('No themer chosen in forge config file.');
        }

        $this->themer = new $themer_name( get_instance() );

        // Register our paths with the themer
        $paths = config_item('forge.collections');

        foreach ($paths as $key => $path) {
            $this->themer->addThemePath($key, $path);
        }
    }

    //--------------------------------------------------------------------

	protected function determineOutputPath($folder='')
	{
		// todo check for global module name...
		$path = APPPATH . $folder;

		$this->gen_path = $path;

		return rtrim($path, '/ ') .'/';
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
//		if (! empty($this->gen_path))
//		{
//			return $this->gen_path;
//		}

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

			$this->gen_path = $path . $folders[$i] .'/';

			return $this->gen_path;
		}

		return null;
	}

	//--------------------------------------------------------------------
}