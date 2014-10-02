# CLI Controllers
You can create controllers intended for use only on the command line by extending the `Myth\Controllers\CLIController`. This will restrict the controller to being called from anywher other than the command line, and load up the CLI library, which provides several tools to make working with the command prompt a pleasant experience. 

CLI Controllers can be used for creating tools that can be called from third-party scripts on your server. You can create tools that handle redundant chores for yourself, or add new features to your code, like the `database tools` that have been provided with Sprint. 

	class myTools extends Myth\Controllers\CLIController {
		. . .
	}

## Calling A CLI Controller
Calling a CLIController-based tool uses the standard CodeIgniter CLI approach. This maps the arguments on the command line to the same that you would enter in a web browser. If you would reach your module/controller at 

	http://mysite.com/database/seed
	
then the relevant command line would be 

	php index.php database seed
	
You can access any of your controllers this way, though the output won't be pretty in most cases. 

This does pose one restriction, though. When creating your command line tools you cannot use typical `options` and `longoption` like you would otherwise. Instead, you will need to handle this through the routes system.

## The CLI Library
The CLI library has been borrowed from the [FuelPHP Framework](http://fuelphp.com/) and provides a number of tools for working with the command line, including getting user input and colored text.

### beep()
Um, beeps at the user. The only parameter is the number of beeps that you want it to make. Please use sparingly. Please. 

	CLI::beep(1);

### color()
Used to display colored text. This is usually used within a `write()` call to color a single portion of the text. The first parameter is the text to display. The second parameter is the color of the text itself. 

The third parameter is the background color to use. Typically you would not want to set this because you don't know the color that user has their terminal set to.

The fourth parameter is any other formatting to apply. Currently only supports 'underline'.

	echo CLI::color("Error: You have been eaten by a grue.", "red");
	
The available foreground colors are: `black`, `dark_gray`, `blue`, `dark_blue`, `light_blue`, `green`, `light_green`, `cyan`, `red`, `light_red`, `purple`, `light_purple`, `light_yellow`, `yellow`, `light_gray`, and `white`.
	
The available background colors are: `black`, `red`, `green`, `yellow`, `blue`, `magenta`, `cyan`, 	`light_gray`.
	
### error()
Displays the text in red and write it to the command line. This is similar to [write](#write) but uses STDERR instead of STDOUT.

	CLI::error('Ooops. You have been eaten by a grue. Again.');

### prompt()
Prompts the user for a bit of information. No parameters are needed to work. Without any parameters, it simply waits for a keypress.

	CLI::prompt();

 The first parameter is the question you want to ask the user. When used by itself, it will accept any answer.
 
	$name = CLI::prompt("What did you name your pet grue?");

The second parameter can be either a string or an array. If it is a string, that becomes the default answer, but will still accept any answer.

	$name = CLI::prompt("What did you name your pet grue?", "Zork");

When the second parameter is an array, it will only accept answers that are within the array.

	$ready = CLI::prompt('Are you ready?', array('y', 'n'));

### wait()
Force the command line to wait a number of seconds. The first parameter is the number of seconds to wait. The second parameter can be set to TRUE to provide a running countdown during the wait. 

	CLI::write('Loading...');
	CLI::wait(5, true);

### write()
Writes a line of text to the command line. The first parameters is the text to display. The second parameter is the foreground color of the text. The third parameter is the background color of the text. It is recommended that you do not set the colors here unless for a very specific reason. Terminal colors can vary wildly and you want to use the standards whenever possible for the broadest compatibility.

	CLI::write('Hello world');
	
You should consider using newline and tab characters in your strings to create a pleasant experience. 

	CLI::write("module_name\tdescription goes here.\n");

You can also use the [color](#color) command within the `write` command to good effect, if used sparingly.

	

