<?php
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