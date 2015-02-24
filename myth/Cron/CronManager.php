<?php namespace Myth\Cron;
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
     * @param bool $force_run
     * @param string $current_time
     * @return string
     */
    public static function run($alias=null, $force_run=false, $current_time='now')
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
            if ($task->isDue($current_time) || $force_run === true)
            {
                $output .= "Running task: {$alias}...";

                try {
                    $result = self::runTask($alias);

                    if (is_bool($result))
                    {
                        $output .= $result === true ? "Done\n" : "Failed\n";
                    }
                    else if (is_string($result))
                    {
                        $output .= "Done with message:\n{$result}\n";
                    }
                }
                catch (\Exception $e)
                {
                    $output .= "[Exception] ". $e->getMessage() ."\n";
                }

                $count++;
            }
            else
            {
                $output .= "'{$alias}' Not scheduled to run until {$task->nextRunDate()}.";
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
