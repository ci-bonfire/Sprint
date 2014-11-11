# Sprint Forge

## Collections
All generators are stored in groups called `collections`. A base collection ships with Sprint that includes a number of useful generators for creating models, libraries, even entire modules. However, you might need slightly different variations of the stock generators to better fit your workflow, match specific project needs, or meet company standards. You can do this easily by [creating new generators](forge/create_gens) in a new collection. 

You should never edit any of the stock generators directly. All your changes would be lost during Sprint upgrades. Instead, create a new folder somewhere on your system. It does not need to be within the application. For many types of generators you would not want to store them per project, but a central location that you could re-use for all of your projects. 

### Configuration
Next you need to edit the `application/config/forge.php` config file to let the system know where your template group is stored. You should put your group before the default Sprint group. When the system looks for templates to render, it looks in each folder, in turn, until it locates the file. By putting your group first, it will override the default templates. This also means that you only need to include the specific templates that you want to modify. 

	$config['forge.collections'] = [
		'personal' => '/full/path/to/new/generators/',
        'sprint'    => MYTHPATH .'generators/'
    ];