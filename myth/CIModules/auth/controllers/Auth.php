<?php

use \Myth\Route as Route;
use \Myth\Auth\LocalAuthentication as LocalAuthentication;

class Auth extends \Myth\Controllers\ThemedController
{

    public function __construct()
    {
        parent::__construct();

        $this->config->load('auth');
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
        $auth->logout();

        $this->setMessage('You have been logged out. Come back soon!', 'success');

        redirect('/');
    }

    //--------------------------------------------------------------------

    public function register()
    {
        $this->load->helper('form');

        if ($this->input->post()) {

            $this->load->model('user_model');

            $post_data = [
                'first_name'   => $this->input->post('first_name'),
                'last_name'    => $this->input->post('last_name'),
                'email'        => $this->input->post('email'),
                'username'     => $this->input->post('username'),
                'password'     => $this->input->post('password'),
                'pass_confirm' => $this->input->post('pass_confirm'),
                'role_id'      => config_item('auth.default_role_id')
            ];

            if ($id = $this->user_model->insert($post_data)) {
                $this->setMessage('Account created. Please login.');
                redirect(Route::named('login'));
            } else {
                if (validation_errors()) {
                    $this->setMessage(validation_errors(), 'danger');
                } else {
                    $this->setMessage('Unable to create user currently. Please try again later.', 'warning');
                    log_message('error', 'User Creation Error: ' . $this->user_model->error());
                }
            }
        }

        $this->addScript('register.js');
        $this->themer->setLayout('login');
        $this->render();
    }

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