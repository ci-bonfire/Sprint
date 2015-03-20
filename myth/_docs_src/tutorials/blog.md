# Simple Blog Tutorial

This tutorial will step you through using creating a very simple blog application using SprintPHP. Throughout this tutorial you will:

* Become comfortable using [Forge](http://sprint.dev/docs/developer/forge/overview) to quickly scaffold your blog.
* Understand how each generated piece of code works within the Sprint methodology, including the controllers, authentication, models, routing and more.
* Become familiar with the basics of the [Themes](http://sprint.dev/docs/developer/general/themes), including simple `callbacks` for using repetitive views throughout the application.

## What We're Building
Before getting started, we should take a minute to decide exactly what we want to accomplish here.

This isn't going to be a fancy, full-featured blog system. Instead, we're just going to be able to do CRUD in a protected area of the site of posts. 

On the front we'll display a paginated overview of the recent posts. We will also need a page to view the individual post. On the sidebar for this page, we'll need a list of recent posts, and we will do this in a way that can be easily reused throughout the site.

We should be able to track views on the posts, and provide another sidebar section with 'Popular Posts', simply by view, not by comments, or anything. 

Speaking of comments - we're not dealing with those in this tutorial. And we're not using tags, or categories or any of the other common blog features. Just keeping it simple. 

This tutorial assumes that you have already [installed](installation) Sprint and it is up and running.

## Forging A Start
In this section we rapidly create the standard CRUD operations that are typically part of the admin area.

### The Table
I usually find that it's easiest to start with the database design first so that I know what I'm working with. In Sprint, it can even generate the code based on the table. So, we'll start by creating a migration file that will create the table for us. This is done with the [migration generator](forge/generators#migration). From the command-line, at the web root, type the following:

	$ php sprint forge migration create_posts_table -fields "id:id title:string body:text deleted:tinyint:1 created_on:datetime"

This will create a migration file for you. It's smart enough to often determine the action to generate based on the name. In this case, it knows it should create a new database table called `posts`. We have used the `-fields` parameter to tell it which fields to create. In this example, we're creating only 5 fields. (I did say we were keeping it simple, right?) 

* id - This is the primary key for the posts. Generated as an unsigned INT(9), auto_incrementing.
* title - The post title as a VARCHAR(255).
* body - The body of the post as TEXT.
* deleted - A flag to tell whether it's been deleted or not. TINYINT(1)
* created_on - a field to store the date and time the post was created on. This will be filled in automatically by the model when we create a new post. 

After running that, you should have a new file at `application/database/migrations/XXXX_Create_posts_table.php`. The XXXX will actually be a timestamp to help the migrations run in the correct order. If you look at the generated file you will see an array of fields in the format that CodeIgniter's [dbforge](http://www.codeigniter.com/userguide3/database/forge.html) is expecting it, as well as the forge commands needed to create the primary key and create the table. 

Now [run the migration](database/migrations#running_migrations) to create the table in the database so it can be used in the next step. 

	$ php sprint database migrate

It will prompt you for the `group` to refresh. Most times, you can simply hit return to accept the default group, `app`, which is the main application. The answer `y` to tell it bring it to the most recent migration. If you examine your database, you'll find the new `posts` table created.

### Scaffolding
Now it's time to create all of standard CRUD functionality.  We do this using the [scaffold generator](forge/generators#scaffold).

	$ php sprint forge scaffold posts 
	
This creates a fully-working Posts CRUD for you. If you visit your site at `http://example.com/posts` you'll be treated with the fruit of your labor. Hard work, I know. The files that were created for you are: 

* `application/database/seeds/PostsSeeder.php` - An empty seed file we will use to enter generic test data
* `application/models/Post_model.php` - Our base model based on [CIDbModel](general/models) that provides many useful methods to work with our database table.
* `application/controllers/Posts.php` - A controller that handles the basic CRUD operations for us. This will be based on [ThemedController](general/themes) so that it will be automatically themed.
* `application/views/posts/index.php` - View to list all posts
* `application/views/posts/create.php` - View with the creation form for new posts
* `application/views/posts/show.php` - View to show a single form
* `application/views/posts/update.php` - View with the editing form for existing posts

**All of these files should be considered starting points only, not the final, production-ready code. Instead, they are simply a way to get you boilerplate code that you can modify to meet your application's needs. And save you hours of development time per project.**

### The Model
Let's take a look at the model file that is generated and see what changes were made based on our fields. Open up `application/models/Post_model.php`. This is a skeleton file with all of the basic options, though it doesn't include all of the class vars available in CIDbModel. Here's an overview of what some of the key values mean. 

`table_name` and `primary_key` should be pretty obvious. The `set_created` and `set_modified` will, if set to TRUE, automatically set the `created_on` and `modified_on` fields for you during inserts and updates. It does this using the value in the `date_format` class var. This defaults to `datetime` becuase I've been told from database guru that it's much easier to work with when you can actually read the value yourself in the database. A few years later and I am definitely a believer.

`soft_deletes` allow you to delete objects without actually permenantly deleting them. It acts like the Recycling Bin in Windows, or the Trash in OS X. By default, deleted items will not show up in `find` queries, but you can always get them to show up by using the `with_deleted()` method as part of your query. 

The `$validation_rules` array holds arrays of [validation rules](http://www.codeigniter.com/userguide3/libraries/form_validation.html#setting-rules-using-an-array) for the different fields. You might need to adjust some of those to your application's needs since the generator doesn't know the details of your application. If you need to add some rules that only happen on insert (like unique or required fields), you can add those rules to the `$insert_validate_rules` array. 

Finally, the `$fields` array should contain a list of all fields in your table. While not strictly necessary, you will achieve a higher performance by filling this array in manually. Otherwise, during inserts or updates, the data you pass in is sanitized so that it only includes valid fields, allowing you to simply pass an array of data that holds fields in other tables that your [observers](general/models#observers) can access, and not have database errors thrown at you. If you don't fill the array in manually, it will have to do a query before doing an insert or update. 

### The Controller
The controller is where you will do the meat of your work. While it works as is, you will definitely need to modify this file to take care of any application-specific information. The generated controller will simply take all of the $_POST array and shove it at the model. This was done more for simplicity than anything else and you might have various changes you want to do there. 

## Restricting Access
Right now, the generated code is really more something that would be in an admin area and not shown to the public. As it stands, though, there is no protection and anyone can create and edit your blog posts. Definitely not what we want so let's restrict things a bit. 

### Admin Controller
To keep information in a single spot, we need to create an AdminController that all of our 'admin'-type content can extend from so we don't have to worry about. Let's do that now. 

Create a new file at `application/libraries/AdminController.php`. The file should look like this: 

	<?php
	
	use Myth\Route;
	
	class AdminController extends \Myth\Controllers\ThemedController {

    	use \Myth\Auth\AuthTrait;

	    public function __construct()
	    {
    	    parent::__construct();

        	$this->restrict( Route::named('login') );
    	}
	}

This simple controller extends the [ThemedController](general/themes) since we know that it will always be based around themes. 

It uses the [AuthTrait](security/auth_trait) to provide a number of simple functions for restricting access based on groups, permissions, etc. In the constructor, we call the `restrict()` method that simply ensures that the user is logged in. For simple sites, this is probably fine. However, you'll want to use groups for more complex sites. 

The parameter passed to the `restrict()` method is the URI to redirect to if the user is not logged in. Here, we're using the [named routes](general/routes#named_routes) feature to redirect to the login page. 

Now, modify the Posts controller to extend the AdminController instead.

	class Posts extends AdminController {

If you try to visit your page again, it will redirect you to the login page. If you haven't created an account, yet, do that now so that you can access the pages again. 

### Admin Area
Since we've created what is basically an admin area, now, let's set the routes up so that it looks like an admin area. Open up the `application/config/routes.php` file and add the following lines:

	$routes->group('admin', function() use ($routes) {
   		$routes->any('posts(.*)', 'posts$1');
	});

This  basically adds a prefix to all of the posts routes of `admin`. Now try to access the new URL at `/admin/posts`. It should show you the same page as before, assuming you're logged in. However, there's two problems: 

1. Some of the views, especially on the `Cancel` buttons for the forms, will take you back to the non-grouped area, so you'll need to manually add the `admin/` prefix to those hrefs in the various views.
2. We can still access the routes outside of the group. In some cases, this won't be a big deal, but especially for things in the admin area, you'll want to ensure that access is prevented. Do this by adding the following line at the end of your routes (but prior to the `map` function that's called at the end of the file): 

```
$routes->block('posts(.*)');
```

Now, any time you try to access any of the routes outside of the admin area it will be redirected and the data can't be accessed. Good. 

## Listing All Posts

### Public Controller





