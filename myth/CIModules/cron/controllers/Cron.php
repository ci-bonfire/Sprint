<?php

use Myth\CLI;

class Cron extends \Myth\Controllers\CLIController {

    public function __construct()
    {
        parent::__construct();

        // Load our tasks into the sytem.
        require APPPATH .'config/cron.php';
    }

    //--------------------------------------------------------------------



    /**
     * Lists All availalbe CLI functions
     */
    public function index()
    {
        echo CLI::write("Cron cli commands:");
        echo CLI::write("\t". CLI::color("show", "yellow") ."\t\tLists the names of all tasks.");
        echo CLI::write("\t". CLI::color("show all", "yellow") ."\tLists all jobs with next and previous run times.");
        echo CLI::write("\t". CLI::color("show {task}", "yellow") ."\tLists next and previous run times for a single {task}");

        echo CLI::write("\t". CLI::color("run", "yellow") ."\t\tRuns all currently scheduled tasks.");
        echo CLI::write("\t". CLI::color("run {task}", "yellow") ."\tRuns only a single {task}.");
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

        /*
         * Headers
         */
        echo CLI::write("Task\t\t\t\tNext Run\t\tPrevious Run");
        echo CLI::write( str_repeat('-', 80) );

        foreach ($tasks as $alias => $task)
        {
            // Alias can only be 24 chars long.
            $alias = strlen($alias) >= 32 ? substr($alias, 0, 28) .'... ' : $alias . str_repeat(" ", 32 - strlen($alias));

            $next = date('D Y-m-d H:i', $task['next_run']);
            $prev = date('D Y-m-d H:i', $task['prev_run']);

            echo CLI::write("{$alias}{$next}\t{$prev}");
        }
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
        $tasks = \Myth\Cron\CronManager::listAll();

        echo CLI::write("\nAvailable Tasks:");

        foreach ($tasks as $alias => $task)
        {
            echo CLI::write("\t{$alias}");
        }
    }

    //--------------------------------------------------------------------

}