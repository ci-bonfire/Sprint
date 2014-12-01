<?php

$descriptions = [
	'migration' => ['model <name>', 'Creates a new migration file.']
];

$long_description = <<<EOT
NAME
	migration - creates a new migration file.

SYNOPSIS
	migration <name> [options]

DESCRIPTION
	Will create a new migration file using the migration library settings and the <name> passed in to determine the name of the file.
	The system scans <name> for common words to help describe the action the migration should take, like creating a table,
	adding a column, or dropping a table or column.

	Fields must adhere to the following rule when being passed in via the -fields option:
		- Each field is described with column_name:field_type
		- A third segment can be present that determines the field length, joined with a colon.
		- If the 'type' segment is 'id', then a INT(9) UNSIGNED primary key is created.

	Examples of Fields:
		name:string             // A VARCHAR(255) called 'name'
		age:int:3               // An INT(3) called 'age'
		"name:string age:int:3" // Must be in quotes when called with multiple fields
		uuid:id                 // Creates a primary key called 'uuid'

OPTIONS
	-fields     A quoted string with the names and types of fields to use when creating a table.

	-dbtable    If present, will override -fields values and attempt to pull the values from an existing database table.


EOT;


