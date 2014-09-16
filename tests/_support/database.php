<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

/*
	Fake Query Builder
 */
class MY_DB {
	/**
     * CI_DB
     */
    public function get() {}
    public function get_compiled_select() {}
    public function get_where() {}
    public function select() {}
    public function select_max() {}
    public function select_min() {}
    public function select_avg() {}
    public function select_sum() {}
    public function from() {}
    public function join() {}
    public function where() {}
    public function or_where() {}
    public function where_in() {}
    public function or_where_in() {}
    public function where_not_in() {}
    public function or_where_not_in() {}
    public function like() {}
    public function or_like() {}
    public function not_like() {}
    public function or_not_like() {}
    public function group_by() {}
    public function distinct() {}
    public function having() {}
    public function or_having() {}
    public function order_by() {}
    public function limit() {}
    public function count_all_results() {}
    public function count_all() {}

    public function group_start() {}
    public function or_group_start() {}
    public function not_group_start() {}
    public function or_not_group_start() {}
    public function group_end() {}

    public function insert() {}
    public function get_compiled_insert() {}
    public function insert_batch() {}
    public function replace() {}
    public function set() {}

    public function update() {}
    public function update_batch() {}
    public function get_compiled_update() {}

    public function delete() {}
    public function empty_table() {}
    public function truncate() {}
    public function get_compiled_delete() {}

    public function start_cache() {}
    public function stop_cache() {}
    public function flush_cache() {}
    public function reset_query() {}

    public function query() {}
    public function simple_query() {}
    public function escape() {}
    public function escape_str() {}
    public function escape_like_str() {}
    public function protect_identifiers() {}
    public function dbprefix() {}
    public function set_dbprefix() {}
    public function error() {}

    public function insert_id() { }
    public function affected_rows() { }
    public function platform() { }
    public function version() { }
    public function last_query() { }
    public function insert_string() { }
    public function update_string() { }

    public function list_tables() { }
    public function table_exists() { }
    public function list_fields() { }
    public function field_exists() { }
    public function field_data() { }
    public function call_function() { }

    /**
     * CI_DB_Result
     */
    public function result() { }
    public function result_array() { }
    public function row() { }
    public function row_array() { }
    public function unbuffered_row() { }
    public function num_rows() { }
    public function num_fields() { }
    public function data_seek() { }

}

/*
	Fake CI_Model for testing our models with...
 */
if (! class_exists('CI_Model'))
{
    class CI_Model {

    	public $db;

    	public function __construct() {	}

    }
}