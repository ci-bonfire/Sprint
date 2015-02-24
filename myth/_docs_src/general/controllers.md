# Controllers

CodeIgniter provides a `CI_Controller` that is meant to be used as the basis for all of your own controllers. It handles the behind-the-scenes work of assigning class vars and the Loader so that you can access them. Sprint extends this concept and provides several additional Controllers that can be used as base classes throughout your project. This helps you to keep from repeating code any more than necessary by providing a central place for any site-wide code to sit.

The `MY_Controller` file is left for you to modify as you need. It extends from `Myth\Controllers\BaseController` that we do all of our work in.

## Provided Controllers
Under the `Myth` namespace, we provide the following controllers for your work to extend from:

- **BaseController** This forms the basis of all of the rest of the controllers, setting up profilers, caching, autoloading language and model files, auto-migrating the application if necessary, and providing common utility methods.
- **ThemedController** Builds on top of BaseController to provide additional methods and variables for simple, consistent theming of your controllers.
- **CLIController** Intended solely for use from the command line, it provides features for colored text and working with interactive CLI scripts.

## BaseController

All of the custom controllers extend from `Myth\Controllers\BaseController`. This class extends the CI_Controller. It intentionally does NOT extend from our HMVC solution since that has caused too many problems and encourages a design pattern that is better  based around libraries that are much more testable.

This controller provides common functionality to all of the other controllers and should be the minimal class you extend if you want to take advantage of Sprint's capabilities.

### Properties

#### $cache_type
This can be set to allow a per-controller override of the primary cache engine configuration settings in `config/application.php`. If not set, it will default to the settings in the configuration file.

#### $backup_cache
This can be set to allow a per-controller override of the backup cache engine configuration settings in `config/application.php`. If not set, it will default to the settings in the configuration file.

#### $ajax_notices
If `true`, notices will be sent back with and AJAX response in the `fragments` array. This works with the provided [eldarion-ajax](https://github.com/eldarion/eldarion-ajax) javascript library. Defaults to `true`.

#### $language_file
If set, the file listed will be automatically loaded. Only loads a single file and is intended to load controller or module-specific language code. Any other language files should be loaded manually, or automatically loaded through the `autoload` config file.

#### $model_file
If set, the listed model will be automatically loaded. Will only load a single model.

### Class Methods
The BaseController provides a number of helper methods for outputting data in various formats and more. They are described below. All of the render methods set the proper content type, and should be called as the last method. Most also turn off the profiler so you don't need to worry about that in your code.

#### setupCache()
This is called during the constructor to get the cache engine up and running. Developers will typically not need to modify it, but it is split out into a separate method for easy child-class overriding if it's needed.

#### autoload()
This is called during the constructor to load the language and model files. Developers will typically not need to modify it, but it is split out into a separate method for easy child-class overriding if it's needed.

#### autoMigrate()
This is called during the constructor to handle the [auto-migration](database/migrations#auto-running_migrations) capabilities. Developers will typically not need to modify it, but it is split out into a separate method for easy child-class overriding if it's needed.

#### setupProfiler()
This is called during the constructor to get the profiler setup. Is split out into a separate method for easy child-class overriding if the profiler capabilities need to be modified.

#### renderText()
Renders a string of arbitrary text. Best used during an AJAX call or web service request that is expecting something other  than proper HTML.

The first parameter is the string to be rendered. To have the string run through the [auto_typography](http://www.codeigniter.com/userguide3/helpers/typography_helper.html?highlight=typography#auto_typography) function, you can pass in `true` as the second parameter.

	$this->renderText($text);

#### renderJS()
Sends the supplied string to the browser with a MIME type of `application/javascript`. Will throw a Logic Exception if the passed element is not a string.

	$this->renderJS($js);

#### renderRealtime()
Disables any output buffering so that any content echoed out will echo out as it happens, instead of waiting for all of the content to echo out. This is especially handy for debugging long running scripts.

	$this->renderRealtime();

#### ajaxRedirect()
This is one of two methods that requires an external dependency. This uses the [eldarion-ajax](https://github.com/eldarion/eldarion-ajax) scripts to break out of the current AJAX method and perform a javascript-powered redirect. Use this while in an AJAX method instead of CodeIgniter's built-in `redirect()` method.

	$this->ajaxRedirect('new/location');

#### renderJson()
Renders an array or object of data to be output in JSON format. The only parameter is the data to be output.

	$this->renderJson($array);

This works hand-in-hand with the `eldarion-ajax` javascript we use. If profiler is turned on, it will reload the profiler in a `div` with `id='profiler'`. If `ajax_notices` is `true`, the notice will replace a `div` with `id='notice'` when it returns.

#### getJson()
Attempts to get any information from `php://input` and return it as JSON data. This should only be used when you know you are expecting JSON data, like from an AJAX or API call.

The first parameter is the type of element to return the data as. The only two value strings are `object` (the default) or `array`.

The second parameter is the number of levels deep to decode. Smaller numbers can decrease processing time at the expense of potentially lost data. Default value is 512.

	$data = $this->getJson('array');

## ThemedController
This controller provides theming functions into the controller with several convenience methods. Makes integrating full-featured templates into your application a breeze. It extends from [BaseController](#basecontroller).

Full details on the usage can be found in the [Theming](general/themes) guide in the docs.

## CLIController
Provides access to the [CLI library](cli/cli_library) and restricts the controller's usage to the Command Line. Extends from [BaseController](#basecontroller) to provide access to all of the tools you're used to.

Full details on the usage can be found in the [CLI Controllers](cli/controllers) guide in the docs.
