<?php

class MY_Form_validation extends CI_Form_validation
{
    /**
     * @var object The CodeIgniter core object.
     */
    public $CI;
    
    //--------------------------------------------------------------------
    
    /**
     * Check if the field has an error associated with it.
     *
     * @param string $field The name of the field
     *
     * @return bool
     */
    public function has_error($field = null)
    {
        if (empty($field)) {
            return false;
        }

        return ! empty($this->_field_data[$field]['error']);
    }

    //--------------------------------------------------------------------

    /**
     * Performs the actual form validation
     *
     * @param string $module Name of the module
     * @param string $group  Name of the group array containing the rules
     *
     * @return bool Success or Failure
     */
//    public function run($module = '', $group = '')
//    {
//        is_object($module) && $this->CI =& $module;
//        return parent::run($group);
//    }

    //--------------------------------------------------------------------

    //--------------------------------------------------------------------
    // Validation Rules
    //--------------------------------------------------------------------

    /**
     * Checks that a value is unique in the database.
     *
     * i.e. '…|required|unique[users.name,users.id]|trim…'
     *
     * <code>
     * "unique[tablename.fieldname,tablename.(primaryKey-used-for-updates)]"
     * </code>
     *
     * @author Adapted from Burak Guzel <http://net.tutsplus.com/tutorials/php/6-codeigniter-hacks-for-the-masters/>
     *
     * @param mixed $value  The value to be checked.
     * @param mixed $params The table and field to check against, if a second
     * field is passed in this is used as "AND NOT EQUAL".
     *
     * @return bool True if the value is unique for that field, else false.
     */
    public function is_unique($value, $params)
    {
        if (empty($this->CI->db))
        {
            $this->CI->load->database();
        }

        // Allow for more than 1 parameter.
        $fields = explode(",", $params);

        // Extract the table and field from the first parameter.
        list($table, $field) = explode('.', $fields[0], 2);

        // Setup the db request.
        $this->CI->db->select($field)
                     ->from($table)
                     ->where($field, $value)
                     ->limit(1);

        // Check whether a second parameter was passed to be used as an
        // "AND NOT EQUAL" where clause
        // eg "select * from users where users.name='test' AND users.id != 4
        if (isset($fields[1])) {
            // Extract the table and field from the second parameter
            list($where_table, $where_field) = explode('.', $fields[1], 2);

            // Get the value from the post's $where_field. If the value is set,
            // add "AND NOT EQUAL" where clause.
            $where_value = $this->CI->input->post($where_field);
            if (isset($where_value)) {
                $this->CI->db->where("{$where_table}.{$where_field} !=", $where_value);
            }
        }

        // If any rows are returned from the database, validation fails
        $query = $this->CI->db->get();
        if ($query->row()) {
            $this->CI->form_validation->set_message('unique', 'The %s field is already in use.');
            return false;
        }

        return true;
    }
}