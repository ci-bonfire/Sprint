# API Tools Overview

Sprint provides a handful of tools for building a JSON-based API server and makes it easy to setup and use. These tools on built on top of the [LocalAuthentication](security/authentication) driver and provide all of the same user throttling for bad logins and other secure tools for working with user accounts, forgotten passwords, etc. 

In addition, these are designed to work out of the box with the  [AuthTrait](security/auth_trait) to provide simple methods to restrict access to groups throughout your application.

## Essential Components
The following are a list of the components that are provided to help you build out your API.

- An [API  Controller](api/controller) with helper methods for return JSON-based success/failure messages and paginating results in a consistent manner.
- Additional Authentication classes, based on [HTTP Basic](api/httpbasic), [HTTP Digest](api/httpdigest) authentication standards, all with built-in throttling and logging.
- The [Routes](general/routes) enhancement provides a number of tools designed to make creating API's simpler.

## Setting Up Your System
While the tools are all provided, you do need to run a generator that will create the proper migrations for you, and will even get you up and running with a basic API Admin area for you.

### APIController
In order to make use of the API-server features provided, your controllers MUST extend the [APIController](api/controller). This class already has the [AuthTrait](security/auth_trait) loaded for you.

## Common Configuration
The configuration values for the API tools are found in the `application/config/api.php` file.

### api.auth_type
This allows you to specify which type of authentication is used, unless you are using the `oAuth2Authentication` library, which requires its own driver. Allowed values are: `basic`, and `digest`.

	$config['api.auth_type'] = 'basic';

* __basic__ This authentication system uses the standard username/password combination that are already present for the users, though it is not very secure. If you choose to use this type, you MUST have the entire domain protected via HTTPS in order to have any modicum of security.
* __digest__ This uses a nonce and a secret key that is known between the client and the server and hashed. Provides much better security than _basic_, but should still be behind HTTPS.

### api.realm
If the authorisation type supports it, this is the default realm that is used across your application. While the specs allow a site to specify different credentials for each realm of the site, this library only supports a single set of username/password combinations per user for the entire site.

	$config['api.realm'] = 'Unnamed';

### api.auth_field
This is the user field that credentials are checked against for those authentication types that support it. Typically either 'username' or 'email'. For example, in basic HTTP authentication, the username field that it supports is mapped against this field.

	$config['api.auth_field'] = 'email';

### api.ip_blacklist_enabled
Determines whether blacklisting of visitors based on IP address is turned on or off. If `true`, then any IP addresses listed here will never be allowed access to the site.

	$config['api.ip_blacklist_enabled'] = true;

### api.ip_blacklist
A comma-seperated list of IP Address that should be banned from the site.

	$config['api.ip_blacklist'] = '252.32.125.32, 162.15.325.124';

### api.ip_whitelist_enabled
Determines whether whitelisting of visitors based on IP address is turned on or off. If `true`, then only IP addresses listed here will be allowed access to the site. All other IP addresses will be blocked.

Local IP addresses are always added in (127.0.0.1 and 0.0.0.0);

	$config['api.ip_whitelist_enabled'] = true;

### api.ip_whitelist
A comma-seperated list of IP Address that should be the only ones allowed access to the site.

	$config['api.ip_whitelist'] = '252.32.125.32, 162.15.325.124';

### api.ajax_only
If `true` then the API will only respond to AJAX requests. Any non-AJAX requests will return a 403 Forbidden HTTP status message.

	$config['api.ajax_only'] = true;

### api.enable_logging
If `true` then will log all requests made to an APIController.

	$config['api.enable_logging'] = false;

In order to run this you must create a table in your database. The api generator will ask you if you want to enable logging and create the migration for you. You will still need to run the migration yourself. 

	$ php sprint forge api
	$ php sprint database migrate

If any of the controller methods don't need to be logged, you can turn it off by setting the class var `enable_logging` to `false` from the method itself.

	$this->enable_logging = false;

### api.enable_rate_limiting
Rate Limiting restricts that a user cannot make more than `X` requests against the API per hour. The exact value is determined by the `api.rate_limits` setting, below. If `true` any user that exceeds that limit within the current hour will have their access denied until the start of the next hour.

If the user exceeds the number of allowed requests the server will respond with an HTTP status code of 429 - Too Many Requests.

This works hand-in-hand with the logging feature so the logging feature MUST be enabled for the rate_limiting to work.

	$config['api.enable_rate_limits'] = false;

This setting can be overridden on a per-controller basis by setting the controller's class var `enable_rate_limits` to the desired setting.

### api.rate_limits
An integer with the number of requests per hour that a user can make against the API.

	$config['api.rate_limits'] = 100;

This setting can be overridden on a per-controller basis by setting the controller's class var `rate_limits` to the desired setting.
