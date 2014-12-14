# Bundled Generators
This is a list of the provided generators with short descriptions of what they do. More detailed information about each one is available from the CLI with the `sprint forge {command} -h` command.

## Model
Provides a skeleton model file that extends `Myth\Models\CIDbModel`. 

When called without a model name, it will ask for the table name and the most common options from you.

	$ php sprint forge model

When called with a model name in the CLI it will assume typical defaults: 

- pluralising the model name (less 'model') for the table name, 
- 'id' for the primary key
- will track created_on and modified_on dates
- 'datetime' format
- will NOT use soft deletes
- will NOT log user activity.

```
$ php sprint forge model temp_model
```

Not matter how you call it, if a table exists with that name in the database already, it will analyse the table and create very basic validation rules for you. 

You will want to customize to match your project's needs. 

## Controller
Creates a simple controller that extends `Myth\Controllers\BaseController` and provides the basic options as well as empty methods for standard CRUD options. Can be called with or without a controller name on the CLI. 
	
	$ php sprint forge controller

This command has very few options that are asked of you, just a model name to autoload, and whether you want to use templating in this controller. If you provide a model name, it assumes that you want to use templating for standard CRUD operations and fills in the CRUD methods with code to handle basic display, creation, etc. This does NOT create the views needed for display, it simply fills in the controller methods.

Note that you can pass the option `-create_views` to the command and it will create your view files for the standard CRUD options. These views are built based on your current [UIKit](general/uikits) settings.

## View
Creates a simple view in `APPPATH .'views'` that contains a single string as a placholder. 

	$php sprint forge view test
	// Creates: APPPATH/views/test.php

You can pass in subfolders with the name and they will be created automatically.

	$ php sprint forge view tests/test
	// Creates: APPPATH/views/tests/test.php

## Seed
Creates an empty [Seeder](database/seeding) ready for your use.

	$ php sprint forge seed user
	// Creates APPPATH/database/seeds/UserSeeder.php

## Migration
Creates a new migration file based on the `name` passed to it. 

	$ php sprint forge migration {name}

Will scan the name to attempt to determine the type of action that it should perform and will build basic methods for you to fine-tune. Some common examples are: 

	create_user_table               // Creates a new table called 'user'
	make_role_table                 // Creates a new table called 'role'
	add_name_column_to_log_table    // Adds a new column called 'name' to the 'log' table
	insert_age_column_log_table     // Adds a new column called 'age' to the 'log' table
	remove_age_column_from_log_table // Removes the 'age' column from the 'log' table
	drop_age_column_log_table       // Removes the 'age' column from the 'log' table
	delete_age_column_log_table     // Removes the 'age' column from the 'log' table

### Fields
You can pass in a list of fields that it should create for you whenever you're making a new table. The fields list is a simple string with `name:type:length` descriptions that define them. 

	$ php sprint forge migration create_user_table -fields "email:string name:varchar:30 age:int:3"
	
The first segment of the field is the name of the column. The second segment is the type of column, like int, bigint, char, datetime, etc. The third, optional, segment is the length of the field.

There are a couple of special "types" that you can use with your fields. 

- `id` - When present, will add that field as a primary key, unsigned and auto_incrementing. You would still need to provide another triplet creating the field for it to work: "uuid:int:9 uuid:id"
- `string` - Will be converted to a `varchar(255)` but is provided as a convenience.
- `number` - another convenience item that is converted to `int(9)`.

### From Existing Table
If you have already created your table, you can easily create a basic migration from it by providing the option `-fromdb`. It will autodetect the name of the table in the migration name and fill out the $dbforge commands to make it work. It cannot detect every nuance of your database table, though, so you should still verify the file. In particular it does not detect foreign keys or other indexes.

	$ php sprint forge migration create_users_table -fromdb

## Scaffold 
This combines all of the other migrations into a single command. It can be used to quickly generate the basic code for an entire module or build a new resource's UI. 

The minimum amount of information needed is the name of the resource. 

	$ php sprint forge scaffold user

This will take the following actions: 

- Create `migration` for the 'users' table. Since no -fields have been passed in, it checks for an existing table to grab the information from. If none exists, will create a very generic set of fields consisting of `id, name, created_on, and modified_on`. 
- Create the `model`.
- Create the `controller` that extends from ThemedController along with all of the code and views for working CRUD.
- Creates an empty `Seeder`


