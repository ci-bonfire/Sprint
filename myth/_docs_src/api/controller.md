# API Controller

The ApiController provides the foundation of all of your API functionality. It takes care of authenticating users for you, enforces any HTTPS requirements, and provides a number of helpful methods for responding to the client.

## Getting Request Data
Any data sent along with a request can be retrieved through the `grabVar()` method. If the request used the GET method, then any `$_GET` variables can be retrieved. For POST requests it will hold the `$_POST` vars. For all others it will attempt to retrieve a JSON body from `php://input` and make that information available.

	$vendor_id = $this->grabVar('vendor_id');

If you need access to any `$_GET` variables when handling a method other than GET, you would need to use the standard tools: either access `$_GET` directly, or use `$this->input->get()`.

## Responding to the Client
One of the biggest advantages of using this controller is the standard return methods that it provides to keep your code readable and consistent. And keeps you from bungling a status code because you're tired or distracted.

### respond()
The low-level method used for responding to any call. Takes care of formatting the results and sending the output to the client.

The first parameter is the data to send. Expected to be an array that is returned as JSON format. The second parameter is the status string or code to use.

If no data or status code is provided, it will return a 404 Not Found error.

	// Returns a 404 with generic message.
	$this->respond();

Otherwise, responds with the HTTP status sent in the second parameter.

	$data = [ 'response' => 'A new user was successfully saved.' ];
	$this->respond($data, 201);

### fail()
The low-level method used to respond and format any error. The return element contains two items: `error` and `error_message`.

The first parameter is the Error Message that is returned to the client. Typically this value is displayed to the end user. The second parameter is the error string. This must match one of the values in the [$codes](#$codes) array.

	$this->fail('That item has been deleted.', 'resource_gone');
	
	// Returns a 410 HTTP Error with the following JSON body:
	{
		"error": "resource_gone",
		"error_message": "That item has been deleted"
	}

### respondCreated()
Returns a 201 HTTP status along with the first parameter as the body. Used after the successful creation of a new resource.

	$data = $this->user_model->find($id);
	$this->respondCreated($data);

### respondDeleted()
Returns a 204 HTTP status along with the first parameter as the body. Used after successfully deleting an existing resource.

	$data = [ 'response' => 'The user was successfully deleted.' ];
	$this->respondDeleted($data);

### failUnauthorized()
Returns a 401 HTTP status with the standard error response. Used when the client is not authorized to perform that action, and when a different authorization would help. This is typically handled automatically by the Authorization classes for you.

	$this->failUnauthorize('You do not have appropriate permissions for that action.');
	// returns
	{
		"error": "unauthorized",
		"error_message": "You do not have appropriate permissions for that action."
	}

### failForbidden()
Returns a 403 HTTP status with the standard error response. Used when the action is not allowed and no form of authorization would help.

	$this->failForbidden('That action is forbidden.');
	// returns
	{
		"error": "unauthorized",
		"error_message": "That action is forbidden."
	}

### failNotFound()
Returns a 404 HTTP status with the standard error response. Used when the requested resource cannot be found.

	$this->failNotFound('That user does not exist.');
	// returns
	{
		"error": "resource_not_found",
		"error_message": "That user does not exist."
	}

### failBadRequest()
Returns a 400 HTTP status with the standard error response. Used when invalid data is presented to the API.

	$this->failBadRequest('No unique id presented.');
	// returns
	{
		"error": "invalid_request",
		"error_message": "No unique id presented."
	}

### failValidationError()
Similar to the `failBadRequest` but specifically intended for when the data doesn't pass validation rules. Returns the same information but creates more semantic code, and allows you to easily change the handling of invalid data if needed. 

	$this->failUnauthorize( validation_errors() );
	// returns
	{
		"error": "invalid_request",
		"error_message": "<p>The username cannot contain spaces.</p>"
	}

### failResourceExists()
Returns a 409 HTTP status with the standard error response. Used when the client is trying to create a resource that already exists.

	$this->failResourceExists('A user with that username already exists.');
	// returns
	{
		"error": "resource_exists",
		"error_message": "A user with that username already exists."
	}

### failResourceGone()
Returns a 410 HTTP status with the standard error response. Used when the client is requesting data that  you know has already been deleted and cannot be retrieved. 

	$this->failResourceGone('That item has been deleted.');
	// returns
	{
		"error": "resource_gone",
		"error_message": "That item has been deleted."
	}

## Custom field selections
To enable a consistent method for limiting the fields returned during an API call, the controller will automatically look for the $_GET var `fields` which can contain a comma-separated list of fields to return. This will NOT automatically be inserted into database queries. Instead, the value is sitting in `$this->selects` for your use whenever you need it.

	// API Call
	GET http://example.com/api/users/123?fields=username,email,last_login
	// In Controller
	$user = $this->user_model->select( $this->selects )->first();

## Paginating Results
The controller will automatically check for a `$_GET` variable named `page` and store that information in the class. From that information, and the `$per_page` variable, it will determine the current `$offset` to use for any database requests.

	$this->model->limit($this->per_page, $this->offset);

### nextURL and prevURL

In addition, it provides two methods, `nextURL()` and `prevURL()` that can help build out URLs to send back with the response to grab the next and previous set of results. Any additional `$_GET` variables that may have been used for this request will be added to the URL, also.

The first parameter is the base URI to use for the link. This is ran through the `site_url()` function to ensure it's portable among different Environments.

	$return = [
		'total' => $total,
		'first' => $first_id,
		'last' => $last_id,
		'next_link' => $this->nextURL("v1/locations/for_survey/{$survey_id}"),
		'prev_link' => $this->prevURL("v1/locations/for_survey/{$survey_id}"),
		'locations' => $locations
	];

### Changing Per Page
Any API call can override the default per page value of 20 by passing the `per_page` $_GET variable to the API. The `$per_page` of the controller will automatically be updated to that.

	http://example.com/api/users?per_page=10&page=2

If the `per_page` var is set to zero, then the `nextURL` and `prevURL` will not produce any content since no paging is happening.

## Multilingual Support
To aid in creating multilingual sites, the controller will automatically parse the `Accept-Language` header and attempt to load the language file specified in `$this->language_file` in that idiom/language. If that language file doesn't exist, it will default to the 'application' language file.

## Class Variables

### $request
An object that holds a few basic items about the request itself, including:

- `ssl` - a boolean value that tells whether the current request is a secure connection.
- `method` - the HTTP method the request was made with. All values are lower case.
- `lang` - The current language that was requested in the `HTTP_ACCEPT_LANGUAGE` header.

### $allowed_http_methods
An array of the only HTTP methods that are accepted. By default, all of the standard HTTP methods are present: `get`, `put`, `post`, `delete`, `options`, `patch`, `head`. If you need to use custom methods, or want to remove some possibilities, modifying this list will do that for you.

### $do_auth_check
If `true`, will load the authentication system and restrict to valid users. If `false`, will bypass authentication, and should only be used during local development debugging. Defaults to `true`.

### $page
The current page of results requested by the client. If set to `0` by the client, no paging will happen.

### $per_page
The standard number of results to return when paginating results. If the value is `0`, then no paging will happen and all results will be returned.

### $offset
Based on the current `page` and `per_page` values, this holds the offset to use for database LIMIT commands.

### $codes
An array of HTTP status strings and their corresponding HTTP status codes to return with it. Used by the `respond` and `fail` methods to determine the correct the HTTP code to return.
