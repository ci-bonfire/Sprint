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

/**
 * Class CronTask
 *
 * Represents a single scheduled item.
 *
 * Used by Myth\Cron\CronManager.
 *
 * Inspired by: https://github.com/mtdowling/cron-expression
 *
 * todo: Support time + string formats (like "3am second Friday")
 *
 * @package Myth\Cron
 */
class CronTask {

    /**
     * The original scheduled string.
     * Any valid relative time string.
     * http://php.net/manual/en/datetime.formats.relative.php
     *
     * @var
     */
    protected $schedule;

    /**
     * Stores the callable or library name:method to run.
     *
     * @var
     */
    protected $task;

    //--------------------------------------------------------------------

    /**
     * Stores our scheduled string and actual task.
     *
     * @param $schedule
     * @param callable $task
     */
    public function __construct($schedule, $task)
    {
        $this->schedule = $schedule;

        // If $task is not callable, it should be a library:method
        // string that we can parse. But it must have the colon in the string.
        if (! is_callable($task) && strpos($task, ':') === false)
        {
            throw new \RuntimeException('Not a valid task.');
        }

        $this->task = $task;
    }

    //--------------------------------------------------------------------

    /**
     * Calculates the next date this task is supposed to run.
     *
     * @param int|'now' $current_time
     *
     * @return timestamp|null
     */
    public function nextRunDate($current_time='now')
    {
        $current_time = is_numeric($current_time) ? (int)$current_time : strtotime($current_time);

        $scheduleType = $this->determineScheduleType($this->schedule);

        switch ($scheduleType)
        {
            case 'time':
                return $this->findDateInterval($this->schedule, 'next', $current_time);
                break;
            case 'ordinal':
                return strtotime($this->schedule, $current_time);
                break;
            case 'increment':
                return strtotime($this->schedule, $current_time);
                break;
        }

        return null;
    }
    
    //--------------------------------------------------------------------

    /**
     * Calculates the last time the task should have ran.
     *
     * @param int|'now' $current_time
     *
     * @return timestamp|null
     */
    public function previousRunDate($current_time='now')
    {
        $current_time = is_numeric($current_time) ? (int)$current_time : strtotime($current_time);

        $scheduleType = $this->determineScheduleType($this->schedule);

        switch ($scheduleType)
        {
            case 'time':
                return $this->findDateInterval($this->schedule, 'prev', $current_time);
                break;
            case 'ordinal':
                return $this->findPreviousOrdinal($this->schedule, $current_time);
                break;
            case 'increment':
                return strtotime('-1 '. $this->schedule, $current_time);
                break;
        }

        return null;
    }

    //--------------------------------------------------------------------

    /**
     * Determines if the task is due to be run now.
     *
     * @param string $current_time
     * @internal param $ int|'now' $current_time
     *
     * @return bool
     */
    public function isDue($current_time='now')
    {
        $current_time = is_numeric($current_time) ? (int)$current_time : strtotime($current_time);

        // For easier matching, and I can't imagine people needing cronjob
        // accuracy to the seconds, we'll just take the current minute.
        return date('Y-m-d H:i', $current_time) == date('Y-m-d H:i', $this->nextRunDate($current_time) );
    }
    
    //--------------------------------------------------------------------

    /**
     * Formats the timestamp produced by nextRunDate and previousRunDate
     * into any format available to date.
     *
     * @param $format_string
     * @return bool|string
     */
    public function format($format_string)
    {
        return date($format_string, strtotime($this->schedule));
    }

    //--------------------------------------------------------------------

    /**
     * Gets the associated task.
     *
     * return callable|string
     */
    public function task()
    {
        return $this->task;
    }

    //--------------------------------------------------------------------

    /**
     * Gets the original schedule string.
     */
    public function schedule()
    {
        return $this->schedule;
    }

    //--------------------------------------------------------------------

    /**
     * Checks the schedule text and determines how we have to treat
     * the schedule when determining next and previous values.
     *
     * Potential Types are:
     *
     *  - increment         Can simply add a +x/-x to the front to get the value.
     *  - time              Something like "every 5 minutes"
     *  - ordinal           Like "first", "second", etc.
     *
     * @param $schedule
     * @return null|string
     */
    public function determineScheduleType($schedule)
    {
        $incs = ['sunday', 'monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday', 'sun', 'mon', 'tue',
                'wed', 'thu', 'fri', 'sat', 'weekday', 'weekdays', 'midnight', 'noon'];
        $bigger_incs = [ 'back of', 'front of', 'first day of', 'last day of'];
        $ordinals = ['first', 'second', 'third', 'fourth', 'fifth', 'sixth', 'seventh', 'eighth', 'ninth', 'tenth', 'eleventh', 'twelfth'];
        $schedule = trim( strtolower($schedule) );

        $multiple_words = strpos($schedule, ' ');
        $first_word = substr($schedule, 0, $multiple_words ? $multiple_words : strlen($schedule));

        // Is the first character a number? Then it's a time
        if ( is_numeric( $first_word ) )
        {
            return 'time';
        }


        // First, try the shorter increments. We do increments in
        // two passes becuase this should be faster than the loop.
        if (in_array($first_word, $incs))
        {
            return 'increment';
        }

        // But we have to loop before checking ordinals since
        // ordinals may have same first word as these phrases.
        foreach ($bigger_incs as $test)
        {
            if (strpos($schedule, $test) === 0)
            {
                return 'increment';
            }
        }

        if (in_array($first_word, $ordinals))
        {
            return 'ordinal';
        }

        return null;
    }

    //--------------------------------------------------------------------

    /**
     * Determines the correct time for 'time' type intervals where
     * the timestamp is expected to happen every 'x period', like
     * 'every 5 minutes', every 3 days, etc.
     *
     * @param $schedule
     * @param $type
     * @return float|int|null
     */
    public function findDateInterval($schedule, $type, $current_time='now')
    {
        $current_time = is_numeric($current_time) ? (int)$current_time : strtotime($current_time);

//        list($int, $period) = explode(' ', $schedule);

        $diff = strtotime($schedule, $current_time) - $current_time;

        $return = null;

        switch ($type)
        {
            case 'next':
                $next = floor($current_time / $diff) * $diff;

                // Does next already match the current time?
                if (date('Y-m-d H:i', $next) == date('Y-m-d H:i', $current_time))
                {
                    $return = $next;
                }
                else {
                    $return = $next + $diff;
                }
                break;
            case 'prev':
                $next = ceil($current_time / $diff) * $diff;
                $return = $next - $diff;
                break;
        }

        if (is_numeric($return))
        {
            $return = (int)$return;
        }

        return $return;
    }

    //--------------------------------------------------------------------

    /**
     * Determines the timestamp of the previous ordinal-based time, like
     * 'second Monday'.
     *
     * @param $schedule
     * @param string $current_time
     * @return int|null
     */
    public function findPreviousOrdinal($schedule, $current_time='now')
    {
        $current_time = is_numeric($current_time) ? (int)$current_time : strtotime($current_time);

        if (empty($schedule)) return null;

        // Loop through months in reverse, checking each one to
        // see if the ordinal is in the past. If so - wer'e done.
        foreach ([0, -1, -2, -3, -4, -5, -6, -7, -8, -9, -10, -11, -12] as $i)
        {
            $lastmonth = strtotime("last day of {$i} month", $current_time);

            $test = strtotime($schedule, $lastmonth);

            if ($test <= $current_time)
            {
                return $test;
            }
        }

        return null;
    }

    //--------------------------------------------------------------------


}