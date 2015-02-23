<?php

class UserMailer extends \Myth\Mail\BaseMailer {

    protected $from     = null;
    protected $to       = null;

    public function __construct()
    {
        $this->from = [ config_item('site.auth_email'), config_item('site.name') ];
    }

    //--------------------------------------------------------------------

    /**
     * Sends the output from the cronjob to the admin.
     *
     * The params array contains:
     *  - user_id
     *  - email
     *  - token
     *
     * @param $params
     * @return bool
     */
    public function didRegister($params=null)
    {
        $data = [
            'user_id'   => $params['user_id'],
            'email'     => $params['email'],
            'link'      => site_url( \Myth\Route::named('activate_user') ),
            'token'     => $params['token'],
            'site_name' => config_item('site.name'),
            'site_link' => site_url()
        ];

        // Send it immediately - don't queue.
        return $this->send($params['email'], lang('auth.register_subject'), $data);
    }

    //--------------------------------------------------------------------

    /**
     * Sends the Password Reset Instructions email.
     *
     * @param array  $user
     * @param string $token
     * @return bool
     */
    public function remindUser($user, $token)
    {
        $data = [
            'email' => $user['email'],
            'code'  => $token,
            'link'  => site_url( \Myth\Route::named('reset_pass') ),
            'site_name' => config_item('site.name'),
            'site_link' => site_url()
        ];

        // Send it immediately - don't queue.
        return $this->send($user['email'], lang('auth.remind_subject'), $data);
    }

    //--------------------------------------------------------------------

    /**
     * Sends the Password Reset Confirmation email.
     *
     * @param array  $user
     * @return bool
     */
    public function resetPassword($user)
    {
        $data = [
            'email' => $user['email'],
            'link'  => site_url( \Myth\Route::named('forgot_pass') ),
            'site_name' => config_item('site.name'),
            'site_link' => site_url()
        ];

        // Send it immediately - don't queue.
        return $this->send($user['email'], lang('auth.reset_subject'), $data);
    }

    //--------------------------------------------------------------------
}
