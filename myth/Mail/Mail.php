<?php

namespace Myth\Mail;

class Mail {

    /**
     * Sends an email, using an existing Mailer, which can
     * be found in application/mailers/. The mailer is the one responsible
     * for determining whether the email will be sent immediately or queued
     * to be sent later.
     *
     * The $mailer_name must include both the mailer name as well as the
     * task, separated by a single colon:
     *      'UserMailer:newUser'
     *
     * @param $mailer_name
     * @param array $params
     * @param array $options
     * @return mixed
     */
    public static function deliver($mailer_name, $params=[], $options=[])
    {
        // Protect users from themselves here.
        str_replace('::', ':', $mailer_name);

        // Try to load our mailer class.
        list($class, $method) = explode(':', $mailer_name);

        if (! is_file(APPPATH .'mailers/'. $class .'.php'))
        {
            throw new \RuntimeException("Unable to locate the mailer: {$class}");
        }

        require_once APPPATH .'mailers/'. $class .'.php';

        if (! class_exists($class, false))
        {
            throw new \RuntimeException("Unable to create instance of class: {$class}");
        }

        $mailer = new $class( $options );

        // try to deliver the mail, but don't send back the contents
        // since we don't want to force the mailers to return anything.
        if (call_user_func_array([$mailer, $method], $params) )
        {
            return true;
        }

        return false;
    }

    //--------------------------------------------------------------------

    /**
     * Adds an item to the email queue to be sent out next time.
     */
    public function queue()
    {

    }

    //--------------------------------------------------------------------

    /**
     * Processes the Email queue sending out emails in chunks.
     */
    public function process($chunk_size=50)
    {

    }

    //--------------------------------------------------------------------


}