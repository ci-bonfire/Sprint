# Caching Strategies

This section provides some things to think about regarding getting the most performance out of your application via View Caching and the specifics of using it within Sprint. 

None of these items are unique to Sprint, but I felt it would be helpful to go over some of them since they will affect how you would structure your application. This also doesn’t cover proxy-caching or anything outside of Sprint and CodeIgniter, though those techniques can definitely give your application an even further burst of speed and reduce your server’s resource usage.

## Full Page Caching
CodeIgniter provides a very simple way to handle [full page caching](http://www.codeigniter.com/userguide3/libraries/output.html#CI_Output.cache) in your applications. 

The ThemedController provides an easy way to perform a full-page cache within the [render()](http://sprint.dev/docs/developer/general/themes#render()) method. You can pass in the number of **minutes** to cache the fully-rendered view for as the second parameter. 

	// Do a full-page cache for 15 minutes
	$this->render($data, 15);

However, when working with dynamic applications this can become a bit difficult to use since there are portions that we need updated based on the current user. That’s where fragment caching comes in.

## Fragment Caching
Fragment caching is simply the act of caching portions of the web page so you don’t have to generate the content again. If you’re using a file-based caching, then it might only make sense to cache the more expensive operations, like compiling search results or recent/popular blog posts. Whereas if you’re using in-memory caching systems like APC or Memcache, where the read operations are lightning quick, you can choose to store more chunks in memory. 

For a great overview of different strategies for naming and deciding what to cache, you should read [this post at Adventures in HttpContext](http://blog.michaelhamrah.com/2012/08/effective-caching-strategies-understanding-http-fragment-and-object-caching/). 

Sprint provides 3 different methods of easily working with and caching fragments and they come into play at different times. 

### display()
The Themer’s [display() method](http://sprint.dev/docs/developer/general/themes#themed_views) allows you to set a cache time by passing in the number of **minutes** to cache as the third parameter.

	<?= $themer->display(‘admin:parts/header’, [], 60) ?>

This is intended to be used from within views to include view fragments inside of the overall view. There’s nothing stopping you from using it in other locations inside of your app if you find a use for it. 

### View callbacks
Callbacks are only available to use from within views and are meant to pull rendered HTML fragments from other classes or controllers. You can tell it to cache the results by passing the number of **minutes** to cache as the second parameter to the `call()` method.

	// Cache for 1 hour
	$themer->call(‘\My\Blog\Posts:recent’, 60);

### AJAX: Refresh

In combination with the eldarion-ajax that Sprint ships with, you can make use of the full page caching and routing system in stock CodeIgniter to create a pretty flexible system.

Since version 3, CodeIgniter has allowed callbacks (or closures) to be used as part of the routing. You can use this to your advantage by keeping  any callback methods inside of a fully-namespaced class and setting up a route that is just there to serve up a fully cached fragment. 

You should also can take advantage of the full-page caching that CodeIgniter provides, unless this is something that changes on a per-user basis, since CodeIgniter’s cache naming is based on full-url only. If you can, though, you should definitely take advantage of it, since it’s the fastest way that CI can serve up the page. 

	// In the route file
	$route[‘recent_posts’] = function() {
		get_instance()->output
			->set_output(
				\My\Blog\Posts::recent();
			)
			->cache(60);
	}

You can now set the `data-refresh-url` attribute of an element that would need to grab those recent posts to point at the route you just created for it. Whenever it was refreshed, it would grab the cached version, if available, without ever hitting the main portion of the app. 

## Russian Doll Caching
You can combine the different types of view caching to create a form of “[Russian Doll](https://signalvnoise.com/posts/3112-how-basecamp-next-got-to-be-so-damn-fast-without-using-much-client-side-ui)” caching system. 

This is  where you do a full-page cache where possible. Inside of that, you might cache a large block of content. Inside that content, you might cache a number of smaller blocks of data. When your entire page cache expires, it will be able to pull only the largest block from the cache which can be especially fast when using in-memory caching systems. 

The biggest things to be concerned about here are your cache times and cache invalidation times. If you need your inner content to refresh faster than your outer content, you will need to do one of 2 things: 

1) Keep your outer content cache time to the shortest time that you would need to update some inner content. 
2) Server up full-page caches with a long cache life, but then immediately hit the server via AJAX for a fragment, which you could use the custom routes above and enable the use of full-page caching for those smaller chunks, also. While this still requires one or more AJAX calls back to the server to get content, the amount of content being sent back is very small, since all of the fragments are stored via full-page caching that doesn’t need the majority of the application to run, keeping it lightweight. 

## Cache Naming
The parts of the ViewThemer and ThemedController that allow caching will provide a generic name for your cache fragments. However, they don’t know enough about your application to create truly effective cache names. They do, however, allow you to pass in your own cache names so you can customize it to your application. 

How you name your cache ids is very specific to your application but, in all cases, they should be crafted to allow for the largest levels of re-usability as possible. 

In some cases, this might include prefixing the cache-name with a user role, such as when the UI shows different bits for the admin then for general users. You don’t want to show the admin tools to the user. You might also have different elements to show to logged in users, versus anonymous users, so prefixing with ‘anon’ could be helpful.

Basically, you want to consider how you’re *what* you’re going to cache, as well as *who* you’re caching it for. For example, you might include the user’s role as well as detailed page information: `role:page:fragment:id`. 

## Conclusion
In the end, no matter what exact strategies you end up using, you need to consider organizing your application so you can reuse your views as much as possible and cache those views for the best performance possible. 