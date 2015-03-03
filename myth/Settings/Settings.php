<?php namespace Myth\Settings;
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
 * Class Settings
 *
 * Allows for key/value storage / retrieval from multiple datastores.
 * By default, supports values within the database and config files.
 *
 * @package Myth\Settings
 */
class Settings {

    /**
     * Holds instantiated data stores.
     *
     * @var array
     */
    protected static $stores = [];

    protected static $default_store = '';

    protected static $initialized = false;

    //--------------------------------------------------------------------

    /**
     * Fires up instances of the datastores and gets us ready to roll.
     */
    public static function init()
    {
        if (self::$initialized === true)
        {
            return;
        }

        // Load up and instantiate our setting stores.
        $user_stores = config_item('settings.stores');

        if (is_array($user_stores))
        {
            foreach ($user_stores as $alias => $class)
            {
                self::$stores[ strtolower($alias) ][] = new $class();
            }
        }

        self::$default_store = config_item('settings.default_store');

        if (empty(self::$stores[ self::$default_store ]))
        {
            show_error( lang('settings.bad_default') );
        }
    }

    //--------------------------------------------------------------------

    /**
     * Inserts/Replaces a single setting item.
     *
     * @param $key
     * @param null $value
     * @param string $group
     * @param string $store
     */
    public static function save($key, $value=null, $group='app', $store=null)
    {
        self::init();

        if (empty($store) || empty(self::$stores[$store]))
        {
            // we've already determined that the default store exists
            // in the init() method.
            $store = self::$default_store;
        }

        return self::$stores[ $store ][0]->save($key, $value, $group);
    }

    //--------------------------------------------------------------------

    /**
     * Retrieves a single item. If not $store is specified, will loop
     * through the stores in order until it finds it. If $store is
     * specified, will only look within that store for it.
     *
     * @param $key
     * @param string $group
     * @param string $store
     * @return mixed
     */
    public static function get($key, $group='app', $store=null)
    {
        self::init();

        $group = strtolower($group);

        // If the store is specified but doesn't exist, crap out
        // so that they developer has a chance to fix it.
        if (! is_null($store) && empty(self::$stores[$store]))
        {
            show_error( sprintf( lang('settings.cant_retrieve'), $store ) );
        }

        // If $store is set, get the value from that store only.
        if (! empty($store))
        {
            return self::$stores[$store]->get($key, $group);
        }

        // Otherwise loop through each store until we find it
        foreach (self::$stores as $s)
        {
            if ($found = $s[0]->get($key, $group))
            {
                return $found;
            }
        }

        // Still here... guess we didn't find anything, then...
        return false;
    }

    //--------------------------------------------------------------------

    /**
     * Deletes a single item.
     *
     * @param $key
     * @param $group
     * @param $store
     * @return mixed
     */
    public static function delete($key, $group='app', $store=null)
    {
        self::init();

        $group = strtolower($group);

        // If the store is specified but doesn't exist, crap out
        // so that they developer has a chance to fix it.
        if (! is_null($store) && empty(self::$stores[$store]))
        {
            show_error( sprintf( lang('settings.cant_retrieve'), $store ) );
        }

        // If $store is set, get the value from that store only.
        if (! empty($store))
        {
            return self::$stores[$store]->delete($key, $group);
        }

        // Otherwise delete from the default store
        return self::$stores[ self::$default_store ]->delete($key, $group);
    }

    //--------------------------------------------------------------------

    /**
     * Searches the store for any items with $field = $value.
     *
     * @param $field
     * @param $value
     * @return mixed
     */
    public static function findBy($field, $value)
    {
        self::init();

        foreach (self::$stores as $s)
        {
            if ($found = $s->findBy($field, $value))
            {
                return $found;
            }
        }

        return false;
    }

    //--------------------------------------------------------------------

    /**
     * Retrieves all items in the store either globally or for a single group.
     *
     * @param string $group
     * @return mixed
     */
    public static function all($group=null, $store=null)
    {
        self::init();

        $group = strtolower($group);

        if (! empty($store))
        {
            return self::$stores[ $store ]->all($group);
        }

        // Otherwise combine the results from all stores
        $results = [];

        foreach (self::$stores as $s)
        {
            $found = $s->all($group);
            if ($found)
            {
                $results = array_merge($results, (array)$found);
            }
        }

        return $results;
    }

    //--------------------------------------------------------------------

    /**
     * Appends a new datastore the end of the curent stores.
     *
     * @param $alias
     * @param $class
     * @return bool
     */
    public static function addStore($alias, $class)
    {
        self::init();

        if (class_exists($class))
        {
            self::$stores[ strtolower($alias) ] = new $class();
            return true;
        }

        return false;
    }

    //--------------------------------------------------------------------

    /**
     * Removes a datastore from the list of stores currently available.
     *
     * @param $alias
     * @return bool
     */
    public static function removeStore($alias)
    {
        self::init();

        $alias = strtolower($alias);

        if (empty(self::$stores[$alias]))
        {
            return false;
        }

        unset(self::$stores[$alias]);

        return true;
    }

    //--------------------------------------------------------------------

}
