<?php

use Myth\Models\CIDbModel as CIDbModel;
use Myth\Auth\LocalAuthentication as Authenticate;
use \Mockery as m;


class LocalAuthenticationTest extends CodeIgniterTestCase {

    protected $auth;

    protected $user_model;

    protected $ci;


    protected $final_user;

    //--------------------------------------------------------------------

    public function __construct()
    {
        parent::__construct();

        $this->final_user = [
            'id' => 15,
            'email' => 'darth@theempire.com',
            'password_hash' => password_hash('father', PASSWORD_BCRYPT),
        ];
    }

    //--------------------------------------------------------------------


    public function _before()
    {
        $this->user_model = m::mock('CIDbModel');
        $session = m::mock('CI_Session');

        $this->ci = get_instance();

        $this->ci->session = $session;

        $this->auth = new Authenticate( $this->ci );
        $this->auth->useModel($this->user_model);
    }

    //--------------------------------------------------------------------

    public function _after()
    {

    }

    //--------------------------------------------------------------------

    public function testIsLoaded()
    {
        $this->assertTrue( class_exists('\Myth\Auth\LocalAuthentication') );
    }

    //--------------------------------------------------------------------

    public function testPasswordLibraryLoaded()
    {
        // Technically, if we are on PHP 5.5+ this will find PHP's
        // built-in command, but that's just fine, also...
        $this->assertTrue( function_exists('password_hash') );
    }

    //--------------------------------------------------------------------

    //--------------------------------------------------------------------
    // validate
    //--------------------------------------------------------------------

    public function testValidateReturnsNullWithNoPassword()
    {
        $data = [
            'email' => 'darth@theempire.com',
        ];

        $result = $this->auth->validate($data);

        $this->assertNull($result);
    }

    //--------------------------------------------------------------------

    public function testValidateReturnsFalseWithUserNotFound()
    {
        $data = [
            'email' => 'darth@theempire.com',
            'password' => 'father'
        ];

        $this->auth->user_model->shouldReceive('where')->with(['email' => 'darth@theempire.com'])->andReturn( $this->auth->user_model );
        $this->auth->user_model->shouldReceive('asArray')->andReturn( $this->user_model );
        $this->auth->user_model->shouldReceive('first')->andReturn(false);

        $result = $this->auth->validate($data);

        $this->assertFalse($result);
    }

    //--------------------------------------------------------------------

    public function testValidateReturnsTrueWithGoodCredentials()
    {
        $data = [
            'email' => 'darth@theempire.com',
            'password' => 'father'
        ];

        $this->auth->user_model->shouldReceive('where')->with(['email' => 'darth@theempire.com'])->andReturn( $this->auth->user_model );
        $this->auth->user_model->shouldReceive('asArray')->andReturn( $this->user_model );
        $this->auth->user_model->shouldReceive('first')->andReturn( $this->final_user );

        $result = $this->auth->validate($data);

        $this->assertTrue($result);
    }

    //--------------------------------------------------------------------

    //--------------------------------------------------------------------
    // Login
    //--------------------------------------------------------------------

    public function testLoginReturnsNullWithNoPassword()
    {
        $creds = array(
            'email' => 'darth@theempire.com'
        );

        $result = $this->auth->login($creds);

        $this->assertNull($result);
    }

    //--------------------------------------------------------------------

    public function testLoginReturnsNullWithNoIdentifier()
    {
        $creds = array(
            'password' => 'father'
        );

        $result = $this->auth->login($creds);

        $this->assertNull($result);
    }

    //--------------------------------------------------------------------

    public function testLoginReturnsFalseWithNoUserFound()
    {
        $creds = array(
            'email' => 'darth@theempire.com',
            'password' => 'father'
        );

        $this->auth->user_model->shouldReceive('where')->with(['email' => 'darth@theempire.com'])->andReturn( $this->auth->user_model );
        $this->auth->user_model->shouldReceive('asArray')->andReturn( $this->auth->user_model );
        $this->auth->user_model->shouldReceive('first')->andReturn( false );

        $result = $this->auth->login($creds);

        $this->assertFalse($result);
    }

    //--------------------------------------------------------------------

    public function testLoginReturnsFalseWithUserFoundAndPasswordsNotMatching()
    {
        $creds = array(
            'email' => 'darth@theempire.com',
            'password' => 'emperordreams'
        );

        $this->auth->user_model->shouldReceive('where')->with(['email' => 'darth@theempire.com'])->andReturn( $this->auth->user_model );
        $this->auth->user_model->shouldReceive('asArray')->andReturn( $this->auth->user_model );
        $this->auth->user_model->shouldReceive('first')->andReturn( $this->final_user );

        $result = $this->auth->login($creds);

        $this->assertFalse($result);
    }

    //--------------------------------------------------------------------

    public function testLoginReturnsTrueWithGoodCreds()
    {
        $creds = array(
            'email' => 'darth@theempire.com',
            'password' => 'father'
        );

        $this->auth->user_model->shouldReceive('where')->with(['email' => 'darth@theempire.com'])->andReturn( $this->auth->user_model );
        $this->auth->user_model->shouldReceive('asArray')->andReturn( $this->auth->user_model );
        $this->auth->user_model->shouldReceive('first')->andReturn( $this->final_user );

        $result = $this->auth->login($creds);

        $this->assertTrue($result);
    }

    //--------------------------------------------------------------------

    public function testLoginSetsUserObjectAndUserGrabsIt()
    {
        $creds = array(
            'email' => 'darth@theempire.com',
            'password' => 'father'
        );

        $this->auth->user_model->shouldReceive('where')->with(['email' => 'darth@theempire.com'])->andReturn( $this->auth->user_model );
        $this->auth->user_model->shouldReceive('asArray')->andReturn( $this->auth->user_model );
        $this->auth->user_model->shouldReceive('first')->andReturn( $this->final_user );

        $result = $this->auth->login($creds);

        $this->assertEquals($this->final_user, $this->auth->user());
    }

    //--------------------------------------------------------------------

    public function testIdReturnsCorrectlyWithValidLogin()
    {
        $creds = array(
            'email' => 'darth@theempire.com',
            'password' => 'father'
        );

        $this->auth->user_model->shouldReceive('where')->with(['email' => 'darth@theempire.com'])->andReturn( $this->auth->user_model );
        $this->auth->user_model->shouldReceive('asArray')->andReturn( $this->auth->user_model );
        $this->auth->user_model->shouldReceive('first')->andReturn( $this->final_user );

        $result = $this->auth->login($creds);

        $this->assertEquals($this->final_user['id'], $this->auth->id());
    }

    //--------------------------------------------------------------------

    //--------------------------------------------------------------------
    // Logout
    //--------------------------------------------------------------------

    public function testLogout()
    {
        $this->ci->session->shouldReceive('sess_destroy');

        $this->auth->logout();
    }

    //--------------------------------------------------------------------

    //--------------------------------------------------------------------
    // Utility Methods
    //--------------------------------------------------------------------

    public function testIsLoggedInReturnsFalseWhenNotLoggedIn()
    {
        $this->assertFalse($this->auth->isLoggedIn());
    }

    //--------------------------------------------------------------------

    public function testIsLoggedInReturnsTrueWhenLoggedIn()
    {
        $creds = array(
            'email' => 'darth@theempire.com',
            'password' => 'father'
        );

        $this->auth->user_model->shouldReceive('where')->with(['email' => 'darth@theempire.com'])->andReturn( $this->auth->user_model );
        $this->auth->user_model->shouldReceive('asArray')->andReturn( $this->auth->user_model );
        $this->auth->user_model->shouldReceive('first')->andReturn( $this->final_user );

        $result = $this->auth->login($creds);

        $this->assertTrue($this->auth->isLoggedIn());
    }

    //--------------------------------------------------------------------

    //--------------------------------------------------------------------
    // Remember Me
    //--------------------------------------------------------------------



}