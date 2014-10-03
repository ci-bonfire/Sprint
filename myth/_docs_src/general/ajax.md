# AJAX Integration

Sprint comes with an elegant AJAX solution already in place in the current themes and ready for you to use. This is all based around [jQuery](http://jquery.com/) and the [Eldarion AJAX](https://github.com/eldarion/eldarion-ajax) library.

This guide will cover the integration as it exists, and the basics of using EldarionAJAX. The library is very flexible and for anything more complex than what we cover here, please see [their docs](https://github.com/eldarion/eldarion-ajax).

If you don't use this integration you should turn off the `use_eldarion` config setting in the application config file. This stops the built-in controllers from using their functionality. This is mainly used in the `BaseController::render_json()` method to return the profiler and notices during AJAX views.

## Including AJAX libraries
To make full use of the included AJAX functionality, you will need to include the following three files in your themes, or build their functionality into your own CSS and javascript files. In the themes that ship with Sprint, these files are already included.

- `{theme}/css/ajax.css` Holds the styling for the `<div #id="ajax-loader"></div>` tag that holds the loading symbol.
- `assets/js/ajax.js` Simply hooks into jQuery and shows/hides the AJAX loader during any AJAX calls. This works with your own jQuery code as well as the include EldarionAJAX library.
- `assets/js/eldarion/eldarion-ajax.min.js` The core Eldarion AJAX library.

## Automatic Features
BaseController is fully integrated with the AJAX libraries out of the box.

When using the `renderJSON` method to return JSON data to your web view, two things will be done automatically for you.

### Status Message Updates
Any message that you have created using `$this->setMessage()` from within your controllers will be automatically updated in your view. This is handled through the [fragments](#fragments) array returned in the JSON. The resulting view is inserted into a div with ID of `notices`. 

	// In your controller
	$this->setMessage('User successfully update.', 'success');
	$this->renderJSON($data);
	
### Profiler Bar Updates
If enabled, the profiler bar is also updated and will replace the existing profiler update, giving you constant access to the stats and debugging power the profiler provides. Even during AJAX development. 

## Implementing AJAX
At it's simplest, all you need to do is add the class `ajax` to any `anchor tag` or `form tag` and that action will automatically be handled via AJAX. The real power comes in how you return the data. 

### Replacing the Current Element
You can replace the current anchor or form with returned HTML. This can be handy for replacing a form with an updated version of itself with error elements. You could replace that same form with a Thank You! note on success. You could also have an anchor tag that, when clicked, loads in an entire page full of stats, if you wanted to.

	// In your HTML
	<a href="/users/ban/{$id}" class="ajax">Ban User</a>
	
	// In your controller
	$return = array('html' => '<p>Banned</p>');
	$this->renderJSON($return);

The string passed back in the `html` element of the `$return` array will replace the calling element that with class=ajax, in this case the anchor tag. 

### Replacing Another Element
You can update any element on the page with content that you return. Any number of items can be updated in a single call, making the `fragments` portion of the return array one of the most powerful. This is used by the [automatic features](#automatic_features) above. This could be used to click a link to complete a Task and have the task list, and the completed task list upated automatically. 

	// In your View
	<a href="tasks/complete_task/{$id}" class="ajax">Done</a>
	<ul id="tasks"> . . .</ul>
	<ul id="done_tasks"> . . . </ul>
	
	// In your controller
	$return = array(
		'fragments' => array(
			'#tasks' => $this->buildTaskList(),
			'#done_tasks' => $this->buildDoneTaskList()
		)
	);
	$this->renderJSON($return);

Each key is a CSS selector for one or more objects in the HTML. That element will be replaced with the contents passed as the value. It is usually best to separate the building of those objects out into separate methods since you'll need to call them multiple places. Doing it in a library is often even better, since you can more easily run tests against a library instead of a controller.

### Appending Data To Element
Instead of refreshing the entire list of tasks, like the previous example, you can also specify that data is simply appended to another element in the HTML. The contents of the `html` element in the returned JSON can be added to the end of another element that is specified with the `data-append` attribute. 

	// In your View
	<a href="tasks/{$id}/done" class="ajax" data-append=".done-tasks">Done</a>
	
	// In your Controller
	$return = array(
		'html' => $this->buildSingleTaskItem()
	);
	$this->renderJSON($return);

This will add the new item to the bottom of the `<ul>` with the class of `done-tasks`.

### Refreshing Another Element
There may be times when you need to simply tell another element to refresh itself, instead of getting the data prepared in the current response. This is often helpful if the elements belong to different modules or sections of the code that would make it impractical to compile the data in the current response. You can tell an object to do that by specifying a `data-refresh` attribute to the **current** object, or the one that initiates the AJAX call. This instructs the object in the data-refresh attribute to call back to the server and get it's updated contents. 

	// In your View
	<a href="tasks/{id}/done" class="ajax" data-refresh=".refreshable">Done</a>
	<ul id="done-tasks" class="refreshable" data-refresh-url="tasks/completed_tasks"> . . .</ul>
	
In this example, when the anchor link is clicked, it will fire off a call to the server to let it know that task is complete. Whenever it's done and receives its response from the server, all elements with a class of `refreshable` will have an AJAX call fired off to the URL in the `data-refresh-url` and they will be replaced with the contents of their own AJAX calls back to the server. 

## And More...
This guide has given you the tools that you will use probably 80%-90% of the time with your AJAX work for simpler jobs. If the task you are working on is a huge single-page application, you will probably need to dig a bit deeper into the vast customization tools that EldarionAJAX provides. For that, please [read their docs](https://github.com/eldarion/eldarion-ajax).