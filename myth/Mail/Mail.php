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
    public static function queue($mailer_name, $params=[], $options=[])
    {
        $data = [
            'mailer'    => $mailer_name,
            'params'    => serialize($params),
            'options'   => serialize($options)
        ];

        $queue = new \Myth\Mail\Queue();

        return $queue->insert($data);
    }

    //--------------------------------------------------------------------

    /**
     * Processes the Email queue sending out emails in chunks.
     * Typically used in a cronjob to send out all queued emails.
     *
     * @param int $chunk_size   // How many emails to send per batch.
     * @return string           // The output of the cronjob...
     */
    public static function process($chunk_size=50)
    {
        $db = new \Myth\Mail\Queue();

        // Grab our batch of emails to process
        $queue = $db->find_many_by('sent', 0);

        if (! $queue)
        {
            // We didn't have an error, we simply
            // didn't have anything to do.
            return true;
        }

        $output = 'Started processing email Queue at '. date('Y-m-d H:i:s') .".\n\n";

        foreach ($queue as $item)
        {
            try {
                if (! Mail::deliver($item->mailer, unserialize($item->params), unserialize($item->options))) {
                    $output .= '[FAILED] ';
                } else {
                    $data = [
                        'sent'    => 1,
                        'sent_on' => date('Y-m-d H:i:s')
                    ];

                    $db->update($item->id, $data);
                }

                $output .= "ID: {$item->id}, Mailer: {$item->mailer}. \n";
            }
            catch (\Exception $e)
            {
                $output .= "[EXCEPTION] ". $e->getMessage() ."\n";
            }
        }

        $output .= "Done processing email Queue at ". date('H:i:s') .".\n";

        return $output;
    }

    //--------------------------------------------------------------------


}