# Settings
The Settings library allows you to manage settings across multiple datastores. By default, Sprint ships with two Stores, Database and Config. 

* The **DatabaseStore** is a full-featured key/value store that allows the settings to be organized into "groups". Typically, this will be by module, but you could use the organization in any way that your app would need. 
* The **ConfigStore** allows you to read and (temporarily) change settings found in config files. 

Together, these form a very flexible method of managing your site settings. For those settings that will change infrequently and don't need user-facing settings, you can store them in the config files and not suffer a database hit as well as making them easily versioned. For settings that might frequently be changed by the end user you can store the values in the database. And you can store default values in config files, and then user-assigned versions in the database. When you go to get a setting, it will first attempt to find it in the database and then fall back to the config files if the setting isn't found. 

## Loading the Settings Library
The `Myth\Settings\Settings` class is full of static methods so you do not need to instantiate the class. The first time it is used, it will load it's settings and instantiate the data stores. 

## Configuration
You can assign the datastores that are to be used as well as the order of searching and the default store in the `application/config/application.php` config file. 

### Available Stores
You can assign any fully-namespaced class that implements the [Myth\Interfaces\SettingsStoreInterface](interfaces/settings) as an available store in the `settings.stores` array. The `key` is the alias the store can later be referenced by. The value is the full name of the class, including namespace if any.

	$config['settings.stores'] = [
        'db'        => '\Myth\Settings\DatabaseStore',
        'config'    => '\Myth\Settings\ConfigStore'
    ];
   
The order the stores are defined in is the order they will be searched in.
   
### Default Store
Assign the default store to use if none is specified. Primarily used when saving items or the findBy method. 

	$config['settings.default_store'] = 'db';

## Using the Library

### Retrieving A Setting
To get a setting you would use the appropriately named `get()` method. The first parameter is the item's name. 

	use Myth\Settings\Settings as Settings;
	$email = Settings::get('site.email');
	
The library will scan through the list of stores attempting to find the specified item. With the defaults it would first check in the database, then in the loaded configuration files.  

You can specify a specific group the setting would belong to by passing the group name in as the second parameter. This allows you to avoid naming collisions across groups. Will return false if the item cannot be found.

	$email = Settings::get('site.email', 'app');
	
When a group is specified and the item cannot be found in the loaded configuration files, it will attempt to load a config file with the same name as the group and look in that file for the results.

If you know which datastore you want to pull the item from, you can specify the alias of the datastore as the third parameter.

	$email = Settings::get('site.email', 'app', 'config');

### Saving An Item
You can save a new item, or update an existing item, with the `save()` method. The first parameter is the name  of the item. The second parameter is the value to set it to. The third parameter is the group name. The fourth parameter is the datastore. If left blank, it will use the default datastore. By default, this means it would save it to the database. 

	Settings::save('site.email', 'darth.vader@theempire.com');

When saved without a group specified, the default group, `app` will be used. 

Not every store supports saving. The `ConfigStore` only supports overriding the value, or setting a new value, during the current page request. It will not save it out to the config file persistently.

### Deleting An Item
You can delete an item with, you guessed it, the `delete()` method. The first parameter is the name of the item. The second parameter is the group name (defaults to 'app'). The third parameter is the datastore to use. If no datastore is specified, will delete it from the default datastore.

	Settings::delete('site.email');

Not all stores support deleting. The `ConfigStore`, for example, does not support this functionality. 
