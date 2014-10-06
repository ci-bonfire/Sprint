# Migrations

Migrations are simple files that hold the commands to apply and remove changes to your database. This allows you and your team to easily keep track of changes made for each new module. They may create tables, modify tables or fields, etc. But they are not limited to just changing the schema. You could use them to fix bad data in the database or populate new fields.

While you could make the changes to the database by hand, migrations provide a simple, consistent way for developers to stay on track with each other's changes. It also makes it simple to apply the changes in your development environment to your production environment.

Using migration files also creates a version of your database that can be included in your current code versioning, whether you use git, svn, or another solution. While you might not have your data backed up in the case of a devastating loss, you can at least recreate your database and start over.

Migrations are contained in sequentially numbered files so the system knows the order to apply them or remove them.

## Migration Groups
By default, migrations are stored under the `application/database/migrations` folder. You can add more folders, or simply relocate the migrations altogether. This is done in the `application/config/migration.php` config file. 

	$config['migration_paths'] = array(
    	'app'       => APPPATH . 'database/migrations/'
	);

To add a new folder where migrations can be found, simply add a new row to the array. The key is an alias that you will use when you call the migrations from the command line, or define the groups to be automatically migrated in the `config/application` file. The *value* is the location on the server where the migrations can be found.

### Module Migrations
Each module can contain its own migrations, that can be applied completely separate of any application or core migrations. This allows for you to easily re-use your modules in other applications.

Module-level migrations are stored in `modules/my_module/migrations` folders.

## Enabling Migrations
A clean install has migrations enabled by default.  However, it is recommended when you move to production to disable migrations for security.

To disable migrations, edit the following line in `application/config/migrations.php` to be `false`.

    $config['migrations_enabled'] = true;


## Anatomy of a Migration
A migration is a subclass of `CI_Migration` that implements two methods: up (perform the required transformations) and down (revert them). Within each migration you can use any of the methods that CodeIgniter provides, like the `dbutils` and `dbforge` classes.


## Creating a Migration
The easiest way to create a migration is to call the `database newMigration` task from the command line.

	php index.php database newMigration {migration_name}
	
The `{migration_name}` will be used as part of the filename. The  filename is prefixed with the current date/time to allow for ease of working in teams. If your migration name was `CreateUserTable` the filename would look something like `20141001052249_CreateUserTable.php`. 

This file will be created in the `app` migration folder be default. You can change by passing in the alias of the folder you want it created in after the migration name. You cannot have it automatically placed within a module's folder currently.

### File Structure
The file is a standard PHP class, that must follow three simple rules:

* The class must be named the same as the file, except the number is replaced by Migration.  For a file named `20141001052249_CreateUserTable.php`, the class would be named `Migration_CreateUserTable`.  The name is case-sensitive.
* The class MUST extend the `CI_Migration` class
* The class MUST include two methods: `up()` and `down()`.  As the names imply, the `up()` method is ran whenever you are migrating up to that version.  The `down()` method is ran whenever uninstalling that migration.


### A Skeleton Migration

    class Migration_CreateUserTable extends CI_Migration {

      function up() {
          ...
      }

      //--------------------------------------------------------------------

      function down() {
          ...
      }

      //--------------------------------------------------------------------
    }

## Running Migrations
Migrations can be run, both up and down by using the `database` tools on the command line.

For all of the following commands, you can replace the group name with `mod:` followed by the module name. 

	php index.php database migrate mod:users

### migrate
This will run the migrations to a specific version, or to the latest if no version is supplied. The first parameter is the `alias` of the migrations group. The second parameter is the version to migrate to. This would be the filename you want to end your migrations on. Any migrations past this will not be ran. If no version is passed in the second parameter, then will be prompted to run to the latest version available for that group, or cancel.

	php index.php database migrate app

### quietMigrate
This is identical to the `migrate` method, but will not return any output to the command line, just a success/fail result. This is useful when part of a build script process.

If no migrations are found, or the database is already at the current migration, the script will return TRUE.

	php index.php database quietMigrate app

### refresh
This will run the down() method on all migrations in the specified group in reverse order, effectively uninstalling those changes, and then rerun them up to the latest available migration. This is useful to reset the data to a pristine version before running [seeds](database/seeds).

	php index.php database refresh app

## Auto-Running Migrations

Migrations can be set to auto-run when discovered by changing a couple of lines in the `application/config/config.php` file. 

	$config['auto_migrate'] = array(
        'app'  
    );

This array holds the group names that you want to have automatically migrated. Each group will be migrated, in order, to its latest available migration. 

These are very handy to have set in both Development and Staging/Test environments, but will slow your site down some since they check on every page load. It is recommended that Production environments set both of these to FALSE and run your migrations manually or as part of an update script.