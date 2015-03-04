<?php

$I = new AcceptanceTester($scenario);
$I->wantTo('create a new user account');

$I->amOnPage('/join');

//--------------------------------------------------------------------
// Error without info
//--------------------------------------------------------------------

$I->expect('the form is not submitted');
$I->seeElement('#submit');
$I->submitForm('form', [], 'submit');
$I->seeElement('.alert-danger');

//--------------------------------------------------------------------
// Error with weak password
//--------------------------------------------------------------------

$fields = [
	'first_name' => 'Test',
	'last_name'  => 'User',
	'email' => 'tester@example.com',
	'username' => 'tester',
	'password' => '123456',
	'pass_confirm' => '123456'
];

$I->expect('the form is not submitted');
$I->submitForm('form', $fields, 'submit');
$I->seeElement('.alert-danger');

//--------------------------------------------------------------------
// Can create user
//--------------------------------------------------------------------

$fields = [
	'first_name' => 'Test',
	'last_name'  => 'User',
	'email' => 'tester@example.com',
	'username' => 'tester',
	'password' => 'mylittlepony',
	'pass_confirm' => 'mylittlepony'
];

$I->expect('the form is submitted and a user is created');
$I->submitForm('form', $fields, 'submit');
$I->dontSeeElement('.alert-danger');
