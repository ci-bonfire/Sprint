# API Generators

The API system provides 2 generators to aid in setting up and developing your own API.

* [Installer](#install)
* [Scaffold](#scaffold)

<a name="install"></a>
## Installer
Since Sprint is not designed solely to create API's you will need some setup to get the system ready to serve your API.  The installer command gets those files in place and ready for you.

You run the installer through the Forge: 

	$ php sprint forge api install

You are first prompted for the type of authentication you want to use, `basic`, `digest` or `none`. Based on your answer here, the `application/config/api.php` will have the correct authentication type set.

Next, if any modifications are needed to the database, migration files will be created for you. You will still need to run the migrations. For example, Digest authentication requires a new field in the user table. 

Finally, you will be prompted do you want to log the API requests? If you say yes, then it will create another migration to create the api_log table. 


<a name="scaffold"></a>
## Scaffold

This generators scaffolds a single API resource, based on an existing model file. It makes a few assumptions that  will be described through the flow of this generator. Throughout the example we will be scaffolding a `users` resource for the API.

	$ php sprint forge api scaffold

### Resource Name
The first element you are asked for is the name of the resource. This should be a plural word, like `users`. This will be part of the API endpoint, like `http://example.com/users`. 

It will create a controller named `Users`. It will look for a model named after the single version of the word (as determined by the [inflector helper](http://www.codeigniter.com/user_guide/helpers/inflector_helper.html). In our example, it would look for `User_model`. This is used to grab the fields in the table and used throughout the controller. 

The controller has all methods in place to implement very basic versions of the endpoints created by the [Route::resources()](http://sprint.dev/docs/developer/general/routes#http_verb_routing). Of special note is a private method called `formatResource()` that is used by all of the other methods to take the raw data from the database and format and cast the output array. You will need to modify this for your exact needs since there will likely be fields in the database that are not intended to be returned in the API.


### Version
Next, you'll be prompted for the `version` this should be included in. This assumes that each version of the API is stored in a sub-folder in the controllers folder. The default value is `v1`.  While there are many ways to handle the versioning of an API, this is the simplest method to handle it and is the only one supported by the generator. If you need to customize this, you should copy the generator into your own [collection](http://sprint.dev/docs/developer/forge/overview#collections) and modify as needed. 

In our example, this creates a URI like `http://example.com/v1/users`. 

### API Blueprint
The next question is to ask whether you want to generate a simple start for documenting this class based on the [API Blueprint](https://apiblueprint.org/) schema. If you say yes, then a new file will be created in the docs folder under the api version: `application/docs/api/v1/users.md`. 

### Model
Finally, if the generator was unable to locate your model, it will prompt you for the model name and try to find it. If it doesn't exist, the script will end here without generating anything. 

## Scaffold Actions
The following actions are all of the possible effects this generator has. Depending on your answers throughout the generator, the exact files generated may be different. All items below assume a version of `v1` and a resource named `users`.

* application/controllers/v1/Users.php
* application/language/english/api_users_lang.php
* application/docs/api/v1/users.md
* MODIFY: application/docs/_toc.ini to include a link to the new file
* MODIFY: application/config/routes.php to include `$route->resources('v1/users')`
* MODIFY: application/config/api.php 
* Create XXXX_Create_api_log_table.php migration file


