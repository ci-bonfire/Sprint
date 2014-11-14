<?php

namespace Myth\Controllers;
use Myth\CLI;

/**
 * Class CLIInterface
 *
 * Provides helpers for creating simple interactive CLI tools.
 *
 * @package Myth\Controllers
 */
class CLIController extends \CI_Controller {

    /**
     * Holds short descriptions for the public functions in this class.
     * Each 'key' in the array should match the function name.
     *
     * @var array
     */
    protected $descriptions = [];

    /**
     * Holds long descriptions for the public functions in this class.
     * Each 'key' in the array should match the function name.
     * @var array
     */
    protected $long_descriptions = [];

    //--------------------------------------------------------------------

    /**
     * Ensures that we are running on the CLI, and collects basic settings
     * like collecting our command line arguments into a pretty array.
     */
    public function __construct()
    {
        parent::__construct();

        // Restrict usage to the command line.
        if (! is_cli() )
        {
            show_error('This controller must be called from the command line.');
        }

        // Make sure the CLI library is loaded and ready.
        $cli = new CLI();
        $cli::_init();
    }

    //--------------------------------------------------------------------

    /**
     * A default index method that all CLI Controllers can share. Will
     * list all of the methods and their short descriptions, if available.
     */
    public function index()
    {
        CLI::new_line();
        CLI::write("Available commands:");

        $this->sayDescriptions($this->descriptions);

        CLI::new_line();
    }

    //--------------------------------------------------------------------


    /**
     * Grabs the short description of a command, if it exists.
     *
     * @param null $method
     */
    public function describeMethod($method=null)
    {
        if (empty($this->descriptions[$method]))
        {
            return CLI::error('Unable to locate method description.');
        }

        CLI::write("\t{$this->descriptions[$method]}", 'yellow');
    }

    //--------------------------------------------------------------------

    public function longDescribeMethod($method=null)
    {
        if (empty($this->long_descriptions[$method]))
        {
            return CLI::error('No help available for that command.');
        }

        CLI::write("\t{$this->long_descriptions[$method]}", 'yellow');
    }

    //--------------------------------------------------------------------

    //--------------------------------------------------------------------
    // Private Methods
    //--------------------------------------------------------------------

    protected function sayDescriptions($descriptions)
    {
        $names      = array_keys($descriptions);
        $syntaxes   = array_column($descriptions, 0);
        $descs      = array_column($descriptions, 1);

        // Pad each item to the same length
        $names      = $this->padArray($names);
        $syntaxes   = $this->padArray($syntaxes);

        // todo Implement nice wrapping of descriptions based on window width
        for ($i=0; $i < count($names); $i++)
        {
            $out = CLI::color($names[$i], 'yellow');

            // The rest of the items stay default color.
            if (isset($syntaxes[$i]))
            {
                $out .= $syntaxes[$i];
            }

            if (isset($descs[$i]))
            {
                $out .= $descs[$i];
            }

            CLI::write($out);
        }
    }

    //--------------------------------------------------------------------

    /**
     * Returns a new array where all of the string elements have
     * been padding with trailling spaces to be the same length.
     *
     * @param array $array
     * @param int $extra // How many extra spaces to add at the end
     * @return array
     */
    protected function padArray($array, $extra=2)
    {
        $max = max(array_map('strlen', $array)) + $extra;

        foreach ($array as &$item)
        {
            $item = str_pad($item, $max);
        }

        return $array;
    }

    //--------------------------------------------------------------------


}