<?php

namespace Myth\Mail;

/**
 * Class BaseMailer
 *
 * Provides the basic functionality that a Mailer will need,
 * along with the ability to configure the email, etc.
 *
 * @package Myth\Mail
 */
class BaseMailer {

    /**
     * How the email is delivered.
     * Either 'send' or 'queue'.
     * @var string
     */
    protected $action = 'send';

    protected $from     = null;
    protected $to       = null;
    protected $reply_to = null;
    protected $cc       = null;
    protected $bcc      = null;

    protected $theme    = 'email';
    protected $layout   = 'index';
    protected $view     = null;

    /**
     * The MailService to use. If NULL
     * will use the system default.
     * @var null
     */
    protected $service_name  = null;

    protected $service = null;

    /**
     * Used for theming the email messages.
     * @var null
     */
    protected $themer = null;

    //--------------------------------------------------------------------

    /**
     * Constructor
     *
     * Simply allows us to override the default settings for this mailer.
     *
     * @param null $options
     */
    public function __construct($options=null)
    {
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

    //--------------------------------------------------------------------

    /**
     * Sends an email immediately using the system-defined MailService.
     *
     * @param string $to        // Who the email is being sent to.
     * @param string $subject   // The subject line for the email
     * @param strign $data      // the key/value pairs to send to the views.
     * @param string $view      // You can override the view used for the email here.
     *                          // You can change themes by prepending theme name
     *                          // like: 'newtheme:newview'
     */
    public function send($to, $subject, $data=[], $view=null)
    {
        $this->startMailService($this->service_name);

        $this->service->to($to);
        $this->service->subject($subject);

        if (is_array($this->from)) {
            $this->service->from($this->from[0], $this->from[1]);
        }
        else
        {
            $this->service->from($this->from);
        }

        if (! empty($this->cc))         $this->service->cc($this->cc);
        if (! empty($this->bcc))        $this->service->bcc($this->bcc);
        if (! empty($this->reply_to))   $this->service->reply_to($this->reply_to);


        // Determine the view to use. We have to hack this a bit with
        // the debug_backtrace, though, to make it all function in the background.
        list(, $method) = debug_backtrace(false);

        $view = 'emails/'. strtolower( get_class($this) ) .'/'. $method['function'];

        // Get our message's text and html versions based on which files exist...
        $basepath = APPPATH .'views/'. $view;

        // Is a text version available?
        if (file_exists($basepath .'.text.php'))
        {
            $text = get_instance()->load->view($view .'.text.php', $data, true);
            $this->service->text_message($text);
        }

        // If an html version is around, we need to theme it out
        if (file_exists($basepath .'.html.php')) {
            $this->startThemer();

            $this->themer->setTheme($this->theme);

            // Determine the correct layout to use
            $layout = ! empty($this->layout) ? $this->layout : NULL;
            $this->themer->setLayout($layout);

            $this->themer->set($data);

            // Render the view into a var we can pass to the layout.
            $content = $this->themer->display($view .'.html.php');

            $this->themer->set('content', $content);

            $this->service->html_message( $this->themer->display($this->theme .':'. $layout) );
        }

        if (! $this->service->send() )
        {
            // todo do something here
            return false;
        }

        return true;
    }

    //--------------------------------------------------------------------

    /**
     * Adds the email to the queue to be sent later. Uses Myth\Mail\Mail:queue.
     *
     * @param string $to        // Who the email is being sent to.
     * @param string $subject   // The subject line for the email
     * @param array  $data      // The key/value pairs to send to the view.
     * @param string $view      // You can override the view used for the email here.
     *                          // You can change themes by prepending theme name
     *                          // like: 'newtheme:newview'
     */
    public function queue($to, $subject, $data=[], $view=null)
    {

    }

    //--------------------------------------------------------------------

    /**
     * Allows you to customize the headers sent with the email. You can
     * do them one at a time by passing $field and $value, or pass an array
     * of $field => $value pairs as the first parameter.
     *
     * @param string|array  $field
     * @param string        $value
     */
    public function header($field, $value=null)
    {
        $this->startMailService();

        $this->service->setHeader($field, $value);
    }

    //--------------------------------------------------------------------

    /**
     * Adds an attachment to the current email that is being built.
     *
     * @param string    $filename
     * @param string    $disposition    like 'inline'. Default is 'attachment'
     * @param string    $newname        If you'd like to rename the file for delivery
     * @param string    $mime           Custom defined mime type.
     */
    public function attach($filename, $disposition=null, $newname=null, $mime=null)
    {
        $this->startMailService();

        $this->service->attach($filename, $disposition, $newname, $mime);
    }

    //--------------------------------------------------------------------

    //--------------------------------------------------------------------
    // Private Methods
    //--------------------------------------------------------------------

    /**
     * Starts up the service name specified in $service_name.
     *
     * @param $service_name
     */
    private function startMailService($service_name=null)
    {
        // Only once!
        if (! empty($this->service) && is_object($this->service))
            return;

        if (empty($service_name)) {
            $service_name = ! empty($this->service_name) ? $this->service_name : config_item('mail.default_service');
        }

        if (! class_exists($service_name))
        {
            throw new \RuntimeException("Unable to find Mail Service: {$service_name}");
        }

        $this->service = new $service_name();
    }

    //--------------------------------------------------------------------

    /**
     * Fires up the default themer so we can use it to theme our HTML messages.
     */
    private function startThemer()
    {
        /*
         * Setup our Template Engine
         */
        $themer = config_item('active_themer');

        if (empty($themer)) {
            throw new \RuntimeException('No Themer chosen.');
        }

        $this->themer = new $themer( get_instance() );

        // Register our paths with the themer
        $paths = config_item('theme.paths');

        foreach ($paths as $key => $path) {
            $this->themer->addThemePath($key, $path);
        }

        // Set our default theme.
        $this->themer->setDefaultTheme( 'email' );
    }

    //--------------------------------------------------------------------

    /**
     * __get magic
     *
     * Allows models to access CI's loaded classes using the same
     * syntax as controllers.
     *
     * @param	string	$key
     */
    public function __get($key)
    {
        return get_instance()->$key;
    }

    //--------------------------------------------------------------------

}