<?php

use \Myth\Events as Events;
use Myth\Mail\Mail as Mail;

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

    return Mail::deliver('UserMailer:didRegister', [$data]);

}, EVENTS_PRIORITY_NORMAL);

//--------------------------------------------------------------------

// Send Forgotten Password email
Events::on('didRemindUser', function($user, $token) {

    return Mail::deliver('UserMailer:remindUser', [$user, $token]);

}, EVENTS_PRIORITY_NORMAL);

//--------------------------------------------------------------------

// Send Reset Password notice
Events::on('didResetPassword', function($user) {

    return Mail::deliver('UserMailer:resetPassword', [$user]);

}, EVENTS_PRIORITY_NORMAL);



//--------------------------------------------------------------------
// Cron Job Events
//--------------------------------------------------------------------

// Send Cron Job Summary Email
Events::on('afterCron', function($output) {

    return Mail::queue('CronMailer:results', [$output]);

}, EVENTS_PRIORITY_NORMAL);