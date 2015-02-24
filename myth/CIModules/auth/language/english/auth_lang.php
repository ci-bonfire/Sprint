<?php

$lang['auth.no_authenticate']       = 'No Authentication System chosen.';
$lang['auth.no_authorize']          = 'No Authorization System chosen.';
$lang['auth.not_enough_privilege']  = 'You do not have sufficient privileges to view that page.';
$lang['auth.not_logged_in']         = 'You must be logged in to view that page.';

$lang['auth.did_login']             = 'Welcome back!';
$lang['auth.did_logout']            = 'You have been logged out. Come back soon!';
$lang['auth.did_register']          = 'Account created. Please login.';
$lang['auth.unkown_register_error'] = 'Unable to create user currently. Please try again later.';

$lang['auth.invalid_user']          = 'Invalid credentials. Please try again.';
$lang['auth.invalid_credentials']   = 'Credentials used are not allowed.';
$lang['auth.too_many_credentials']  = 'Too many credentials were passed in. Must be limited to one besides password.';
$lang['auth.invalid_email']         = 'Unable to find a user with that email address.';
$lang['auth.invalid_password']      = 'Unable to find a valid login with that password.';
$lang['auth.bruteBan_notice']       = "Your account has had excessive login attempts. To protect the account you must wait 15 minutes before another attempt can be made.";

$lang['auth.remember_label']        = 'Remember me on this device';
$lang['auth.email']                 = 'Email Address';
$lang['auth.password']              = 'Password';
$lang['auth.pass_confirm']          = 'Password (Again)';
$lang['auth.signin']                = 'Sign In';
$lang['auth.register']              = 'Join Us!';

$lang['auth.password_strength']     = 'Password Strength';
$lang['auth.pass_not_strong']       = 'The Password must be stronger.';

$lang['auth.have_account']          = 'Already a member? <a href="'. site_url( \Myth\Route::named('login') ) .'">Sign In</a>';
$lang['auth.need_account']          = 'Need an Account? <a href="'. site_url( \Myth\Route::named('register') ) .'">Sign Up</a>';
$lang['auth.forgot_pass']           = '<a href="'. site_url( \Myth\Route::named('forgot_pass') ) .'">Forgot your Password?</a>';

$lang['auth.first_name']            = 'First Name';
$lang['auth.last_name']             = 'Last Name';
$lang['auth.username']              = 'Username';

$lang['auth.forgot']                = 'Forgot Password';
$lang['auth.forgot_note']           = 'No problem. Enter your email and we will send instructions.';
$lang['auth.send']                  = 'Send';
$lang['auth.send_success']          = 'The email is on its way!';

$lang['auth.reset']                 = 'Reset Password';
$lang['auth.reset_note']            = 'Please follow the instructions in the email to reset your password.';
$lang['auth.pass_code']             = 'Reset Code';
$lang['auth.new_password']          = 'Choose a New Password';
$lang['auth.new_password_success']  = 'Your password has been changed. Please sign in.';

$lang['auth.activate_account']      = 'Activate Account';
$lang['auth.activate']              = 'Activate';
$lang['auth.inactive_account']      = 'Your account is not active.';

$lang['auth.register_subject']      = "Open to activate your account at ". config_item('site.name');
$lang['auth.activate_no_user']      = 'Unable to find a user with those credentials.';
$lang['auth.remind_subject']        = "Here's how to reset your password...";
$lang['auth.need_reset_code']       = 'You must provide the Reset Code.';
$lang['auth.reset_no_user']         = 'Unable to find an account with that email and reset code. Please try again.';
$lang['auth.reset_subject']         = "Your password has been reset.";

$lang['auth.permission_not_found']  = 'Unable to locate that Permission.';
$lang['auth.group_not_found']       = 'Unable to locate that Group.';

$lang['auth.throttled']             = 'Not currently allowed. Please wait %s seconds before trying again.';
