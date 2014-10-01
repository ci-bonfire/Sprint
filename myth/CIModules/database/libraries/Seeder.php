<?php

use Myth\CLI as CLI;

/**
 * Class Seeder
 *
 * Provides a base class for creating seeders.
 */
class Seeder {

    public $error_string    = '';

    protected $ci;

    protected $is_cli = false;

    protected $db;
    protected $dbforge;

    //--------------------------------------------------------------------

    public function __construct ()
    {
        $this->ci =& get_instance();

        $this->is_cli = $this->ci->input->is_cli_request();

        if ($this->is_cli)
        {
            $cli = new CLI();
            $cli::_init();
        }

        $this->ci->load->dbforge();

        // Setup some convenience vars.
        $this->db       =& $this->ci->db;
        $this->dbforge  =& $this->ci->dbforge;
    }

    //--------------------------------------------------------------------


    /**
     * Run the database seeds. It's where the magic happens.
     * This method MUST be overridden by child classes.
     */
    public function run ()
    {

    }

    //--------------------------------------------------------------------

    /**
     * Loads the class file and calls the run() method
     * on the class.
     *
     * @param $class
     */
    public function call ($class)
    {
        if (empty($class))
        {
            // Ask the user...
            $class = trim( CLI::prompt("Seeder name") );

            if (empty($class)) {
                return CLI::error("\tNo Seeder was specified.");
            }
        }

        $path = APPPATH .'database/seeds/'. str_replace('.php', '', $class) .'.php';

        if ( ! is_file($path))
        {
            return CLI::error("\tUnable to find seed class: ". $class);
        }

        try {
            require $path;

            $seeder = new $class();

            $seeder->run();

            unset($seeder);
        }
        catch (\Exception $e)
        {
            show_error($e->getMessage(), $e->getCode());
        }

        return Cli::write("\tSeeded: $class", 'green');
    }

    //--------------------------------------------------------------------

}