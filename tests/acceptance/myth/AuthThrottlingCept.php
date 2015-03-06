<?php

$I = new AcceptanceTester($scenario);
$I->wantToTest('users are throttled under brute force attacks');

// Test user
$I->haveInDatabase('users', [
	'id'    => 1,
	'email' => 'tester@example.com',
	'username' => 'tester',
	'password_hash' => '$2y$10$wEzfeu4AdmR4mQSit3TCH.je1THv/Z8XqzDI4AOCov4lssqeA/gwS',  // 'mylittlepony'
	'created_on' => date('Y-m-d H:i:s', strtotime('-1 month')),
	'timezone' => 'UM6',
	'language' => 'english',
	'active' => 1,
	'deleted' => 0
]);

// First - ensure that we are logged out
$I->amOnPage('/logout');

$I->amOnPage('/login');

// Now populate the auth_login_attempts table with 101 attempts
for ($i = 0; $i <= 101; $i++)
{
//	$I->submitForm('form', ['email' => 'tester@example.com', 'password' => 'badstuff'], 'submit');
	$I->haveInDatabase('auth_login_attempts', [
		'email' => 'tester@example.com',
		'datetime' => date('Y-m-d H:i:s')
	]);
}

//--------------------------------------------------------------------
// Try to login - should be on same page with error
//--------------------------------------------------------------------

$I->submitForm('#login_form', ['email' => 'tester@example.com', 'password' => 'mylittlepony'], '#submit');
$I->seeElement('.alert-danger');
$I->see('throttled');
$I->dontSeeInDatabase('auth_logins', [
	'user_id' => 1,
	'ip_address' => '127.0.0.1'
]);
