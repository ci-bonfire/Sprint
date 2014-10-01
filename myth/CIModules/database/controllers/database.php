<?php

use \Myth\CLI as CLI;

class Database extends \Myth\Controllers\CLIController
{

    //-------------------------------------------------------------------

    /**
     * Lists the available commands in this controller.
     */
    public function index()
    {
        echo CLI::write("\nThe database tools provides the following commands:");
        echo CLI::write(CLI::color("migrate", 'yellow') . "\t\tmigrate [\$to] \t\tRuns the migrations up or down until schema at version \$to");
        echo CLI::write(CLI::color("quietMigrate", 'yellow') . "\tquiteMigrate [\$to] \tSame as migrate, but without any feedback.");
        echo CLI::write(CLI::color("refresh", 'yellow') . "\t\trefresh\t\t\tRuns migrations to version 0 (uninstall), and then back to the latest migration.");
        echo CLI::write(CLI::color('newMigration', 'yellow') . "\tnewMigration [\$name]\tCreates a new migration file.");
        echo CLI::write(CLI::color('seed', 'yellow') . "\t\tseed [\$name]\t\tRuns the named database seeder.");

        echo CLI::new_line();
    }

    //--------------------------------------------------------------------


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
     * @param string $type  'app', 'myth', 'all' or {module_name}
     * @param null $to
     * @param bool $silent If TRUE, will NOT display any prompts for verification.
     */
    public function migrate($type='app', $to = null, $silent = false)
    {
        $this->load->library('migration');

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
            if ($silent)
            {
                return true;
            }
            else {
                return CLI::write("\tDatabase is already at the desired version ({$to})", 'yellow');
            }
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
                die(var_dump($result));
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
     * @param string $type  'app', 'myth', 'all' or {module_name}
     * @param null $to
     */
    public function quietMigrate($type='app', $to = null)
    {
        return $this->migrate($type, $to, true);
    }

    //--------------------------------------------------------------------


    /**
     * Migrates the database back to 0, then back up to the latest version.
     */
    public function refresh($type='app')
    {
        $this->load->library('migration');

        if ($result = $this->migration->version($type, 0) === false) {
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
    public function newMigration($name = null, $alias = 'app')
    {
        if (empty($name)) {
            $name = CLI::prompt('Migration name? ');

            if (empty($name)) {
                return CLI::error("\tYou must provide a migration name.", 'red');
            }
        }

        $this->load->config('migration');
        $paths = config_item('migration_paths');

        // Does the alias path exist in our config?
        if (empty($paths[$alias])) {
            return CLI::error("\tThe migration path for '{$alias}' does not exist.'");
        }

        // Does the path really exist?
        if (! is_dir($paths[$alias])) {
            return CLI::error("\tThe path for '{$alias}' is not a directory.");
        }

        // Is the folder writeable?
        if (! is_writeable($paths[$alias])) {
            return CLI::error("\tThe folder for '{$alias}' migrations is not writeable.");
        }

        $this->load->library('migration');

        $file = $this->migration->make_name($name);

        $path = $paths[$alias] . $file;

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
     * Installs any database seeds stored in database/seeds
     */
    public function seed($name = 'DatabaseSeeder')
    {
        $this->load->library('seeder');

        $this->seeder->call($name);
    }

    //--------------------------------------------------------------------

}