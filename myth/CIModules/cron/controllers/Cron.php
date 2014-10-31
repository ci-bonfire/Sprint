<?php

use Myth\CLI;
use Myth\Settings\Settings;

class Cron extends \Myth\Controllers\CLIController {

    public function __construct()
    {
        parent::__construct();

        // Load our tasks into the sytem.
        require APPPATH .'config/cron.php';
    }

    //--------------------------------------------------------------------


    /**
     * Lists All available CLI functions
     */
    public function index()
    {
        echo CLI::write("Cron cli commands:");
        echo CLI::write("\t". CLI::color("show", "yellow") ."\t\tLists the names of all tasks.");
        echo CLI::write("\t". CLI::color("show all", "yellow") ."\tLists all jobs with next and previous run times.");
        echo CLI::write("\t". CLI::color("show {task}", "yellow") ."\tLists next and previous run times for a single {task}");

        echo CLI::write("\t". CLI::color("run", "yellow") ."\t\tRuns all currently scheduled tasks.");


        echo CLI::write("\t". CLI::color("disable", "yellow") ."\t\tDisables the cron system and will not run any tasks.");
        echo CLI::write("\t". CLI::color("enable", "yellow") ."\t\tEnables the cron system and will run tasks again.");

        echo CLI::write("\t". CLI::color("suspend {task}", "yellow") ."\tStops a single task from running until resumed.");
        echo CLI::write("\t". CLI::color("resume {task}", "yellow") ."\tResumes execution of a single suspended task.");
    }

    //--------------------------------------------------------------------

    /**
     * Runs all of the tasks (after checking their time, of course...)
     *
     * @param string $alias
     * @return mixed
     */
    public function run($alias=null)
    {
        // Has the system been disabled?
        if (Settings::get('is_disabled', 'cron') == 'y')
        {
            return CLI::error('The cron system has been disabled. No tasks were run.');
        }

        // Run one task or all?
        if (! empty($alias))
        {
            $tasks = \Myth\Cron\CronManager::task($alias);

            if (is_null($tasks))
            {
                return CLI::error("Unable to find the task: '{$alias}'.");
            }

            $tasks = [ $alias => $tasks];
        }
        else
        {
            $tasks = \Myth\Cron\CronManager::tasks();
        }

        if (empty($tasks))
        {
            return CLI::write("There are no tasks to run at this time.");
        }

        // We need to be able to check against suspended tasks.
        $suspended = Settings::get('suspended_tasks', 'cron');

        if (! is_array($suspended))
        {
            $suspended = array($suspended);
        }

        // Loop over all of our tasks, checking them against the
        // suspended tasks to see if they're okay to run.

        // Collect the output of the actions so that we can make
        // it available to the event (for sending emails and the like)
        $output = '';

        echo CLI::write('Starting Tasks...');

        foreach ($tasks as $alias => $task)
        {
            if (in_array($alias, $suspended))
            {
                echo CLI::write("\t[Suspended] {$alias} will not run until resumed.", 'yellow');
                $output .= "[Suspended] {$alias} will not run until resumed.";
                continue;
            }

            echo CLI::write("\tRunning task: {$alias}...");
            $output .= \Myth\Cron\CronManager::run($alias);
        }

        // Give other people a chance to respond.
        echo CLI::write('Done. Firing the event so others can play too...');

        \Myth\Events::trigger('afterCron', [$output]);

        // And we're out of here boys and girls!
        echo CLI::write('Done');
    }

    //--------------------------------------------------------------------


    /**
     * Lists one or more tasks with their scheduled run times.
     *
     * @param null $task
     * @return mixed
     */
    public function show($task=null)
    {
        if (empty($task))
        {
            return $this->listTaskNames();
        }

        if (trim(strtolower($task)) == 'all')
        {
            $tasks = \Myth\Cron\CronManager::listAll();
        }
        else
        {
            $tasks = \Myth\Cron\CronManager::task($task);
        }

        if (! is_array($tasks))
        {
            $tasks = [ $task => [
                'next_run'  => $tasks->nextRunDate(),
                'prev_run'  => $tasks->previousRunDate()
            ]];
        }

        if (! count($tasks))
        {
            return CLI::found('No tasks found.', 'red');
        }

        $suspended = Settings::get('suspended_tasks', 'cron');

        if (empty($suspended))
        {
            $suspended = [];
        }
        /*
         * Headers
         */
        echo CLI::write("Task\t\t\t\tNext Run\t\tPrevious Run");
        echo CLI::write( str_repeat('-', 80) );

        foreach ($tasks as $alias => $task)
        {
            // Suspended?
            $color = 'yellow';
            $extra = '';

            if (in_array($alias, $suspended) )
            {
                $color = 'blue';
                $extra = "\t[Suspended]";
            }

            // Alias can only be 24 chars long.
            $alias = strlen($alias) >= 32 ? substr($alias, 0, 28) .'... ' : $alias . str_repeat(" ", 32 - strlen($alias));

            $next = date('D Y-m-d H:i', $task['next_run']);
            $prev = date('D Y-m-d H:i', $task['prev_run']);

            echo CLI::write("{$alias}{$next}\t{$prev}{$extra}", $color);
        }
    }

    //--------------------------------------------------------------------

    /**
     * Stops a task from being executed during the normal cron runs.
     *
     * @param $alias
     */
    public function suspend($alias)
    {
        // Verify the task actually exists.
        $task = \Myth\Cron\CronManager::task($alias);

        if (is_null($task))
        {
            return CLI::error("Unable to find the task: {$alias}.");
        }

        // Update the existing setting.
        $suspended = Settings::get('suspended_tasks', 'cron');

        if (empty($suspended))
        {
            $suspended = [];
        }

        $suspended[] = $alias;

        if (Settings::save('suspended_tasks', $suspended, 'cron') )
        {
            return CLI::write('Done');
        }

        echo CLI::error('Unkown problem saving the settings.');
    }

    //--------------------------------------------------------------------

    /**
     * Allows the execution of a suspended task to continue again
     * during normal cron execution.
     *
     * @param $alias
     */
    public function resume($alias)
    {
        // Verify the task actually exists.
        $task = \Myth\Cron\CronManager::task($alias);

        if (is_null($task))
        {
            return CLI::error("Unable to find the task: {$alias}.");
        }

        // Update the existing setting.
        $suspended = Settings::get('suspended_tasks', 'cron');

        if (! empty($suspended))
        {
            unset($suspended[ array_search($alias, $suspended) ]);

            if (! Settings::save('suspended_tasks', $suspended, 'cron') )
            {
                return CLI::error('Unkown problem saving the settings.');
            }
        }

        return CLI::write('Done');
    }

    //--------------------------------------------------------------------

    /**
     * Disables the cron tasks and stops the system from running any tasks.
     * To start the system allowing it to run again, use the `enable` command.
     */
    public function disable()
    {
        if (! Settings::save('is_disabled', 'y', 'cron'))
        {
            return CLI::error('Unknown problem saving the setting. '. CLI::color('Cron jobs will still run!', 'yellow'));
        }

        CLI::write('Done');
    }
    
    //--------------------------------------------------------------------

    /**
     * Resumes the running of tasks after the system has been disabled
     * with the `disable` command.
     */
    public function enable()
    {
        if (! Settings::save('is_disabled', 'n', 'cron'))
        {
            return CLI::error('Unknown problem saving the setting. '. CLI::color('Cron jobs will NOT run!', 'yellow'));
        }

        CLI::write('Done');
    }

    //--------------------------------------------------------------------


    //--------------------------------------------------------------------
    // Private Methods
    //--------------------------------------------------------------------

    /**
     * Lists out all available tasks, names only.
     */
    private function listTaskNames()
    {
        $suspended = Settings::get('suspended_tasks', 'cron');

        if (empty($suspended))
        {
            $suspended = [];
        }

        $tasks = \Myth\Cron\CronManager::listAll();

        echo CLI::write("\nAvailable Tasks:");

        foreach ($tasks as $alias => $task)
        {
            $color = 'yellow';
            $extra = '';

            if (in_array($alias, $suspended) )
            {
                $color = 'blue';
                $extra = "[Suspended]";
            }

            echo CLI::write("\t{$extra} {$alias}", $color);
        }
    }

    //--------------------------------------------------------------------

}