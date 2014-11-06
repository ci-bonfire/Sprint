# Installation

## Overview
Sprint does not have an installation script. There are, however, two steps to do to make the system usable as a whole. 

### Composer Install
After you've extracted your files you should jump onto the command line, in your web root folder, and tell [Composer](https://getcomposer.org/) to install the dependencies for you.

	$ composer install

Next, you should get the autoload files generated so that all of Sprint's files can be found

	$ composer dump-autoload
	
When you need to get the most performance out the system, you will want to rebuild the autoload files using the optimization tag. This scans all of the files that are discoverable, like any PSR-0/4 compliant folders and builds a class map, significantly improving your speed since it already knows where all of the files are located and doesn't have to scan the filesystem.

	$ composer dump-autoload -o

### Migrate the Database
The next step is to get the database setup. This gets all of the tables for the authentication system, the email queue and more ready to go for you. 

Make sure that you've edited your database config file with the appropriate settings (I always recommend copying it into `application/config/development/database.php` and using that instead - and using .gitignore to ignore that file. Helps keep things out of repos that no one else needs). Also, make sure the database exists. 

Once that's ready, from the web root at the command line run the [migrations](database/migrations): 

	$ php index.php database migrate

When it prompts you for information, just accept the defaults: the "app" group, and 'Y' you want to migrate to the latest version.

### Enjoy A Break

You're done!

## Folder Permissions

On the development server, the following folders should be writeable. Some of these should remain writeable on production, like `cache` and `logs` but the rest can, and probably should, be locked down a bit.

    application/cache
    application/logs
    application/database/migrations
    application/database/seeds
