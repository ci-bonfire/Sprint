# Enhanced Routes

CodeIgniter's Router is showing it's age against the powerful solutions found in other PHP frameworks like Laravel. Sprint's Routing is meant to help bring CodeIgniter into the modern age, or at least help it take a step in that direction.

The Route library is the core of the new flexibility. It is inspired by Jamie Rumbelow's excellent [Pigeon](https://github.com/jamierumbelow/pigeon) class, as well as [Laravel's routing system](http://laravel.com/docs/routing).

## Initializing the class
This class is automatically loaded for you in the router and you should not have need to do it yourself. If you do, though, it is found through the autoloader: 

	$route = new \Myth\Route();

## Basic Routes
When creating your routes file, you have the option of sticking with the standard CodeIgniter routes array, or using Sprint's `Route` library. The Route library provides enhanced features, including named routes, grouping, creating `areas`, and more.

When using the Route library, you use the `any()` method in place of adding to the `$routes` array.

	$route->any('from', 'to');

This is identical to the old:

	$routes['from'] = 'to';
	
Optionally, you can pass in an array of options as the third paramater. Currently, there are 3 global options that can be used for nearly any of the Route libraries methods. 

## Global Route Options
With the exception of the `group()` method, all of the route-generating methods can be passed an array of options as the last parameter. The available options are:

### Named Routes
`as` saves this route under the name of your choice. This allows you to modify the route in your routes file, without breaking any code throughout your application.

	$route->any('login', 'users/login', ['as' => 'simple_login']);
	// Can then be used later with the `named` method
	redirect( Route::named('simple_login') );

### Parameter Offsets
 `offset` allows you to increment the numbered placeholders in the 'to' portion of the route by the amount you specify. This is occasionally handy when you are pulling a previous number out of the URL, but don't want it sent to your methods, such as in API versioning. Also handy when grouping routes or routing  a resource.
 
 	$route->get('users/(:num)', 'users/show/$1', ['offset' => 1]);
 	// Creates:
 	$route['users/(:num)'] = 'users/show/$2);

### Subdomains
`subdomain` allows to you restrict routes to one or more subdomains. This only references the FIRST subdomain, so that's something to watch out for if your site has multiple subdomains. Can be used for language detection (`en.example.com`) or detecting a mobile version of a site (`m.example.com`).

	$route->get('users/(:num)', 'users/show/$1', ['subdomain' => 'en']);

You can also pass an array of subdomains to allow that route on ANY of those subdomains. 

	$route->get('users/(:num)', 'users/show/$1', ['subdomain' => ['en', 'fr'] ] );

If you want to ensure that a route shows up on any subdomain, but NOT on a URI without a subdomain, pass in `'*'` as the subdomain to match. Note that this only works on domains with two parts, like `example.com`. This will return false positives when more parts exist, like `example.co.uk`.

	$route->get('users/(:num)', 'users/show/$1', ['subdomain' => '*']);

## HTTP Verb Routing

To make building REST-based routing simpler and more consistent, you can use the

    $route->resources('controller_name');

This function will automatically create RESTful resources for the common HTTP verbs. In this example, `controller_name` is the name of the controller you want to map the resources to. If you controller is named `photos`, you would call it like:

    $route->resources('photos');

If the `photos` controller is part of the `Gallery` module, then you would route it like:

    $route->resources('gallery/photos');

This would map the resources to the `Photos` controller, like:

HTTP Verb   |  Path             |  action   |  used_for
------------|-------------------|-----------|----------------
GET         | /photos           | index     | display a list of photos
GET         | /photos/new       | creation_form | return an HTML form for creating a new photo
POST        | /photos           | create    | create a new photo
GET         | /photos/{id}      | show      | display a specific photo
GET         | /photos/{id}/edit | editing_form      | return the HTML for editing a single photo
PUT         | /photos/{id}      | update    | update a specific photo
DELETE      | /photos/{id}      | destroy   | delete a specific photo
OPTIONS  | /photos 			| index			| Showing information about the API request.

### Single Verbs

You can also set a single verb-based routes with any of the route methods:

    $route->get('from', 'to');
    $route->post('from', 'to');
    $route->put('from', 'to');
    $route->delete('from', 'to');
    $route->head('from', 'to');
    $route->patch('from', 'to');
    $route->options('from', 'to');

These routes will then only be available when the corresponding HTTP verb is used to initiate the call. Each of these methods accepts an options array as the third parameter. See Global Route Options, above.

### Multiple Verbs
If you need to match multiple verbs against a single route, you can use the `match()` method. This is just like the single verb usage (`get`, `post`, etc) but accepts an array of verbs to match as the first parameter.

	$route->match( ['get', 'post'], 'from', 'to');

### Customizing Resourceful Routes

While the standard naming convention provided by the `resources` Route method will often serve you well, you may find that you need to customize the route to easily control where your URL's route to.

#### Specifying a controller to use

You can pass an array of options into the `resources` method as the second parameter. By specifying a `controller` key, you will tell the router to replace all instances of the original route with the defined controller, like:

    $route->resources('photos', ['controller' => 'images'] );

Will recognize incoming paths beginning with `/photos` but will route to the `images` controller:

#### Specifying the module to use

You can also specify a module to use in the options array by passing a `module` key. This is helpful when the module and controller share different names.

    $route->resources('photos', ['module' => 'gallery', 'controller' => 'images'] );

Will recognize incoming paths beginning with `/photos` but will route to the `gallery/images` module and controller.

#### Constraining the {id} format

By default, the {id} used in the routing allows any letter, lower- or upper-case, any digit (0-9), a dash (-) and an underscore(_). If you need to restrict the {id} to another format, you may use the `constraint` option to pass a new, valid, format string:

    $route->resources('photos', ['constraint' => '(:num)'] );

 Would restrict the {id} to be only numerals, while:

    $route->resources('photos', ['constraint' => '([A-Z][A-Z][0-9]+)'] );

would restrict the {id} to be something like RR27.

## Constraints
To make your routes more readable and specific, you can use a the provided constraint options in your routes. These are replaced with the appropriate regex pattern before being passed to the router. Constraints are wrapped in curly braces to differentiate them from the standard patterns. The provided constraints are: 

* `{any}` - converts to `(:any)`. Primarily there in case you accidentally do that instead of the traditional version.
* `{num}` - converts to `(:num)`. Primarily there in case you accidentally do that instead of the traditional version.
* `{id}` - converts to `(:num)`. Simply makes the code a bit more readable and semantic.
* `{name}` - converts to `([a-zA-Z']+)`. Intended for string-based words with no spaces. Primarily people's names. 

### Custom Constraints
You can add your own constraints to use within your routes with the `registerConstraint()` method.

	$route->registerConstraint( 'my_name', '(pattern)' );

These can then be used within any of your routing calls.

	$route->any('users/{my_name}', 'users/by_name/$1');
	

## Route Groups

There are times when you'll want to group a disparate set of routes under a single section. You can use route groups for this.

    Route::group('api', function() {
        Route::all('users', 'users/index');
        Route::get('photos', 'photos/show');
    });

Would be equivalent to the following routes:

    $route['api/users'] = 'users/index';
    $route['api/photos'] = 'photos/show';



## Routing Areas

Areas provide a way for modules to assign controllers to an area of the site based on the name of the controller. This can be used for making a `/developer` area of the site that all modules can add functionality into.

This can be better explained with an example. We want to provide a collection of tools available under the /developer URL of our site. We have a number of modules, like a database manager, a code builder, etc, that all need to have easy access to that area. Instead of creating routes for each module, we'll just create a general set of routes that will take any controller named `developer.php` in any of our modules, and route it to `developer/{module_name}/{method}`.

    $route->area('developer');

If, we change our mind down the road and want to rename all of the URL's to `/tools`instead of `/developer`, we can do that by passing in two parameters instead. The first is the name of route (tools in this case), and the second is the controller to map to.

    $route->area('tools', 'developer');

This creates a series of routes that map the parameters into the module. It's a little hacky but works well for up to 5 parameters. If you need more than that, you might examine your application to see if you could use the routes differently or restructure your application. The equivalent CI routes would be:

    $route['tools/(:any)/(:any)/(:any)/(:any)/(:any)/(:any)']   = '$1/developer/$2/$3/$4/$5/$6';
    $route['tools/(:any)/(:any)/(:any)/(:any)/(:any)']          = '$1/developer/$2/$3/$4/$5';
    $route['tools/(:any)/(:any)/(:any)/(:any)']                 = '$1/developer/$2/$3/$4';
    $route['tools/(:any)/(:any)/(:any)']                        = '$1/developer/$2/$3';
    $route['tools/(:any)/(:any)']                               = '$1/developer/$2';
    $route['tools/(:any)']                                      = '$1/developer';

If you need to offset your parameter numbers for the above routes, you can pass on 'offset' key/value in your options array as the last parameter.

   
## Blocking Routes

You might find times where you need to block access to one or more routes. For example, you might have relocated the default user login page so that script-kiddies couldn't find your page by assuming it's a Sprint site and would be at a normal location. In this case, you would want to block any access to /users/login, which would normally work just fine. In this case you can use the `block()` method to block as many routes as you'd like.

    $route->block('users/login', 'photos/(:num)');

    // The same as:
    $route['users/login']    = '';
    $route['photos/(:num)']  = '';