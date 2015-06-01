# Views

Sprint uses CodeIgniter's standard views to form the basis of its Template system. The documentation in here only applies when using the default `ViewThemer` engine.

## Using the Template System
In order to use the view and template methods in this document, your controller must extend `Myth\Controllers\ThemedController` or one of its children.

	class SuperHeroController extends \Myth\Controllers\ThemedController
	{
	    ...
	}

## Views
The system expects your views to be follow some conventions to simplify organizing your view files and make the whole a little easier and cleaner to use. The following conventions are used when trying to determine the view file that should be rendered:

* The file must be under the `application/views` folder or the `views` folder within a module.
* It must be in a folder named after the `controller`.
* The name of the view file must match the `method name` that it is associated with.

If you were in the `members` controller, and the `profile()` method, the system would expect the file to be at

	application/views/members/profile.php

If your controller is in a sub-folder, the views should follow that same structure. So if that members controller was under the `admin` folder, the view would then be found at

	application/views/admin/members/profile.php

### Specifying Which View
If you need to use a view that is named differently than the conventions above you can tell it which view to use with the `setView` method.

	$this->themer->setView('alternate/path/to/my_view');
 
Your view must be relative the `application/views` folder, a `module views` folder, or a full system path to anywhere on your server that your script can access.

### Displaying The View
When using ThemedController you should use the new `render()` method to display your views in place of the traditional `$this->load->view()` that you’re used to. This will automatically detect the correct view (as described above) and hooks into the Theme system (described below).

	$this->render();

You can optionally pass in custom data to display, like with the traditional method, as the first parameter. This makes the key/value pairs available in the view for you to use.

	$data = ['user' => $user];
	$this->render($data);

If you want this view to be cached, but not to use the full page caching that CodeIgniter provides for some reason, you can pass in the number of seconds to cache the rendered view by as the second parameter.

	// Cache for 5 minutes
	$this->render($data, 300);

### View Data
While you can collect data into a single variable to pass into the render method, it is often convenient to prepare data for the view from different methods. You can do this with the `setVar()` method.

The first parameter is the name that you want the value to be called in the view itself. The second parameter is the value itself.

	$this->setVar('user', $user);

If the first parameter is an array, the `$value` parameter will be ignored and all of the values in the `$name` array will be treated key/value pairs to make available in the view.

	$data = [
	    'user'     => $user,
	    'location' => $location
	];
	$this->setVar($data);

### Auto-Escaping Data
To help protect your site against malicious entries, all strings set by `setVar()` are escaped using [Zend Framework's Escaper](http://framework.zend.com/manual/current/en/modules/zend.escaper.introduction.html). If an array is passed, then any string values within that array will be escaped. Other forms of variables are left untouched.

Whenever you use the `setVar()` method, you should consider the context that you are using this variable, in order to maintain good secure best practices on your site.  You should pass the context in as the third parameter. 

	$this->setVar('user', $username, 'html');

The Escaper recognizes the following contexts: 

- [html](http://framework.zend.com/manual/current/en/modules/zend.escaper.escaping-html.html) - escape a string for use within the HTML Body.
- [htmlAttr](http://framework.zend.com/manual/current/en/modules/zend.escaper.escaping-html-attributes.html) - escape a string for use within an HTML attribute
- [js](http://framework.zend.com/manual/current/en/modules/zend.escaper.escaping-javascript.html) - escape a string for use within variables and data within dynamic javascript
- [css](http://framework.zend.com/manual/current/en/modules/zend.escaper.escaping-css.html) - escape a string for use within variables and data in dynamic CSS 
- [url](http://framework.zend.com/manual/current/en/modules/zend.escaper.escaping-url.html) - escape a string for use within data being inserted into a URL, and not entire URL's.

To create a secure application, with industry-standard protection against, read through each of the linked articles above and ensure that you understand how to escape data, and within what context you are operating, otherwise, many other security measures could prove invalid. 

NOTE: This escape is meant to replace the use of CodeIgniter's `xss_clean()` functionality.  

If you don’t want a certain variable to be auto-escaped, then you can pass if `false` as the fourth parameter.

	$this->setVar('user', $user, 'html', false);

You can turn this feature off globally by editing `application/config/application.php` and turn the `auto_escape` feature off. **This is not recommended unless you are prepared to ensure that every use of untrusted data within your application is covered.**

	$config['theme.auto_escape'] = false;

If you have specific data that you want to escape within your view, you can use the `esc()` function, which is the same function that is used by the ThemedController to handle the escaping.  The first parameter is the data to escape. The second parameter is the context (see above).

	// In a View
	<?= esc($username, 'htmlAttr') ?>

Note that any data passed into the `render()` method is also auto-escaped. **Since the script has no way of knowing the context this data will be used in, it is assumed that all data is going to be used within the HTML body. If any data passed here is not to be used in an HTML context, you are creating potential XSS security holes.**

	// $data is escaped
	$this->render($data);

### Overriding Module Views
If you need to override the views of any module you can do so by creating a new view in the appropriately-named sub-folder within the `application/views` folder, which will be used instead of the one from the module.

For example, if you want to customize the login or register views, which are located in the Auth module, you would need to create new views at: 

	application/views/auth/login.php
	application/views/auth/register.php

Additionally, themes can contain files that will override the application's view files, permitting themes to easily customize nearly any part of the site. All that you need to do is to provide a folder  and view within the theme file and it will automatically be used. This applies to variants as well as standard views.

	themes/bootstrap3/auth/login.php
	themes/bootstrap3/auth/register.php

## Variants
Variants are different versions of a single view that must be presented at different times. This is most commonly done to provide customized layouts or views for mobile phones or tablets.

Imagine you have a `members` controller, `profile` method and you want to customize the views for cell phones and tablets to make the best use of the devices. You would start with the desktop version, with a view named `members/profile.php`. The phone and tablet varieties should then be named:

* tablet - `members/profile+tablet.php`
* phone - `members/profile+phone.php`

These files could have completely different layouts, if needed, and could each specify only the CSS and Javascript it needed.

Out of the box, that's all that variants are used for. However, don't let that stop you from using it for any purpose you can think of. You might have different theme variants that users can choose from, or customize the content shown based on whether the current user is an administrator, etc.

### Available Variants
If you would like to add additional variants for the system to recognize, you can modify the `application/config/application.php` file as needed. The key in the `theme.variants` setting is a name it can be referenced by. The value in the array is the postfix to add to the end of the file (but before the file extension).

	$config['theme.variants'] = array(
	    'phone'  => '+phone',
	    'tablet' => '+tablet',
	);

### Auto-Detecting Variant
If you would like Sprint to attempt to automatically determine which variant to use (between desktop, phone or tablet only) modify the `theme.autodetect_variant` config setting in the same file.

	$config['theme.autodetect_variant'] = true;
 
If TRUE, this will use the [Mobile Detect][1] library to determine. This library is built buy the gang at [BrowserStack][2] and kept up to date.

If you need to modify how this works (by adding browser or OS detection, for example) you will need to modify the `__construct()` method of the `ThemedController` file. Or you can turn off autodetection and do your own detection routine in `MY_Controller`.

[1]:	http://mobiledetect.net/
[2]:	http://www.browserstack.com/