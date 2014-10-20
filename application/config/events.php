<?php

use \Myth\Events as Events;

//--------------------------------------------------------------------
// EVENTS
//--------------------------------------------------------------------
// This file lets you subscribe functionality to happen at different
// Event points throughout the application.
//
// These Events are separate from CodeIgniter's built-in Hooks, and are
// slightly more flexible, using features in PHP 5.4+.
//

//--------------------------------------------------------------------
// User Authentication Events
//--------------------------------------------------------------------

// Send New User Registration Email
Events::on('didRegisterUser', function($data) {

    if ($data['method'] != 'email')
    {
        return true;
    }

    $ci =& get_instance();

    // If method is 'email', we need to fire off the email...
    $ci->load->library('email');

    $ci->email->to($data['email']);
    $ci->email->from(config_item('site.auth_email'), config_item('site.name'));
    $ci->email->subject( lang('auth.register_subject') );

    $data = [
        'user_id'   => $data['user_id'],
        'email'     => $data['email'],
        'link'      => site_url( \Myth\Route::named('activate_user') ),
        'token'     => $data['token'],
        'site_name' => config_item('site.name')
    ];

    $ci->email->message( $ci->load->view('emails/activation', $data, true) );

    if (! $ci->email->send(false))
    {
        log_message('error', $ci->email->print_debugger(array('headers')) );
    }

}, EVENTS_PRIORITY_NORMAL);

//--------------------------------------------------------------------

// Send Forgotten Password email
Events::on('didRemindUser', function($user, $token) {

    // Send the email
    $ci =& get_instance();

    $ci->load->library('email');

    $ci->email->to($user['email']);
    $ci->email->from(config_item('site.auth_email'), config_item('site.name'));
    $ci->email->subject( lang('auth.remind_subject') );

    $data = [
        'email' => $user['email'],
        'code'  => $token,
        'link'  => site_url( \Myth\Route::named('reset_pass') ),
        'site_name' => config_item('site.name')
    ];

    $ci->email->message( $ci->load->view('emails/forgot_password', $data, true) );

    if (! $ci->email->send(false))
    {
        log_message('error', $ci->email->print_debugger(array('headers')) );
    }

}, EVENTS_PRIORITY_NORMAL);

//--------------------------------------------------------------------

// Send Reset Password notice
Events::on('didResetPassword', function($user) {

    // Send a transactional email
    $ci =& get_instance();

    $ci->load->library('email');

    $ci->email->to($user['email']);
    $ci->email->from(config_item('site.auth_email'), config_item('site.name'));
    $ci->email->subject( lang('auth.reset_subject') );

    $data = [
        'email' => $user['email'],
        'link'  => site_url( \Myth\Route::named('forgot_pass') ),
        'site_name' => config_item('site.name')
    ];

    $ci->email->message( $ci->load->view('emails/password_reset', $data, true) );

    if (! $ci->email->send(false))
    {
        log_message('error', $ci->email->print_debugger(array('headers')) );
    }

}, EVENTS_PRIORITY_NORMAL);