# Site Restructuring For Security
By default, Sprint comes with a flat directory structure that makes it simple to drop into any web root -- whether localhost or a primary domain on cPanel hosting -- and have it up and running quickly. It works simply for both newer developers and more experienced developers. But it does have its drawback from a security standpoint.

> DISCLAIMER: I am, by no means, a security expert. Any knowledge that I have has been gathered from reading articles from people smarter and more experienced than I am. If you know that I've explained something incorrectly and can tell me the correct solution, or can point to research that invalidates anything said here, please feel free to drop me a line and correct me. I'll make sure to read through it and try to correct either my docs or the code itself. 

## What Are The Security Risks
The largest risk is that of a server mis-configuration. It can easily happen, and has happened to even [large corporations like Facebook](http://blog.eun.org/drizzdo/2007/08/facebook_source_code_exposed.html). While typically not devastating, a misconfigured server can send raw source code as text to the browser, instead of running the PHP code itself. 

This has two primary ramifications: 

- **Source Code Scanning** - outsiders can scan the code the determine things about the site, like the structure, potentially the framework used, etc. All of this information can then be used to determine a plan of attack against the site if the outsider wishes harm. Any known security issues with the framework can then be put into play once they've determined the system it's built on.
- **Configuration Revealed** - Potentially worse, is that they could see the configuration settings of your application and get your database information, or the API settings for your credit card processor, your email service, and more. 

While sites have run in the web root for as long as the web has been around, when security is a concern -- which it should be for any site that allows user login -- the more difficult you make it for an intruder to gain any information about your site, the more difficult is it for them to hack it.

## What Is The Solution?
The best solution against this type of mistake is to move as much of your site's code out of the web root as possible. You will need to keep the site's index file and any assets (stylesheets, javascript, images, etc) in the web root, but all other folders can be placed up a level, just outside of web root. To make that work, you would need to modify the following items.

### Index File - Folder Locations
In the main index file (the one that is now in the web root, or public, folder), you need to tell it where it can find a few items, like the rest of the code...

- **autoloader** - modify the include path of the autoloader to point to the new location ( `../vendor/autoload.php` )
- **system_path** - `../system`
- **application_folder** - `../application`

### Module Locations
The module locations currently use FCPATH to locate the `myth` folder. Open up `application/config/config.php` and modify the locations as needed. 

	$config['modules_locations'] = array(
	    APPPATH .'modules/'             => '../modules/',
    	APPPATH .'../myth/CIModules/'   => '../../myth/CIModules/'
	);

### Theme Locations
Similar to the module locations, the themes will need to be relocated. In default Sprint, there is no magic assets handler. If you've implemented something like that you can, and should, leave the themes out of the web root. Otherwise, you will need to move your `themes` folder into the web root so that the assets can be found that are specific to the theme. 

Then, open up `application/config/application.php` and modify the theme locations to point to the new location.

	$config['theme.paths'] = array(
        'bootstrap'  => APPPATH .'../public/themes/bootstrap3',
        'foundation' => APPPATH .'../public/themes/foundation5',
        'docs'       => APPPATH .'../public/themes/docs'
    );
   
### sprint File
The `sprint` CLI script will also need to have the `$apppath` variable modified to point to the new APPPATH location.