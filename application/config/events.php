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
if (!defined('BASEPATH')) exit('No direct script access allowed');

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