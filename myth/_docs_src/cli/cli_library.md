# The CLI Library
The CLI library has been borrowed from the [FuelPHP Framework](http://fuelphp.com/) and tweaked a bit and given some features, and provides a number of tools for working with the command line, including getting user input and colored text.

## Basic Methods

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

## Utility Methods
### segment()
This method works much like the `segment` method of the URI library. It breaks the command you called on the CLI down into segments and makes each available in a 1-indexed array. This method only includes the portion of the command line prior to any arguments. Returns NULL if no segment exists.

	// The command: 
	$ sprint database migrate -help app
	
	CLI::segment(1) = 'database'
	CLI::segment(2) = 'migrate'
	CLI::segment(3) = null

### cli_string()
Much like it's URI counterpart, `uri_string` this simply returns the relevant portions of the CLI command as a string. This only includes information up until the first argument. 

	// The command: 
	$ sprint database migrate -help app
	
	CLI::cli_string() = 'database migrate'

### getWidth()
### getHeight()
These commands return the current size of the terminal window. On Windows, where this information is not available, default values of width=80 and height=32 are returned.  This may be useful in your scripts but is used by the CLIController to provide elegant wrapping of descriptions.

### showProgress()
Allows your scripts to show a progress bar when you're performing a long action. The first parameter is the current step. The second parameter is the total number of steps. You must call this each time you need to update the progress. 

	for ($i=1; $i < 100; $i += 5)
	{
   	 	CLI::showProgress($i, 100);
    	usleep(100000);
	}
	
	// Creates: 
	[####......] 42% Complete
	
Once the progress reaches 100% it will dissappear from the screen automatically. You can force it to end prematurely by passing in `false` as the only parameter.
	
	CLI::showProgress(false);
