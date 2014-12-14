# SettingsStoreInterface
Sprint comes with two SettingsStores -- one uses the database and the other config files -- but you could easily extend it to create a memcache store for faster storage if your site needed it. Each new Store must extends `Myth\Settings\SettingsStoreInterface` and implementing the five required methods.

## Groups
Settings can be organized into groups of items. Typically, these will be module names, but could be for any use. The default group is `app` which is the generic group used by much of the site's settings. The Interface must find a way to support working with groups of items. 

## Default Return Value
When a method doesn't have an answer, it should return FALSE. While NULL might be a more appropriate choice, when dealing with a key/value store like this, it would be valid to have NULL values stored for items.

## Required Methods

### save()
Inserts or Updates a single setting item. The first parameter is the `key` or alias of the item. The second parameter is the $value to set the item to. The third parameter is the group name. The group defaults to 'app'.

	public function save($key, $value=null, $group='app');

### get()
Retrieves the value for a single item. The first parameter will be the key name. The second parameter is the group name. 

	public function get($key, $group='app');
	
### delete()
Deletes a single key/value pair. This should be a permenant deletion not a "soft delete". The first parameter is the key name. The second parameter is the group.

	public function delete($key, $group='app');
	
### findBy()
Searches the value stores for one with matching requirements. For example, in the database store, you could search by group.

	public function findBy($field, $value);

### all()
Returns many items. If nothing is passed in as the first parameter, it should return all available configuration values. If a group name is passed as the only parameter, it should return all key/value pairs belonging to that group.

	public function all($group=null);


May not be relevant to all implementations.