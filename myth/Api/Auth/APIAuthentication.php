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

	protected $logged_in = false;

	protected $realm = 'WallyWorld';

	//--------------------------------------------------------------------

	public function __construct($ci=null)
	{
		parent::__construct($ci);

		$this->ci->config->load('api');

		// Has the IP address been blacklisted?
		if (config_item('auth.ip_blacklist_enabled'))
		{
			$this->checkIPBlacklist();
		}
	}

	//--------------------------------------------------------------------

	/**
	 * Sets the realm used by the authentication. The system truly only
	 * supports a single realm across the entire application, but this
	 * allows it to be set by the controller.
	 *
	 * @param $realm
	 *
	 * @return $this
	 */
	public function setRealm($realm)
	{
	    $this->realm = $realm;
		return $this;
	}

	//--------------------------------------------------------------------

	/**
	 * Checks to see if someone is authorized via HTTP Basic Authentication.
	 *
	 * @return bool
	 */
	public function tryBasicAuthentication()
	{
		$username = null;
		$password = null;

		// mod_php
		if ($this->ci->input->server('PHP_AUTH_USER')) {
			$username = $this->ci->input->server('PHP_AUTH_USER');
			$password = $this->ci->input->server('PHP_AUTH_PW');
		}

		// most other servers
		elseif ($this->ci->input->server('HTTP_AUTHENTICATION')) {
			if (strpos(strtolower($this->ci->input->server('HTTP_AUTHENTICATION')), 'basic') === 0) {
				list($username, $password) = explode(':', base64_decode(substr($this->ci->input->server('HTTP_AUTHORIZATION'), 6)));
			}
		}

		// If credentials weren't provided, we can't do anything
		// so request authorization by the client.
		if (empty($username) || empty($password))
		{
			$this->ci->output->set_header('WWW-Authenticate: Basic realm="'. config_item('api.realm') .'"');
			return false;
		}

		$data = [
			config_item('api.auth_field') => $username,
			'password'  => $password
		];

	    $user = $this->validate($data, true);

		$this->user = $user;

		return $user;
	}

	//--------------------------------------------------------------------

	/**
	 * Checks to see if someone is authorized via HTTP Digest Authentication.
	 */
	public function tryDigestAuthentication()
	{
		$digest_string = '';

		// We need to test which server authentication variable to use
		// because the PHP ISAPI module in IIS acts different from CGI
		if ($this->input->server('PHP_AUTH_DIGEST'))
		{
			$digest_string = $this->input->server('PHP_AUTH_DIGEST');
		}
		elseif ($this->input->server('HTTP_AUTHORIZATION'))
		{
			$digest_string = $this->input->server('HTTP_AUTHORIZATION');
		}

		// No digest string? Then you're done. Go home.
		if (empty($digest_string))
		{
			$this->ci->output->set_header();
		}
	}

	//--------------------------------------------------------------------

	/**
	 * Attempts to log a user into the API via the configured 'api.auth_type'
	 * config variable in config/api.php.
	 *
	 * NOTE: Since this is intended for API use, it is a STATELESS implementation
	 * and does not support remember me functionality.
	 *
	 * This basically replaces the login() method due to the way the AuthTrait
	 * works.
	 *
	 * @return bool
	 */
	public function viaRemember()
	{
		$user = false;

		switch (config_item('api.auth_type'))
		{
			case 'basic':
				$user = $this->tryBasicAuthentication();
				break;
			case 'digest':
				$user = $this->tryDigestAuthentication();
				break;
		}

		if (! $user)
		{
			$this->user = null;
			return $user;
		}

		// If the user is throttled due to too many invalid logins
		// or the system is under attack, kick them back.
		// We need to test for this after validation because we
		// don't want it to affect a valid login.

		// If throttling time is above zero, we can't allow
		// logins now.
		if ($time = (int)$this->isThrottled($user['email']) > 0)
		{
			$this->error = sprintf(lang('auth.throttled'), $time);
			return false;
		}

		$this->loginUser($user);

		Events::trigger('didLogin', [$user]);

		return true;
	}

	//--------------------------------------------------------------------

	//--------------------------------------------------------------------
	// Protected Methods
	//--------------------------------------------------------------------

	/**
	 * Checks the client's IP address against any IP addresses specified
	 * in the api config file. If any are found, the client is refused
	 * access immediately.
	 */
	public function checkIPBlacklist()
	{
	    $blacklist = explode(',', config_item('api.ip_blacklist'));

		array_walk($blacklist, function (&$item, $key) {
			$item = trim($item);
		});

		if (in_array($this->ci->input->ip_address(), $blacklist))
		{
			throw new \Exception('IP Address is denied.', 401);
		}
	}
	
	//--------------------------------------------------------------------

	/**
	 * Handles the nitty gritty of actually logging our user into the system.
	 * Does NOT perform the authentication, just sets the system up so that
	 * it knows we're here.
	 *
	 * @param $user
	 */
	protected function loginUser($user)
	{
		// Save the user for later access
		$this->user = $user;

		// Clear our login attempts
		$this->ci->login_model->purgeLoginAttempts($user['email']);

		// We'll give a 20% chance to need to do a purge since we
		// don't need to purge THAT often, it's just a maintenance issue.
		// to keep the table from getting out of control.
		if (mt_rand(1, 100) < 20)
		{
			$this->ci->login_model->purgeOldRememberTokens();
		}
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
		return $this->logged_in;
	}

	//--------------------------------------------------------------------


}