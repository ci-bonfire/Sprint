# Views

Bonfire uses CodeIgniter's standard views to form the basis of its Template system. The documentation in here only applies when using the default `ViewTemplate` engine. See [Custom Template Engines](template_engines.md) for more information about using other template systems with Bonfire.

## Using the Template System
In order to use the view and template methods in this document, your controller must extend the `ThemedController` or one of its children, like the `FrontController` or `AdminController`.

	class SuperHeroController extends \Bonfire\Libraries\Controllers\ThemedController
	{
	    . . .
	}

## Views
The system expects your views to be follow some conventions to simplify organizing your view files and make the system a little easier and cleaner to use.  The following conventions are used when trying to determine the view file we should render: 

* The file must be under the `application/views` folder or the `views` folder within a module.
* It must be in a folder named after the `controller`.
* The name of the view file must match the `method name` that it is associated with.

If you were in the `members` controller, and the `profile()` method, the system would expect the file to be at

	application/views/members/profile.php

If your controller is in a sub-folder, the views should follow that same structure. So it that members controller was under the `admin` folder, the view would then be found at

	application/views/admin/members/profile.php


### Specifying Which View
If you need to use a view that is named differently than the conventions above you can tell it which view to use with the `setView` method.

	$this->template->setView('alternate/path/to/my_view');
 
Your view must be relative the `application/views` folder, a `module views` folder, or a full system path to anywhere on your server that your script can access.

### Displaying The View
When using ThemedController you should use the new `render()` method to display your views in place of the traditional `$this->load->view()` that you’re used to. This will automatically detect the correct view (as described above) and hooks into the Theme system (described below).

	$this->render();

You can optionally pass in custom data to display, like with the traditional method, as the first parameter. This makes the key/value pairs available in the view for you to use. 

	$data = ['user' => $user];
	$this->render($data);

### View Data
While you can collect data into a single variable to pass into the render method, it is often convenient to prepare data for the view from different methods. You can do this with the `setVar()` method.

The first parameter is the name that you want the value to be called in the view itself. The second parameter is the value itself.

	$this->setVar('user', $user);

If the first parameter is an array, the $value parameter will be ignored and all of the values in the $name array will be treated key/value pairs to make available in the view. 

	$data = [
			'user' => $user,
			'location' => $location
	];
	$this->setVar($data);


## Variants
Variants are different versions of a single view that must be presented at different times. This is most commonly done to provide customized layouts or views for mobile phones or tablets. 

Imagine you have a `members` controller, `profile` method and you want to customize the views for cell phones and tablets to make the best use of our devices. You would start with the desktop version, with a view named `members/profile.php`. The phone and tablet varieties should then be named: 

* tablet - `members/profile+tablet.php`
* phone - `members/profile+phone.php`

These files could have completely different layouts, if needed, and could each specify only the CSS and Javascript it needed.

Out of the box, that's all that variants are used for. However, don't let that stop you from using it for any purpose you can think of. You might have different theme variants that users can choose from, or customize the content shown based on whether the current user is an administrator, etc.

### Available Variants
If you would like to add additional variants for the system to recognize, you can modify the `application/config/app.php` file as needed. The key in the `template.variants` setting is a name it can be referenced by. The value in the array is the postfix to add to the end of the file (but before the file extension).

	$config['template.variants'] = [
	    'phone' => '+phone',
	    'table' => '+tablet'
	];

### Auto-Detecting Variant
If you would like Bonfire to attempt to automatically determine which variant to use (between desktop, phone or tablet only) modify the `autodetect_variant` config setting in the same file.

	$config['autodetect_variant'] = true;
 
If TRUE, this will use the [Mobile Detect](http://mobiledetect.net/) library to determine. This library is built buy the gang at [BrowserStack](http://www.browserstack.com/) and kept up to date. 

If you need to modify how this works (by adding browser or OS detection, for example) you will need to modify the `__construct()` method of the `ThemedController` file. Or you can turn of autodetection and do your own detection routine in `MY_Controller`. 
