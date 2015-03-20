# Users
Users in the system are stored using the same generic [CIDbModel](general/models) that is made available to you. This means that you have all of the standard CRUD methods and power/flexiblity that class allows. In addition this guide provides references to common tasks you will take with users. 

## Meta Information
You can store meta information about users, like profile fields (first name, website, etc) or other data that is common across your application, but too specific for Sprint to provide.  In many cases this is handled automatically for you when you insert or update a single user record. The User_model stores meta information in the `user_meta` table. 

The exact fields that should be considered meta fields are specified in the user model itself. 

	protected $meta_fields = ['first_name', 'last_name'];

	$user = [
		'username' => 'darth',
		'email' => 'darth@theempire.com',
		'first_name' => 'Darth',					// Meta field
		'last_name' => 'Vader'						// Meta field
	];
	// Stores both normal and meta data for a new user
	$uid = $this->user_model->insert($user);

### Retrieving User Meta
If you're getting a user from the database at the same time that you need the meta information, you can use the `withMeta()` method of the User_model to automatically retrieve the user's meta data along with the normal data. 

	$user = $this->user_model->with_meta()->find( 124 );
	// Returns: 
	[
		'username' => 'darth',
		'email' => 'darth@theempire.com',
		'first_name' => 'Darth',
		'last_name' => 'Vader'	
	]

#### getMetaItem()
If you just need to retrieve a single meta value for a user, this method is your answer. The first parameter is the ID of the user. The second parameter is the name of the meta data item to retrieve. 

	$last_name = $this->user_model->getMetaItem(124, 'last_name');

If the key doesn't exist in the database for that user, it will return `null` instead.

### Saving Single Meta Items
When you just need to save a single meta item to a user, you can use the `saveMetaToUser()` method. The first parameter is the ID of the user. The second parameter is the field name. The third parameter is the value.

	$this->user_model->saveMetaToUser(124, 'son', 'Luke Skywalker');

Before this method runs, the event `beforeAddMetaToUser` is triggered.

### Deleting One or More Meta Items
When you need to delete a single meta item from a user, you can use the `removeMetaFromUser()` method. The first parameter is the ID of the user. The second paramter is the name of the key you want to remove. This can also be an array of keys, if you need to delete multiple items.

	// Delete single item
	$this->user_model->removeMetaFromUser(124, 'son');
	// Delete multiple items
	$this->user_model->removeMetaFromUser(124, ['son', 'mother', 'father'] );

Before this method runs, the event `beforeRemoveMetaFromUser` is triggered.
