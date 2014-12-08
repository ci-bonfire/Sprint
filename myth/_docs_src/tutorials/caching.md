# Caching Strategies

This section discusses ways to get the most performance out of your application via View Caching and the specifics of using it within Sprint. 

None of these items are unique to Sprint, but I felt it would be helpful to go over some of them since they will affect how you would structure your application.

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


