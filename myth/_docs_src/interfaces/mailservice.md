# MailServiceInterface
[Mail Services](http://sprint.dev/docs/developer/general/email#mail_services) are used to handle the actual construction and sending of emails through different providers. You might create new MailServices to handle sending mail via third party services like Postmark or Mandrill.

## send()
Does the actual delivery of the email. All of the information must have been already set through other class methods (like `to` and `from`) before calling this method. 

The only parameter is a boolean value that tells the system whether all values should be reset after sending the email. This should default to `true`.

## attach()
Allows an attachment to be sent along with the email. Follows the same format as CodeIgniter's email class. Please see [their docs](http://www.codeigniter.com/user_guide/libraries/email.html) for more details.

The first parameter is the `filename`. The second parameter is the disposition. The third parameter is the name you'd like to rename the file to for delivery. The fourth parameter is a custom mime type that you want to send the file as.

## setHeader()
Allows custom headers to be set for this emai only. Not all services will allow this.

The first parameter is the field name, while the second is the value to set the header to.

## to()
Accepts the email address that the email should be delivered to. An array can be provided to set the value to more than one email address.

	$mailService->to( 'darth@theempire.com');
	$mailService->to( ['darth@theempire.com', 'luke@skywalkerranch.com'] );

##  from()
Sets the values that the email should come from. The first parameter is the email address. The second, optional, parameter is the name to display.

## cc()
Sets one or an array of emails that this email should be CC'd to.

## bcc()
Sets one or an array of emails that this email should be BCC'd to.

## reply_to()
Sets the reply to address for this email. The first parameter is the email address to use. The second, optional, parameter is the name to display.

## subject()
Sets the subject line to be used for the email. 

## html_message()
Takes a single string that sets any HTML content to be sent along with this email, if any.

## text_message()
Takes a single string that sets any TEXT content to be sent along with this email, if any.

## format()
Sets the format to send the email as. Either 'html' or 'text'. 

## reset()
Resets all class values. By default, it will erase the attachments, also. If you want the attachment to stay so you can send it to other people, you can pass in FALSE as the only parameter.