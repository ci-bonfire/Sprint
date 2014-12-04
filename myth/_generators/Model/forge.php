<?php

$descriptions = [
    'model' => ['model <name>', 'Creates a new model file that extends from CIDbModel.']
];

$long_description = <<<EOT
NAME
	model - creates a new model.

SYNOPSIS
	model <name> [options]

DESCRIPTION
	Provides a skeleton model file that extends Myth\Models\CIDbModel.

	When called without a model name, it will ask for the table name and the most common options from you.

	When called with a model name in the CLI it will assume typical defaults:

		- pluralising the model name (less 'model') for the table name,
		- 'id' for the primary key
		- will track created_on and modified_on dates
		- 'datetime' format
		- will NOT use soft deletes
		- will NOT log user activity.

	No matter how you call it, if a table exists with that name in the database already, it will analyse the table
	and create very basic validation rules for you.

	You will want to customize to match your project's needs.

OPTIONS
	-table          The name of the database table to use

	-primary_key    The name of the column to use as the primary key

	-set_created    If 'y', informs the model to automatically set created_on timestamps

	-set_modified   If 'y', informs the model to automatically set modified_on timestamps

	-date_format    Format to store created_on and modified_on values. Either 'date', 'datetime' or 'int'

	-soft_delete    If 'y', informs the model to use soft deletes instead of permenant deletes.

	-log_user       If 'y', informs the model to track who created, modified or deleted objects.
EOT;
