<?php

namespace Myth\Settings;

use Myth\Interfaces\SettingsStoreInterface;

/**
 * Class ConfigStore
 *
 * Allows for storing/retrieving settings based on standard
 * CodeIgniter configuration files.
 *
 * When dealing with config files, the 'groups' portion
 * is equal to the config file name. For groups in modules,
 * you would need to use 'module/config' instead of 'config'
 * as the group name.
 *
 * NOTE: This datastore does NOT provide item permenance or
 * deletion capabilities. It works with CodeIgniter's built-in
 * configuration system which will not modify files.
 *
 * @package Myth\Settings
 */
class ConfigStore implements SettingsStoreInterface {

    protected $ci;

    //--------------------------------------------------------------------

    public function __construct( $ci=null )
    {
        if (is_object($ci))
        {
            $this->ci =& $ci;
        }
        else {
            $this->ci =& get_instance();
        }
    }

    //--------------------------------------------------------------------

    /**
     * Inserts/Replaces a single setting item.
     *
     * @param $key
     * @param null $value
     * @param string $group
     */
    public function save($key, $value=null, $group='app')
    {
        // Treat the 'app' group as our primary, un-sectioned
        // config files.
        if ($group != 'app')
        {
            return $this->ci->config->set_item($key, $value);
        }

        // Otherwise, we'll need to ensure a section exists
        if (! isset($this->ci->config->config[$group]))
        {
            $this->ci->config->config[$group] = [];
        }

        $this->ci->config->config[$group][$key] = $value;
    }

    //--------------------------------------------------------------------

    /**
     * Retrieves a single item. If the config system doesn't have the
     * value loaded, yet, it will attempt to load a config file matching
     * the 'group' name and grab the value from that.
     *
     * @param $key
     * @param string $group
     * @return mixed
     */
    public function get($key, $group='application')
    {
        // First, see if CI already has this key loaded.
        $result = $this->ci->config->item($key);

        // This bit will fail when the actual value is NULL
        // but should result in false hits infrequently.
        if ($result !== null)
        {
            return $result;
        }

        // Try to load the 'group' file, then try to load a
        // config file that matches the group name
        if ($this->ci->load->config($group, false, true) )
        {
            $result = $this->ci->config->item($key);
        }

        return $result;
    }

    //--------------------------------------------------------------------

    /**
     * Deletes a single item.
     *
     * NOT supported by this store.
     *
     * @param $key
     * @param $group
     * @return mixed
     */
    public function delete($key, $group='app')
    {
        return false;
    }

    //--------------------------------------------------------------------

    /**
     * Searches the store for any items with $field = $value.
     *
     * Does not work.
     *
     * @param $field
     * @param $value
     * @return mixed
     */
    public function findBy($field, $value)
    {
        return false;
    }

    //--------------------------------------------------------------------

    /**
     * Retrieves all items in the store either globally or for a single group.
     *
     * @param string $group
     * @return mixed
     */
    public function all($group=null)
    {
        if (empty($group))
        {
            return $this->ci->config->config;
        }

        // If we're a group, does the group already exists
        // as a 'section' in the config array?
        if (isset($this->ci->config->config[$group]))
        {
            return $this->ci->config->config[$group];
        }

        // Still here? Attempt to load the file into a section
        // and try one last time.
        if ($this->ci->load->config($group))
        {
            return $this->ci->config->config($group);
        }

        return null;
    }

    //--------------------------------------------------------------------
}