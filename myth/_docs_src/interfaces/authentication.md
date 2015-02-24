# AuthenticateInterface

This interface defines the methods required when implementing a new type of Authentication. Depending on the type of authentiation provided, some methods might not make sense. In those cases, that method should return `true`, so as not to stop script execution checks.

## login()
Attempts to log a user into the system. Should provide the same results as the `validate()` method when checking if a user is valid.

The first parameter is expected to be an array of credentials, simple key/value pairs, to check against in the database. Should respect the config setting `auth.valid_fields` when determining which fields can be used. These fields do not include the `password` field, which should always be expected.

The second parameter is a boolean value for whether the user should be remembered or not, and automatically logged in during future visits. This defaults to `false` and must respect the config setting `auth.allow_remembering`.

	$credentials = [
		'email'    => 'darth@theempire.com',
		'password' => 'lukiesdad'
	];
	login($credentials, true);

This method is expected to trigger the `didLogin` event, passing an array representing the logged in user as the only payload.

Should return a boolean value representing the success of logging the user in.

This method is responsible for testing the user against the isThrottled() method to determine if they should be delayed or are currently allowed to login.

## validate()
Will determine whether a user is valid or not, but does not log them into the system. This is typically used by the `login()` method to perform the actual validation since it should return the identical result.

The first parameter is the credentials array. This is exactly as described for the login method, above.

The second parameter is a boolean value that determines whether the method should return the $user array. If false, it should simply return the boolean value `true`. If `true`, should return an array with the $user information from the database.

	$credentials = [
		'email'    => 'darth@theempire.com',
		'password' => 'lukiesdad'
	];
	$user = validate($credentials, true);

## logout()
Takes the necessary steps to ensure that a user is logged out, their session is destroyed, and they will not be automatically logged in upon their next visit. There are no parameters.

	logout();

## isLoggedIn()
Determines if the user is currently logged in. If not, it should return `false`. If the user is logged in, it should ensure that the class `$this->user` holds the array of the current user's information. There are no parameters.

	isLoggedIn();

## viaRemember()
If  your class supports remembering users to automatically log in later, then this method should attempt to verify the user and log them in. There are no parameters.

This must respect the config setting `auth.allow_remembering`.

	viaRemember()

Returns boolean to represent whether the user was considered logged in after this method ran.

## registerUser()
Registers a new user and ensures the activation process is ran, like sending an email, or putting them in a queue for moderation, etc. Includes creating the new user in the database. The only parameter is an array of data to save to the users table when creating the new user.

Should trigger the `didRegisterUser` event with the following array of data:

	$data = [
		'user_id',
		'email',
		'token',   // The random activation hash that can be used in the email.
		'method'   // either: auto, email, or manual. You could add additional methods if needed.
	];

Returns a boolean value representing the success/failure of the registration process.

## activateUser()
Used to very the user values and activate a user. The only parameter is an array of data containing the information to check against the user:

	$data = [
		'email'  // The user's email to find the user with
		'code'   // The activation that should be hashed and compared with the database
	];

Should return a boolean representing the success of activating the user.

Should trigger the event `didActivate` passing along the array representing the user as the only payload.

## activateUserById()
Allows the system to manually activate a user with a known ID. This method assumes that the user has already been validated and will do no further checks, but should set the `active` flag on the user to `1` and clear the activate hash. The only parameter is the ID of the user to activate.

	activateUserById($id);

Should trigger the `didActivate` event, passing along an array with the users data.

Should return a boolean value.

## user()
Returns an array of data representing the current user. Does not accept any parameters.

Should return `null` if the current user object is not set.

## id()
Returns the currently logged in user's ID only. Does not accept any parameters.

Should return `null` if the current user object is not set.

## isThrottled()
Determines if a user is currently being throttled based on the number of bad login attempts, etc.

Should return the number of seconds that a user must wait before attempting another login.

## remindUser()
Used to send a password reset link email eo tht euser associated with the passed in `$email` address in the first parameter.

## resetPassword()
Validates the credentials provided and, if valid, resets the password to the new value provided. The `$credentials` array MUST contain a 'code' key with the string to hash and check agains the reset_hash in the user table.

The first parameter is the `$credentials` array. The second parameter is the new password. The third parameter is the password confirmation which must match the password.

## changeStatus()
Used to change the user's status. This can allow for banning or suspending users, though the exact effects are not hard-coded into the system currently. The first parameter is a string with the new status. The second parameter is a string meant to provide the reason for the status change.

## useModel()
Used to allow the calling script to define the User Model to use. The first parameter is the model instance.

## error()
Simply returns the current error string, if any, or `null` if none.

## purgeLoginAttempts()
Purges all login attempts from the system for a single email address. The only parameter is the email address to purge results for.

Should trigger the `didPurgeLoginAttempts` event passing through the email address as the only payload.

## purgeRememberTokens()
Purges all remember me tokens from the system for a single email address, effectively logging that user out of all devices. The only parameter is the email address to purge results for.

Should trigger the `didPurgeRememberTokens` event passing through the email address as the only payload.
