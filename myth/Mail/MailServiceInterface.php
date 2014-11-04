<?php

namespace Myth\Mail;

/**
 * Interface MailService
 *
 * Standardizes the Interface used to send email via
 * different services. Sprint ships with a CIMailService
 * that uses the built-in Email library to send emails, but
 * you could also create new interfaces for Postmark or Mandrill.
 *
 * @package Myth\Interfaces
 */
interface MailServiceInterface {

    /**
     * Does the actual delivery of a message.
     *
     * @param bool  $clear_after    If TRUE, will reset the class after sending.
     *
     * @return mixed
     */
    public function send($clear_after=true);

    //--------------------------------------------------------------------

    /**
     * Adds an attachment to the current email that is being built.
     *
     * @param string    $filename
     * @param string    $disposition    like 'inline'. Default is 'attachment'
     * @param string    $newname        If you'd like to rename the file for delivery
     * @param string    $mime           Custom defined mime type.
     */
    public function attach($filename, $disposition=null, $newname=null, $mime=null);

    //--------------------------------------------------------------------

    /**
     * Sets a header value for the email. Not every service will provide this.
     *
     * @param $field
     * @param $value
     * @return mixed
     */
    public function setHeader($field, $value);

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
    public function to($email);
    
    //--------------------------------------------------------------------

    /**
     * Sets who the email is coming from.
     *
     * @param $email
     * @param null $name
     * @return mixed
     */
    public function from($email, $name=null);

    //--------------------------------------------------------------------

    /**
     * Sets a single additional email address to 'cc'.
     *
     * @param $email
     * @return mixed
     */
    public function cc($email);

    //--------------------------------------------------------------------

    /**
     * Sets a single email address to 'bcc' to.
     *
     * @param $email
     * @return mixed
     */
    public function bcc($email);

    //--------------------------------------------------------------------

    /**
     * Sets the reply to address.
     *
     * @param $email
     * @param $name
     * @return mixed
     */
    public function reply_to($email, $name=null);

    //--------------------------------------------------------------------

    /**
     * Sets the subject line of the email.
     *
     * @param $subject
     * @return mixed
     */
    public function subject($subject);

    //--------------------------------------------------------------------

    /**
     * Sets the HTML portion of the email address. Optional.
     *
     * @param $message
     * @return mixed
     */
    public function html_message($message);

    //--------------------------------------------------------------------

    /**
     * Sets the text portion of the email address. Optional.
     *
     * @param $message
     * @return mixed
     */
    public function text_message($message);

    //--------------------------------------------------------------------

    /**
     * Sets the format to send the email in. Either 'html' or 'text'.
     *
     * @param $format
     * @return mixed
     */
    public function format($format);

    //--------------------------------------------------------------------

    /**
     * Resets the state to blank, ready for a new email. Useful when
     * sending emails in a loop and you need to make sure that the
     * email is reset.
     *
     * @param bool $clear_attachments
     * @return mixed
     */
    public function reset($clear_attachments=true);

    //--------------------------------------------------------------------
}