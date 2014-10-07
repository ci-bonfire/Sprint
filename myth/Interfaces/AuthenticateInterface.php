<?php

namespace Myth\Interfaces;

/**
 * Class AuthenticateInterface
 *
 * Provides standard interface for the authentication process.
 * This handles Authentication (determining if the user is who
 * they say they are) only. Authorization (determining if a user
 * is allowed to do something) is handled via the AuthorizeInterface.
 *
 * @package Myth\Interfaces
 */
interface AuthenticateInterface {

    /**
     * Attempt to log a user into the system.
     *
     * $credentials is an array of key/value pairs needed to log the user in.
     * This is often email/password, or username/password.
     *
     * @param array $credentials
     * @param bool  $remember
     * @param null  $redirect
     */
    public function login($credentials, $remember=false, $redirect=null);

    //--------------------------------------------------------------------

    /**
     * Validates user login information without logging them in.
     *
     * $credentials is an array of key/value pairs needed to log the user in.
     * This is often email/password, or username/password.
     *
     * @param array $credentials
     * @return mixed
     */
    public function validate($credentials);

    //--------------------------------------------------------------------

    /**
     * Logs a user out and removes all session information.
     *
     * @return mixed
     */
    public function logout();

    //--------------------------------------------------------------------

    /**
     * Checks whether a user is logged in or not.
     *
     * @return bool
     */
    public function isLoggedIn();

    //--------------------------------------------------------------------

    /**
     * Attempts to log a user in based on the "remember me" cookie.
     *
     * @return bool
     */
    public function viaRemember();

    //--------------------------------------------------------------------

    /**
     * Grabs the current user object. Returns NULL if nothing found.
     *
     * @return object|null
     */
    public function user();

    //--------------------------------------------------------------------

    /**
     * A convenience method to grab the current user's ID.
     *
     * @return int|null
     */
    public function id();

    //--------------------------------------------------------------------

    /**
     * Tells the system to start throttling a user. This may vary by implementation,
     * but will often add additional time before another login is allowed.
     *
     * @param $userId
     * @return mixed
     */
    public function throttle($userId);

    //--------------------------------------------------------------------

    /**
     * Sends a password reminder email to the user associated with
     * the passed in $email.
     *
     * @param $email
     * @return mixed
     */
    public function remindUser($email);

    //--------------------------------------------------------------------

    /**
     * Validates the credentials provided and, if valid, resets the password.
     *
     * @param $credentials
     * @return mixed
     */
    public function resetPassword($credentials);

    //--------------------------------------------------------------------

    /**
     * Provides a way for implementations to allow new statuses to be set
     * on the user. The details will vary based upon implementation, but
     * will often allow for banning or suspending users.
     *
     * @param $newStatus
     * @param null $message
     * @return mixed
     */
    public function changeStatus($newStatus, $message=null);

    //--------------------------------------------------------------------

    /**
     * Allows the consuming application to pass in a reference to the
     * model that should be used.
     *
     * The model MUST extend Myth\Models\CIDbModel.
     *
     * @param $model
     * @return mixed
     */
    public function useModel($model);

    //--------------------------------------------------------------------

}