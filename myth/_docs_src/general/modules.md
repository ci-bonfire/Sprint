# Working With Modules

Modules are simply mini-applications that are semi-self-contained and can be easily integrated into other applications or even distributed to other developers, if desired. They can contain `config files`, `controllers`, `helpers`, `libraries`, `models` and `views`.  Common uses might be for a blog, the user management section of a site and more. 

## Module Locations
By default, you can store modules in the `application/modules` folder. You can edit this by adding to the array of module locations in the `application/config/config.php` file. 

	$config['modules_locations'] = array(
    	APPPATH .'modules/'             => '../modules/',
	    APPPATH .'../myth/CIModules/'   => '../../myth/CIModules/'
	);

The key of each element is the full system path. The value of each element is the offset from the `application/controllers` folder to the new modules folder.

When a module is being located it takes the first one found. If you have a module of the same name in two different folders, then the one in the first modules_locations folder will be the module used. To allow you to override the function of any of Sprint's bundled modules, you should ensure that the `CIModules` entry is always the last element in the array.

## Module Structure
The name of the folder is the name of the module, as far as the system is concerned. So, a module named `Users` would need a folder (lowercase) named `users`.

	/application
		/modules
			/users
				/config
				/controllers
				/helpers
				/libraries
				/models
				/views
				
You can map a call to URL with the name of the module, as long as your controller name is the same as the module name. A module named `users` would have a controller named `users.php`, and a class named `Users`. This could be reached in the browser at `example.com/users`. 

## Module Helpers
A utility class, `Myth\Modules` provides a number of small commands to work with modules. For the most part you shouldn't need access to these, unless you are creating code that helps manipulate modules. 

### listModules()
Returns a list of all modules in the system. 

	print_r( Modules::listModules() );
	
	Array (
		[0] => builder
		[1] => database
		[2] => docs
	)

### controllerExists()
Looks within a module to see if a certain controller exists. The first parameter is the name of the controller, and the second parameter is the module name.

It returns either TRUE or FALSE.

    if (Modules::controllerExists('content', 'users')) { . . . }
    
### filePath()
Locates a file within a module and returns the path to that file. The first parameter is the name of the module. The second parameter is the name of the folder. The last parameter is the name of the file that you're looking for (including the extension).

It returns the full server path to the file, if found.

    $path = Modules::filePath('users', 'assets', 'js/users.js');

### path()
Returns the full server path to a module and, optionally, a folder within that module. The first parameter is the name of the module. The second parameter is the name of the folder.

    $path = Modules::path('users', 'assets');

### files()
Returns an associative array of files within one or more modules.

The first parameter is the name of the module to restrict the search to. If left NULL, this will provide a list of all files within all of the modules. If a module name is specified, the search will be restricted to that module's files only.

    Modules::files('sysinfo');

    // Produces:
    Array
    (
        [sysinfo] => Array
        (
            [config] => Array
                (
                    [0] => config.php
                )

            [controllers] => Array
                (
                    [0] => developer.php
                )

            [language] => Array
                (
                    [english] => Array
                        (
                            [0] => sysinfo_lang.php
                        )

                    [persian] => Array
                        (
                            [0] => sysinfo_lang.php
                        )

                    [portuguese_br] => Array
                        (
                            [0] => sysinfo_lang.php
                        )

                    [spanish_am] => Array
                        (
                            [0] => sysinfo_lang.php
                        )

                )

            [views] => Array
                (
                    [developer] => Array
                        (
                            [0] => _sub_nav.php
                            [1] => index.php
                            [2] => modules.php
                            [3] => php_info.php
                        )

                )

        )

    )


The second parameter lets you specify a folder within that module to limit the file search to. If left NULL, it will provide all of the files.