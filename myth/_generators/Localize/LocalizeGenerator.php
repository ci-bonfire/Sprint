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
            case 'theme':
                $this->makeTheme($segments);
                break;
            case 'controller':
                $this->makeController($segments);
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

        CLI::write( CLI::color('install', 'yellow') .'      install       Creates migration file for the localizations table' );
        CLI::write( CLI::color('controller', 'yellow') .'   controller    Copies controller view files to localized folder' );
        CLI::write( CLI::color('theme', 'yellow') .'        theme         Copies theme view files to localize folder' );
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

    /**
     * Creates the theme's sub-folder.
     *
     * @param array $segments
     *
     * @return bool
     */
    private function makeTheme( array $segments )
    {
        $theme = ! empty($segments[0]) ? $segments[0] : null;
        $lang  = ! empty($segments[1]) ? $segments[1] : null;

        // Theme
        if (empty($theme))
        {
            $theme = CLI::prompt("Theme alias");
            $theme = strtolower($theme);
        }

        $source = $this->verifyTheme($theme);

        // Language
        if (empty($lang))
        {
            $lang = CLI::prompt('Translation name');
            $lang = strtolower($lang);

            $this->verifyLanguage($lang);
        }

        // Copy the folder to a temp folder to avoid nesting
        // and infinite recursion issues.
        $temp = APPPATH .'cache/copy_temp/'. $lang;

        if (! $this->copyDirectory($source, $temp))
        {
            CLI::error('Error creating theme translation folder.');
        }

        // Now move the folder into the theme location
        rename($temp, $source .'/'. $lang);

        return true;
    }

    //--------------------------------------------------------------------

    private function makeController( $segments )
    {
        die('Not Implemented yet');
    }

    //--------------------------------------------------------------------

    /**
     * Verifies that the main language folder contains a translation
     * folder. If it doesn't, will verify with the user or exit.
     *
     * @param $language
     *
     * @return bool
     */
    private function verifyLanguage( $language )
    {
        $continue = false;

        if (! is_dir(APPPATH .'language/'. $language))
        {
            CLI::write("No translation exists for language: ". CLI::color($language, 'yellow') );
            $check = CLI::prompt('Continue', ['y', 'n']);

            if ($check == 'y' || $check == 'n')
            {
                $continue = true;
            }
        }

        if ($continue)
        {
            return true;
        }

        CLI::error('Cannot continue.');
        exit(1);
    }

    //--------------------------------------------------------------------

    /**
     * Ensures the a theme with the alias, $theme, exists. Returns
     * the location.
     *
     * @param $theme
     *
     * @return string
     */
    private function verifyTheme( $theme )
    {
        $folders = config_item('theme.paths');

        if (! array_key_exists($theme, $folders) || ! is_dir($folders[$theme]) )
        {
            CLI::error('Theme does not exist: '. CLI::color($theme, 'yellow'));
            exit(1);
        }

        $path = $folders[$theme];
        $path = rtrim($path, '/ ');

        return $path;
    }

    //--------------------------------------------------------------------

}
