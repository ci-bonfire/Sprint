<?php namespace Myth\Auth;

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

trait AuthTrait {

	/**
	 * Instance of Authentication Class
	 * @var null
	 */
	public $authenticate = null;

	/**
	 * Instance of Authorization class
	 * @var null
	 */
	public $authorize = null;

	private $classes_loaded = false;

	//--------------------------------------------------------------------

	/**
	 * Verifies that a user is logged in
	 *
	 * @param null $uri
	 */
	public function restrict($uri=null)
	{
	    $this->setupAuthClasses();

		if ($this->authenticate->isLoggedIn())
		{
			return true;
		}

		if (method_exists($this, 'setMessage'))
		{
			$this->setMessage( lang('auth.not_logged_in') );
		}

		if (empty($uri))
		{
			redirect( \Myth\Route::named('login') );
		}

		redirect($uri);
	}

	//--------------------------------------------------------------------


	/**
	 * Ensures that the current user is in at least one of the passed in
	 * groups. The groups can be passed in as either ID's or group names.
	 * You can pass either a single item or an array of items.
	 *
	 * If the user is not a member of one of the groups will return
	 * the user to the page they just came from as shown in
	 * $_SERVER['']
	 *
	 * Example:
	 *  restrictToGroups([1, 2, 3]);
	 *  restrictToGroups(14);
	 *  restrictToGroups('admins');
	 *  restrictToGroups( ['admins', 'moderators'] );
	 *
	 * @param mixed  $groups
	 * @param string $uri   The URI to redirect to on fail.
	 *
	 * @return bool
	 */
	public function restrictToGroups($groups, $uri='')
	{
	    $this->setupAuthClasses();

		if ($this->authenticate->isLoggedIn())
		{
			if ($this->authorize->inGroup($groups, $this->authenticate->id() ) )
			{
				return true;
			}
		}

		if (method_exists($this, 'setMessage'))
		{
			$this->setMessage( lang('auth.not_enough_privilege') );
		}

		if (empty($uri))
		{
			redirect( \Myth\Route::named('login') .'?request_uri='. current_url() );
		}

		redirect($uri .'?request_uri='. current_url());
	}

	//--------------------------------------------------------------------

	/**
	 * Ensures that the current user has at least one of the passed in
	 * permissions. The permissions can be passed in either as ID's or names.
	 * You can pass either a single item or an array of items.
	 *
	 * If the user does not have one of the permissions it will return
	 * the user to the URI set in $url or the site root, and attempt
	 * to set a status message.
	 *
	 * @param $permissions
	 * @param string $uri   The URI to redirect to on fail.
	 *
	 * @return bool
	 */
	public function restrictWithPermissions($permissions, $uri='')
	{
	    $this->setupAuthClasses();

		if ($this->authenticate->isLoggedIn())
		{
			if ($this->authorize->hasPermission($permissions, $this->authenticate->id() ) )
			{
				return true;
			}
		}

		if (method_exists($this, 'setMessage'))
		{
			$this->setMessage( lang('auth.not_enough_privilege') );
		}

		if (empty($uri))
		{
			redirect( \Myth\Route::named('login') .'?request_uri='. current_url() );
		}

		redirect($uri .'?request_uri='. current_url());
	}

	//--------------------------------------------------------------------

	/**
	 * Ensures that the Authentication and Authorization libraries are
	 * loaded and ready to go, if they are not already.
	 *
	 * Uses the following config values:
	 *      - auth.authenticate_lib
	 *      - auth.authorize_lib
	 */
	public function setupAuthClasses()
	{
		if ($this->classes_loaded)
		{
			return;
		}

		get_instance()->config->load('auth');
		get_instance()->load->language('auth/auth');

		/*
		 * Authentication
		 */
		$auth = config_item('auth.authenticate_lib');

		if (empty($auth)) {
			throw new \RuntimeException( lang('auth.no_authenticate') );
		}

		$this->authenticate = new $auth( get_instance() );

		get_instance()->load->model('auth/user_model', 'user_model', true);
		$this->authenticate->useModel( get_instance()->user_model );

		// Try to log us in automatically.
		if (! $this->authenticate->isLoggedIn())
		{
			$this->authenticate->viaRemember();
		}

		/*
		 * Authorization
		 */
		$auth = config_item('auth.authorize_lib');

		if (empty($auth)) {
			throw new \RuntimeException( lang('auth.no_authenticate') );
		}

		$this->authorize = new $auth();
	}

	//--------------------------------------------------------------------

}