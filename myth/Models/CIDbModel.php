<?php namespace Myth\Models;
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

//--------------------------------------------------------------------

/**
 * BF_Model
 *
 * An extension of CodeIgniter's built-in model that provides a convenient
 * base to quickly and easily build your database-backed models off of.
 *
 * Provides observers, soft-deletes, basic CRUD functions, helpful functions,
 * and more.
 *
 * This pulls many ideas and inspiration from Jamie Rumbelow's excellent MY_Model
 * and the ideas described in his CodeIgniter Handbook, as well as from Laravel
 * and Rails.
 *
 * NOTE: The methods in this model do not take advantage of the clean syntax of
 * method chaining. THIS IS BY DESIGN! and allows our mocking of the database
 * class to work in a simple and clean way. Do not change this.
 *
 * @package Bonfire
 * @author Bonfire Dev Team
 * @license http://opensource.org/licenses/MIT
 *
 * /**
 * The following properties are used to provide autocomplete for IDE's.
 *
 * Thanks to:  https://gist.github.com/topdown/1697338
 *
 * @property \CI_DB_query_builder    $db
 * @property \CI_DB_utility          $dbutil
 * @property \CI_DB_forge            $dbforge
 * @property \CI_Benchmark           $benchmark
 * @property \CI_Calendar            $calendar
 * @property \CI_Cart                $cart
 * @property \CI_Config              $config
 * @property \CI_Controller          $controller
 * @property \CI_Email               $email
 * @property \CI_Encrypt             $encrypt
 * @property \CI_Exceptions          $exceptions
 * @property \CI_Form_validation     $form_validation
 * @property \CI_Ftp                 $ftp
 * @property \CI_Hooks               $hooks
 * @property \CI_Image_lib           $image_lib
 * @property \CI_Input               $input
 * @property \CI_Lang                $lang
 * @property \CI_Loader              $load
 * @property \CI_Log                 $log
 * @property \CI_Model               $model
 * @property \CI_Output              $output
 * @property \CI_Pagination          $pagination
 * @property \CI_Parser              $parser
 * @property \CI_Profiler            $profiler
 * @property \CI_Router              $router
 * @property \CI_Session             $session
 * @property \CI_Table               $table
 * @property \CI_Trackback           $trackback
 * @property \CI_Typography          $typography
 * @property \CI_Unit_test           $unit_test
 * @property \CI_Upload              $upload
 * @property \CI_URI                 $uri
 * @property \CI_User_agent          $user_agent
 * @property \CI_Xmlrpc              $xmlrpc
 * @property \CI_Xmlrpcs             $xmlrpcs
 * @property \CI_Zip                 $zip
 * @property \CI_Javascript          $javascript
 * @property \CI_Jquery              $jquery
 * @property \CI_Utf8                $utf8
 * @property \CI_Security            $security
 */
class CIDbModel
{

    /**
     * The model's default table name.
     *
     * @var string;
     */
    protected $table_name;

    /**
     * The model's default primary key.
     *
     * @var string
     */
    protected $primary_key = 'id';

    /**
     * The type of date/time field used for created_on and modified_on fields.
     * Valid types are: 'int', 'datetime', 'date'
     *
     * @var string
     * @access protected
     */
    protected $date_format = 'datetime';

    /*
        Var: $log_user
        If TRUE, will log user id for 'created_by', 'modified_by' and 'deleted_by'.

        Access:
            Protected
    */
    protected $log_user = FALSE;



    /**
     * Whether or not to auto-fill a 'created_on' field on inserts.
     *
     * @var boolean
     * @access protected
     */
    protected $set_created = TRUE;

    /**
     * Field name to use to the created time column in the DB table.
     *
     * @var string
     * @access protected
     */
    protected $created_field = 'created_on';

    /*
        Var: $created_by_field
        Field name to use to the created by column in the DB table.

        Access:
            Protected
    */
    protected $created_by_field = 'created_by';



    /**
     * Whether or not to auto-fill a 'modified_on' field on updates.
     *
     * @var boolean
     * @access protected
     */
    protected $set_modified = TRUE;

    /**
     * Field name to use to the modified time column in the DB table.
     *
     * @var string
     * @access protected
     */
    protected $modified_field = 'modified_on';

    /*
        Var: $modified_by_field
        Field name to use to the modified by column in the DB table.

        Access:
            Protected
    */
    protected $modified_by_field = 'modified_by';


    /**
     * Support for soft_deletes.
     */
    protected $soft_deletes = FALSE;
    protected $soft_delete_key = 'deleted';
    protected $temp_with_deleted = FALSE;

    /*
        Var: $deleted_by_field
        Field name to use for the deleted by column in the DB table.

        Access:
            Protected
    */
    protected $deleted_by_field = 'deleted_by';



    /**
     * Various callbacks available to the class. They are simple lists of
     * method names (methods will be ran on $this).
     */
    protected $before_insert = array();
    protected $after_insert = array();
    protected $before_update = array();
    protected $after_update = array();
    protected $before_find = array();
    protected $after_find = array();
    protected $before_delete = array();
    protected $after_delete = array();

    protected $callback_parameters = array();



    /*
        If TRUE, inserts will return the last_insert_id. However,
        this can potentially slow down large imports drastically
        so you can turn it off with the return_insert_id(false) method.
     */
    protected $return_insert_id = true;

    /**
     * By default, we return items as objects. You can change this for the
     * entire class by setting this value to 'array' instead of 'object'.
     * Alternatively, you can do it on a per-instance basis using the
     * 'as_array()' and 'as_object()' methods.
     */
    protected $return_type = 'object';
    protected $temp_return_type = NULL;

    /**
     * Protected, non-modifiable attributes
     */
    protected $protected_attributes = array();



    /**
     * An array of validation rules. This needs to be the same format
     * as validation rules passed to the Form_validation library.
     */
    protected $validation_rules = array();

    /**
     * Optionally skip the validation. Used in conjunction with
     * skip_validation() to skip data validation for any future calls.
     */
    protected $skip_validation = FALSE;

    /**
     * An array of extra rules to add to validation rules during inserts only.
     * Often used for adding 'required' rules to fields on insert, but not udpates.
     *
     *   array( 'username' => 'required|strip_tags' );
     * @var array
     */
    protected $insert_validate_rules = array();



    /**
     * @var Array Columns for the model's database fields
     *
     * This can be set to avoid a database call if using $this->prep_data()
     */
    protected $fields = array();

    //--------------------------------------------------------------------

    /**
     * Does basic setup. Can pass the database connection into the constructor
     * to use that $db instead of the global CI one.
     *
     * @param object $db // A database/driver instance
     * @param object $form_validation // A form_validation library instance
     */
    public function __construct($db = null, $form_validation = null)
    {
        // Always protect our attributes
        array_unshift($this->before_insert, 'protect_attributes');
        array_unshift($this->before_update, 'protect_attributes');

        // Check our auto-set features and make sure they are part of
        // our observer system.
        if ($this->set_created === true) array_unshift($this->before_insert, 'created_on');
        if ($this->set_modified === true) array_unshift($this->before_update, 'modified_on');

        // Make sure our temp return type is correct.
        $this->temp_return_type = $this->return_type;

        // Make sure our database is loaded
        if (!is_null($db)) {
            $this->db = $db;
        }
        else {
            // Auto Init the damn database....
            $this->load->database();
        }

        // Do we have a form_validation library?
        if (! is_null($form_validation)) {
            $this->form_validation = $form_validation;
        }
        else {
            $this->load->library('form_validation');
        }
        
        log_message('debug', 'CIDbModel Class Initialized');
    }

    //--------------------------------------------------------------------

    //--------------------------------------------------------------------
    // CRUD Methods
    //--------------------------------------------------------------------

    /**
     * A simple way to grab the first result of a search only.
     */
    public function first()
    {
        $rows = $this->limit(1, 0)->find_all();

        if (is_array($rows) && count($rows)) {
            return $rows[0];
        }

        return $rows;
    }

    //--------------------------------------------------------------------


    /**
     * Finds a single record based on it's primary key. Will ignore deleted rows.
     *
     * @param  mixed $id The primary_key value of the object to retrieve.
     * @return object
     */
    public function find($id)
    {
        $this->trigger('before_find', ['id' => $id, 'method' => 'find']);

        // Ignore any soft-deleted rows
        if ($this->soft_deletes) {
            // We only need to modify the where statement if
            // temp_with_deleted is false.
            if ($this->temp_with_deleted !== true) {
                $this->db->where($this->table_name . "." . $this->soft_delete_key, false);
            }

            $this->temp_with_deleted = false;
        }

        $this->db->where($this->primary_key, $id);
        $row = $this->db->get($this->table_name);
        $row = $this->temp_return_type == 'array' ? $row->row_array() : $row->row(0, $this->temp_return_type);

        if ( ! empty($row))
        {
            $row = $this->trigger('after_find', ['id' => $id, 'method' => 'find', 'fields' => $row]);
        }

        // Reset our return type
        $this->temp_return_type = $this->return_type;

        return $row;
    }

    //--------------------------------------------------------------------

    /**
     * Fetch a single record based on an arbitrary WHERE call. Can be
     * any valid value to $this->db->where(). Will not pull in deleted rows
     * if using soft deletes.
     *
     * @return object
     */
    public function find_by()
    {
        $where = func_get_args();
        $this->_set_where($where);

        // Ignore any soft-deleted rows
        if ($this->soft_deletes) {
            // We only need to modify the where statement if
            // temp_with_deleted is false.
            if ($this->temp_with_deleted !== true) {
                $this->db->where($this->table_name . "." . $this->soft_delete_key, false);
            }

            $this->temp_with_deleted = false;
        }

        $this->trigger('before_find', ['method' => 'find_by', 'fields' => $where]);

        $row = $this->db->get($this->table_name);
        $row = $this->temp_return_type == 'array' ? $row->row_array() : $row->row(0, $this->temp_return_type);

        if ( ! empty($row))
        {
            $row = $this->trigger('after_find', ['method' => 'find_by', 'fields' => $row]);
        }

        // Reset our return type
        $this->temp_return_type = $this->return_type;

        return $row;
    }

    //--------------------------------------------------------------------

    /**
     * Retrieves a number of items based on an array of primary_values passed in.
     *
     * @param  array $values An array of primary key values to find.
     *
     * @return object or FALSE
     */
    public function find_many($values)
    {
        $this->db->where_in($this->primary_key, $values);

        return $this->find_all();
    }

    //--------------------------------------------------------------------

    /**
     * Retrieves a number of items based on an arbitrary WHERE call. Can be
     * any set of parameters valid to $db->where.
     *
     * @return object or FALSE
     */
    public function find_many_by()
    {
        $where = func_get_args();
        $this->_set_where($where);

        return $this->find_all();
    }

    //--------------------------------------------------------------------

    /**
     * Fetch all of the records in the table. Can be used with scoped calls
     * to restrict the results.
     *
     * @return object or FALSE
     */
    public function find_all()
    {
        $this->trigger('before_find', ['method' => 'find_all']);

        // Ignore any soft-deleted rows
        if ($this->soft_deletes) {
            // We only need to modify the where statement if
            // temp_with_deleted is false.
            if ($this->temp_with_deleted !== true) {
                $this->db->where($this->table_name . "." . $this->soft_delete_key, false);
            }

            $this->temp_with_deleted = false;
        }

        $rows = $this->db->get($this->table_name);
        $rows = $this->temp_return_type == 'array' ? $rows->result_array() : $rows->result($this->temp_return_type);

        if (is_array($rows)) {
            foreach ($rows as $key => &$row) {
                $row = $this->trigger('after_find', ['method' => 'find_all', 'fields' => $row] );
            }
        }

        // Reset our return type
        $this->temp_return_type = $this->return_type;

        return $rows;
    }

    //--------------------------------------------------------------------

    /**
     * Inserts data into the database.
     *
     * @param  array $data An array of key/value pairs to insert to database.
     * @param  array $skip_validation If TRUE, will skip validation of data for this call only.
     * @return mixed       The primary_key value of the inserted record, or FALSE.
     */
    public function insert($data, $skip_validation = null)
    {
        $skip_validation = is_null($skip_validation) ? $this->skip_validation : $skip_validation;

        if ($skip_validation === FALSE) {
            $data = $this->validate($data, 'insert', $skip_validation);
        }

        if ($data !== FALSE) {
            $data = $this->trigger('before_insert', ['method' => 'insert', 'fields' => $data]);

            $this->db->insert($this->table_name, $this->prep_data($data) );

            if ($this->return_insert_id) {
                $id = $this->db->insert_id();

                $this->trigger('after_insert', ['id' => $id, 'fields' => $data, 'method' => 'insert']);

                return $id;
            }

            return TRUE;
        } else {
            return FALSE;
        }
    }

    //--------------------------------------------------------------------

    /**
     * Inserts multiple rows into the database at once. Takes an associative
     * array of value pairs.
     *
     * $data = array(
     *     array(
     *         'title' => 'My title'
     *     ),
     *     array(
     *         'title'  => 'My Other Title'
     *     )
     * );
     *
     * @param  array $data An associate array of rows to insert
     * @param  array $skip_validation If TRUE, will skip validation of data for this call only.
     * @return bool
     */
    public function insert_batch($data, $skip_validation = null)
    {
        $skip_validation = is_null($skip_validation) ? $this->skip_validation : $skip_validation;

        if ($skip_validation === FALSE) {
            $data = $this->validate($data, 'insert', $skip_validation);
        }

        if ($data !== FALSE) {
            $data['batch'] = true;
            $data = $this->trigger('before_insert', ['method' => 'insert_batch', 'fields' => $data] );
            unset($data['batch']);

            return $this->db->insert_batch($this->table_name, $data);
        } else {
            return FALSE;
        }
    }

    //--------------------------------------------------------------------

    /**
     * Performs the SQL standard for a combined DELETE + INSERT, using
     * PRIMARY and UNIQUE keys to determine which row to replace.
     *
     * See CI's documentation for the replace method. We simply wrap
     * our validation and triggers around their method.
     *
     * @param $data
     * @param null $skip_validation
     * @return bool
     */
    public function replace($data, $skip_validation=null)
    {
        $skip_validation = is_null($skip_validation) ? $this->skip_validation : $skip_validation;

        if ($skip_validation === FALSE) {
            $data = $this->validate($data, 'insert', $skip_validation);
        }

        if ($data !== FALSE) {
            $this->db->replace($this->table_name, $this->prep_data($data));

            if ($this->return_insert_id) {
                $id = $this->db->insert_id();

                $this->trigger('after_insert', ['id' => $id, 'fields' => $data, 'method'=>'replace']);

                return $id;
            }

            return TRUE;
        } else {
            return FALSE;
        }
    }

    //--------------------------------------------------------------------


    /**
     * Updates an existing record in the database.
     *
     * @param  mixed $id The primary_key value of the record to update.
     * @param  array $data An array of value pairs to update in the record.
     * @param  array $skip_validation If TRUE, will skip validation of data for this call only.
     * @return bool
     */
    public function update($id, $data, $skip_validation = null)
    {
        $skip_validation = is_null($skip_validation) ? $this->skip_validation : $skip_validation;

        if ($skip_validation === FALSE) {
            $data = $this->validate($data);
        }

        // Will be false if it didn't validate.
        if ($data !== FALSE) {
            
            $data = $this->trigger('before_update', ['id' => $id, 'method' =>'update', 'fields' => $data] );
            
            $this->db->where($this->primary_key, $id);
            $this->db->set( $this->prep_data($data) );
            $result = $this->db->update($this->table_name);

            $this->trigger('after_update', ['id' => $id, 'fields' => $data, 'result' => $result, 'method' => 'update']);

            return $result;
        } else {
            return FALSE;
        }
    }

    //--------------------------------------------------------------------

    /**
     * Updates multiple records in the database at once.
     *
     * $data = array(
     *     array(
     *         'title'  => 'My title',
     *         'body'   => 'body 1'
     *     ),
     *     array(
     *         'title'  => 'Another Title',
     *         'body'   => 'body 2'
     *     )
     * );
     *
     * The $where_key should be the name of the column to match the record on.
     * If $where_key == 'title', then each record would be matched on that
     * 'title' value of the array. This does mean that the array key needs
     * to be provided with each row's data.
     *
     * @param  array $data An associate array of row data to update.
     * @param  string $where_key The column name to match on.
     * @return bool
     */
    public function update_batch($data, $where_key)
    {
        foreach ($data as &$row) {
            $row = $this->trigger('before_update', ['method' => 'update_batch', 'fields' => $row] );
        }

        $result = $this->db->update_batch($this->table_name, $data, $where_key);

        foreach ($data as &$row) {
            $this->trigger('after_update', ['fields' => $data, 'result' => $result, 'method' => 'update_batch']);
        }

        return $result;
    }

    //--------------------------------------------------------------------

    /**
     * Updates many records by an array of ids.
     *
     * While update_batch() allows modifying multiple, arbitrary rows of data
     * on each row, update_many() sets the same values for each row.
     *
     * $ids = array(1, 2, 3, 5, 12);
     * $data = array(
     *     'deleted_by' => 1
     * );
     *
     * $this->model->update_many($ids, $data);
     *
     * @param  array $ids An array of primary_key values to update.
     * @param  array $data An array of value pairs to modify in each row.
     * @param  array $skip_validation If TRUE, will skip validation of data for this call only.
     * @return bool
     */
    public function update_many($ids, $data, $skip_validation = null)
    {
        if (!is_array($ids) || count($ids) == 0) return NULL;

        $skip_validation = is_null($skip_validation) ? $this->skip_validation : $skip_validation;

        if ($skip_validation === FALSE) {
            $data = $this->validate($data, 'update', $skip_validation);
        }

        $data = $this->trigger('before_update', ['ids' => $ids, 'method' => 'update_many', 'fields' => $data]);

        // Will be false if it didn't validate.
        if ($data !== FALSE) {
            $this->db->where_in($this->primary_key, $ids);
            $this->db->set($data);
            $result = $this->db->update($this->table_name);

            $this->trigger('after_update', ['ids' => $ids, 'fields' => $data, 'result'=>$result, 'method' => 'update_many']);

            return $result;
        } else {
            return FALSE;
        }
    }

    //--------------------------------------------------------------------

    /**
     * Update records in the database using a standard WHERE clause.
     *
     * Your last parameter should be the $data array with values to update
     * on the rows. Any additional parameters should be provided to make up
     * a typical WHERE clause. This could be a single array, or a column name
     * and a value.
     *
     * $data = array('deleted_by' => 1);
     * $wheres = array('user_id' => 15);
     *
     * $this->update_by($wheres, $data);
     * $this->update_by('user_id', 15, $data);
     *
     * @param array $data An array of data pairs to update
     * @param one or more WHERE-acceptable entries.
     * @return bool
     */
    public function update_by()
    {
        $args = func_get_args();
        $data = array_pop($args);
        $this->_set_where($args);

        $data = $this->trigger('before_update', ['method' => 'update_by', 'fields' => $data]);

        // Will be false if it didn't validate.
        if ($this->validate($data) !== FALSE) {
            $this->db->set( $this->prep_data($data) );
            $result = $this->db->update($this->table_name);

            $this->trigger('after_update', ['method' => 'update_by', 'fields' => $data, 'result' => $result] );

            return $result;
        } else {
            return FALSE;
        }
    }

    //--------------------------------------------------------------------

    /**
     * Updates all records and sets the value pairs passed in the array.
     *
     * @param  array $data An array of value pairs with the data to change.
     * @param  array $skip_validation If TRUE, will skip validation of data for this call only.
     * @return bool
     */
    public function update_all($data, $skip_validation = FALSE)
    {
        $data = $this->trigger('before_update', ['method' => 'update_all', 'fields' => $data] );

        $skip_validation = is_null($skip_validation) ? $this->skip_validation : $skip_validation;

        if ($skip_validation === FALSE) {
            $data = $this->validate($data);
        }

        // Will be false if it didn't validate.
        if ($data !== FALSE) {
            $this->db->set( $this->prep_data($data) );
            $result = $this->db->update($this->table_name);

            $this->trigger('after_update', ['method' => 'update_all', 'fields' => $data, 'result' => $result] );

            return $result;
        } else {
            return FALSE;
        }
    }

    //--------------------------------------------------------------------

    /**
     * Increments the value of field for a given row, selected by the
     * primary key for the table.
     *
     * @param $id
     * @param $field
     * @param int $value
     * @return mixed
     */
    public function increment($id, $field, $value=1)
    {
        $value = (int)abs($value);

        $this->db->where($this->primary_key, $id);
        $this->db->set($field, "{$field}+{$value}", false);

        return $this->db->update($this->table_name);
    }

    //--------------------------------------------------------------------

    /**
     * Increments the value of field for a given row, selected by the
     * primary key for the table.
     *
     * @param $id
     * @param $field
     * @param int $value
     * @return mixed
     */
    public function decrement($id, $field, $value=1)
    {
        $value = (int)abs($value);

        $this->db->where($this->primary_key, $id);
        $this->db->set($field, "{$field}-{$value}", false);

        return $this->db->update($this->table_name);
    }

    //--------------------------------------------------------------------

    /**
     * Deletes a row by it's primary key value.
     *
     * @param  mixed $id The primary key value of the row to delete.
     * @return bool
     */
    public function delete($id)
    {
        $this->trigger('before_delete', ['id' => $id, 'method' => 'delete'] );

        $this->db->where($this->primary_key, $id);

        if ($this->soft_deletes) {
            $sets = $this->log_user && is_object($this->authenticate)
                ? array($this->soft_delete_key => 1, $this->deleted_by_field => $this->authenticate->id())
                : array($this->soft_delete_key => 1);

            $result = $this->db->update($this->table_name, $sets);
        } // Hard Delete
        else {
            $result = $this->db->delete($this->table_name);
        }

        $this->trigger('after_delete', ['id' => $id, 'method' => 'delete', 'result' => $result] );

        return $result;
    }

    //--------------------------------------------------------------------

    public function delete_by()
    {
        $where = func_get_args();
        $this->_set_where($where);

        $where = $this->trigger('before_delete', ['method' => 'delete_by', 'fields' => $where]);

        if ($this->soft_deletes) {
            $sets = $this->log_user && is_object($this->authenticate)
                ? array($this->soft_delete_key => 1, $this->deleted_by_field => $this->authenticate->id())
                : array($this->soft_delete_key => 1);

            $result = $this->db->update($this->table_name, $sets);
        } else {
            $result = $this->db->delete($this->table_name);
        }

        $this->trigger('after_delete', ['method' => 'delete_by', 'fields' => $where, 'result' => $result] );

        return $result;
    }

    //--------------------------------------------------------------------

    public function delete_many($ids)
    {
        if (!is_array($ids) || count($ids) == 0) return NULL;

        $ids = $this->trigger('before_delete', ['ids' => $ids, 'method' => 'delete_many'] );

        $this->db->where_in($this->primary_key, $ids);

        if ($this->soft_deletes) {
            $sets = $this->log_user && is_object($this->authenticate)
                ? array($this->soft_delete_key => 1, $this->deleted_by_field => $this->authenticate->id())
                : array($this->soft_delete_key => 1);

            $result = $this->db->update($this->table_name, $sets);
        } else {
            $result = $this->db->delete($this->table_name);
        }

        $this->trigger('after_delete', ['ids' => $ids, 'method' => 'delete_many', 'result' => $result]);

        return $result;
    }

    //--------------------------------------------------------------------

    //--------------------------------------------------------------------
    // Scope Methods
    //--------------------------------------------------------------------

    /**
     * Sets the value of the soft deletes flag.
     *
     * @param  boolean $val If TRUE, should perform a soft delete. If FALSE, a hard delete.
     */
    public function soft_delete($val = TRUE)
    {
        $this->soft_deletes = $val;

        return $this;
    }

    //--------------------------------------------------------------------

    /**
     * Temporarily sets our return type to an array.
     */
    public function as_array()
    {
        $this->temp_return_type = 'array';

        return $this;
    }

    //--------------------------------------------------------------------

    /**
     * Temporarily sets our return type to an object.
     *
     * If $class is provided, the rows will be returned as objects that
     * are instances of that class. $class MUST be an fully qualified
     * class name, meaning that it must include the namespace, if applicable.
     *
     * @param string $class
     * @return $this
     */
    public function as_object($class=null)
    {
        $this->temp_return_type = ! empty($class) ? $class : 'object';

        return $this;
    }

    //--------------------------------------------------------------------

    /**
     * Also fetches deleted items for this request only.
     */
    public function with_deleted()
    {
        $this->temp_with_deleted = TRUE;

        return $this;
    }

    //--------------------------------------------------------------------

    /**
     * Returns whether the current setup will return
     * soft deleted rows.
     *
     * @return bool
     */
    public function get_with_deleted()
    {
        return $this->temp_with_deleted;
    }

    //--------------------------------------------------------------------


    /**
     * Sets the $skip_validation parameter.
     *
     * @param bool $skip
     * @return $this
     */
    public function skip_validation($skip = true)
    {
        $this->skip_validation = $skip;

        return $this;
    }

    //--------------------------------------------------------------------


    //--------------------------------------------------------------------
    // Utility Methods
    //--------------------------------------------------------------------

    /**
     * Counts number of rows modified by an arbitrary WHERE call.
     * @return INT
     */
    public function count_by()
    {
        $where = func_get_args();
        $this->_set_where($where);

        return $this->db->count_all_results($this->table_name);
    }

    //--------------------------------------------------------------------

    /**
     * Counts total number of records, disregarding any previous conditions.
     *
     * @return int
     */
    public function count_all()
    {
        return $this->db->count_all($this->table_name);
    }

    //--------------------------------------------------------------------

    /**
     * Getter for the table name.
     *
     * @return string The name of the table used by this class.
     */
    public function table()
    {
        return $this->table_name;
    }

    //--------------------------------------------------------------------

    /**
     * Set the return_insert_id value.
     *
     * @param  boolean $return If TRUE, insert will return the insert_id.
     */
    public function return_insert_id($return = true)
    {
        $this->return_insert_id = (bool)$return;

        return $this;
    }

    //--------------------------------------------------------------------

    /**
     * A convenience method to return only a single field of the specified row.
     *
     * @param mixed $id The primary_key value to match against.
     * @param string $field The field to search for.
     *
     * @return bool|mixed The value of the field.
     */
    public function get_field($id = NULL, $field = '')
    {
        $this->db->select($field);
        $this->db->where($this->primary_key, $id);
        $query = $this->db->get($this->table_name);

        if ($query && $query->num_rows() > 0) {
            return $query->row()->$field;
        }

        return FALSE;

    }

    //---------------------------------------------------------------

    /**
     * Checks whether a field/value pair exists within the table.
     *
     * @param string $field The field to search for.
     * @param string $value The value to match $field against.
     *
     * @return bool TRUE/FALSE
     */
    public function is_unique($field, $value)
    {
        $this->db->where($field, $value);
        $query = $this->db->get($this->table_name);

        if ($query && $query->num_rows() == 0) {
            return TRUE;
        }

        return FALSE;

    }

    //---------------------------------------------------------------

    /**
     * Adds a field to the protected_attributes array.
     *
     * @param $field
     *
     * @return mixed
     */
    public function protect($field)
    {
        $this->protected_attributes[] = $field;

        return $this;
    }

    //--------------------------------------------------------------------

    /**
     * Get the field names for this model's table.
     *
     * Returns the model's database fields stored in $this->fields
     * if set, else it tries to retrieve the field list from
     * $this->db->list_fields($this->table_name);
     *
     * @return array    Returns the database fields for this model
     */
    public function get_fields()
    {
        if (empty($this->fields)) {
            $this->fields = $this->db->list_fields($this->table_name);
        }

        return $this->fields;
    }

    //--------------------------------------------------------------------

    /**
     * Extracts the model's fields (except the key and those handled by
     * Observers) from the $post_data and returns an array of name => value pairs
     *
     * @param Array $post_data The post data, usually $this->input->post() when called from the controller
     *
     * @return Array    An array of name => value pairs containing the data for the model's fields
     */
    public function prep_data($post_data)
    {
        $data = array();
        $skippedFields = array();

        if (empty($post_data))
        {
            return [];
        }

        // Though the model doesn't support multiple keys well, $this->key
        // could be an array or a string...
        $skippedFields = array_merge($skippedFields, (array)$this->primary_key);

        // Remove any protected attributes
        $skippedFields = array_merge($skippedFields, $this->protected_attributes);

        $fields = $this->get_fields();

        // If the field is the primary key, one of the created/modified/deleted
        // fields, or has not been set in the $post_data, skip it
        foreach ($post_data as $field => $value) {
            if (in_array($field, $skippedFields) ||
                ! in_array($field, $fields))
            {
                continue;
            }

            $data[$field] = $value;
        }

        return $data;
    }

    //--------------------------------------------------------------------

    /**
     * Returns the last query string, if available. Simply a wrapper for
     * CodeIgniter's database method of the same name.
     *
     * @return string
     */
    public function last_query ()
    {
        return $this->db->last_query();
    }

    //--------------------------------------------------------------------

    /**
     * Returns the elapsed time for the last query that was executed, if
     * available, or NULL if not available, like if debug mode is off.
     *
     * @return mixed
     */
    public function last_query_time ()
    {
        $times = $this->db->query_times;

        if (! is_array($this->db->query_times) || ! count($this->db->query_times))
        {
            return null;
        }

        return end($times);
    }

    //--------------------------------------------------------------------

    //--------------------------------------------------------------------
    // Observers
    //--------------------------------------------------------------------

    /**
     * Sets the created on date for the object based on the
     * current date/time and date_format. Will not overwrite existing.
     *
     * @param array $row The array of data to be inserted
     *
     * @return array
     */
    public function created_on($row)
    {
        if (empty($row['fields']))
        {
            return null;
        }

        $row = $row['fields'];

        // Created_on
        if (! array_key_exists($this->created_field, $row))
        {
            $row[$this->created_field] = $this->set_date();
        }

        // Created by
        if ($this->log_user && ! array_key_exists($this->created_by_field, $row) && is_object($this->authenticate))
        {
            // If you're here because of an error with $this->authenticate
            // not being available, it's likely due to you not using
            // the AuthTrait and/or setting log_user after model is instantiated.
            $row[$this->created_by_field] = (int)$this->authenticate->id();
        }

        return $row;
    } // end created_on()

    //--------------------------------------------------------------------

    /**
     * Sets the modified_on date for the object based on the
     * current date/time and date_format. Will not overwrite existing.
     *
     * @param array $row The array of data to be inserted
     *
     * @return array
     */
    public function modified_on($row)
    {
        if (empty($row['fields']))
        {
            return null;
        }

        $row = $row['fields'];

        if (is_array($row) && ! array_key_exists($this->modified_field, $row))
        {
            $row[$this->modified_field] = $this->set_date();
        }

        // Modified by
        if ($this->log_user && ! array_key_exists($this->modified_by_field, $row) && is_object($this->authenticate))
        {
            // If you're here because of an error with $this->authenticate
            // not being available, it's likely due to you not using
            // the AuthTrait and/or setting log_user after model is instantiated.
            $row[$this->modified_by_field] = $this->authenticate->id();
        }

        return $row;
    }

    //--------------------------------------------------------------------

    //--------------------------------------------------------------------
    // Internal Methods
    //--------------------------------------------------------------------

    /**
     * Set WHERE parameters
     */
    protected function _set_where($params)
    {
        if (count($params) == 1) {
            $this->db->where($params[0]);
        } else {
            $this->db->where($params[0], $params[1]);
        }
    }

    //--------------------------------------------------------------------

    /**
     * Triggers a model-specific event and call each of it's observers.
     *
     * @param string $event The name of the event to trigger
     * @param mixed $data The data to be passed to the callback functions.
     *
     * @return mixed
     */
    public function trigger($event, $data = false)
    {
        if (! isset($this->$event) || ! is_array($this->$event))
        {
            if (isset($data['fields']))
            {
                return $data['fields'];
            }

            return $data;
        }

        foreach ($this->$event as $method)
        {
            if (strpos($method, '('))
            {
                preg_match('/([a-zA-Z0-9\_\-]+)(\(([a-zA-Z0-9\_\-\., ]+)\))?/', $method, $matches);
                $this->callback_parameters = explode(',', $matches[3]);
            }

            $data = call_user_func_array(array($this, $method), array($data));
        }

        // In case no method called or method returned
        // the entire data array, we typically just need the $fields
        if (isset($data['fields']))
        {
            return $data['fields'];
        }

        // A few methods might need to return 'ids'
        if (isset($data['ids']))
        {
            return $data['ids'];
        }

        return $data;
    }

    //--------------------------------------------------------------------

    /**
     * Validates the data passed into it based upon the form_validation rules
     * setup in the $this->validate property.
     *
     * If $type == 'insert', any additional rules in the class var $insert_validate_rules
     * for that field will be added to the rules.
     *
     * @param  array $data An array of validation rules
     * @param  string $type Either 'update' or 'insert'.
     * @return array/bool       The original data or FALSE
     */
    public function validate($data, $type = 'update', $skip_validation = null)
    {
        $skip_validation = is_null($skip_validation) ? $this->skip_validation : $skip_validation;

        if ($skip_validation) {
            return $data;
        }

        // We need the database to be loaded up at this point in case
        // we want to use callbacks that hit the database.
        if (empty($this->db))
        {
            $this->load->database();
        }

        if (!empty($this->validation_rules)) {
            $this->form_validation->set_data($data);

            if (is_array($this->validation_rules)) {
                // Any insert additions?
                if ($type == 'insert'
                    && !empty($this->insert_validate_rules)
                    && is_array($this->insert_validate_rules)
                ) {
                    foreach ($this->validation_rules as &$row) {
                        if (isset($this->insert_validate_rules[$row['field']])) {
                            $row ['rules'] .= '|' . $this->insert_validate_rules[$row['field']];
                        }
                    }
                }

                $this->form_validation->set_rules($this->validation_rules);

                if ($this->form_validation->run('', $this) === TRUE) {
                    return $data;
                } else {
                    return FALSE;
                }
            } else {
                if ($this->form_validation->run($this->validate, $this) === TRUE) {
                    return $data;
                } else {
                    return FALSE;
                }
            }
        } else {
            return $data;
        }
    }

    //--------------------------------------------------------------------

    /**
     * Protect attributes by removing them from $row array. Useful for
     * removing id, or submit buttons names if you simply throw your $_POST
     * array at your model. :)
     *
     * @param object /array $row The value pair item to remove.
     */
    public function protect_attributes($row)
    {
        foreach ($this->protected_attributes as $attr) {
            if (is_object($row)) {
                unset($row->$attr);
            } else {
                unset($row[$attr]);
            }
        }

        return $row;
    }

    //--------------------------------------------------------------------

    /**
     * A utility function to allow child models to use the type of
     * date/time format that they prefer. This is primarily used for
     * setting created_on and modified_on values, but can be used by
     * inheriting classes.
     *
     * The available time formats are:
     * * 'int'      - Stores the date as an integer timestamp.
     * * 'datetime' - Stores the date and time in the SQL datetime format.
     * * 'date'     - Stores teh date (only) in the SQL date format.
     *
     * @param mixed $user_date An optional PHP timestamp to be converted.
     *
     * @access protected
     *
     * @return int|null|string The current/user time converted to the proper format.
     */
    protected function set_date($user_date = NULL)
    {
        $curr_date = !empty($user_date) ? $user_date : time();

        switch ($this->date_format) {
            case 'int':
                return $curr_date;
                break;
            case 'datetime':
                return date('Y-m-d H:i:s', $curr_date);
                break;
            case 'date':
                return date('Y-m-d', $curr_date);
                break;
        }

    }//end set_date()

    //--------------------------------------------------------------------

    /**
     * Returns an array containing the 'code' and 'message' of the
     * database's error, as provided by CI's database drivers.
     *
     * @return mixed
     */
    public function error($db_array_only=false)
    {
        // Send any validation errors if we have any.
        if (function_exists('validation_errors') && validation_errors() && ! $db_array_only)
        {
            return validation_errors();
        }

        // No validation errors? Return the db error.
        $error = $this->db->error();

        if ($db_array_only)
        {
            return $error;
        }

        if (! empty($error['code']))
        {
            return "Database Error {$error['code']}: {$error['message']}.";
        }

        // No errors found.
        return '';
    }

    //--------------------------------------------------------------------

    //--------------------------------------------------------------------
    // Magic Methods
    //--------------------------------------------------------------------

    /**
     * __get magic
     *
     * Allows models to access CI's loaded classes using the same
     * syntax as controllers.
     *
     * This is the same as what CI's model uses, but we keep it
     * here since that's the ONLY thing that CI's model does.
     *
     * @param    string $key
     */
    public function __get($key)
    {
        // Give them first crack at any protected class vars
        if (isset($this->$key))
        {
            return $this->$key;
        }

        // Debugging note:
        //	If you're here because you're getting an error message
        //	saying 'Undefined Property: system/core/Model.php', it's
        //	most likely a typo in your model code.
        return get_instance()->$key;
    }

    //--------------------------------------------------------------------

    /**
     * Provide direct access to any of CodeIgniter's DB methods but
     * make it look like it's part of the class, purely for convenience.
     *
     * @param $name
     * @param $params
     */
    public function __call($name, $params)
    {
        if (method_exists($this->db, $name))
        {
            call_user_func_array([$this->db, $name], $params);
            return $this;
        }
    }

    //--------------------------------------------------------------------


}
