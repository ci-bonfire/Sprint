<?php

namespace Myth\Settings;

use Myth\Interfaces\SettingsInterface;

class DatabaseModel implements SettingsInterface {

    protected $ci;

    protected $db;

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
     * Inserts or Replaces an setting value.
     *
     * @param $key
     * @param null $value
     * @param string $group
     * @return bool
     */
    public function save($key, $value=null, $group='app')
    {
        if (empty($this->ci->db) || ! is_object($this->ci->db))
        {
            return false;
        }

        $where = [
            'name'  => $key,
            'group' => $group
        ];
        $this->ci->db->delete('settings', $where);

        if (is_array($value) || is_object($value))
        {
            $value = serialize($value);
        }

        $data = [
            'name'  => $key,
            'value' => $value,
            'group' => $group
        ];
        return $this->ci->db->insert('settings', $data);
    }

    //--------------------------------------------------------------------

    /**
     * Retrieves a single item.
     *
     * @param $key
     * @param string $group
     * @return mixed
     */
    public function get($key, $group='app')
    {
        if (empty($this->ci->db) || ! is_object($this->ci->db))
        {
            return false;
        }

        $where = [
            'name'  => $key,
            'group' => $group
        ];

        $query = $this->ci->db->where($where)
                              ->get('settings');

        if (! $query->num_rows())
        {
            return false;
        }

        $value = $query->row()->value;

        // Check to see if it needs to be unserialized
        $data = @unserialize($value);   // We don't need to issue an E_NOTICE here...

        // Check for a value of false or
        if ($value === 'b:0;' || $data !== false)
        {
            $value = $data;
        }

        return $value;
    }

    //--------------------------------------------------------------------

    /**
     * Deletes a single item.
     *
     * @param $key
     * @param $group
     * @return mixed
     */
    public function delete($key, $group='app')
    {
        if (empty($this->ci->db) || ! is_object($this->ci->db))
        {
            return false;
        }

        $where = [
            'name'  => $key,
            'group' => $group
        ];

        return $this->ci->db->delete('settings', $where);
    }

    //--------------------------------------------------------------------

    /**
     * Searches the store for any items with $field = $value.
     *
     * @param $field
     * @param $value
     * @return mixed
     */
    public function findBy($field, $value)
    {
        if (empty($this->ci->db) || ! is_object($this->ci->db))
        {
            return false;
        }

        $query = $this->ci->db->where($field, $value)
                              ->get('settings');

        if (! $query->num_rows())
        {
            return false;
        }

        return $query->result_array();
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
        if (empty($this->ci->db) || ! is_object($this->ci->db))
        {
            return false;
        }

        $query = $this->ci->db->get('settings');

        if (! $query->num_rows())
        {
            return false;
        }

        return $query->result_array();
    }

    //--------------------------------------------------------------------
}