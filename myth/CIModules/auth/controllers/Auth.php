<?php
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

use \Myth\Route as Route;
use \Myth\Auth\LocalAuthentication as LocalAuthentication;

class Auth extends \Myth\Controllers\ThemedController
{

    public function __construct()
    {
        parent::__construct();

        $this->config->load('auth');
        $this->lang->load('auth');
        $this->load->library('session');
    }

    //--------------------------------------------------------------------

    public function login()
    {
        $this->load->helper('form');

        $auth = new LocalAuthentication();
        $this->load->model('user_model');
        $auth->useModel($this->user_model);

        $redirect_url = $this->session->userdata('redirect_url');

        // No need to login again if they are already logged in...
        if ($auth->isLoggedIn())
        {
            unset($_SESSION['redirect_url']);
            redirect($redirect_url);
        }

        if ($this->input->post())
        {
            $post_data = [
                'email'    => $this->input->post('email'),
                'password' => $this->input->post('password')
            ];

            $remember = (bool)$this->input->post('remember');

            if ($auth->login($post_data, $remember))
            {
	            // Is the user being forced to reset their password?
	            if ($auth->user()['force_pass_reset'] == 1)
	            {
		            redirect( Route::named('change_pass') );
	            }

                unset($_SESSION['redirect_url']);
                $this->setMessage(lang('auth.did_login'), 'success');
                redirect($redirect_url);
            }

            $this->setMessage($auth->error(), 'danger');
        }

        $this->themer->setLayout('login');
        $this->render();
    }

    //--------------------------------------------------------------------

    public function logout()
    {
        $auth = new LocalAuthentication();
        $this->load->model('user_model');
        $auth->useModel($this->user_model);

        if ($auth->isLoggedIn())
        {
            $auth->logout();

            $this->setMessage(lang('auth.did_logout'), 'success');
        }

        redirect('/');
    }

    //--------------------------------------------------------------------

    public function register()
    {
        $this->load->helper('form');

        if ($this->input->post())
        {
            $auth = new LocalAuthentication();
            $this->load->model('user_model');
            $auth->useModel($this->user_model);

            $post_data = [
                'first_name'   => $this->input->post('first_name'),
                'last_name'    => $this->input->post('last_name'),
                'email'        => $this->input->post('email'),
                'username'     => $this->input->post('username'),
                'password'     => $this->input->post('password'),
                'pass_confirm' => $this->input->post('pass_confirm')
            ];

            if ($auth->registerUser($post_data))
            {
                $this->setMessage(lang('auth.did_register'), 'success');
                redirect( Route::named('login') );
            }
            else
            {
                $this->setMessage($auth->error(), 'danger');
            }
        }

        $this->addScript('register.js');
        $this->themer->setLayout('login');
        $this->render();
    }

    //--------------------------------------------------------------------

    public function activate_user()
    {
        $this->load->helper('form');

        if ($this->input->post())
        {
            $auth = new LocalAuthentication();
            $this->load->model('user_model');
            $auth->useModel($this->user_model);

            $post_data = [
                  'email' => $this->input->post('email'),
                  'code'  => $this->input->post('code')
            ];

            if ($auth->activateUser($post_data))
            {
                $this->setMessage(lang('auth.did_activate'), 'success');
                redirect( Route::named('login') );
            }
            else
            {
                $this->setMessage($auth->error(), 'danger');
            }
        }

        $data = [
            'email' => $this->input->get('e'),
            'code'  => $this->input->get('code')
        ];

        $this->themer->setLayout('login');
        $this->render($data);
    }

    //--------------------------------------------------------------------


    public function forgot_password()
    {
        $this->load->helper('form');

        if ($this->input->post())
        {
            $auth = new LocalAuthentication();
            $this->load->model('user_model');
            $auth->useModel($this->user_model);

            if ($auth->remindUser($this->input->post('email')))
            {
                $this->setMessage(lang('auth.send_success'), 'success');
                redirect( Route::named('reset_pass') );
            }
            else
            {
                $this->setMessage($auth->error(), 'danger');
            }
        }

        $this->themer->setLayout('login');
        $this->render();
    }

    //--------------------------------------------------------------------

    public function reset_password()
    {
        $this->load->helper('form');

        if ($this->input->post())
        {
            $auth = new LocalAuthentication();
            $this->load->model('user_model');
            $auth->useModel($this->user_model);

            $credentials = [
                'email' => $this->input->post('email'),
                'code'  => $this->input->post('code')
            ];

            $password     = $this->input->post('password');
            $pass_confirm = $this->input->post('pass_confirm');

            if ($auth->resetPassword($credentials, $password, $pass_confirm))
            {
                $this->setMessage(lang('auth.new_password_success'), 'success');
                redirect( Route::named('login') );
            }
            else
            {
                $this->setMessage($auth->error(), 'danger');
            }
        }

        $data = [
            'email' => $this->input->get('e'),
            'code'  => $this->input->get('code')
        ];

        $this->addScript('register.js');
        $this->themer->setLayout('login');
        $this->render($data);
    }

    //--------------------------------------------------------------------

	/**
	 * Allows a logged in user to enter their current password
	 * and create a new one. Often used as part of the force password
	 * reset process, but could be used within a user area.
	 */
	public function change_password()
	{
		$auth = new LocalAuthentication();
		$this->load->model('user_model');
		$auth->useModel($this->user_model);

		if (! $auth->isLoggedIn())
		{
			redirect( Route::named('login') );
		}

		$this->load->helper('form');

		if ($this->input->post())
		{
			$current_pass = $this->input->post('current_pass');
			$password     = $this->input->post('password');
			$pass_confirm = $this->input->post('pass_confirm');

			// Does the current password match?
			if (! password_verify($current_pass, $auth->user()['password_hash']))
			{
				$this->setMessage( lang('auth.bad_current_pass'), 'warning');
				redirect( current_url() );
			}

			// Do the passwords match?
			if ($password != $pass_confirm)
			{
				$this->setMessage( lang('auth.pass_must_match'), 'warning');
				redirect( current_url() );
			}

			$hash = \Myth\Auth\Password::hashPassword($password);

			if (! $this->user_model->update( $auth->id(), ['password_hash' => $hash, 'force_pass_reset' => 0]) )
			{
				$this->setMessage( 'Error: '. $this->user_model->error(), 'danger');
				redirect( current_url() );
			}

			$redirect_url = $this->session->userdata('redirect_url');
			unset($_SESSION['redirect_url']);

			$this->setMessage( lang('auth.new_password_success'), 'success' );

			$auth->logout();

			redirect( Route::named('login') );
		}

		$this->addScript('register.js');
		$this->themer->setLayout('login');
		$this->render();
	}

	//--------------------------------------------------------------------


    //--------------------------------------------------------------------
    // AJAX Methods
    //--------------------------------------------------------------------

    /**
     * Checks the password strength and returns pass/fail.
     *
     * @param $str
     */
    public function password_check($str)
    {
        $this->load->helper('auth/password');

        $str = urldecode( base64_decode($str) );

        $strength = isStrongPassword($str);

        $this->renderJSON(['status' => $strength ? 'pass' : 'fail']);
    }

    //--------------------------------------------------------------------

}
