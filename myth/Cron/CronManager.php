<?php

namespace Myth\Cron;

use Myth\Cron\CronTask as CronTask;

class CronManager {

    protected static $tasks = [];

    //--------------------------------------------------------------------

    /**
     * Schedules a new task in the system.
     *
     * @param $alias
     * @param $time_string
     * @param callable|string $task
     */
    public static function schedule($alias, $time_string, $task)
    {
        // Valid Alias?
        if (! is_string($alias) || empty($alias))
        {
            throw new \RuntimeException("Invalid (empty) alias.");
        }

        // Valid TimeString?
        if (! is_string($time_string) || empty($time_string))
        {
            throw new \RuntimeException("Invalid (empty) time string.");
        }

        // Valid Task?
        if (! is_callable($task) && ! is_string($task))
        {
            throw new \RuntimeException("Invalid task passed for task: {$alias}");
        }

        static::$tasks[$alias] = new CronTask($time_string, $task);
    }

    //--------------------------------------------------------------------

    /**
     * Removes a single task from the system.
     *
     * @param $alias
     */
    public static function remove($alias)
    {
        if (empty(static::$tasks[$alias]))
        {
            return null;
        }

        unset(static::$tasks[$alias]);

        return true;
    }

    //--------------------------------------------------------------------


    /**
     * Provides an array of all tasks in the system. The format will be
     * like:
     *      [
     *          'alias' => [
     *              'next_run'  => '123456789',
     *              'prev_run'  => '123456789',
     *              'task'  => mixed
     *          ],
     *          ...
     *      ]
     */
    public static function listAll($current_time='now')
    {
        if (! count(static::$tasks))
        {
            return null;
        }

        $output = array();

        foreach (static::$tasks as $alias => $task)
        {
            $output[$alias] = [
                'next_run'  => $task->nextRunDate($current_time),
                'prev_run'  => $task->previousRunDate($current_time),
                'task'      => $task->task()
            ];
        }

        return $output;
    }

    //--------------------------------------------------------------------

    /**
     * Gets the task object assigned to an alias.
     *
     * @param $alias
     * @return null|CronTask object
     */
    public static function task($alias)
    {
        if (empty(static::$tasks[$alias]) )
        {
            return null;
        }

        return static::$tasks[$alias];
    }

    //--------------------------------------------------------------------

    /**
     * Returns all tasks currently in the system.
     *
     * @return array
     */
    public static function tasks()
    {
        return count(static::$tasks) ? static::$tasks : null;
    }

    //--------------------------------------------------------------------

    /**
     * Runs all tasks scheduled to run right now.
     *
     * Can be limited to a single task by passing it's alias in the first param.
     *
     * Returns the output of all task runs as a single string. Perfect for
     * emailing to users on completion
     *
     * @param null $alias
     * @param string $current_time
     * @return string
     */
    public static function run($alias=null, $current_time='now')
    {
        $tasks = static::$tasks;

        if (! empty($alias) && isset($tasks[$alias]))
        {
            $tasks = [$alias => $tasks[$alias] ];
        }

        $output = '';

        $count = 0; // How many tasks have ran?

        foreach ($tasks as $alias => $task)
        {
            if ($task->isDue($current_time))
            {
                $output .= "Running task: {$alias}...";

                try {
                    $result = self::runTask($alias);

                    $output .= $result === true ? "Done\n" : "Failed\n";
                }
                catch (\Exception $e)
                {
                    $output .= "[Exception] ". $e->getMessage() ."\n";
                }

                $count++;
            }
        }

        if (! $count)
        {
            $output .= "No Tasks scheduled to run currently.";
        }

        return $output;
    }

    //--------------------------------------------------------------------

    /**
     * Clears all tasks from the system
     */
    public static function reset()
    {
        static::$tasks = [];
    }

    //--------------------------------------------------------------------

    //--------------------------------------------------------------------
    // Protected Methods
    //--------------------------------------------------------------------

    /**
     * Runs a single Task.
     *
     * NOTE: Tasks MUST return a true/false value only.
     *
     * @param $alias
     * @return bool
     */
    protected static function runTask($alias)
    {
        $task = static::$tasks[$alias]->task();

        $success = false;

        // If it's a standard callable item,
        // then run it.
        if (is_callable($task))
        {
            $success = call_user_func($task);
        }

        // Otherwise, if it's a string it should be
        // a library:method string so try to run it.
        else if (is_string($task) && ! empty($task) && strpos($task, ':') !== false)
        {
            list($class, $method) = explode(':', $task);

            // Let PHP try to autoload it through any available autoloaders
            // (including Composer and user's custom autoloaders). If we
            // don't find it, then assume it's a CI library that we can reach.
            if (class_exists($class)) {
                $class = new $class();
            } else {
                get_instance()->load->library($class);
                $class =& get_instance()->$class;
            }

            if (! method_exists($class, $method)) {
                log_message('error', "[CRON] Method not found: {$class}::{$method}");
                return $success;
            }

            // Call the class with our parameters
            $success = $class->{$method}();
        }

        return $success;
    }

    //--------------------------------------------------------------------

}