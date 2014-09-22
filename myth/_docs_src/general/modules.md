# Module Development
A **module** is a collection of files that group functionality. This makes it simple to reuse the code in other projects or distribute the code. Modules can do as much or as little as they want: 

* Be small integration plugins or other small tasks background tasks
* Tap into system events to execute code when system actions occur.
* Create new controllers that can be mapped in the frontend or backend.
* Update the database during install/update routines
* And much more...

Third party modules have access to the full system, just like the bundled modules.

## Creating A Module
Creating a module is simple and only requires two things: 

* a folder: `application/modules/modulename/`
* a module definition file: `application/modules/modulename/modulename.php`

Optionally, you can include a similar folder structure to the application folder, including: 

* One or more models at `application/modules/modulename/models/`
* One or more controllers at `application/modules/modulename/controllers`
* A views folder at `application/modules/modulename/views/`
* Additional routes specific to this module at: `application/modules/modulename/config/routes.php`
* Helper files at `application/modules/modulename/helpers/`
* Library files at `application/modules/modulename/libraries/`

Of coures, you have access to all standard CodeIgniter libraries/helpers, as well as any Bonfire-specific code, or even anything you can load through Composer.

## Module Definition File
Location: `application/modules/modulename/modulename.php`

This file has 3 main purposes: 

1. Perform any installation/upgrade tasks, such as database table creation/modification, settings creation, etc.
2. Registering any menu items your module needs.
3. Subscribing to any System Events that you want to tap into to modify execution.

At its simplest, the module definition file could look like this: 

	class Modulename extends \Bonfire\Libraries\Module {
		public $version = '1.0';
		public $name = 'modulename';
		public $description = 'My module description here.';
	}
	
Here, we've specified the current module name, version and description. We've also extended the Module class which handles the shared logic for modules.

If there is a class conflict (e.g., your frontend controller class is `Modulename` and you can't use `Modulename` for the definition class) you can use `ModulenameModule` (e.g. `SearchModule`) as the class name for the definition file.

The CodeIgniter superobject is now accessible within our module definition file at ` $this->ci`.

**No other code is required** in the module definition, but few modules will be that simple.

### Install/Upgrade routines
All installation and upgrade tasks should be handled through module-specific migration files. These files should be located under `application/modules/modulename/migrations/` and follow the same rules as standard migrations.

Note that migrations will only be run for modules that have been installed already. For new modules that have not been installed, the migrations will initially be ran during the installation of the module.

### Uninstallation
Not all modules need uninstall code. By default, any module-specific migrations will be reverted to version 0 if you tell Bonfire to remove the data from the system, otherwise the database is left intact and the module can be activated at any time.

If you need to handle other specific tasks during your module's uninstall routine, you can create an `uninstall()` method in the module definition file and it will be called prior to the migrations being rolled back.

### Admin Initialization
When an admin page is loaded, any modules that have the `initAdmin()` method in the module definition file will have that method ran. This gives you the opportunity to run do things like add menu items. 

	class Modulename extends \Bonfire\Libraries\Module {
		// .. variables, update logic, etc...
		
		function initAdmin() {
			$this->menu->addChild(new MenuItem('modulename'), 'Content');
		}
	}
	
### Frontend Initialization
Similar to `initAdmin()`, `init()` is executed on every pageload that is not within the Admin pages.