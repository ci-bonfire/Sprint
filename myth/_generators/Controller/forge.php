<?php

$descriptions = [
    'controller' => ['controller <name> [<base>]', 'Creates a new controller file that extends from <base> or BaseController.']
];

$long_description = <<<EOT
NAME
	controller - creates a new controller and possibly it's CRUD.

SYNOPSIS
	controller <name> [options]

DESCRIPTION
	At its most basic, creates a new Controller with stub outs for the common CRUD methods.

	If the -themed option is present it will do two things. First, the controller will extend from
	Myth\Controllers\ThemedController instead of Myth\Controllers\BaseController. Second, it will
	create the basic code to generate a working set of CRUD methods, as well as their views, ready
	for you to customize.

	If the -model option is present, it will add the model to be autoloaded. Since a model is present
	it will also force the use of themes and generate all of the CRUD code for you.

OPTIONS
	-model  The name of a model to autoload. If present, also acts as if -themed option is passed.

	-themed If present, forces use of a ThemedController and generates views.
EOT;
