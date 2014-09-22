# Bonfire Controllers

CodeIgniter provides a `CI_Controller` that is meant to be used as the basis for all of your own controllers. It handles the behind-the-scenes work of assigning class vars and the Loader so that you can access them. Bonfire extends this concept and provides several additional Controllers that can be used as base classes throughout your project. This helps you to keep from repeating code any more than necessary by providing a central place for any site-wide code to sit. 

The `MY_Controller` file is currently not used by Bonfire and is left alone so that you can use it for your own needs.

## Extending Controllers
Each controller is stored in its own file in the `application/libraries/Controllers` folder and the file is named the same as the class name. They are all namespaced in `\Bonfire\Controllers\{controller_name}`. To use the controller as a base class, you should extend your controller from the appropriate Bonfire Controller.

	class SomeController extends \Bonfire\Controllers\ThemedController {
	    . . .
	}

## BaseController

All of the custom controllers extend from the `Base_Controller`.  This class extends the MX\_Controller which gives you all of the power of WireDesign’s [HMVC](https://bitbucket.org/wiredesignz/codeigniter-modular-extensions-hmvc/wiki/Home) available to all of your classes.  That allows for a different way of working, but also a very powerful one, and one that is not necessary to use.

This controller provides common functionality to all of the other controllers and should be the minimal class you extend if you want to take advantage of Bonfire's capabilities.

#### Properties

##### $autoload
This array provides a simple means of loading various libraries, helpers and models automatically within your application. 

	protected $autoload = array(
	    'helper'    => array(),
	    'language'  => array(),
	    'libraries' => array('settings/settings_lib'),
	    'model'     => array(),
	    'modules'   => array(),
	    'sparks'    => array()
	);

You will not, however, want to extend this directly since you will override parent class autoload needs. Instead, set the value in your class' constructor prior to calling the parent's constructor.

	public function __construct()
	{
	    $this->autoload['helpers'][] = 'my_new_helper';
	
	    parent::__construct();
	}

##### $init\_methods
This is an array of method names that will be called during the initial `init()` method call in the constructor. This is intended for use by Traits. If your class uses a Trait then you'll need to put it's init method in here. This is already handled for you by the child Controller classes.

	class ThemedController extends BaseController {
	    use ThemeableTrait;
	
	    protected $init_methods = [
	        'init_themes'
	    ];
	}

#### Class Methods
The BaseController provides a number of helper methods for outputting data in various formats and more. They are described below. All of the render methods set the proper content type, and should be called as the last method. Most also turn off the profiler so you don't need to worry about that in your code.

##### renderText()
Renders a string of arbitrary text. Best used during an AJAX call or web service request that is expecting something other  than proper HTML. 

The first parameter is the string to be rendered. To have the string run through the `auto_typography` method, you can pass in TRUE as the second parameter.

	$this->renderText($text);

##### renderJson()
Renders an array or object of data to be output in JSON format. The only parameter is the data to be output.

	$this->renderJson($array);

##### renderJS()
Sends the supplied string to the browser with a MIME type of text/javascript. Will throw a Logic Exception if the passed element is not a string.

	$this->renderJS($js);

##### renderRealtime()
Disables any output buffering so that any content echoed out will echo out as it happens, instead of waiting for all of the content to echo out. This is especially handy for debugging long running scripts.

	$this->renderRealtime();

##### ajaxRedirect()
This is the only method that requires any external dependencies. This uses the [eldarion-ajax](https://github.com/eldarion/eldarion-ajax) scripts (which are used extensively throughout Bonfire) to break out of the current AJAX method and perform a javascript-powered redirect. Use this while in an AJAX method instead of CodeIgniter's built-in `redirect()` method.

	$this->ajaxRedirect( 'new/location' );

##### getJson()
Attempts to get any information from `php://input` and return it as JSON data. This should only be used when you know you are expecting JSON data, like from an AJAX or API call.

The first parameter is the type of element to return the data as. The only two value strings are `object` (the default) or `array`. 

The second parameter is the number of levels deep to decode. Smaller numbers can increase processing time at the expense of potentially lost data. Default value is 512. 

	$data = $this->getJson('array');

##### callFilters()
This is used by the [Filters](#filters) system. Can be called from, for example, a hook to run custom filters.

The only parameter is the `type` or name of the filters group to run, like `before` or `after`.

	$this->callFilters('before');
 
## ThemedController
This controller provides theming functions into the controller with several convenience methods. Makes integrating full-featured templates into your application a breeze. It extends from [BaseController](#basecontroller).

Full details on the usage can be found in the [Theming](#themes) guide in the docs.

## FrontController

The `Front_Controller` is intended to be used as the base for any public-facing controllers.  As such, anything that needs to be done for the front-end can be done here.

Currently, it simply ensures that the Assets and Template libraries are available.  You could also set the active and default themes here, if you create a parent theme ‘framework’ to use with all of your sites that you extend with child themes.


## AuthenticatedController

This controller forms the base for the Admin Controller.  It was broken into two parts in case you needed to create a front-end area that was only accessible to your users, but that was not part of the Admin area and didn’t share the same themes, etc.  All changes you make here will affect your Admin Controller’s, though, so use with care.  If you need to, reset the values in the Admin Controller.

This controller currently...

* Loads in all of the authentication library
* Restricts access to only logged in users
* Gets form\_validation setup and working correctly with HMVC.


## AdminController

The final controller sets things up even more for use within the Admin area of your site.  That is, the area that Bonfire has setup for you as a base of operations.  It currently...

* Sets the pagination settings for a consistent user experience.
* Gets the admin theme loaded and makes sure that some consistent CSS files are loaded so we don’t have to worry about it later.

	 
## Filters
Filters allow methods to be called at certain points during a controller's execution. They are typically used to filter access to a method, or perform cleanup actions after a method. 

### Defining Filters
Filters are defined globally in a config located at `application/config/filters.php`.  They must be anonymous functions, and accept two parameters: `$params` and `$ci`.

	$config['auth'] = function ($params, &$ci)
	{
	    . . .
	}

The first parameter is an array of params, typically provided when you assign the filter in your controller. The second is an instance of the CodeIgniter superglobal object. This ensures that all functions share the same instance of $ci and aids in testing functions.

If you need to access a class, or just wish to keep your functionality in a class, then you can call the class from the global namespace within this function.

During the execution of the filter, a successful check within the filter requires that nothing is returned. If anything is returned, execution is stopped and the controller's method is never executed. This is handy for redirecting the user to a login screen, for example.

### Assigning Filters
To assign filters to specific methods within your controller you must fill out the `$filtered_methods` class variable with the `before` and `after` arrays filled with the names of the filters to call.

	class Users extends Base_Controller {
	    protected $filtered_methods = [
	        'method_name'   => [
	            'before'    => 'auth',
	            'after'     => 'someFilter'
	        ]
	    ]
	}

To specify multiple filters on a method, separate each with a pipe (`|`).
 
	'before'    => 'auth|anotherFilter',
 
### Filter Execution
 Bonfire has two times that the filters are executed.
 
 * `before` - executed during the controller's instantiation, but before the method has been called. This means that all classes, helpers, libraries, etc, have been loaded and are available for you use. 
 * `after` - called in the `post_controller` hook.

You can also call the `callFilters` method at any time with your own name, and then add those into the $filtered\_methods array just like a before or after call.

### Pre-defined Filters
Bonfire provides several pre-defined filters ready for your use. 

* `debug` - Checks for the presence of the $\_GET variable `?debug` and defines the constant 'DEBUG\_MODE' to TRUE, if the current environment is 'development'. This allows you to build in additional debug code to your modules and simply add a URL flag to turn it on.
