# Debugging Tools

Sprint ships with some powerful debugging tools out of the box. 

## Forensics
The first is my own [Forensics](general/forensics) which is a drop-in replacement for CodeIgniter's [profiler](http://www.codeigniter.com/userguide3/general/profiling.html). It also comes with a Console for logging extra information to the profiler bar. 

## PHP Error
Next is [PHP Error](http://phperror.net/), which replaces the standard PHP error reporting with lots of great information, and even lets you see the exact spot in the file where it happened. This is only available in the development [environment](http://www.codeigniter.com/userguide3/general/environments.html), and can be turned off with the `use_php_error` config setting.

## Kint
The last tool is the one you will probably use the most: [Kint](http://raveren.github.io/kint/) - a super-powered replacement for the trusty `var_dump` and `debug_backtrace` methods. While you will get most of the information about it from their site, I'll highlight the commands you need to know here. 

### d()
Displays a ton of information about the object that is passed into the method in an interactive report. You have to try it to believe it. This does NOT stop script execution.

	$user = $this->user_model->find(15);
	d($user);

You can pass multiple objects into it for logging and they will be displayed one after the other.

	d($user1, $user2);

### dd()
Just like `d()`, but will stop execution of the script at this point.

### s()
Displays a non-interactive, light version of the `d()` method that is just text. Basically a smarter version of `echo '<pre>'. print_r($user, true)`. Will NOT stop script execution.

	s($user);

### sd()
The same as `s()` but will stop execution of the script at this point.

### Kint::trace()
Displays a more informative and flexible version of debug_backtrace() up to that point of the script's execution.

	Kint::trace()
	// or...
	d(1);
