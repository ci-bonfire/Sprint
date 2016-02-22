<?php

$I = new AcceptanceTester($scenario);
$I->wantTo('login a valid user');

// Test user
$I->haveInDatabase('users', [
	'id'    => 1,
	'email' => 'tester@example.com',
	'username' => 'tester',
	'password_hash' => '$2y$10$wEzfeu4AdmR4mQSit3TCH.je1THv/Z8XqzDI4AOCov4lssqeA/gwS',  // 'mylittlepony'
	'created_on' => date('Y-m-d H:i:s', strtotime('-1 month')),
	'active' => 1,
	'deleted' => 0,
	'force_pass_reset' => 0
]);

// First - ensure that we are logged out
$I->amOnPage('/logout');

$I->amOnPage('/login');

//--------------------------------------------------------------------
// Error without info
//--------------------------------------------------------------------

$I->expect('the form is not submitted');
$I->seeElement('#submit');
$I->submitForm('form', [], '#submit');
$I->seeElement('.alert-danger');

//--------------------------------------------------------------------
// Error without bad password
//--------------------------------------------------------------------

$I->expect('error returned due to bad password');
$I->seeElement('#submit');
$I->submitForm('#login_form', ['email' => 'tester@example.com', 'password' => 'badstuff'], '#submit');
$I->seeElement('.alert-danger');

$I->expect('login attempt was logged');
$I->seeInDatabase('auth_login_attempts', ['user_id' => '1']);

//--------------------------------------------------------------------
// Error without bad email
//--------------------------------------------------------------------

$I->expect('error returned due to bad email');
$I->seeElement('#submit');
$I->submitForm('#login_form', ['email' => 'testy@examples.com', 'password' => 'mylittlepony'], '#submit');
$I->seeElement('.alert-danger');

$I->expect('login attempt was logged');
$I->seeInDatabase('auth_login_attempts', ['ip_address' => '::1']);

//--------------------------------------------------------------------
// Successfully login
//--------------------------------------------------------------------

$I->expect('am logged in');
$I->seeElement('#submit');
$I->submitForm('#login_form', ['email' => 'tester@example.com', 'password' => 'mylittlepony'], '#submit');
$I->dontSeeElement('.alert-danger');

$I->expect('login action was logged');
$I->seeInDatabase('auth_logins', ['user_id' => 1]);

$I->expect('login attempts were cleared');
$I->dontSeeInDatabase('auth_login_attempts', ['user_id' => '1']);