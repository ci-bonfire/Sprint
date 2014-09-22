# Writing Your Own Documentation

Sprint makes including documentation with your application, or even just one of your custom modules, as simple as including some text files.

## Docs Locations

To create application-specific documentation that can easily be versioned and shipped out with your application, simply place [Markdown](http://daringfireball.net/projects/markdown/)-formatted text files in the `application/docs` folder. (Technically, we use [Markdown Extra](http://michelf.ca/projects/php-markdown/extra/) for even more formatting possibilities).

For any modules that you create and want to create documentation for, just place the same Markdown-formatted docs in the module's folder, under a new folder named `docs`.

    my_module/
        css/
        docs/
        . . .

The files must have the file extension of `.md` in order to be recognized by the system. When a page is displayed, the Document Map in the sidebar will automatically scan your docs and generate the map based on the `<h2>` and <`h3>` tags in the document.

### Documentation Groups
By default, Sprint ships with documentation in two groups, one for your application and one for Sptint's docs. These can be found at `application/docs` and `myth/_docs_src` respectively. These groups are the items you see in the topbar when viewing the default documentation. You can create your own groups to further customize your documentation.

The settings are located in the docs module, `myth/CIModules/docs/config/docs.php` configuration file. To make changes to this you should consider copying this file to the `application/config` folder.

	$config['docs.folders'] = [
	    'application'   => APPPATH .'docs',
    	'developer'     => APPPATH .'../myth/_docs_src'
	];

The keys of the array determine the URI segment they will be displayed at, as well as the name that is displayed in the topbar. The value of the array items is the folder where the doc files can be found.


## Table of Contents

Documentation requires a Table of Contents file to be used. This allows for specifying custom names for the files, as well as splitting doc files into logical groups that you specify, though only one level deep.

To use a TOC, create a file called `_toc.ini` within your docs folder. This file will be used to display the links, instead of auto-generating the links from the existing files. This file is a standard PHP .ini file. The "options" within the file are the filename and the display name. The filename is listed on the left of the '=' with the text used to display the link listed on the right.

    my_page = My Great New Documentation Package

To group the files and provide a header, you would use the .ini's section syntax.

	[Section 1]
	my_page = My New Documentation Package

The filename must include in it's "path" the area the documentation came from, either 'application', 'developer', or your module's folder name.

    application/my_page = My New Documentation Package
    my_module/my_page   = My New Module Docs
    
## Configuring Documentation
The docs system allows you to do some simple customization that allows you to integrate it into the needs of your application easily. The system uses 2 groups 'application' and 'developer' to separate your application specific documentation from Sprint's core documentation.

All documentation config settings can be found in the module's config file at `myth/CIModules/docs/config/docs.php`. 

### Setting Theme
To specify a theme to be used only for the documentation, set the `docs.theme` setting. This allows you to completely customize the how the information looks and is displayed to match your branding, ad needs, etc.

    $config['docs.theme'] = 'docs';
    
### Default Group
You can specify any of the available docs groups to be the default group displayed when a user simply views `/docs` on your site.

	$config['docs.default_group'] = 'developer';

### File Extension
The docs system requires that your files be in Markdown formatting, but you can use any file extension that you choose. Note, though, that it only recognizes a single extension, so if you change it, you must change the file extension for all of the files within `bonfire/docs` also.

	$config['docs.extension']    = '.md';

### Permitted Environments
You can restrict your documentation to only certain [environments](http://ellislab.com/codeigniter/user-guide/general/environments.html). This is handy if you're creating docs for your development team but don't want it to accidentally show up on your production site.

	$config['docs.permitted_environments'] = array('development', 'testing', 'production');