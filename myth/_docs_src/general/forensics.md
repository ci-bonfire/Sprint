# Profiling Your App

Sprint includes a drop-in replacement for the standard [CodeIgniter Profiler](http://www.codeigniter.com/userguide3/general/profiling.html) and the instructions for general usage are the same for both.  The new system, named "Forensics", does have an extra logging feature that you can use, though, in addition to the standard features. 

## Loading the Profiler
You can turn profiling on or off, system-wide in the `application/config/application.php` file. This is then turned on in the `setupProfiler` method of the [BaseController](general/controllers). Certain methods, like some of the `render*` methods, will automatically turn it off, as will the [CLIController](cli/controllers) since the profiler doesn't work well in those situations.

	$config['show_profiler'] = true;
	
At any time, you can turn off profiling in your application with the standard method: 

	$this->output->enable_profiler(false);

## The Console
The primary added feature is the `Console` class that provides a simple method to log object and memory usage throughout the request cycle to the profile bar. This class is automatically loaded when the profiler is so you do not need to load it separately. 

### log($data)
This function accepts any data type and simply creates a pretty, readable output of the variable, using print_r(). Very handy for logging where you are in the script execution, or outputting the contents of an array, or stdObject to your new 'console'.

	Console::log( $data );

### logMemory($variable, $name)
The log_memory function has two uses.

1) When no parameters are passed in, it will record the current memory usage of your script. This is perfect for watching a loop and checking for memory leaks.

2) If you pass in the `$variable` and `$name` parameters, will output the amount of memory that variable is using to the console.

	// Record current script memory usage
	Console::logMemory();
	
	// Records the approximate amount of memory a variable is using.
	Console::log($user, 'user');
