# Mail System

The Mail System is designed to give you the most flexibility with your emails. It consists of 4 primary parts: 

- **Mail** - The main access point. Provides a simple method to send prepared emails.
- **Mailers** - Controller-like code that you create that defines the requirements for the email
- **Mail Services** - Allows multiple services to actually deliver the email. Out of the box Sprint ships with a `CIMailService` that is a simple wrapper for CodeIgniter's built-in email library, and `LogMailService` which just logs the files to the server but doesn't send them. Great for testing the email service and mail layouts.
- **Themes/Views** - Built around the existing [Themers](general/themes) and standard views it allows you to easily create one or more themes for all of the emails that your site delivers and keep a consistent look that's easy to change. 

On top of this, the Mail System allows your emails to either be sent immediately, or placed into a local queue to be sent out in batches. This requires a [cron job](general/cronjobs) to run on the server.

It is not required to use this system to deliver emails. You can still use CodeIgniter's built-in Email library in your applications. This system provides much more flexbility and power for when you need it.

## Mail
This is the face of the mail system, though possibly provides the fewest actual capabilities. It is primarily concerned with launching [Mailers](#mailers) to send the appropriate email and processing the queue during a cronjob. 

### Sending EMails
Sending mail is done using the `deliver()` method. This method simply ensures that the correct mailer is called to handle the actual email. The first parameter is the name of the mailer class and the name of the method, joined by a colon. 

	use Myth\Mail;
	Mail::deliver('UserMailer:didRegister');

This would look for a mailer with a filename and a classname of `UserMailer`. It would load that class up and run the `didRegister` method, which is responsible for formatting and sending the email.

You can pass parameters to the mailer's method by passing them in an array as the second parameter of the deliver method. 

	$params = [
		$user,
		$token
	];
	Mail::deliver('UserMailer:didRegister', $params);
	
	// The Mailer
	class UserMailer extends BaseMailer {
		public function didRegister( $user, $token ) {
			. . . 
		}
	}

If you need to override the default options of the Mailer class (things like default $from email address or format) you can pass the options in as the third parameter to the deliver method. 

	$options = [
		'format'	=> 'text',
		'from'	=> 'darth@theempire.com'
	];
	Mail::deliver('UserMailer:didRegister', $params, $options);
	
	// In BaseMailer
	public function __construct( $options ) {
		if (is_array($options))
        {
            foreach ($options as $key => $value)
            {
                if (isset($this->$key))
                {
                    $this->$key = $value;
                }
            }
        }
	}
	
## Mailers
Mailers are like controllers, but for emails. They compile the information needed to create the email, decide the email format, theme, attach any files, etc. You will create mailers for each email. You don't need to create one _class_ for each email, but one _method_. You can separate the mailers into as many or as few files as you'd like. They are typically used to organize by topic, though, like UserMailer or CronMailer.

Mailers must extend from `Myth\Mail\BaseMailer` in order to have the needed functionality to actually send or queue emails. 

### Default Options
Each Mailer class can have a set of default options that will be used when sending emails. For the most part, they make up the basics of the email itself, like who it's from, any one that needs to be cc'd, etc. These can all be overridden per email.

- **$from** Who the email is from. Can be either a string or an array. If a string it should be the email address. You can include both an email address and the display name as the first and second elements of an array.
- **$to** The email address the generated email will be sent to. Primarily useful for setting system-related emails the go to the admin or developer.
- **$reply_to** The email address that replies should be sent to.
- **$cc** A single email address that should be cc'd on these emails.
- **$bcc** A single email address that should be bcc'd on these emails.
- **$theme** The email theme that should be used for these emails, if any. Defaults to `email`.
- **$layout** The layout file within that theme that should be used. Defaults to `index`.
- **$service_name** You can override the default MailService that should be used to deliver these emails here. If left null, it's default state, it will use the default one.

### The Mailer Class
The Mailer classes must be located under the `application/mailers` folder. Each file should have the exact name as the class, and share the same capitalization. If the class name is `UserMailer` the file must be named `UserMailer.php`. 

The class must extend from `Myth\Mail\BaseMailer`. 

	use Myth\Mail;
	class UserMailer extends BaseMailer {
		 . . .
	}

Once inside the class you have full access to the CodeIgniter superglobal just like you would from a Controller. This means that almost anything you could do in a controller you can do here to build your email. You can access models via `$this->load->model()` or load helpers or other libraries, if needed.

### The Mailer Methods
Each method within a Mailer class represents one specific email. It is resonsible for taking the data that was passed to it and pulling things together to build the email. The only parameters it should expect are those that you decided to pass to it when you called it. This should be consistent across all of the places that you call it. 

	$params = [
		$user,
		$token
	];
	Mail::deliver('UserMailer:didRegister', $params);
	
	// The Mailer Method
	public function didRegister( $user, $token ) {
		. . . 
	}

From within your method, you will need to call either the `send()` method or the `queue()` method at the end of your method. This allows you to keep everything about this email in a single location including how it's built, how it's sent, etc. These methods are described in detail below. 

### send()
This method sends the email immediately and will not put it in the queue. The first parameter is the email address it should be sent to. The second parameter is the subject line of the email. The third parameter is an array of key/value pairs to be made available to the view when rendering it, just like with data passed to a standard CodeIgniter view.

	public function didRegister( $user, $token ) {
		$data = [
			'user' => $user,
			'loginLink' => site_url( Route::named('login') )
		];
		
		$this->send($user->email, 'Welcome to My Site!', $data);
	}

The theme and view will be automatically built for you based on conventions that are described in detail in the section about [Mail Themes](#themes_and_views), below. As well, the current class options will be used to build the rest of the email, like `$from`, `$cc`, etc.

If you need to specify a different view to be used than what would be chosen automatically you can pass the view in as the fourth parameter.

	$this->send($user->email, 'Welcome to My Site!', $data, 'a_different_view');

### queue()
This method functions identically, except it stores the email to be sent at a later time. All of the parameters are identical.

	$this->queue($user->email, 'Welcome to My Site!', $data, 'a_different_view');

### headers()
Allows you to set custom headers that need to be sent with this email.  The first parameter is the name of the header to set. The second parameter is the value.

	$this->header('Your-Header-Name', 'the header value');

### attach()
Allows you to add an attachment to your email. The first parameter is the filename to attach. This should include the path so that the file can be found. The second parameter is the `disposition`, either `inline` or `attachment`. The default is `attachment`. The third value is the filename to rename to when sent. The fourth parameters allows you to set a custom mime type. Only the first parameters is required.

	$this->attach('path/to/file.zip');


## Themes and Views
The Mail system uses the same views and [theme system](general/themes) that you're used to. By default, the theme 'email' will be used, with the 'index' layout. Your actual email content will be nestled inside that layout. 

The system will determine the view to use in a very similar fashion to standard views. It will look in `application/views/emails/{mailername}/{method}.{format}.php`. The mailer name and view name will be all lowercase. 

### Setting Current Theme
Within each Mailer you can modify the theme that is being used by setting the class variable `$theme` to the alias of the desired theme. This must match the theme alias in the [configuration](general/themes#theme_locations). 

The default layout to use within that theme can also be set in a class variable called `$layout`.

	class UserMailer extends BaseMailer {
		protected $theme = 'email';
		protected $layout = 'index';
	}
	
These can, of course, be set within any of the methods, also. 

	$this->theme = 'email';
	$this->layout = 'index';

The simple theme provided with Sprint uses Zurb's [Ink Email Framework](http://zurb.com/ink/). If you are going to use this default email with or without CSS changes, you will need to run it through the [Ink Inliner](http://zurb.com/ink/inliner.php) first. 

### Formatted Emails
Most emails today are sent as HTML emails, though you do want to provide options on most sites. To make this simple, the system will look for view names with the format appended. If it finds the file, that format will be added to the outgoing email. 

For example, if we want the user didRegister email to have only an email version, we would need a view file at: `application/views/emails/usermailer/didregister.html.php`

If you want to provide a text-only alternative, you could also have `didregister.text.php`. 

If both files exist, then they will both be sent along, providing an alternative for mail apps that don't support HTML emails.

## Configuration
The first configuration option available outside of the Mailers is the option to set the Mail Service to use. This can be set in the `application/config/application.php` config file.  

	$config['mail.default_service'] = '\Myth\Mail\CIMailService';
	
The value must be a fully-namespaced class name that will be instantiated and used by the all Mailers.

### Pretend To Send Mail
You can tell the system to simply pretend to send emails by setting the `mail.pretend` config setting to true. This happens in the BaseMailer during the send method. It doesn't affect the the queue method at all.

	$config['mail.pretend'] = false;

## Mail Services
Mail Services take the constructed email and send them across the internet. Sprint ships with two Mail Services, but it should be fairly easy to create your own if you need to use third-party services like [Mandrill](http://mandrill.com/) or [PostMark](https://postmarkapp.com/) for mail delivery.

At this time, Mail Services only handle sending outgoing email and cannot recieve mail or track statistics.

### CIMailService
CIMailService is a wrapper for the standard CodeIgniter email library. See CodeIgniter's [documentation](http://www.codeigniter.com/userguide3/libraries/email.html) for details. 

### LogMailService
LogMailService is intended for use during development, as it does not actually send any emails. Instead, it creates a copy of the outgoing emails in an emails directory under the defined log folder. By default, this would be `application/logs/emails`. HTML emails will be rendered there so you can verify that the emails are generated correctly. 

When the file is written out, it uses the following format: 

	yyyymmddhhiiss_email.{format}

The email for UserMailer:didRegister might look something like: 

	20141104101321_darth.theempire.com.html
	20141104101321_darth.theempire.com.txt
	
Note that the email address will have most common symbols converted to a period to prevent any disruptive filenames. 

### Creating Your Own
All custom Mail Services must implement `Myth\Mail\MailServiceInterface`. The interface itself is described on the [Mail Service Interface](interfaces/mailservice) page.