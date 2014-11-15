<?php

use \Myth\Route as Route;
use \Myth\Auth\LocalAuthentication as LocalAuthentication;

class Auth extends \Myth\Controllers\ThemedController
{

    public function __construct()
    {
        parent::__construct();

        $this->config->load('auth');
        $this->lang->load('auth');
        $this->load->driver('session');
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
            $this->session->unset_userdata('redirect_url');
            redirect($redirect_url);
        }

        if ($this->input->post()) {

            $post_data = [
                'email'     => $this->input->post('email'),
                'password'  => $this->input->post('password')
            ];

            $remember = (bool)$this->input->post('remember');

            if ($auth->login($post_data, $remember))
            {
                $this->session->unset_userdata('redirect_url');
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

        if ($this->input->post()) {

            $auth = new LocalAuthentication();
            $this->load->model('user_model');
            $auth->useModel($this->user_model);

            $post_data = [
                'first_name'   => $this->input->post('first_name'),
                'last_name'    => $this->input->post('last_name'),
                'email'        => $this->input->post('email'),
                'username'     => $this->input->post('username'),
                'password'     => $this->input->post('password'),
                'pass_confirm' => $this->input->post('pass_confirm'),
                'role_id'      => config_item('auth.default_role_id')
            ];

            if ($auth->registerUser($post_data)) {
                $this->setMessage(lang('auth.did_register'), 'success');
                redirect( Route::named('login') );
            } else {
                $this->setMessage( $auth->error(), 'danger' );
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
                  'email'   => $this->input->post('email'),
                  'code'   => $this->input->post('code')
            ];

            if ($auth->activateUser($post_data))
            {
                $this->setMessage(lang('auth.did_activate'), 'success');
                redirect( Route::named('login') );
            }
            else
            {
                $this->setMessage( $auth->error(), 'danger');
            }
        }

        $data = [
            'email' =>  $this->input->get('e'),
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

            if ($auth->remindUser( $this->input->post('email') ))
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
                'email'         => $this->input->post('email'),
                'code'          => $this->input->post('code')
            ];
            $password = $this->input->post('password');
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
            'email' =>  $this->input->get('e'),
            'code'  => $this->input->get('code')
        ];

        $this->addScript('register.js');
        $this->themer->setLayout('login');
        $this->render($data);
    }

    //--------------------------------------------------------------------




    //--------------------------------------------------------------------
    // AJAX Methods
    //--------------------------------------------------------------------

    /**
     * Checks the password strength and returns pass/fail.
     *
     * @param null $str
     */
    public function password_check($str)
    {
        $this->load->helper('auth/password');

        $strength = isStrongPassword($str);

        $this->renderJSON(['status' => $strength ? 'pass' : 'fail']);
    }

    //--------------------------------------------------------------------


}