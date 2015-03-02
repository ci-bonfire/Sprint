<?php namespace Myth\Controllers;
/**
 * Sprint
 *
 * A set of power tools to enhance the CodeIgniter framework and provide consistent workflow.
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 *
 * @package     Sprint
 * @author      Lonnie Ezell
 * @copyright   Copyright 2014-2015, New Myth Media, LLC (http://newmythmedia.com)
 * @license     http://opensource.org/licenses/MIT  (MIT)
 * @link        http://sprintphp.com
 * @since       Version 1.0
 */

use Myth\CLI as CLI;

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
            show_error( lang('cli_required') );
        }

        // Make sure the CLI library is loaded and ready.
//        CLI::_init();
    }

    //--------------------------------------------------------------------

    /**
     * A default index method that all CLI Controllers can share. Will
     * list all of the methods and their short descriptions, if available.
     */
    public function index()
    {
        CLI::new_line();
        CLI::write( lang('cli.available_commands') );

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
            return CLI::error( lang('cli.bad_description') );
        }

        CLI::write("\t{$this->descriptions[$method]}", 'yellow');
    }

    //--------------------------------------------------------------------

    public function longDescribeMethod($method=null)
    {
        if (empty($this->long_descriptions[$method]))
        {
            return CLI::error( lang('cli.no_help') );
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
                $out .= CLI::wrap($descs[$i], 125, strlen($names[$i]) + strlen($syntaxes[$i]));
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
