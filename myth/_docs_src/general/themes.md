# Themes

Themes provide a simple, yet very flexible, way of providing consistent interfaces to your web pages. Instead of specifying all of the generic HTML for every type of page on your site, you can create a layout that then pulls in the proper parts to create a single page. All of this then wraps around your page content.

## Theme Setup
Themes are simply a collection of layout views and their assets that are used to determine the overall look of a page. They are the wrappers around your [Views](general/views).

### Theme Locations
Themes are typically stored in the same root folder as the rest of the project, alongside the `system` and `application` folders.  Each theme is required to have an entry in the `config/application.php` configuration file.

	$config['theme.paths'] = array(
		'bootstrap'  => FCPATH .'themes/bootstrap3',
		'foundation' => FCPATH .'themes/foundation5',
		'docs'       => FCPATH .'themes/docs',
		'email'      => FCPATH .'themes/email'
	);

The keys in the array are the name they are referenced by when removing the them or using the `display` method (see below). These names must match the folder name within the themes folder in order for the views to be found. The values are the paths to the themes folder, relative to the site's `index.php`.

### Themers
The Theme system is expandable to use most any PHP template library out there. To do so you would create a new class that extends from the [Myth\Interfaces\ThemerInterface](interfaces/themers) class. The class is then instantiated at run time using the class that is set in the `config/application.php` configuration file.

	$config['active_themer'] = '\Myth\Themers\ViewThemer';

## Using Themes

In order to use the theming system, your controller must extend the `ThemedController`. This provides a set of simple methods to make using themes simple.

### render()
The `render()` method is the primary means of interacting with the system. Use it to display the final page to the user. It should be the last action in your method. The view name will be automatically determined by the system based on the `{module}/{controller}/{method}` names, unless you've set the view to use yourself with `$this->themer->setView()`.

	$this->render();

You can pass in an array of data to be sent to the view as the first parameter.

	$data = [‘user’, $user];
	$this->render($data);

If you want to cache the output of the rendered file, you can pass in the number of **minutes** to cache it for as the second parameter.

	// Cache for 5 minutes
	$this->render($data, 5);

This will perform a standard CodeIgniter [full page cache](http://www.codeigniter.com/userguide3/general/caching.html).

### Flash Messages
ThemedController also provides an extended version of the CodeIgniter's flash messages that allow it to work in the current page view, and not require the page to be refreshed for the session data to be available.

You add a message with the `setMessage()` method. The first parameter is the message itself. The second parameter is the message type, like `info`, `warning`, etc. These will be applied as a class to the surrounding alert div when displayed on screen, and should match the values of the CSS framework you are using, if any.

	$this->setMessage(validation_errors(), 'warning');

Within your views and layouts, you can use the `message()` method to retrieve the current message data. This is done for you, though, by the `render()` method. It is available within your views and layouts as the `$notice` variable, already formatted and ready to be displayed.

### Themed Views
While you can always use CodeIgniter's `$this->load->view()` method within your views, it is recommended that you use the Template library's `display()` method instead. This helps to respect caching rules, and makes
it simple to grab view fragments from the theme folder.

The template engine is available from within views as the `$themer` variable and provides full access to all of the theme engine's methods.

To display a non-themed view, you just need to pass in the name of the view that you want to render as the first parameter.

	$themer->display('my_view');

If you need to pass in a specific set of data, then you can pass in an array of key/value pairs as the second parameter.

	<?= $themer->display('my_view', $data); ?>

To display view fragments from within your theme itself, you will prefix the name of the theme, followed by a colon, to the view name. 

	<?= $themer->display('admin:parts/header'); ?>

If you want to call a view, but don't know what the current theme will be, you can use the `{theme}` placeholder where the theme name would be. It will be replaced with the currently active theme.

	<?= $themer->display('{theme}:parts/header'); ?>

### Parsing Views
Any file ran through the themer's `display()` method has the option of being ran through the [Template Parser](http://www.codeigniter.com/userguide3/libraries/parser.html). This is turned off by default, but can be turned on globally by setting the appropriate config variable to true in `application/config/application.php`.

	$config['theme.parse_views'] = false;

If you need to control when it's parsing views and when it isn't, you can keep the config variable set to `false` and then call the `parseViews()` method of the Themer. The only parameter is a boolean that tells it whether to parse the views or not.

	$this->themer->parseViews(true);

Note that when parsing views, the `$themer` and `$uikit` objects are not available within the view files being parse.

### Caching Views

If you want to cache that particular view, perhaps due to it containing some callbacks that might be intensive, you can pass the number of **minutes** to cache the output for as the second parameter.

	<?= $themer->display('admin:parts/header’, [], 5); ?>

You can specify a custom name for the cache key by passing it in as the fourth parameter.

	<?= $themer->display('admin:parts/header’, [], 15, ‘anon-admin_header’); ?>

## Callbacks
Callbacks allow you to call other code from within your theme file. They allow you to easily include formatted modules, or simply to collect pieces of commonly used code from across your application into one place so that you don't have to keep coding that information.

For example, say you had a set of recent blog posts. That block of posts might show up on the home page, on the sidebar of the blog, and on the individual posts page. That's three different places in your controllers that you would need to remember to call the model and include this data as a view variable. Instead, we can create a callback that will load the model, grab the data, render it into a view for us, and spit out the formatted data.

### Calling the Callbacks
Callbacks are intended to be used within view files. They should return the HTML, not echo it out.

	<?= $themer->call('posts:recent show=10 order=title dir=asc'); ?>

The first parameter is a string that allows you to, very flexibly, define the class, the method and any number of named parameters that can be sent to the callback. The parameters are passed as an array of key/value pairs. In the case of this example, it would pass in the following array.

	$params = [
		'show'  => 10,
		'order' => 'title',
		'dir'   => 'asc'
	];

	class Posts {
		public function recent($params) {...}
	}

### Caching Callback Results
You can tell the system to simply cache the results for a period of time, instead of hitting the callback constantly. This is done by passing in the number of **minutes** to cache the results for. This will use the built-in CodeIgniter caching library that is setup in the BaseController, and defined in either the application config file or the controller itself. 

	// Cache it for 1 hour
	 <?= $themer->call('posts:recent show=10 order=title dir=asc', 60); ?>

You can specify a custom name for the cache key by passing it in as the third parameter.

	<?= $themer->call('posts:recent show=10 order=title dir=asc', 60, ‘anon-recent_posts’); ?>

### Creating Callbacks
Callbacks are simple classes. The system will attempt to locate them through Composer's autoload and, if that doesn't work, will try to load them as CodeIgniter libraries.

The methods must NOT be static methods.

The methods must return the data as a string. This will be cached, if desired, and echoed out directly the view file.


## Meta Tags
The ThemedController has an instance of `Myth\MetaCollection` available to any child controller as `$this->meta`. This provides a simple interface for collecting your desired HTML meta tags from anywhere in your controller.

### Setting An Item
You set an item using the `set()` method. The first parameter is the name of the meta tag, the second parameter is the value to set it to. 

	$this->meta->set('keywords', 'one,two,three');

You can set multiple items at once by passing in an array of key/value pairs as the first parameter.

	$meta = [
		'keywords' => 'one,two,three',
		'description' => 'Alphabet Street Songs'
	];
	$this->meta->set( $meta );
	
You can also set an item by assigning the value to the `$meta` object itself.

	$this->meta->keywords = 'one,two,three';

If you pass an array of items as the `value`, then it will be imploded and joined with a comma.

	$this->meta->keywords = ['one', 'two', 'three'];
	// Becomes 'one,two,three'

#### Auto-Escaping Tag Values

By default, all data is escaped to create a more secure usage of all values. This is done with the [esc()](general/views#auto-escaping_data) method that is used to sanitize data in the views. If you do not want the value to be escaped, you can pass `TRUE` as the third parameter to the `set()` method.

	$this->meta->set( 'tag', 'value', true);

### Setting Items In Config File
You can define default site-wide meta tags by entering them in the config array in `application/config/html_meta.php`. 

	$config['meta'] = [
	    'x-ua-compatible'   => 'ie=edge',
    	'viewport'          => 'width=device-width, initial-scale=1',
	];

These item will be loaded by default. You can override the values of any presets via the `set()` method as normal.

### Getting A Value
You can retrieve a value at any time with the `get()` method. The only parameter is the name of tag to get. 

	echo $this->meta->get('keywords');

Alternatively, you can retrieve it as if it's a property of the object. 

	echo $this->meta->keywords;

### Rendering Meta Tags
The ThemedController will ensure that the meta object is available within your themes as the `$html_meta` object. You can have it create the tags for you by calling the `renderTags()` method. 

	<?= $html_meta->renderTags() ?>

This will render out tags for each of the meta items you've specified. 

```
	<meta name="keywords" content="one,two,three" >
```

The `charset` item will get special treatment since it's not structured the same as others. 

```
	<meta charset="utf-8">
```

Also, any `http-equiv` tags will be rendered out appropriately.

```
	<meta http-equiv="cache-control" content="no-cache">
```

### Defining New HTTP-Equiv Tags
The class is only aware of a handful of http-equiv tags (cache-control, content-language, content-type, default-style, expires, pragma, refresh, and set-cookie). There are a large number of custom tags that can be used, though. To ensure they are output correctly, you should use the `registerHTTPEquivTag()` method. The only parameter is the name of the tag.

	$this->meta->registerHTTPEquivTag('custom1');

Now, when the tag is rendered, it will use the `http-equiv` instead of `name`.

### Define HTTP-Equiv Tags In Config File
You can also register site-wide http-equiv tags in the `application/config/html_meta.php` file.

	$config['http-equiv'] = [ 'x-dns-prefetch-control' ];
