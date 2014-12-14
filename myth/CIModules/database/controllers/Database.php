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

use \Myth\CLI as CLI;

class Database extends \Myth\Controllers\CLIController
{

    protected $descriptions = [
        'migrate'       => ['migrate [$to]',        'Runs the migrations up or down until schema at version \$to'],
        'quietMigrate'  => ['quiteMigrate [$to]',   'Same as migrate but without any feedback.'],
        'refresh'       => ['refresh',              'Runs migrations back to version 0 (uninstall) and then back to the most recent migration.'],
        'newMigration'  => ['newMigration [$name]', 'Creates a new migration file.'],
        'seed'          => ['seed [$name]',         'Runs the named database seeder.']
    ];

    protected $long_descriptions = [
        'migrate'       => '',
        'quietMigrate'  => '',
        'refresh'       => '',
        'newMigration'  => '',
        'seed'          => ''
    ];

    //-------------------------------------------------------------------

    //--------------------------------------------------------------------
    // Migration Methods
    //--------------------------------------------------------------------

    /**
     * Provides a command-line interface to the migration scripts.
     * If no $to is provided, will migrate to the latest version.
     *
     * Example:
     *      > php index.php database migrate
     *
     * @param string $type 'app', 'myth', 'all' or {module_name}
     * @param null $to
     * @param bool $silent If TRUE, will NOT display any prompts for verification.
     * @return bool|void
     */
    public function migrate($type=null, $to = null, $silent = false)
    {
        $this->load->library('migration');

        if (empty($type))
        {
            $type = CLI::prompt("Migration group to refresh?", $this->migration->default_migration_path());

            if (empty($type))
            {
                return $silent ? false : CLI::error("\tYou must supply a group to refresh.");
            }
        }

        // Get our stats on the migrations
        $latest = $this->migration->get_latest($type);
        $latest = empty($latest) ? 0 : $latest;

        if (empty($latest)) {
            return CLI::write("\tNo migrations found.", 'yellow');
        }

        $current = $this->migration->get_version($type);

        // Already at the desired version?
        if (! is_null($to) && $current == $to)
        {
            return $silent ? true : CLI::write("\tDatabase is already at the desired version ({$to})", 'yellow');
        }

        $target = is_null($to) ? $latest : $to;

        // Just to be safe, verify with the user they want to migrate
        // to the latest version.
        if (is_null($to)) {
            // If we're in silent mode, don't prompt, just go to the latest...
            if (! $silent) {
                $go_ahead = CLI::prompt('Migrate to the latest available version?', array('y', 'n'));

                if ($go_ahead == 'n') {
                    return CLI::write('Bailing...', 'yellow');
                }
            }

            if (! $this->migration->latest($type)) {
                return CLI::error("\n\tERROR: " . $this->migration->error_string() . "\n");
            }
        } else {
            if ($this->migration->version($type, $to) === false) {
                return CLI::error("\n\tERROR: " . $this->migration->error_string() . "\n");
            }
        }

        return $silent ? true :
            CLI::write("\n\tSuccessfully migrated database from version {$current} to {$target}.\n", 'green');
    }

    //--------------------------------------------------------------------

    /**
     * Performs a migration that does not prompt for any information.
     * Suitable for use within automated scripts that can't be
     * bothered with answering questions.
     *
     * @param string $type 'app', 'myth', 'all' or {module_name}
     * @param null $to
     * @return bool|void
     */
    public function quietMigrate($type='app', $to = null)
    {
        return $this->migrate($type, $to, true);
    }

    //--------------------------------------------------------------------


    /**
     * Migrates the database back to 0, then back up to the latest version.
     *
     * @param string $type  The group or module to refresh.
     * @return mixed
     */
    public function refresh($type=null)
    {
        $this->load->library('migration');

        if (empty($type))
        {
            $type = CLI::prompt("Migration group to refresh?", $this->migration->default_migration_path());

            if (empty($type))
            {
                return CLI::error("\tYou must supply a group to refresh.");
            }
        }

        if ($this->migration->version($type, 0) === false) {
            return CLI::error("\tERROR: " . $this->migration->error_string());
        }

        CLI::write(CLI::color("\tCleared the database.", 'green'));

        if ($this->migration->latest($type) === false) {
            return CLI::error("\tERROR: " . $this->migration->error_string());
        }

        CLI::write("\tRe-installed the database to the latest migration.", 'green');
    }

    //--------------------------------------------------------------------

    /**
     * Creates a new migration file ready to be used.
     *
     * @param $name
     */
    public function newMigration($name = null, $type = 'app')
    {
        if (empty($name)) {
            $name = CLI::prompt('Migration name? ');

            if (empty($name)) {
                return CLI::error("\tYou must provide a migration name.", 'red');
            }
        }

        $this->load->library('migration');

        $path = $this->migration->determine_migration_path($type);

        // Does the alias path exist in our config?
        if (! $path) {
            return CLI::error("\tThe migration path for '{$type}' does not exist.'");
        }

        // Does the path really exist?
        if (! is_dir($path)) {
            return CLI::error("\tThe path for '{$type}' is not a directory.");
        }

        // Is the folder writeable?
        if (! is_writeable($path)) {
            return CLI::error("\tThe folder for '{$type}' migrations is not writeable.");
        }

        $file = $this->migration->make_name($name);

        $path = rtrim($path, '/') .'/'. $file;

        $contents = <<<EOT
<?php

/**
 * Migration: {clean_name}
 *
 * Created by: SprintPHP
 * Created on: {date}
 */
class Migration_{name} extends CI_Migration {

    public function up ()
    {

    }

    //--------------------------------------------------------------------

    public function down ()
    {

    }

    //--------------------------------------------------------------------

}
EOT;
        $contents = str_replace('{name}', $name, $contents);
        $contents = str_replace('{date}', date('Y-m-d H:i:s a'), $contents);
        $contents = str_replace('{clean_name}', ucwords(str_replace('_', ' ', $name)), $contents);

        $this->load->helper('file');

        if (write_file($path, $contents)) {
            return CLI::write("\tNew migration created: " . CLI::color($file, 'yellow'), 'green');
        }

        return CLI::error("\tUnkown error trying to create migration: {$file}", 'red');
    }

    //--------------------------------------------------------------------

    //--------------------------------------------------------------------
    // Seeding Methods
    //--------------------------------------------------------------------

    /**
     * Installs any database seeds stored in database/seeds. Seeds just need to
     * extend the Seeder class and have a method named run.
     */
    public function seed($name = null)
    {
        $this->load->library('seeder');

        $this->seeder->call($name);
    }

    //--------------------------------------------------------------------

}