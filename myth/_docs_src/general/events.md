# Events

The Events system can be thought of as a more flexible version of CodeIgniter's built-in Hooks. It allows events to be executed at different points during code execution without having to modify the actual code itself. Sprint includes a number of events sprinkled at key points througout the system execution that can allow you to perform new actions when a new user registers with your site, for example.

You are highly encouraged to use this system and provide events within any modules that you distribute to allow users the most flexibility when using your code. The [Mail system](general/email) is easiest to use when combined with the Events library.

## Loading the Library
The `Myth\Events` library is a static library so no instantiation of this library is needed.

## Listening For Existing Events
In order to listen for any existing events and have your code executed at these Event triggers, you will use the `on()` method. Any number of listeners can be setup for a single Event.

In general, these will be placed in the `application/config/events.php` config file, but you can always call the method at run-time also. You could organize your events into different files if desired. The only requirement is that they are included from the events config file. To save on memory, the events config file is not read until the first event is triggered, at which time the events config file is read and all of the events are registered.

The first parameter of the `on()` method is the name of the event that you're listening for. The second parameter is any callable function, class/method pair or closure.

	// Fire an existing function.
	Events::on('didRegisterUser', 'myFunction');
	// Fire a class' static method
	Events::on('didRegisterUser', ['myClass', 'myMethod'] );
	Events::on('didRegisterUser', 'myClass::myMethod' );
	// Fire an instantiated class' method
	Events::on('didRegisterUser', [$myClass, 'myMethod'] );
	// Fire an anonymous function
	Events::on('didRegisterUser', function() {...} );

### Priorities
You can specify the priority of the method when listening for the Event by passing an unsigned integer as the third parameter.  The lower the number, the higher the priority. So a priority of `1` is as high as you can get. Any tied priorities will be resolved in the order they were created.

	Events::on('didRegisterUser', 'myFunction', 75);

The class defines 3 constants to help keep things consistent and readable.

	EVENTS_PRIORITY_HIGH	// Equals 10
	EVENTS_PRIORITY_NORMAL	// Equals 100
	EVENTS_PRIORITY_LOW		// Equals 200

### Cancelling Event Execution

Each event is called, in order of descending priority, until one of two conditions is met: either all listeners have executed successfully, or a registered method returns `false`. Once a method has returned false, the remaining listeners are not executed and the trigger itself returns `false` to your script. This allows you to define conditions, like checking permissions, in a high-priority listener, and cancelling the remaining objects if the condition is not met. This is especially useful with events triggered **before** actions occur, as it gives a chance to bail before taking the action.

	if (\Myth\Events::trigger('beforeDeleteUser') === false)
	{
		return false;
	}

## Triggering Events
Within your code you can fire off events with the `trigger()` method. The first parameter is the name of the event. This should match the name that events are listening for.

	Events::trigger('didRegisterUser');

When the event is triggered, the listeners are executed in order, until they are all finished or one returns `false`. More details are provided under the [Cancelling Event Execution](#cancelling_event_execution) section.

### Passing Arguments
You can pass arguments to the listening methods by passing them in an array as the second parameter to the `trigger` method.

	\Myth\Events::trigger('didRegisterUser', [ $user ]);
	Events::on('didRegisterUser', function($user) {...} );

	\Myth\Events::trigger('beforeAction', [ $user, $current_user ] );
	Events::on('beforeAction', function ($user, $current_user) {...} );

A common typo to be aware of is happens when passing a single object (like the `$user` array) in as the only argument. It is extremely easy to forget to wrap that in an array.

	// Wrong
	\Myth\Events::trigger('didRegisterUser', $user );
	// Right
	\Myth\Events::trigger('didRegisterUser', [ $user ]);

## Removing Listeners
While you likely won't use these methods much, it is possible to remove listeners that have already been registered.

### Remove Single Listener

You Remove already listening events with the `removeListener()`. The first parameter is the name of the event. The second parameter is the callable that was passed in the first time. Anonymous methods cannot be removed.

	\Myth\Events::removeListener('didRegisterUser', 'myFunction');

### Remove All Listeners For Event
In order to remove all listeners from a single event, you would use the `removeAllListeners()` method. The only parameter is the name of the event.

	\Myth\Events::removeAllListeners('didRegisterUser');

### Removing All Listeners
If you want to remove all listeners to all events and clear out the system, you can do so with the `removeAllListeners()` and NOT passing in any parameters.

	\Myth\Events::removeAllListeners();

## Provided Events
The following is a list of all Events provided in SprintPHP core. This does not include any third-party code that might prevent their own.

### Users

<table>
	<thead>
		<tr>
			<th>Event Name</th>
			<th>Parameters</th>
			<th>Description</th>
		</tr>
	</thead>
	<tbody>
		<tr>
			<td>beforeAddMetaToUser</td>
			<td>(int)$user_id, (mixed)$key</td>
			<td>Called prior to adding a meta value to a user.</td>
		</tr>
		<tr>
			<td>beforeRemoveMetaFromUser</td>
			<td>(int)$user_id, (mixed)$key</td>
			<td>Called prior to removing a meta value from a user.</td>
		</tr>
	</tbody>
</table>

### User Authentication

<table>
	<thead>
		<tr>
			<th>Event Name</th>
			<th>Parameters</th>
			<th>Description</th>
		</tr>
	</thead>
	<tbody>
		<tr>
			<td>didLogin</td>
			<td>(array)$user</td>
			<td>Called after a user has logged in manually. NOT triggered for 'remember me' logins.</td>
		</tr>
		<tr>
			<td>beforeLogout</td>
			<td>(array)$user</td>
			<td>Called prior to logging a user out.</td>
		</tr>
		<tr>
			<td>didRegisterUser</td>
			<td>(array)$data</td>
			<td>Called after a new user registers. `$data` includes 'user_id', 'email', 'token', and 'method' (activation method)</td>
		</tr>
		<tr>
			<td>didActivate</td>
			<td>(array)$user</td>
			<td>Called after a user has been activated, either manually or automatically by the system.</td>
		</tr>
		<tr>
			<td>didRemindUser</td>
			<td>(array)$user, (str)$token</td>
			<td>After a user has had their password reset hash created. Used to send instructional email. `$token` is the code that should be provided in the email to reset their password.</td>
		</tr>
		<tr>
			<td>didResetPassword</td>
			<td>(array)$user</td>
			<td>After a user has reset their password.</td>
		</tr>
		<tr>
			<td>didPurgeLoginAttempts</td>
			<td>(str)$email</td>
			<td>Called after a user's LoginAttempts have been purged.</td>
		</tr>
		<tr>
			<td>didPurgeRememberTokens</td>
			<td>(str)$email</td>
			<td>After a user's RememberMe Tokens have been purged.</td>
		</tr>
	</tbody>
</table>


### User Authorization

<table>
	<thead>
		<tr>
			<th>Event Name</th>
			<th>Parameters</th>
			<th>Description</th>
		</tr>
	</thead>
	<tbody>
		<tr>
			<td>beforeAddUserToGroup</td>
			<td>(int)$user_id, (mixed)$group</td>
			<td>Called just prior to adding a user to a group.</td>
		</tr>
		<tr>
			<td>didAddUserToGroup</td>
			<td>(int)$user_id, (mixed)$group</td>
			<td>Called after adding a user to a group.</td>
		</tr>
		<tr>
			<td>beforeRemoveUserFromGroup</td>
			<td>(int)$user_id, (mixed)$group</td>
			<td>Called just prior to removing a user from a group.</td>
		</tr>
		<tr>
			<td>didRemoveUserFromGroup</td>
			<td>(int)$user_id, (mixed)$group</td>
			<td>Called after removing a user from a group.</td>
		</tr>
		<tr>
			<td>beforeAddPermissionToUser</td>
			<td>(int)$user_id, (mixed)$permission</td>
			<td>Called prior to adding a permission to a user.</td>
		</tr>
		<tr>
			<td>beforeRemovePermissionFromUser</td>
			<td>(int)$user_id, (mixed)$permission</td>
			<td>Called prior to removing a permission from a user.</td>
		</tr>
	</tbody>
</table>


### Cron Jobs

<table>
	<thead>
		<tr>
			<th>Event Name</th>
			<th>Parameters</th>
			<th>Description</th>
		</tr>
	</thead>
	<tbody>
		<tr>
			<td>afterCron</td>
			<td>(string) $output</td>
			<td>The resulting output from running the cron jobs.</td>
		</tr>
	</tbody>
</table>
