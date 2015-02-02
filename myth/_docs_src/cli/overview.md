# Sprint CLI

## Introduction

Sprint ships with a simple CLI tool called 'sprint'. While not neccessary to use to run your [CLI-base Controllers](cli/controllers), it makes the experience a bit more pleasant and provides some additional functionality when working with CLI Controllers.

## Usage

At it's most basic, `sprint` simply provides an interface to the standard CodeIgniter ability to call controllers from the command line. In this case, though, the controllers must extend from `Myth\Controllers\CLIController`. 

The command must be called from the web root, unless you modify the `$apppath` variable found in the `sprint` file to point to the main `index.php` file.

In stock CodeIgniter, you would call a controller like this: 

	$ php index.php {controller} {method} {param1} {param2}

Using the sprint CLI tool you would make the call like: 

	$ php sprint {controller} {method} {param1} {param2}
	// Migrating the database:
	$ php sprint database migrate

If you're using a Linux or OS X system, you make it more user-friendly by ensuring the sprint file has execute permissions on your local machine and PHP is in your $PATH. Then you can call sprint without prefixing it with php.

	chmod u+x sprint
	sprint database migrate

### Listing Tool Commands
When calling a CLIController based tool, you can list all of the commands available within that tool using by simply calling the tool name.

	$ php sprint database
	// Outputs: 
	migrate       migrate [$to]         Runs the migrations up or down until schema at version \$to
	quietMigrate  quiteMigrate [$to]    Same as migrate but without any feedback.
	refresh       refresh               Runs migrations back to version 0 (uninstall) and then back to the most recent migration.
	newMigration  newMigration [$name]  Creates a new migration file.
	seed          seed [$name]          Runs the named database seeder.

### Getting Command Help
If you need additional help on any command within a tool, you can ask sprint to show you it's help by passing the `-h`, `-help`, or `--help` arguments after the command name. 

	$ php sprint database migrate -h

### Displaying Sprint's Current Version
You may view Sprint's current version by passing only the `-v` or `--version` arguments to the sprint command. 

	$ php sprint --version

