# Creating New Generators

While Sprint comes bundled with a number of generators already, it is easy to create your own.

## The Generator Class
Each generator is a single class that extends from `Myth\Forge\BaseGenerator`. The class must the name of the folder it is contained within, with `Generator` appended to it. It is case sensitive. So a Model generator would be located at:

	/collection_folder
		/Model
			ModelGenerator.php

The class name would need to match the file name.

	class ModelGenerator extends Myth\Forge\BaseGenerator {
		...
	}

### run()
The only required method is the `run()` method which contains all of the directions for this generator to work. From here, you can create new directories and files, modify files, add routes to the application route file and more. It takes no parameters

	public function run()
	{
		...
	}

## Template Files
Each generator must store it's template files within a sub-folder within the template group folder. This folder name must match the name of the `$generator_name` class variable within the generator. The filename must end in `.tpl.php` in order for it to be found.

If you're creating a generator named `my_model` you would need to create something like the following file:

	/group_folder
		/my_model
			my_model.tpl.php

You can include other template files within the folder that can be included with the `render()` method of the generator. You might use these for organizing larger class files, or when multiple files will need to be generated. They must all have the `.tpl.php` file names.

If you need to include code that will be PHP code once the template has been rendered, but you don't want it to execute during the rendering process, you can use one of two different placeholders:

	@php	// becomes <?php
	@=		// becomes <?=

## Function Reference
The following methods are included in the BaseGenerator class for you to use while running your generator.

### createDirectory()
Allows you to create a new directory anywhere on the sytem that your script has access to. The first parameters is the path to the the new folder. It can be either a full system path or a relative path. If you are intended to create folders within the application folder, be sure to use `APPPATH` for greatest flexbilitiy if the file structure changes from project to project.

	$path = APPPATH .'modules/my_module';
	$this->createDirectory($path);

If it is unable to create the folder it will throw a RuntimeException.

By default the folder will be created with 0755 permissions, but you can change those by passing in the new permissions in the second parameter. It must start with a 0.

	$this->createDirectory($path, 0777);

### createFile()
Allows you to create a new file and determine the contents in that file. The first parameter is the path to the file, including the filename and extension. The second parameter is a string with the contents of the file.

	$this->createFile($path, $content);

By default, it will not overwrite the file, but if you want it to allow it to be overwritten, you can pass in `true` as the third parameter.

	$this->createFile($path, $content, true);

The file is created with 0644 file permissions. If you wish to change that, you can pass the new permissions (starting with a 0) as the fourth parameter.

	$this->createFile($path, $content, false, 0655);

### copyFile()
If you simply need to copy a file and not change the contents you can use `copyFile` to do so. The first parameter is the source file, which must be located within your generator's folder or a sub-folder. The second file is the destination path. The third parameter accepts a boolean to determine if the file should be overwritten.

	$this->copyFile($source, $destination);

### copyTemplate
This is a shortcut method that combines the `render()` and `createFile()` methods into a single step. This will likely be your most-used command. The first parameter is the name of the template to use. The second parameter is the destination path including filename and extension. The third parameter is an array of key/value pairs that will be passed to the template when it's being rendered. The fourth parameter accepts a boolean to determine if the file should be overwritten.

	$this->copyTemplate($template, $destination, $data);

### injectIntoFile()
This allows you to modify existing files. Use with care. The first parameter is the path of the target file, including filename and extension. The second parameter is the content you want to insert.

	$content = "This is the content that we're sticking in this file.";
	$file = APPPATH .'config/application.php';
	$this->injectIntoFile($file, $content);

If the file is not writeable, the system will throw a RuntimeException.

By default, the content is append to the end of the file. You can change that by passing in one of several options as the third parameter.

#### prepend
If you pass in the string `prepend` the `$content` will be placed at the very beginning on the file. If you are modifying a file that requires a first line (like `<?php`) then you will need to ensure that line is part of your `$content` in order for it to work.

#### append
Places the content at the end of the file. This is the default action.

#### before
You can pass an array with key=`before` to insert your content directly before the array's value. Because we are comparing entire lines when searching within the files, it must include the line ending ("\n") as part of the search string.

	$this->injectIntoFile($file, $content, ['before' => "This is the line we will place content before.\n" ] );

#### after
Just like `before` except this places the content immediately after the search line. Don't forget the line ending.

	$this->injectIntoFile($file, $content, ['after' => "This is the line we will place content after.\n" ] );

#### replace
This will search the file for the passed in string. All instances found will be replaced with `$content`.

	$this->injectIntoFile($file, $content, ['replace' => "{custom_search_string}" ] );

#### regex
This will run a preg_replace against the contents of the file. You will pass the pattern as the value of the array.

	$this->injectIntoFile($file, $content, ['regex' => "/pattern/" ] );

### generate()
This runs another generator, allowing to group many generators into a single task. This is how entire modules can be built at once.

@todo: write me once the functionality is built!

### readme()
Displays the contents of a file within your generator's folder onto the command line. By default it will look for a file named readme, but could be any plain text file.

	/collection_folder
		/Model
			ModelGenerator.php
			readme.txt

	$this->readme('readme.txt');

### render()
This will use the `ViewThemer` to render one of your template files within your generator's folder, applying any data passed to it, and return the resulting content as a single string, ready to be passed into the `createFile` command.  The file to be rendered must end in `.tpl.php`.

	/collection_folder
		/Model
			ModelGenerator.php
			model.tpl.php

	$output = $this->render('model', ['model_name' => $name]);

### route()
Allows you to add a new route to the `application/config/routes.php` file. It will be generated using the [Route](general/routes) library syntax. The first parameter is the left portion of the route. The second parameter is the right portion of the route. The third parameter accepts an array of options matching those accepted by the Route library commands. This allows you to set names for the route, etc. The fourth parameter accepts a string with any HTTP method that the route should accept (like `get`, `put`, `post`, etc). Defaults to `any`.

	$this->route('my/route/(:any)', 'final/route/$1', ['as' => 'demo'], 'get');
