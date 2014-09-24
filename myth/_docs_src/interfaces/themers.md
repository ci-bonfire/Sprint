# ThemerInterface

You can incorporate third part theme engines into the system very easily by extending `Myth\Interfaces\ThemerInterface` and filling in the required methods. This should allows you to fairly easily incorporate [Plates](http://platesphp.com/), [Twig](http://twig.sensiolabs.org/) or the template engine of your choice, and not have to change much, if anything, in your controllers. Views might still need some tweaking to work with the new system. 

In order to get up to speed as fast as possible, you should examine `myth/Controllers/ThemedController.php` and `myth/Themers/ViewThemer.php`. This will show you how the pieces fit together. This guide simply provides details about the interface and the role of each method.

## Required Methods

### render()
This is the main function that is called from a controller. It is reponsible for rendering the layout and returning the output to the calling function. DO NOT echo the resulting content out, but return it as a string instead.

The only parameter this method accepts in the name of an alternate layout file to use.

### content()
Renders out the individual view for that controller/method. This can be called from within the theme files themselves, or, as is done in the ViewThemer, called from the render() method and stored in a variable that is passed onto the view. 

The method should return the contents of the rendered view as a string.

### setTheme()
Sets the theme to use when the `render()` method is called. Called by the `ThemeController` render method, if the controller has set a different theme to use.

### theme()
Returns the active theme.

### setDefaultTheme()
Sets the default theme to use if no other theme has overriden. Called by the `ThemeController` to set the theme to use. 

### setView()
Sets the current view to use when running the `content()` method. This is unused in the default ThemeController, but users can use it to override the default view selection, if you provide that in your Themer.

### view()
Returns the current view that will be used during the `content()` method.

### setLayout()
Sets the current layout to use when running the `render()` method. Must be the name of one of the layout files within the current theme. IS case-sensitive.

### layout()
Returns the name of the current layout file that will be used.

### set()
Sets data to make available to the view files. Should be able to handle a key and value passed in as two separate parameters, or an array of key/value pairs. The key is the name that the user expects the data available as within the view. 

### get()
Returns the variable that was passed in earlier. The only parameter is the key to return the value of.

### parseViews()
While this method must be implemented, not every themer will use it. It allows the user to specify that they want additional parsing done on the view. By default, it would expect to use CodeIgniter's parser to be used, but this could be specified in your themer.

### addThemePath()
Adds a new path for the themer to look at for a theme. The first parameter is the alias that the theme will be accessed by. The second is the path where the layout files can be found at.

### removeThemePath()
Accepts the alias of the theme and removes it from the available themes.

### display()
Used from within layout and view files to display other, theme or non-theme, view files. Expected to be able to parse the theme name out of the passed in file. In this case the string passed in as the only parameter would have the theme name followed by a colon, then the theme file to render. 

	echo $this->themer->display('admin:navigation');

Often used for displaying view partials. 

### setVariant()
Sets the variatn used when creating view names. Variants can be anything, but by default, are used to render specific templates for desktop, tablet, and phone. The name of the variant is added to the view name.

	$this->setVariant('phone');
	$this->display('header');
	// Tris to display "views/header+phone.php".
	
The available variants are defined in the `config/application.php` file. 

### removeVariant()
Removes a variant from the list of available variants.