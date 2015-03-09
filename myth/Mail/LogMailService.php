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

/**
 * Class LogMailService
 *
 * Provides a development-only Mail Service that simply logs files
 * to the current log folder, in an email sub-folder.
 *
 * @package Myth\Mail
 */
class LogMailService implements MailServiceInterface {

    protected $ci = null;

    protected $format = 'html';
    protected $headers = [];
    protected $subject = null;

    protected $html_message = null;
    protected $text_message = null;

    //--------------------------------------------------------------------

    public function __construct()
    {
        $this->ci =& get_instance();

        $this->ci->load->library('email');
    }

    //--------------------------------------------------------------------


    /**
     * Does the actual delivery of a message. In this case, though, we simply
     * write the html and text files out to the log folder/emails.
     *
     * The filename format is: yyyymmddhhiiss_email.{format}
     *
     * @param bool  $clear_after    If TRUE, will reset the class after sending.
     *
     * @return mixed
     */
    public function send($clear_after=true)
    {

        // Ensure we have enough data
        if (empty($this->to) || empty($this->subject) ||
            (empty($this->html_message) && empty($this->text_message))
        )
        {
            throw new \RuntimeException( lang('mail.invalid_log_data') );
        }

        $symbols = ['#', '%', '&', '{', '}', '\\', '/', '<', '>', '*', '?', ' ', '$', '!', '\'', '"', ':', '@', '+', '`', '='];

        $email = str_replace($symbols, '.', strtolower($this->to) );

        $filename = date('YmdHis_'). $email;

        // Ensure the emails folder exists in the log folder.
        $path = config_item('log_path');
        $path = ! empty( $path ) ? $path : APPPATH .'logs/';
        $path = rtrim($path, '/ ') .'/email/';

        if (! is_dir($path))
        {
            mkdir($path, 0777, true);
        }

        get_instance()->load->helper('file');

        // Write our HTML file out
        if (! empty($this->html_message) && ! write_file( $path . $filename . '.html', $this->html_message ) )
        {
            throw new \RuntimeException( sprintf( lang('mail.error_html_log'), $path, $filename) );
        }

        // Write our TEXT file out
        if (! empty($this->text_message) && ! write_file( $path . $filename . '.txt', $this->text_message ) )
        {
            throw new \RuntimeException( sprintf( lang('mail.error_text_log'), $path, $filename) );
        }

        return true;
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
        return;
    }

    //--------------------------------------------------------------------

    /**
     * Sets a header value for the email. Not every service will provide this.
     *
     * @param $field
     * @param $value
     * @return mixed
     */
    public function setHeader($field, $value)
    {
        $this->headers[$field] = $value;

        return $this;
    }

    //--------------------------------------------------------------------

    //--------------------------------------------------------------------
    // Options
    //--------------------------------------------------------------------

    /**
     * Sets the email address to send the email to.
     *
     * @param $email
     * @return mixed
     */
    public function to($email)
    {
        $this->to = $email;

        return $this;
    }

    //--------------------------------------------------------------------

    /**
     * Sets who the email is coming from.
     *
     * @param $email
     * @param null $name
     * @return mixed
     */
    public function from($email, $name=null)
    {
        if (! empty($name))
        {
            $this->from = [$email, $name];
        }
        else
        {
            $this->from = $email;
        }

        return $this;
    }

    //--------------------------------------------------------------------

    /**
     * Sets a single additional email address to 'cc'.
     *
     * @param $email
     * @return mixed
     */
    public function cc($email)
    {
        $this->cc = $email;

        return $this;
    }

    //--------------------------------------------------------------------

    /**
     * Sets a single email address to 'bcc' to.
     *
     * @param $email
     * @return mixed
     */
    public function bcc($email)
    {
        $this->bcc = $email;

        return $this;
    }

    //--------------------------------------------------------------------

    /**
     * Sets the reply to address.
     *
     * @param $email
     * @return mixed
     */
    public function reply_to($email, $name=null)
    {
        if (! empty($name))
        {
            $this->reply_to = [$email, $name];
        }
        else
        {
            $this->reply_to = $email;
        }

        return $this;
    }

    //--------------------------------------------------------------------

    /**
     * Sets the subject line of the email.
     *
     * @param $subject
     * @return mixed
     */
    public function subject($subject)
    {
        $this->subject = $subject;

        return $this;
    }

    //--------------------------------------------------------------------

    /**
     * Sets the HTML portion of the email address. Optional.
     *
     * @param $message
     * @return mixed
     */
    public function html_message($message)
    {
        $this->html_message = $message;

        return $this;
    }

    //--------------------------------------------------------------------

    /**
     * Sets the text portion of the email address. Optional.
     *
     * @param $message
     * @return mixed
     */
    public function text_message($message)
    {
        $this->text_message = $message;

        return $this;
    }

    //--------------------------------------------------------------------

    /**
     * Sets the format to send the email in. Either 'html' or 'text'.
     *
     * @param $format
     * @return mixed
     */
    public function format($format)
    {
        $this->format = $format;

        return $this;
    }

    //--------------------------------------------------------------------
    /**
     * Resets the state to blank, ready for a new email. Useful when
     * sending emails in a loop and you need to make sure that the
     * email is reset.
     *
     * @param bool $clear_attachments
     * @return mixed
     */
    public function reset($clear_attachments=true)
    {
        $this->to = null;
        $this->from = null;
        $this->reply_to = null;
        $this->cc = null;
        $this->bcc = null;
        $this->subject = null;
        $this->html_message = null;
        $this->text_message = null;
        $this->headers = [];

        return $this;
    }

    //--------------------------------------------------------------------
}
