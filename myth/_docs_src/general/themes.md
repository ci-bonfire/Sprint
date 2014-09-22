# Themes

Themes provide a simple, yet very flexible, way of providing consistent interfaces to your web pages. Instead of specifying all of the generic HTML for every type of page on your site, you can create a layout that then pulls in the proper parts to create a single page. All of this then wraps around your page content.

## Theme Setup
Themes are simply a collection of layout views and their assets that are used to determine the overall look of a page. They are the wrappers around your [Views](general/views). 

### Theme Locations
Bonfire currently stores themes in the same root folder as the rest of the project, alongside the `bonfire` and `application` folders.  All themes are expected to live there. Each theme is required to have an entry in the `config/app.php` configuration file.  

	$config['template.theme_paths'] = [
    	'admin' => APPPATH .'../themes/admin',
	    'docs'  => APPPATH .'../themes/docs'
	];

The keys in the array are the name they are referenced by when removing the them or using the `display` method (see below). These names must match the folder name within the themes folder in order for the views to be found.

If you need to add configuration items on the fly, you will need to modify that config entry since it is used by a couple of different libraries. 

### Template Engines
Bonfire's Template system is expandable to use most any PHP template library out there. To do so you would create a new class that extends from the [Bonfire\Interfaces\TemplateInterface](interfaces/template) class. The class is then instantiated at run time using the class that is set in the `config/app.php` configuration file.

	$config['di.templateEngine'] = 'Bonfire\Libraries\ViewTemplate';
	
The default engine is the `ViewTemplate` that simply uses stock CodeIgniter views to build the layout with no special code needed. 

## Using Themes

In order to use the theming system, your controller must extend the `ThemedController` or one of its children, like the FrontController or AuthenticatedController. This provides a set of simple methods to make using themes simple.

### render()
The `render()` method is the primary means of interacting with the system. Use it to display the final page to the user. It should be the last action in your controller. 

	$this->render();

You can pass an array of key/value pairs as the only parameter. Those variables will be availalbe in the views, just like a standard Codeigniter view. More information about the views and view data can be found under the [Views](general/views) guide.

### Flash Messages
ThemedController also provides an extended version of the CodeIgniter's flash messages that allow it to work in the current page view, and not require the page to be refreshed for the session data to be available.

You add a message with the `setMessage()` method. The first parameter is the message itself. The second parameter is the message type, like `info`, `warning`, etc. These will be applied as a class to the surrounding alert div when displayed on screen, and should match the values of the CSS framework you are using, if any.

	$this->setMessage( validation_errors(), 'warning');

Within your views and layouts, you can use the `message()` method to retrieve the current message data. This is done for you, though, by the `render()` method. It is available within your views and layouts as the `$notice` variable, already formatted and ready to be displayed.

### Themed Views
While you can always use CodeIgniter's `$this->load->view()` method within your views, it is recommended that you use the Template library's `display()` method instead. This helps to respect caching rules, and makes
it simple to grab view fragments from the theme folder.

The template engine is available from within views as the `$template` variable and provides full access to all of the template engine's methods.

To display a non-themed view, you just need to pass in the name of the view that you want to render as the first parameter. 

	$template->display('my_view');
	
If you need to pass in a specific set of data, then you can pass in an array of key/value pairs as the second parameter. 

	<?= $template->display('my_view', $data); ?>
	
To display view fragments from within your theme itself, you will prefix the name of the theme, followed by a colon, to the view name. 

	<?= $template->display('admin:parts/header'); ?>


## Creating Themes
