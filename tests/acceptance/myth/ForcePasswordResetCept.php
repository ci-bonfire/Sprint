<?php

$I = new AcceptanceTester($scenario);
$I->wantToTest('force password reset redirects properly');

// Test user
$I->haveInDatabase('users', [
	'id'    => 1,
	'email' => 'tester@example.com',
	'username' => 'tester',
	'password_hash' => '$2y$10$wEzfeu4AdmR4mQSit3TCH.je1THv/Z8XqzDI4AOCov4lssqeA/gwS',  // 'mylittlepony'
	'created_on' => date('Y-m-d H:i:s', strtotime('-1 month')),
	'active' => 1,
	'deleted' => 0,
	'force_pass_reset' => 1
]);

// First - ensure that we are logged out
$I->amOnPage('/logout');

$I->amOnPage('/login');


//--------------------------------------------------------------------
// Successfully login
//--------------------------------------------------------------------

$I->expect('am sent to change password page');
$I->seeElement('#submit');
$I->submitForm('#login_form', ['email' => 'tester@example.com', 'password' => 'mylittlepony'], '#submit');
$I->dontSeeElement('.alert-danger');
$I->see('Change Password');
$I->seeInCurrentUrl('password');