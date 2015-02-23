# HTTP Basic Authentication
This is the simplest, and least secure, of the methods supported. It uses the same username/email and password combination that users are familiar with. In order to have any modicum of security, though, the API must exist on HTTPS protocol. This class does use all of the same security built into the [LocalAuthentication](security/authentication) system to secure the password. 

## Enabling Basic Authentication
Enabling basic authentication is simple: 

1. Edit `config/api.php` and set `api.auth_type` to `basic`. 
2. Also set the value of `api.realm` to something specific to your site. Often the site name is good.
3. Ensure that your controllers extend from `APIController`.

## How does Basic work?
When an API request comes into your site it must pass the username and password in the `Authorization` header as a base64-encoded string prefixed with the word 'Basic'. The exact method to do that in your specific use case is beyond the scope of this documentation and isn't something that I'll be able to support in the forums.

An example cURL request might be like: 

	$process = curl_init($host);
	curl_setopt($process, CURLOPT_HTTPHEADER, array('Content-Type: application/xml', $additionalHeaders));
	curl_setopt($process, CURLOPT_HEADER, 1);
	curl_setopt($process, CURLOPT_USERPWD, $username . ":" . $password);
	curl_setopt($process, CURLOPT_TIMEOUT, 30);
	curl_setopt($process, CURLOPT_POST, 1);
	curl_setopt($process, CURLOPT_POSTFIELDS, $payloadName);
	curl_setopt($process, CURLOPT_RETURNTRANSFER, TRUE);
	$return = curl_exec($process);
	curl_close($process);

If the header is missing, the system will return a 401 Unauthorized header along with the WWW-Authenticate header telling the app that it should be using Basic authentication.

