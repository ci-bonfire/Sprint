<?php

namespace Myth\Mail;

/**
 * Class CIMailService
 *
 * Simply provides an interface for CodeIgniter's built-in email
 * library as a Mail Service.
 *
 * @package Myth\Mail
 */
class CIMailService implements MailServiceInterface {

    protected $ci = null;

    protected $format = 'html';

    //--------------------------------------------------------------------

    public function __construct()
    {
        $this->ci =& get_instance();

        $this->ci->load->library('email');
    }

    //--------------------------------------------------------------------


    /**
     * Does the actual delivery of a message.
     *
     * @param bool  $clear_after    If TRUE, will reset the class after sending.
     *
     * @return mixed
     */
    public function send($clear_after=true)
    {
        return $this->ci->email->send($clear_after);
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
        $this->ci->email->attaach($filename, $disposition, $newname, $mime);
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
        $this->ci->email->set_header($field, $value);

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
        $this->ci->email->to($email);

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
        $this->ci->email->from($email, $name);

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
        $this->ci->email->cc($email);

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
        $this->ci->email->bcc($email);

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
        $this->ci->email->reply_to($email, $name);
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
        $this->ci->email->subject($subject);

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
        $this->ci->email->message($message);

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
        $this->ci->email->set_alt_message($message);

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
        $this->ci->email->clear($clear_attachments);

        return $this;
    }

    //--------------------------------------------------------------------
}