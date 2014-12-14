# Sprint Models

Keeping with the MVC spirit, Sprint uses Models to allow you interact with your database in a simple, consistent manner. By using the **CIDbModel** as the base class for all of your models, you can very quickly setup a simple model capable of finding records, creating new and editing existing records, deleting records, checking if a key/value is unique in this table, counting the results, and more.

CIDbModel acts as a middleman layer between your models and CodeIgniter's standard Model class, working hand-in-hand with ActiveRecord query builder. If you don't need any special queries, you can have a working model in just a handful of lines.


## A Skeleton Model

To get started with a new model, you can use the following skeleton file:


    class X_model extends \Myth\Models\CIDbModel
	{
        protected $table_name	= '';
        protected $key			= 'id';
        protected $date_format	= 'datetime';
        protected $log_user		= FALSE;

        protected $set_created	= TRUE;
        protected $created_field	= 'created_on';
        protected $created_by_field	= 'created_by';

        protected $set_modified		= FALSE;
        protected $modified_field	= 'modified_on';
        protected $modified_by_field = 'modified_by';

		protected $soft_deletes	= FALSE;
        protected $soft_delete_key    = 'deleted';
        protected $deleted_by_field = 'deleted_by';

        // Observers
        protected $before_insert    = array();
        protected $after_insert     = array();
        protected $before_update    = array();
        protected $after_update     = array();
        protected $before_find      = array();
        protected $after_find       = array();
        protected $before_delete    = array();
        protected $after_delete     = array();

        protected $return_insert_id = true;
        protected $return_type      = 'object';
        protected $protected_attributes = array();

        protected $validation_rules         = array();
        protected $insert_validation_rules  = array();
        protected $skip_validation          = false;
        protected $empty_validation_rules   = array();
        
        protected $fields = array();
    }


This is the bare minimum needed to take advantage of CIDbModel's built-in functions. All variables shown here are set to their default, so you don't need to show them if you are using the default values.  Model_name is the name of your class and follows the same rules as [CodeIgniter models](http://codeigniter.com/user_guide/general/models.html).

CIDbModel supports quite a few ways to customize how your class works with the database.

You can easily create a new skeleton model by using the [Forge CLI command](https://github.com/ci-bonfire/Sprint/blob/develop/myth/_docs_src/forge/generators.md).

### $table_name

The var `$table_name` should be set to the name of the table in your database. If you database is set to use a prefix (Sprint defaults to a `bf_` prefix), you should leave the prefix off. So a table named `bf_users` should be entered as `users`.


### $key

The var `$key` should be the name of the primary key for your table. CIDbModel requires that your table has primary key. If it doesn't you should extend Model and will need to write your own methods to interface with the database. The `$key` is expected to be linked to an INT field.


### $soft_deletes

Sprint uses the concept of *soft deletes* that will set a flag that an item has been deleted instead of actually deleting the item. This allows you to later restore the user in case the deletion was accidental, or to keep a permanent record of any sensitive information, like transaction records.

To use soft_deletes, your table must have a `deleted` field that is a **TINYINT (1)**. A value of `0` means the record has not been deleted, while a value of `1` shows that the item has been deleted.
The name of the `deleted` field may be modified by setting `$deleted_field`.

If `$soft_deletes == TRUE`, Sprint will automatically update the record to set `deleted` to a value of `1`.

If `$soft_deletes == FALSE`, the record will be permanently deleted from the database.


### $date_format

Determines the type of field that is used to store created and modified dates. The possible values are:

- ‘int’ - A Unix integer timestamp. (This is the default)
- ‘datetime’ Is a MySQL Datetime field. ( YYYY-MM-DD HH:MM:SS )
- ‘date’ is a MySQL Date field. ( YYYY-MM-DD )

While ‘int’ seems to be one of the most common amongst PHP developers, datetime should be at least considered since it makes inspecting your data within the database much easier to interpret, though it does take a little bit more work during the script execution.


### $set_created

Sprint can automatically set your created on dates and times for you, in the format specified through `$date_format`. To use this, your table must have a `created_on` field of the proper type.

If `$set_created == TRUE`, Sprint will set the `created_on` field value for you at the time of an `insert()` call.
The name of the `created_on` field may be modified by setting `$created_field`.


### $set_modified

Sprint can automatically set your modified on dates and times for you, in the format specified through `$date_format`. To use this, your table must have a `modified_on` field of the proper type.
The name of the `modified_on` field may be modified by setting `$modified_field`.

If `$set_created == TRUE`, Sprint will set the `created_on` field value for you at the time of an `insert()` call.

### $created_field
### $modified_field

`created_field` and `modified_field` specify the name of the field that the time is inserted into. Defaults to *created_on* and *modified_on*.


### $log_user

`log_user` provides a way to keep a small activity trail of actions related to each record.  When TRUE, it will populate a field in the record with the user id. This applies to the `insert`, `update` and `deleted` commands, and their related methods, like `update_by`.

The name of the fields to store the user id in can be set by changing the `created_by_field`, `modified_by_field` and `deleted_by_field` values. They default to `created_by`, `modified_by` and `deleted_by`, respectively.

### $deleted_field` & `$deleted_by_field

`deleted_field` and `deleted_by_field` specify the name of the fields used to determine whether a row has been deleted (when `$soft_deletes` == true) and the user which deleted the row (when `$log_user` == true).

### $escape

When FALSE, the `select()` method will not try to protect your field names with backticks. This is useful if you need a compound statement.


### $db_con

Holds the database connection details for this model only. Can be either a string or an array as per the [CodeIgniter manual](http://codeigniter.com/user_guide/database/connecting.html). This is useful if you have a single model that needs to use a database connection different than the rest, like a logging class.

### $return_type

Specifies whether the model returns records as an object or an array. The only valid values here are `object` or `array`.

The format can be overridden on a per-call basis using the `as_array` and `as_object` methods.

    $user = $this->user_model->as_array()->find($id);

### $protected_attributes

This is simply a list of keys that will always be removed from the data arrays passed to the insert, update, and similar methods. This is convenient if you like to throw your $_POST arrays directly at the model, but don't want the 'submit' inputs being saved, or for always removing the 'id' if it's passed in.

    protected $protected_attributes = array( 'submit', 'id' );

### $fields
The fields array contains a list of all fields within the database table. For performance reasons, this should be filled out, otherwise the first insert or update call will hit the database to retrieve the field names. This is used by the `prep_data()` to ensure that any insert or update calls only contain existing fields, saving your from errant database errors. 

This is primarily in place to allow you to provide more data than you need within the table to the model, and have the extra data processed by one of the observers.

	protected $fields = array('id', 'first_name', 'last_name');

## Provided Methods

By using the skeleton file, you get a number of methods ready to use on your model, in addition to all of the standard CodeIgniter Query Builder methods. All of these methods can be overriden in your own model if you need to customize them by joining other tables, processing the results before handing off to the controller, etc.


    $user = $this->user_model->select(‘id, username, email’)
                             ->where(‘deleted’, 1)
                             ->limit(10,0)
                             ->find_all();


If you need to do additional processing, join tables, etc than you can do that in your model using CodeIgniter’s built-in Query Builder commands.


    class User_model extends CIDbModel {
        public function find_all()
        {
            $this->db->join(...);
            return parent::find_all();
        }
    }

## Selecting Data

### first()
A convenience method that returns the first row found that matches the criteria that you've set.

	$user = $this->user_model->where('banned', false)
													 ->order_by('created_on', 'desc')
													 ->first();

### find()

The `find()` method is used to locate a single record based on it's `id`.


    $user = $this->user_model->find($id);

    echo $user->username;


Returns an object with the results if found, or `FALSE` if not found.


### find_by()

A convenience method that combines the `where()` and `find()` methods. Expects to return a single result, so you should search on a field that will have unique values.


    $this->user_model->find_by('email', 'darth@theempire.com');


This method can also be called with only a single associative array as the first parameter. This allows you set multiple criteria to search by.


    $user = $this->user_model->find_by( array('email'=>'darth@theempire.com', 'deleted'=>0) );

    # SQL: SELECT * FROM `bf_users` WHERE email='darth@theempire.com' AND deleted='0'


This defaults to combining all criteria as "AND" but can be modified by passing the the type into the third parameter:


    $user = $this->user_model->find_by( array('email'=>'darth@theempire.com', 'deleted'=>0), null, 'OR' );

    # SQL: SELECT * FROM `bf_users` WHERE email='darth@theempire.com' OR deleted='0'

### find_many()
Locates all records in the table that have primary keys matching the keys sent in the first parameter.  The first paremeter is an array of `ids`. 

 	$ids = array(1, 2, 15, 38);
	$this->user_model->find_many($ids);

### find_all()

Locates all records in the table.

    $this->user_model->find_all();


If you need to modify the search criteria you can use any of the chainable methods.


    $users = $this->user_model->where('deleted', 1)
                              ->limit(25)
                              ->find_all();

    foreach ($users as $user)
    {
        echo $user->username;
    }


Returns an array of objects where each object holds the results of a single record.


### find_many_by()
Locates all records matching certain criteria. This is a convenience method for using a `where()` and a `find_all()` in one command.

    $this->user_model->find_many_by('deleted', 1);

Any of the standard options available to a CodeIgniter `where()` method may be used here.

    $this->user_model->find_all_by('deleted', 1);
    $this->user_model->find_all_by('deleted !=', 0);
    $this->user_model->find_all_by( array('email'=>'darth@theempire.com', 'deleted'=>0) );


Returns an array of objects where each object holds the results of a single record.

## Inserting Data

### insert()
Creates a new record. Will set the `created_on` field if the model is setup to allow that. The first parameter should be an associative array of field/values to insert.


    $user = array(
        'email'     => 'dart@theempire.com',
        'username'  => 'darth.vader'
    );
    $this->user_model->insert($user);

    # SQL: INSERT INTO `bf_users` (email, username, created_on) VALUES ('darth@theempire.com', 'darth.vader', 1321645674);

Returns an INT ID of the new record on success, or `FALSE` on failure.


### insert_batch()
Allows for inserting more than one record at a time. Works just like CodeIgniter’s stock method, but handles setting the table name for you.


    $data = array(
       array(
          'title' => 'My title' ,
          'name' => 'My Name' ,
          'date' => 'My date'
       ),
       array(
          'title' => 'Another title' ,
          'name' => 'Another Name' ,
          'date' => 'Another date'
       )
    );

    $this->db->insert_batch('mytable', $data);

### replace()
Performs the SQL standard for a combined DELETE + INSERT, using primary and unique keys to determine which rows to replace.

See CI's documentation for the replcae method. This is simply a wrapper to allow our validation and triggers to work with the method.

## Updating Data

### update()
Updates an existing record in the database by ID. Will set the correct time for the `modified_on` field, if the model requires it.

    $user = array(
        'email'     => 'dart@theempire.com',
        'username'  => 'darth.vader'
    );
    $this->user_model->update($user_id, $user);

    # SQL: UPDATE `bf_users` SET email='darth@theempire.com', username='darth.vader', modified_on=1321645674 WHERE id=1;


Returns a boolean `TRUE/FALSE` on success/failure.


### update_by()

Updates a single record in the database by a standard where clause. Will set the correct time for the `modified_on` field, if the model requires it.

Your last parameter should be the $data array with values to update on the rows. Any additional parameters should be provided to make up a typical WHERE clause. This could be a single array, or a column name and a value.

	$data = array('deleted_by' => 1);
	$wheres = array('user_id' => 15);
	$this->model->update_by($wheres, $data);
	# SQL: UPDATE `bf_users` SET deleted_by=1, modified_on=1321645674 WHERE user_id=15;

    $user = array(
        'email'     => 'dart@theempire.com',
        'username'  => 'darth.vader'
    );
    $this->user_model->update_by('is_father', 1, $user);

    # SQL: UPDATE `bf_users` SET email='darth@theempire.com', username='darth.vader', modified_on=1321645674 WHERE is_father=1;



### update_batch()

Updates multiple records with a single method call.


	$data = array(
    	 array(
    	 	'title' => 'My title' ,
			'name' => 'My Name 2' ,
			'date' => 'My date 2'
	     ),
    	array(
        	'title' => 'Another title' ,
	        'name' => 'Another Name 2' ,
    	    'date' => 'Another date 2'
	     )
	  );

	$this->model->update_batch($data, 'title');

The first parameter is an array of values. The second parameter is the where key.

### update_many()
Updates a number of rows with primary keys that match the array values passed into the first parameter. The second parameter is an array with the column/value pairs to update. 

	$ids = array(1, 2, 3, 5, 12);
	$data = array( 'deleted_by' => 1);
	$this->model->update_many($ids, $data);
	
You can skip validation for this call only by passing in TRUE as the third parameter.

### increment()
Increments the value of a single row's column.  The column must be an integer-based column. The first paremeter is row's primary_key. The second parameter is the column name. The third parameter is the amount to increment the value by. By default, it will increment it 1. 

	$this->model->increment($id, 'hits', 5);
	# SQL: UPDATE `page_views` SET hits=hits+5 WHERE id=$id;

### decrement()
decrements the value of a single row's column.  The column must be an integer-based column. The first paremeter is row's primary_key. The second parameter is the column name. The third parameter is the amount to decrement the value by. By default, it will decrement it by 1. 

	$this->model->decrement($id, 'hits', 5);
	# SQL: UPDATE `page_views` SET hits=hits-5 WHERE id=$id;

## Deleting Data

### delete()

Deletes a single record from the database. If `$soft_deletes` are on, then will just set the `deleted` field to `1`. Otherwise, will permanently delete the record from the database.

	$this->user_model->delete($user_id);

    # SQL w/ soft deletes: UPDATE bf_users SET deleted=1 WHERE id=$user_id;
    # SQL w/out soft deletes: DELETE FROM bf_users WHERE id=$user_id;


Returns a boolean `TRUE/FALSE` on success/failure.

###  delete_by()

Deletes one or more records that match certain requirements. If `$soft_deletes == true`, will set the `deleted` field to 1, otherwise will delete the record permenantly.

The first parameter accepts an array of key/value pairs to form the ‘where’ portion of the query.


    $wheres = array(
        ‘active’    => 0,
        ‘last_login’ => ‘< ‘. time()
    );
    $this->model->delete_by($wheres);

### delete_many()
Deletes all rows that have primary key values contained within the array passed into the first parameter.

	$ids = array(1, 2, 3, 5, 12);
	$this->model->delete_many($ids);
	
	#SQL with soft_deletes: UPDATE bf_users SET deleted=1 WHERE id IN ($ids);
	# SQL w/out soft_deletes: DELETE FROM bf_users WHERE id IN ($ids);

## Utility Methods

### is_unique()

Checks to see if a given field/value combination would be unique in the table.

    $this->user_model->is_unique('email', 'darth@theempire.com');


### count_all()

Counts all records in the table.

    $this->user_model->count_all();


Returns an INT containing the number of results, or FALSE.


### count_by()

Counts the number of elements that match the field/value pair.

    $this->user_model->count_by('delete', 1);


Returns an INT containing the number of results, or FALSE.



### get_field()

A convenience method to return only a single field of the specified row. The first parameter is the ID of the row to search in. The second parameter is the column to return the value of.

    $this->user_model->get_field($user_id, 'email');


Returns the value of the row's field, or FALSE.

### prep_data()

Intended to be called by a controller and/or extended in the model, `prep_data` processes an array of field/value pairs (can be the result of `$this->input->post()`) and attempts to setup a `$data` array suitable for use in the model's `insert`/`update` methods. The output array will not include the model's `key`, `created_on`, `created_by`, `modified_on`, `modified_by`, `deleted`, or `deleted_by` fields, or fields indicated as the primary key in the model's `field_info` array.

For example, the user_model extends prep_data to map field names from the view that don't match the tables in the database and ensure fields that should not be set are not set:


    public function prep_data($post_data)
    {
        $data = parent::prep_data($post_data);

        if ( ! empty($post_data['timezones'])) {
            $data['timezone'] = $post_data['timezones'];
        }
        if ( ! empty($post_data['password'])) {
            $data['password'] = $post_data['password'];
        }
        if ($data['display_name'] === '') {
            unset($data['display_name']);
        }
        if (isset($post_data['restore']) && $post_data['restore']) {
            $data['deleted'] = 0;
        }
        if (isset($post_data['unban']) && $post_data['unban']) {
            $data['banned'] = 0;
        }
		if (isset($post_data['activate']) && $post_data['activate']) {
			$data['active'] = 1;
		} elseif (isset($post_data['deactivate']) && $post_data['deactivate']) {
			$data['active'] = 0;
		}

        return $data;
    }


The User Settings controller then uses the model's `prep_data` method to process the post data before inserting/updating the user:


	private function save_user($type='insert', $id=0, $meta_fields=array(), $cur_role_name = '')
	{
        /* ... Omitting validation setup and gathering of user_meta data ... */

		// Compile our core user elements to save.
        $data = $this->user_model->prep_data($this->input->post());

		if ($type == 'insert') {
			$activation_method = $this->settings_lib->item('auth.user_activation_method');

			// No activation method
			if ($activation_method == 0) {
				// Activate the user automatically
				$data['active'] = 1;
			}

			$return = $this->user_model->insert($data);
			$id = $return;
		} else {	// Update
			$return = $this->user_model->update($id, $data);
		}

        /* ... Omitting saving user_meta data and event trigger ... */

		return $return;

	}//end save_user()

### last_query()
Returns the last query the database executed. Note that this is not specific to this model, but to the database driver in general, as it simply provides a convenient way to tap into CodeIgniter's database method of the same name.

	$this->model->last_query();

### last_query_time()
Returns the elapsed time for the last query. Simply provides a method to tap into CodeIgniter's stats. 

	$time = $this->user_model->last_query_time();
	// Returns 0.0034525

## Return Types

You can temporarily override the type of records returned by the model by using the folliwing commands. This allows you to use objects as a default since they consume less memory, but ask for the results as an array for a single method that you need the extra flexibilty arrays provide.

### as_array()

A chainable method that specifies the model should return the results as an array (for single results) or an array of arrays (for multiple rows). This overrides the models `$result_type` class variable.

### as_object()

A chainable method that specifies the model should return the results as an object (for single results) or an array of objects (for multiple rows). This overrides the models `$result_type` class variable.

### as_json()

A chainable method that specifies the model should return the results as a JSON object suitable for returning in AJAX methods. This overrides the models `$result_type` class variable.


## Chainable Methods

Thanks to CodeIgniter's [ActiveRecord](http://ellislab.com/codeigniter/user-guide/database/active_record.html) library, it is very simply to modify the CIDbModel's methods. This can be done through either chainable methods or by extending methods.

Chainable methods are a feature of PHP 5 and higher that allow you to return the results of one function into another, and to keep this 'chain' of events continuing through several functions. Sprint duplicates several of the stock ActiveRecord methods in CIDbModel to make it simple and elegant to customize your queries.

Sprint's model supports chaining for most of the ActiveRecord methods available, including:

* select
* select_max
* select_min
* select_avg
* select_sum
* distinct
* from
* join
* where
* or_where
* where_in
* or_where_in
* where_not_in
* or_where_not_in
* like
* not_like
* or_like
* or_not_like
* group_by
* having
* or_having
* order_by
* limit
* offset
* set
* count_all_results
* group_start
* or_group_start
* not_group_start
* or_not_group_start
* group_end
* get_compiled_select
* get_compiled_insert
* get_compiled_update
* get_compiled_delete

All of these methods accept the same parameters as their [CodeIgniter](http://ellislab.com/codeigniter/user-guide/database/active_record.html) counterparts. These are included for the sole reason of making your syntax more expressive. You can now do things like:

    $this->user_model->where('city', 'Detroit')
                     ->or_where('city', 'Cleveland')
                     ->join('tour_dates', 'x on y')
                     ->find_all();



### where()

Modifies the query to a specific `where` condition. Can be used with any of the read-type queries (find, find_all, etc).

The first parameter is the field to match against. The second parameter is the value of the field to find.

Accepts any of the standard CodeIgniter ActiveRecord where statements.


    $this->user_model->where('email', 'darth@theempire.com');
    $this->user_model->where('email !=', 'darth@theempire.com');
    $this->user_model->where( array('email' => 'darth@theempire.com') );

    $this->user_model->where('email', 'darth@theempire.com')
                     ->find_all();


You can also pass an array of field/value pairs as the first parameter. In this case, the second parameter is ignored.


    $wheres = array(
        ‘active’        => 1,
        ‘deleted’   => 0
    );
    $results = $this->model->where($wheres)->find_all();



## Extending Methods

While it is possible to modify the query via the chainable methods any time you need results in your controller, it is highly recommended to extend the model's methods to bring you the results you need. This keeps all of your changes to queries in a single place.

Sometimes, you might want to do some additional processing to the database results before passing it on to the controller. This is another perfect example of when to extend the model's method.

To extend an existing method, you simply create a new method in your model that accepts the same parameters as the original CIDbModel method.


    // Extend the existing functionality.
    public function find($id=null)
    {
        $result = parent::find($id);

        if ($result)
        {
            $result->display_name = $this->format_name($result);
        }

        return $result;
    }




## Modify Query in Controller

You can modify a query in your model for a single use by using CodeIgniter's ActiveRecord commands in your controllers. Since CIDbModel uses the ActiveRecord commands, the changes in your controller will affect the results of the next query in your model.


    // In your controller.
    $this->db->join('other_table', 'link_field = users.id', 'left');
    $user = $this->user_model->find($user_id);



## Observers

Observers provide a simple and convenient method for your models to change portions of the data at certain execution points within a model’s interaction. This can be very handy for adding in the created_on time before inserting a record, or deleting related records in other tables whenever a user is deleted.

The following events can be observed by your class:

- before_insert
- after_insert
- before_update
- after_update
- before_find
- after_find
- before_delete
- after_delete
- empty_validation_rules

These are each arrays that should have the name of the methods to call, in order of priority as the array’s elements.


    protected $before_insert = array(‘set_created_on’, ‘another_callback’);


To observe an event and have your methods called you simply add the method name to the definition array and create a new function. The first parameter will be an array of data passed from the calling method. It will contain the following variables: 

* **id** Will be present if an `insert` type method. Will be the primary key of the row that was just inserted. 
* **method** Will be the name of the method that called. Like `insert`, `update`, `update_batch`, etc.
* **fields** The data provided by the method. For inserts and updates it is the data that was just inserted/updated. When using the insert/update_batch methods, it will be an array of all of the row data that you can loop over. 


    protected function set_created_on($data)
    {
    	if ($data['method'] == 'insert_batch' || $data['method'] ==  'update_batch')
    	{
    		return $data;
    	}
    
        if (!array_key_exists($this->created_field, $data['fields']))
        {
            $row[$this->created_field] = $this->set_date();
        }

        return $row;
    }


## Validating Data

The model should contain all of the validation rules for your data so that it is always kept in a single place with the model that represents it. Sprint's models provide a simple way to automatically have your data validated during inserts and updates.

### Basic Validation

The `$validation_rules` variable can take an array of data that follows the same format as CodeIgniter's [Form Validation Library](http://ellislab.com/codeigniter/user-guide/libraries/form_validation.html#validationrulesasarray).

    protected $validation_rules = array(
        array(
            'field' => 'username',
            'label' => 'Username',
            'rules' => 'trim|strip_tags|min_length[4]|xss_clean'
        ),
        array(
            'field' => 'password',
            'label' => 'lang:bf_password',
            'rules' => 'trim|min_length[8]'
        )
    );


Note: the value of the `label` can be retrieved from a language file by prefixing the name of the entry in the language file with `lang:`, as in the example for the password field, above.

During an insert or update, the data passed in is automatically validated using the form_validaiton library. If the validation doesn't pass successfully, the insert/update method will return a value of FALSE and the form_validation_ library will function as expected, providing errors through `validation_errors` and `form_error`.

### Insert Rules Customization

Often, you will have certain rules that are slightly different during object creation than you will during an update. Frequently, this is as simple as having a field required during inserts, but not during updates. You can handle this by adding any additional rules for inserts in the `$insert_validation_rules` class variable.

    protected $insert_validation_rules = array(
       'password'   => 'required|matches[pass_confirm]'
    );

Unlike, the $validation_rules array, the $insert_validation_rules array consists of the field name as the key, and the additional rules as the value. Theses rules are added at the end of the normal rules string before being passed to the form_validation library.

### Skipping Validation

If you need to turn off validation for any reason (like performance durin a large CSV import) you can use the `skip_validation()` method, passing either TRUE or FALSE to the skip or not skip the validation process. This stays in effect as long as the model is loaded but will reset the next time the model is loaded in memory. Typically the next page request.

    $this->user_model->skip_validation(true);

    $this->user_model->skip_validation(true)->insert($data);

### Traditional validation using the Model's validation rules

If you wish to perform validation in the Controller (or another Model), you can retrieve the validation rules from the Model using the `get_validation_rules()` method, passing either 'update' or 'insert' to determine whether the `$insert_validation_rules` are added (you will probably want to disable the model's validation when calling the `insert()`/`update()` methods using the `skip_validation()` method or the model's `skip_validation` property). The rules may then be passed to CI's Form Validation library to perform validation:

    $this->form_validation->set_rules($this->example_model->get_validation_rules('update'));

    if ($this->form_validation->run() === false) {
        return false;
    }

### Generating validation rules

If you want to generate the validation rules in code (rather than supplying a hard-coded array), you can supply the name of a function to the `$empty_validation_rules` observer to generate the validation rules. The function will receive an array of the current validation rules (usually empty or a non-array value, but if multiple functions are used with the observer, it may be a valid array), and is expected to return an array of validation rules.

For instance, you could create a function that uses $this->db->field_data($this->table_name) to retrieve the field information directly from the database, then iterate through the results to create validation rules for each field based on the information returned by the database.

Because it is faster to use the array, the observer will not be called if the array has been set (and the array generated by the observer when it is called will be assigned to the array to prevent the current instance of the model from attempting to generate the rules again).
