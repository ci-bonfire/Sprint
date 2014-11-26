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

## View
Creates a simple view in `APPPATH .'views'` that contains a single string as a placholder. 

	$php sprint forge view test
	// Creates: APPPATH/views/test.php

You can pass in subfolders with the name and they will be created automatically.

	$ php sprint forge view tests/test
	// Creates: APPPATH/views/tests/test.php
