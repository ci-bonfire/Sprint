<?php namespace Myth\Api\Auth;
use Myth\Auth\AuthenticateInterface;

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

class HTTPBasicAuthentication implements AuthenticateInterface {

	protected $user = null;

	//--------------------------------------------------------------------

	/**
	 * Attempt to log a user into the system.
	 *
	 * $credentials is an array of key/value pairs needed to log the user in.
	 * This is often email/password, or username/password.
	 *
	 * NOTE: Since this is intended for API use, it is a STATELESS implementation
	 * and does not support remember me functionality.
	 *
	 * Valid credentials:
	 *  - username
	 *  - email
	 *  - realm
	 *
	 * @param $credentials
	 * @param bool $remember
	 */
	public function login($credentials, $remember=false)
	{

	}

	//--------------------------------------------------------------------

	/**
	 * Validates user login information without logging them in.
	 *
	 * $credentials is an array of key/value pairs needed to log the user in.
	 * This is often email/password, or username/password.
	 *
	 * NOTE: Since this is intended for API use, it is a STATELESS implementation
	 * and does not support remember me functionality.
	 *
	 * @param $credentials
	 * @param bool $return_user
	 * @return mixed
	 */
	public function validate($credentials, $return_user=false)
	{

	}

	//--------------------------------------------------------------------

	/**
	 * Logs a user out and removes all session information.
	 *
	 * NOTE: Since this is intended for API use, it is a STATELESS implementation
	 * and does not support remember me functionality.
	 *
	 * @return mixed
	 */
	public function logout()
	{
		return true;
	}

	//--------------------------------------------------------------------

	/**
	 * Checks whether a user is logged in or not.
	 *
	 * @return bool
	 */
	public function isLoggedIn()
	{

	}

	//--------------------------------------------------------------------

	/**
	 * Attempts to log a user in based on the "remember me" cookie.
	 *
	 * NOTE: Since this is intended for API use, it is a STATELESS implementation
	 * and does not support remember me functionality.
	 *
	 * @return bool
	 */
	public function viaRemember()
	{
		return false;
	}

	//--------------------------------------------------------------------

	/**
	 * Registers a new user and handles activation method.
	 *
	 * @param $user_data
	 * @return bool
	 */
	public function registerUser($user_data)
	{

	}

	//--------------------------------------------------------------------

	/**
	 * Used to verify the user values and activate a user so they can
	 * visit the site.
	 *
	 * @param $data
	 * @return bool
	 */
	public function activateUser($data)
	{

	}

	//--------------------------------------------------------------------

	/**
	 * Used to allow manual activation of a user with a known ID.
	 *
	 * @param $id
	 * @return bool
	 */
	public function activateUserById($id)
	{

	}

	//--------------------------------------------------------------------

	/**
	 * Grabs the current user object. Returns NULL if nothing found.
	 *
	 * @return array|null
	 */
	public function user()
	{
		return $this->user;
	}

	//--------------------------------------------------------------------

	/**
	 * A convenience method to grab the current user's ID.
	 *
	 * @return int|null
	 */
	public function id()
	{

	}

	//--------------------------------------------------------------------

	/**
	 * Tells the system to start throttling a user. This may vary by implementation,
	 * but will often add additional time before another login is allowed.
	 *
	 * @param $email
	 * @return mixed
	 */
	public function isThrottled($email)
	{

	}

	//--------------------------------------------------------------------

	/**
	 * Sends a password reminder email to the user associated with
	 * the passed in $email.
	 *
	 * @param $email
	 * @return mixed
	 */
	public function remindUser($email)
	{

	}

	//--------------------------------------------------------------------

	/**
	 * Validates the credentials provided and, if valid, resets the password.
	 *
	 * @param $credentials
	 * @param $password
	 * @param $passConfirm
	 * @return mixed
	 */
	public function resetPassword($credentials, $password, $passConfirm)
	{

	}

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
	public function changeStatus($newStatus, $message=null)
	{

	}

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
	public function useModel($model)
	{

	}

	//--------------------------------------------------------------------

	/**
	 * Returns the current error string.
	 *
	 * @return mixed
	 */
	public function error()
	{

	}

	//--------------------------------------------------------------------

	/**
	 * Purges all login attempt records from the database.
	 *
	 * @param $email
	 */
	public function purgeLoginAttempts($email)
	{

	}

	//--------------------------------------------------------------------

	/**
	 * Purges all remember tokens for a single user. Effectively logs
	 * a user out of all devices. Intended to allow users to log themselves
	 * out of all devices as a security measure.
	 *
	 * @param $email
	 */
	public function purgeRememberTokens($email)
	{
		return true;
	}

	//--------------------------------------------------------------------
}