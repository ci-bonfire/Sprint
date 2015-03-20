# Simple Blog Tutorial

This tutorial will step you through using creating a very simple blog application using SprintPHP. Throughout this tutorial you will:

* Become comfortable using [Forge](http://sprint.dev/docs/developer/forge/overview) to quickly scaffold your blog.
* Understand how each generated piece of code works within the Sprint methodology, including the controllers, authentication, models, routing and more.
* Become familiar with the basics of the [Themes](http://sprint.dev/docs/developer/general/themes), including simple `callbacks` for using repetitive views throughout the application.

## What We're Building
Before getting started, we should take a minute to decide exactly what we want to accomplish here.

This isn't going to be a fancy, full-featured blog system. Instead, we're just going to be able to do CRUD in a protected area of the site of posts. 

On the front we'll display a paginated overview of the recent posts. We will also need a page to view the individual post. On the sidebar for this page, we'll need a list of recent posts, and we will do this in a way that can be easily reused throughout the site.

About comments - we're not dealing with those in this tutorial. And we're not using tags, or categories or any of the other common blog features. Just keeping it simple. 

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
Now that we have a simple admin area in place and some basic CRUD so we can work with posts, we need to provide a way for those posts to be viewed on the site itself. So we'll start with the controller. 

### Public Controller
The fastest way to create a new controller is to use Forge again.

	$ php sprint forge controller Blog
	
When asked for the model name, tell it to use the `post_model` that we just created. Once it is created, you'll notice it's created all of the same CRUD methods that we had before. The only methods that we'll need to keep are the `index()` and `show()` methods. Delete the rest. 

The `index()` method will be used to list all of the posts in the system. The `show()` method will show an individual post. Show isn't really a great name, though, so let's change that function to be named `post()`. Much better.

### The View
If you visit `/blog` in your browser it will throw an error because we haven't created the view yet. But how does it know which view, you ask? The error says it is looking for `blog/index.php` but the `index()` method never uses `$this->load->view()` so how does it know what to show? 

In the controller you have the following line of code: 

	$this->render();

This method is in one of the methods provided in the `ThemedController` and can often be used without any other configuration. It will look in the views folder for a folder named the same as the controller (blog) and a view file with the same name as the current method (index). In this case it's looking for `application/views/blog/index.php`. This can be customized, but we won't go over that right now.

So create a new view file and we will add the following code: 

	<h2>Blog Posts</h2>

	<div class="post-list">

	    <?php if (isset($rows) && is_array($rows) && count($rows)) : ?>

			// List our posts here

    	<?php else: ?>

	        <div class="alert alert-warning">
            	Sad day. You haven't told anyone the secrets of the Universe yet.
        	</div>

    	<?php endif; ?>

	</div>

We don't actually have any posts in the system, yet, so let's take a look at database Seeding and get some dummy data in the system. You could use the forms we created earlier, but we're exploring what's possible here, right? 

### Database Seeding with Faker
Seeding a database is simply a simple way to put data into the database. I usually find myself using this with dummy data for during development, though I've used it for actual client data we didn't want to lose (large surveys with lots of data) or as a repeatable way that we can get client data into the production system in place of creating custom importers. 

A seed file has aleady been created as part of the scaffolding we did earlier. You can find it at `application/database/seeds/PostsSeeder.php`. Open it up now and we'll create a few example posts using [Faker](https://github.com/fzaninotto/Faker). 

First we have to install it, though, so open up `composer.json` and add the following to the `require-dev` section: 

	"fzaninotto/faker": "1.5.*@dev"

Install the package with Composer's install command (assuming you have it installed globally): 

	$ composer install

Since this is the first time after installing Sprint, it may require you to use `update` instead to get packages up to date with the latest releases. Go ahead and do that. 

Back to the Seed file. Open it up and add the following to the `run()` method: 

```
$this->ci->load->model('post_model');

$faker = \Faker\Factory::create();

for ($i=1; $i <= 10; $i++)
{
    $data = [
       	'title' => $faker->sentence(),
       	'body'  => implode("\n\n", $faker->paragraphs(4) ),
       	'deleted' => 0
   	];

   	$this->ci->post_model->skip_validation()->insert($data);
}
```

This creates random lorum ipsum titles and bodies with 4 paragraphs each, inserting them into the database through the Post_model that was scaffolded for us. Time to run the seeder from the command line. 

	$ php sprint database seed PostsSeeder

We always have to specify the name of the seeder to run, but that seeder can call out to other seeders to organize longer seeds better. This one is simple, though, so that's all we need. Check your database and you'll see that we have 10 dummy posts we can work with. 

### Back to the View
The easiest way to display all of the posts in a list is to simply enter the code between the `if...elseif` tags in the index view. However, we want to explore a little more of our setup here, so we will create a new file that contains just the code for displaying a single post. That way it can be reused for the single post pages, also.

Create a new file at `application/views/blog/post.php`. You will notice by the naming that it's the shares the same name as the method that will show an individual post. That means this view will be used automatically by the Template system for that method.

Enter the following code and save it: 

```
<div class="post">

    <div class="post-head">
        <h3>
            <a href="<?= site_url( 'blog/post/'. $row['id'] ) ?>"><?= $row['title'] ?></a>
        </h3>
        <p class="small">Posted on <?= date('Y-m-d', strtotime($row['created_on']) ) ?> at <?= date('g:ia', strtotime($row['created_on']) )  ?></p>
    </div>

    <div class="post-body">
        <?= auto_typography($row['body']) ?>
    </div>

    <hr/>
</div>
```

Now, tell the `index` view how to render it by inserting the following code between the `if...elseif` tags: 

```
<?php foreach ($rows as $row) : ?>

    <?= $themer->display('blog/post', ['row' => $row]); ?>

<?php endforeach; ?>
```

This will loop over all of the rows that were found and uses `$themer->display()` to show the view. `$themer` is the instance `ViewThemer` that is used by the ThemedController to do the `render` functionality and provides a number of handy methods. 

The `display` method is the same one used by the template engine to render all views. We use it here, instead of the standard `$this->load->view` because it provides a caching feature if you want to cache a view for some reason. It also provides the ability to pull views from the any theme if you need it, so it's good to get used to using it. 

The first parameter is the name of the view. It doesn't know about the controller that you're in so you must pass the full path (within the `views` folder) along with the view name itself. The second parameter is an array of data to pass to the view, just like a traditional CodeIgniter call.

We need to display the body nicely so we'll use the [auto_typography](http://www.codeigniter.com/userguide3/helpers/typography_helper.html) method that CodeIgniter provides. That means we'll need to load it in both the `index()` and `post()` methods.

	$this->load->helper('typography');

Refresh the page and you should see all of your posts displayed in their full glory. Making it attractive isn't the goal of this tutorial, sorry. We really should [paginate](http://www.codeigniter.com/userguide3/libraries/pagination.html) the results, too, but I'll leave that as an exercise for you.

Clicking on the title of each post will take you to the individual post page. There you go. A more or less fully-functioning blog system. I know it's pretty bare bones, but that was pretty quick and painless, right? There's one (big) thing missing from every blog system, though: a list of recent posts in the sidebar. Let's fix that as the last bit of this tutorial.

## Recent Posts
First, we need a place to put it, and they often show up in the sidebar, so let's make a sidebar here. The two demo themes that come with Sprint both have a number of pre-made layouts that you can use and tweak to your needs. In our case, we want one with two columns, and the sidebar on the right. Take a look at the files under `themes/bootstrap3` (the default theme) and you will see all of the layouts you can choose from, including the one we want, `two_right`. 

Since it would be nice to have the sidebar show up on both of the pages we just created, let's set the layout for as something that all methods in the Blog controller will use. Edit the controller's class variable, `$layout` to be the name of the layout we chose.

	protected $layout = 'two_right';

Refresh your page and you'll see that you now have a sidebar on the right with a few dummy links. These are part of the demos that Sprint ships with and we will replace them right now. To keep things clean, we'll separate the sidebar content out into a new theme view called `sidebar`. Appropriate, right? 

Edit `themes/bootstrap3/two_right.php` and replace the demo links with the following code:

```
<!-- Sidebar -->
<div class="col-sm-3 col-md-2">

    <?= $themer->display('{theme}:sidebar'); ?>

</div>
``` 

This displays a view called `sidebar`. Whenever we want to display a themed view, we prefix the view name with the name of the theme, separated by a single colon. So this could have been `boostrap3:sidebar`. However, to keep things simple you can replace the theme name with the placeholder `{theme}` and it will look in the current theme for that file. 

Now create a new file `themes/bootstrap3/sidebar.php`. Enter the following code into it: 

```
<?= $themer->call('posts:recent show=5') ?>
```

This uses a [callback](general/themes#callbacks), a feature that allows you access easily insert the output of snippets of code elsewhere in your project. The string for the `call()` method is a very flexible way to list the class/method and an array of named parameters to pass to that method. 

The first part of the string is the class/method to call. This can be any standard callable in PHP, like a namespaced class, closure, etc. It can also be a CodeIgniter library, which is what we're doing here. So we are calling a library named `posts` and it's method named `recent`. After that, we are providing a parameter named `show` with a value of 5. Any parameters are passed to the callback as a single array of key/value pairs. 

Create the new library file at `application/libraries/Posts.php`, and add the following code to it: 

```
<?php

class Posts {

    public function recent($params=[])
    {
        $ci =& get_instance();

        $limit = ! empty($params['show']) ? (int)$params['show'] : 0;

        $ci->load->model('post_model');

        $posts = $ci->post_model->limit($limit, 0)->order_by('created_on', 'desc')->find_all();

        if (empty($posts))
        {
            return '';
        }

        return $ci->load->view('blog/recent', ['posts' => $posts], true);
    }

    //--------------------------------------------------------------------

}
```

This implements the `posts:recent` that the callback is looking for. The first thing it does is to grab an instance of the CodeIgniter superobject. Next it looks in the `$params` array to see if a limit has been passed via the named parameter of `show`. If it doesn't exist, it will show all posts.

Next it grabs the data from the `Post_model`, limiting the number of results, and ordering by the created on date in descending order so the newest post is always on top. 

Callbacks must always return a string. If no posts are found, we return an empty string. If we do have posts then we have another view format them and the return the contents of that parsed view. This string is then displayed in the sidebar. 

One of the great things about callbacks is that it allows you to cache the results and never hit the library or the database for those results as long as your cache engine is setup. To cache, you would pass the number of  *minutes* to cache the results for as the second parameter of the `call()` method: 

```
// Cache it for one hour
<?= $themer->call('posts:recent show=5', 60) ?>
```

One last step, and that's to create the view used by the callback, so create a new file at `application/views/blog/recent.php` and use this code: 

```
<h4>Recent Posts</h4>

<ul class="nav nav-list">
    <?php foreach ($posts as $post) : ?>
        <li>
            <a href="<?= site_url( 'blog/post/'. $post['id'] ) ?>"><?= $post['title'] ?></a>
        </li>
    <?php endforeach; ?>
</ul>
```

Refresh the pages and you should see the list of recent posts show up on both of the new pages. 

## In Closing
While the blog engine itself is pretty basic, we've touched on a wide variety of Sprint's powerful feature set that you'll find yourself using over and over. Continue to explore the docs and you'll keep finding great new ways to combine the features and drastically reduce the amount of time that you spend in development.


