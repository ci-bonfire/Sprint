<?php

$descriptions = [
	'scaffold' => ['scaffold <name>', 'Creates an MVC triad based around a single data type.']
];

$long_description = <<<EOT
NAME
	scaffold - creates the models, views, controllers and migrations necessary for a single data type.

SYNOPSIS
	scaffold <name> [options]

DESCRIPTION
	Given the name of a single data type, like 'post', it creates the Model, Controller with basic CRUD operations,
	the required views and the migration necessary to quickly scaffold out, or prototype, a new data type. This is
	intended to quickly create code that can be edited as needed. Regenerating the code will overwrite the files,
	not update them.

	All HTML will be generating using the current UIKit as specified in `application/config/application.php`.

OPTIONS
	-fields     A quoted string with the names and types of fields to use when creating a table.

	-fromdb     If present, will override -fields values and attempt to pull the values from an existing database table. This has no value, the table is discovered from the migration name.

	-module     If present, will create this as a separate module, instead of incorporating it into the existing app.
EOT;
