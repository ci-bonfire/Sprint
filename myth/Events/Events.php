<?php namespace Myth\Events;
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

define('EVENTS_PRIORITY_LOW', 200);
define('EVENTS_PRIORITY_NORMAL', 100);
define('EVENTS_PRIORITY_HIGH', 10);

class Events {

    /**
     * The list of listeners.
     *
     * @var array
     */
    protected static $listeners = [];

    /**
     * Flag to let us know if we've read from the config file
     * and have all of the defined events.
     *
     * @var bool
     */
    protected static $have_read_from_file = false;

    //--------------------------------------------------------------------

    /**
     * Registers an action to happen on an event. The action can be any sort
     * of callable:
     *
     *  Events::on('create', 'myFunction');               // procedural function
     *  Events::on('create', ['myClass', 'myMethod']);    // Class::method
     *  Events::on('create', [$myInstance, 'myMethod']);  // Method on an existing instance
     *  Events::on('create', function() {});              // Closure
     *
     * @param $event_name
     * @param callable $callback
     * @param int $priority
     */
    public static function on($event_name, callable $callback, $priority=EVENTS_PRIORITY_NORMAL)
    {
        if (! isset(self::$listeners[$event_name]))
        {
            self::$listeners[$event_name] = [
                true,   // If there's only 1 item, it's sorted.
                [$priority],
                [$callback]
            ];
        }
        else
        {
            self::$listeners[$event_name][0] = false; // Not sorted
            self::$listeners[$event_name][1][] = $priority;
            self::$listeners[$event_name][2][] = $callback;
        }
    }
    
    //--------------------------------------------------------------------

    /**
     * Runs through all subscribed methods running them one at a time,
     * until either:
     *  a) All subscribers have finished or
     *  b) a method returns false, at which point execution of subscribers stops.
     *
     * @param $event_name
     * @return bool
     */
    public static function trigger($event_name, array $arguments = [])
    {
        // Read in our config/events file so that we have them all!
        if (! self::$have_read_from_file)
        {
            if (is_file(APPPATH .'config/events.php'))
            {
                include APPPATH .'config/events.php';
            }
            self::$have_read_from_file = true;
        }

        foreach (self::listeners($event_name) as $listener)
        {
            $result = call_user_func_array($listener, $arguments);

            if ($result === false)
            {
                return false;
            }
        }

        return true;
    }
    
    //--------------------------------------------------------------------

    /**
     * Returns an array of listeners for a single event. They are
     * sorted by priority.
     *
     * If the listener could not be found, returns FALSE, or TRUE if
     * it was removed.
     *
     * @param $event_name
     * @return array
     */
    public static function listeners($event_name)
    {
        if (! isset(self::$listeners[$event_name]))
        {
            return [];
        }

        // The list is not sorted
        if (! self::$listeners[$event_name][0])
        {
            // Sort it!
            array_multisort(self::$listeners[$event_name][1], SORT_NUMERIC, self::$listeners[$event_name][2]);

            // Mark it as sorted already!
            self::$listeners[$event_name][0] = true;
        }

        return self::$listeners[$event_name][2];
    }

    //--------------------------------------------------------------------

    /**
     * Removes a single listener from an event.
     *
     * If the listener couldn't be found, returns FALSE, else TRUE if
     * it was removed.
     *
     * @param $event_name
     * @param callable $listener
     * @return bool
     */
    public static function removeListener($event_name, callable $listener)
    {
        if (! isset(self::$listeners[$event_name]))
        {
            return false;
        }

        foreach (self::$listeners[$event_name][2] as $index => $check)
        {
            if ($check === $listener)
            {
                unset(self::$listeners[$event_name][1][$index]);
                unset(self::$listeners[$event_name][2][$index]);

                return true;
            }
        }

        return false;
    }

    //--------------------------------------------------------------------

    /**
     * Removes all listeners.
     *
     * If the event_name is specified, only listeners for that event will be
     * removed, otherwise all listeners for all events are removed.
     *
     * @param null $event_name
     */
    public static function removeAllListeners($event_name=null)
    {
        if (! is_null($event_name))
        {
            unset(self::$listeners[$event_name]);
        }
        else {
            self::$listeners = [];
        }
    }

    //--------------------------------------------------------------------

}
