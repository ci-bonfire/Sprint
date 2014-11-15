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
     * Registers a new user and handles activation method.
     *
     * @param $user_data
     * @return bool
     */
    public function registerUser($user_data);

    //--------------------------------------------------------------------

    /**
     * Used to verify the user values and activate a user so they can
     * visit the site.
     *
     * @param $data
     * @return bool
     */
    public function activateUser($data);

    //--------------------------------------------------------------------

    /**
     * Used to allow manual activation of a user with a known ID.
     *
     * @param $id
     * @return bool
     */
    public function activateUserById($id);

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
     * @param $email
     * @return mixed
     */
    public function isThrottled($email);

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
    public function resetPassword($credentials, $password, $passConfirm);

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

    /**
     * Returns the current error string.
     *
     * @return mixed
     */
    public function error();

    //--------------------------------------------------------------------

    /**
     * Purges all login attempt records from the database.
     *
     * @param $email
     */
    public function purgeLoginAttempts($email);

    //--------------------------------------------------------------------

    /**
     * Purges all remember tokens for a single user. Effectively logs
     * a user out of all devices. Intended to allow users to log themselves
     * out of all devices as a security measure.
     *
     * @param $email
     */
    public function purgeRememberTokens($email);

    //--------------------------------------------------------------------

}