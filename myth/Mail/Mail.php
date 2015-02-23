<?php namespace Myth\Mail;
/**
 * Sprint
 *
 * A set of power tools to enhance the CodeIgniter framework and provide consistent workflow.
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 *
 * @package     Sprint
 * @author      Lonnie Ezell
 * @copyright   Copyright 2014-2015, New Myth Media, LLC (http://newmythmedia.com)
 * @license     http://opensource.org/licenses/MIT  (MIT)
 * @link        http://sprintphp.com
 * @since       Version 1.0
 */

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

        if (! method_exists($mailer, $method))
        {
            throw new \BadMethodCallException("Mailer method does not exist: {$class}::{$method}");
        }

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
     *
     * @param string $mailer_name
     * @param array $params
     * @param array $options
     * @param \Myth\Mail\Queue $queue
     *
     * @return mixed
     */
    public static function queue($mailer_name, $params=[], $options=[], &$queue=null)
    {
        $data = [
            'mailer'    => $mailer_name,
            'params'    => serialize($params),
            'options'   => serialize($options)
        ];

        if (empty($queue))
        {
            $queue = new \Myth\Mail\Queue();
        }

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
    public static function process($chunk_size=50, &$db=null)
    {
        if (empty($db))
        {
            $db = new \Myth\Mail\Queue();
        }

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