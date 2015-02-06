<?php namespace Myth\Api\Auth;
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

use Myth\Auth\LocalAuthentication;

class APIAuthentication extends LocalAuthentication {



	//--------------------------------------------------------------------

	public function __construct($ci=null)
	{
		parent::__construct($ci);
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
	public function validate($credentials=null, $return_user=false)
	{
		// We will still use the normal user/password combo
	}

	//--------------------------------------------------------------------

	//--------------------------------------------------------------------
	// UNUSED METHOD OVERRIDES
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
	 *
	 * @return bool|mixed|void
	 */
	public function login($credentials, $remember=false)
	{
		throw new \BadMethodCallException('This method is not used in the Authentication class.');
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
		throw new \BadMethodCallException('This method is not used in the Authentication class.');
	}

	//--------------------------------------------------------------------

	/**
	 * Checks whether a user is logged in or not.
	 *
	 * @return bool
	 */
	public function isLoggedIn()
	{
		throw new \BadMethodCallException('This method is not used in the Authentication class.');
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
		throw new \BadMethodCallException('This method is not used in the Authentication class.');
	}

	//--------------------------------------------------------------------

}