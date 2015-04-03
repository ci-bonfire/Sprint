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

use Myth\CLI;

class LocalizeGenerator extends \Myth\Forge\BaseGenerator {

    public function run($segments=[], $quiet=false)
    {
        // Show an index?
        if (empty($segments[0]))
        {
            return $this->displayIndex();
        }

        $action = array_shift($segments);

        switch ($action)
        {
            case 'install':
                $this->install();
                break;
            default:
                if (! $quiet)
                {
                    CLI::write('Nothing to do.', 'green');
                }
                break;
        }
    }

    //--------------------------------------------------------------------

    private function displayIndex()
    {
        CLI::write("\nAvailable Localization Tools");

        CLI::write( CLI::color('install', 'yellow') .'   install        Creates migration file for the localizations table' );
    }

    //--------------------------------------------------------------------

    //--------------------------------------------------------------------
    // Actions
    //--------------------------------------------------------------------

	private function install()
	{
        $this->load->library('migration');

        $name = "create_localize_table";

        $destination = $this->migration->determine_migration_path('app', true);

        $file = $this->migration->make_name($name);

        $destination = rtrim($destination, '/') .'/'. $file;

        $data = [
            'today' => date( 'Y-m-d H:ia' )
        ];

        if (! $this->copyTemplate( 'migration', $destination, $data, true) )
        {
            CLI::error('Error creating migration file.');
        }


		return true;
	}

	//--------------------------------------------------------------------

}
