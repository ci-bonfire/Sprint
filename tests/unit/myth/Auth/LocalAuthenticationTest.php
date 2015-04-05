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
            'active' => 1
        ];
    }

    //--------------------------------------------------------------------


    public function _before()
    {
        $this->user_model = m::mock('User_model');
        $session = m::mock('CI_Session');

        $this->ci = get_instance();

        $this->ci->load->model('auth/login_model');

        $this->ci->session = $session;
        $this->ci->login_model = m::mock('Login_model');

        $this->auth = new Authenticate( $this->ci );
        $this->auth->useModel($this->user_model, true);

    }

    //--------------------------------------------------------------------

    public function _after()
    {

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
        $this->auth->user_model->shouldReceive('as_array')->andReturn( $this->user_model );
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
        $this->auth->user_model->shouldReceive('as_array')->andReturn( $this->user_model );
        $this->auth->user_model->shouldReceive('first')->andReturn( $this->final_user );

        $result = $this->auth->validate($data);

        $this->assertTrue($result);
    }

    //--------------------------------------------------------------------

	public function testValidateReturnsFalseWithInvalidField()
	{
		$data = [
			'display_name' => 'darth@theempire.com',
			'password' => 'father'
		];

		$result = $this->auth->validate($data);

		$this->assertFalse($result);
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

	    $this->ci->login_model->shouldReceive('recordLoginAttempt');

        $result = $this->auth->login($creds);

        $this->assertNull($result);
    }

    //--------------------------------------------------------------------

    public function testLoginReturnsNullWithNoIdentifier()
    {
        $creds = array(
            'password' => 'father'
        );

        $this->auth->user_model->shouldReceive('select')->andReturn( $this->auth->user_model );
        $this->auth->user_model->shouldReceive('where')->andReturn( $this->auth->user_model );
        $this->auth->user_model->shouldReceive('first')->andReturn( null );

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
        $this->auth->user_model->shouldReceive('as_array')->andReturn( $this->auth->user_model );
        $this->auth->user_model->shouldReceive('first')->andReturn( false );

	    $this->ci->login_model->shouldReceive('recordLoginAttempt');

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
        $this->auth->user_model->shouldReceive('as_array')->andReturn( $this->auth->user_model );
        $this->auth->user_model->shouldReceive('first')->andReturn( $this->final_user );
        $this->ci->login_model->shouldReceive('recordLoginAttempt');

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
        $this->auth->user_model->shouldReceive('as_array')->andReturn( $this->auth->user_model );
        $this->auth->user_model->shouldReceive('first')->andReturn( $this->final_user );
        $this->ci->session->shouldReceive('set_userdata')->with('logged_in', true);
        $this->ci->login_model->shouldReceive('purgeLoginAttempts')->with('darth@theempire.com');
        $this->ci->login_model->shouldReceive('recordLogin')->with($this->final_user);
        $this->ci->login_model->shouldReceive('purgeOldRememberTokens')->zeroOrMoreTimes();
        $this->ci->login_model->shouldReceive('distributedBruteForceTime')->andReturn(0);
        $this->ci->login_model->shouldReceive('lastLoginAttemptTime')->andReturn(0);
        $this->ci->login_model->shouldReceive('countLoginAttempts')->andReturn(0);

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
        $this->auth->user_model->shouldReceive('as_array')->andReturn( $this->auth->user_model );
        $this->auth->user_model->shouldReceive('first')->andReturn( $this->final_user );
        $this->ci->session->shouldReceive('set_userdata')->with('logged_in', true);
        $this->ci->login_model->shouldReceive('purgeLoginAttempts')->with('darth@theempire.com');
        $this->ci->login_model->shouldReceive('recordLogin')->with($this->final_user);
        $this->ci->login_model->shouldReceive('purgeOldRememberTokens')->zeroOrMoreTimes();
        $this->ci->login_model->shouldReceive('distributedBruteForceTime')->andReturn(0);
        $this->ci->login_model->shouldReceive('lastLoginAttemptTime')->andReturn(0);
        $this->ci->login_model->shouldReceive('countLoginAttempts')->andReturn(0);

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
        $this->auth->user_model->shouldReceive('as_array')->andReturn( $this->auth->user_model );
        $this->auth->user_model->shouldReceive('first')->andReturn( $this->final_user );
        $this->ci->session->shouldReceive('set_userdata')->with('logged_in', true);
        $this->ci->login_model->shouldReceive('purgeLoginAttempts')->with('darth@theempire.com');
        $this->ci->login_model->shouldReceive('recordLogin')->with($this->final_user);
        $this->ci->login_model->shouldReceive('purgeOldRememberTokens')->zeroOrMoreTimes();
        $this->ci->login_model->shouldReceive('distributedBruteForceTime')->andReturn(0);
        $this->ci->login_model->shouldReceive('lastLoginAttemptTime')->andReturn(0);
        $this->ci->login_model->shouldReceive('countLoginAttempts')->andReturn(0);

        $result = $this->auth->login($creds);

        $this->assertEquals($this->final_user['id'], $this->auth->id());
    }

    //--------------------------------------------------------------------

    //--------------------------------------------------------------------
    // Logout
    //--------------------------------------------------------------------

    public function testLogout()
    {
        $this->ci->session->shouldReceive('sess_regenerate');
        $this->ci->login_model->shouldReceive('deleteRememberToken')->once();

        $this->auth->logout();
    }

    //--------------------------------------------------------------------

    //--------------------------------------------------------------------
    // Throttling
    //--------------------------------------------------------------------

    /**
     * @group throttle
     */
    public function testThrottlingReturnsFalseIfNotThrottledWithFirstFailedAttempt()
    {
        $email = 'darth@theempire.com';

        // Not under a distributed brute force attack.
        $this->ci->login_model->shouldReceive('distributedBruteForceTime')->once()->andReturn(0);
        // Not under a brute force attack
        $this->ci->session->shouldReceive('userdata')->with('bruteBan')->once()->andReturn(false);
        $this->ci->login_model->shouldReceive('lastLoginAttemptTime')->with($email)->once()->andReturn(0);
        $this->ci->login_model->shouldReceive('countLoginAttempts')->with($email)->once()->andReturn(0);

        $this->assertFalse($this->auth->isThrottled($email));
    }

    //--------------------------------------------------------------------

    /**
     * @group throttle
     */
    public function testThrottlingReturnsFalseIfNotThrottledWithAllowedFailedAttempts()
    {
        $email = 'darth@theempire.com';

        // Not under a distributed brute force attack.
        $this->ci->login_model->shouldReceive('distributedBruteForceTime')->once()->andReturn(0);
        // Not under a brute force attack
        $this->ci->session->shouldReceive('userdata')->with('bruteBan')->once()->andReturn(false);
        $this->ci->login_model->shouldReceive('lastLoginAttemptTime')->with($email)->once()->andReturn(0);
        $this->ci->login_model->shouldReceive('countLoginAttempts')->with($email)->once()->andReturn(3);

        $this->assertFalse($this->auth->isThrottled($email));
    }

    //--------------------------------------------------------------------

    /**
     * @group throttle
     */
    public function testThrottlingReturnsTimeWhenThrottled()
    {
        $email = 'darth@theempire.com';

        // Not under a distributed brute force attack.
        $this->ci->login_model->shouldReceive('distributedBruteForceTime')->once()->andReturn(0);
        // Not under a brute force attack
        $this->ci->session->shouldReceive('userdata')->with('bruteBan')->once()->andReturn(false);
        $this->ci->login_model->shouldReceive('lastLoginAttemptTime')->with($email)->once()->andReturn( time() );
        $this->ci->login_model->shouldReceive('countLoginAttempts')->with($email)->once()->andReturn(6);

        $this->assertEquals(2, $this->auth->isThrottled($email));
    }

    //--------------------------------------------------------------------

    /**
     * @group throttle
     */
    public function testThrottlingReturnsTimeWhenThrottled2()
    {
        $email = 'darth@theempire.com';

        // Not under a distributed brute force attack.
        $this->ci->login_model->shouldReceive('distributedBruteForceTime')->once()->andReturn(0);
        // Not under a brute force attack
        $this->ci->session->shouldReceive('userdata')->with('bruteBan')->once()->andReturn(false);
        $this->ci->login_model->shouldReceive('lastLoginAttemptTime')->with($email)->once()->andReturn( time() );
        $this->ci->login_model->shouldReceive('countLoginAttempts')->with($email)->once()->andReturn(7);

        $this->assertEquals(4, $this->auth->isThrottled($email));
    }

    //--------------------------------------------------------------------

    /**
     * @group throttle
     */
    public function testThrottlingReturnsTimeWhenThrottled3()
    {
        $email = 'darth@theempire.com';

        // Not under a distributed brute force attack.
        $this->ci->login_model->shouldReceive('distributedBruteForceTime')->once()->andReturn(0);
        // Not under a brute force attack
        $this->ci->session->shouldReceive('userdata')->with('bruteBan')->once()->andReturn(false);
        $this->ci->login_model->shouldReceive('lastLoginAttemptTime')->with($email)->once()->andReturn( time() );
        $this->ci->login_model->shouldReceive('countLoginAttempts')->with($email)->once()->andReturn(8);

        $this->assertEquals(8, $this->auth->isThrottled($email));
    }

    //--------------------------------------------------------------------

    /**
     * @group throttle
     */
    public function testThrottlingReturnsTimeWhenThrottled4()
    {
        $email = 'darth@theempire.com';

        // Not under a distributed brute force attack.
        $this->ci->login_model->shouldReceive('distributedBruteForceTime')->once()->andReturn(0);
        // Not under a brute force attack
        $this->ci->session->shouldReceive('userdata')->with('bruteBan')->once()->andReturn(false);
        $this->ci->login_model->shouldReceive('lastLoginAttemptTime')->with($email)->once()->andReturn( time() );
        $this->ci->login_model->shouldReceive('countLoginAttempts')->with($email)->once()->andReturn(9);

        $this->assertEquals(16, $this->auth->isThrottled($email));
    }

    //--------------------------------------------------------------------

    /**
     * @group throttle
     */
    public function testThrottlingReturnsTimeWhenThrottled5()
    {
        $email = 'darth@theempire.com';

        // Not under a distributed brute force attack.
        $this->ci->login_model->shouldReceive('distributedBruteForceTime')->once()->andReturn(0);
        // Not under a brute force attack
        $this->ci->session->shouldReceive('userdata')->with('bruteBan')->once()->andReturn(false);
        $this->ci->login_model->shouldReceive('lastLoginAttemptTime')->with($email)->once()->andReturn( time() );
        $this->ci->login_model->shouldReceive('countLoginAttempts')->with($email)->once()->andReturn(10);

        $this->assertEquals(32, $this->auth->isThrottled($email));
    }

    //--------------------------------------------------------------------

    /**
     * @group throttle
     */
    public function testThrottlingReturnsTimeWhenThrottled6AndIsAboveMaxLimit()
    {
        $email = 'darth@theempire.com';

        // Not under a distributed brute force attack.
        $this->ci->login_model->shouldReceive('distributedBruteForceTime')->once()->andReturn(0);
        // Not under a brute force attack
        $this->ci->session->shouldReceive('userdata')->with('bruteBan')->once()->andReturn(false);
        $this->ci->login_model->shouldReceive('lastLoginAttemptTime')->with($email)->once()->andReturn( time() );
        $this->ci->login_model->shouldReceive('countLoginAttempts')->with($email)->once()->andReturn(11);

        $this->assertEquals(45, $this->auth->isThrottled($email));
    }

    //--------------------------------------------------------------------

    /**
     * @group throttle
     */
    public function testThrottlingUnderBruteForceFirstTime()
    {
        $email = 'darth@theempire.com';

        // Not under a distributed brute force attack.
        $this->ci->login_model->shouldReceive('distributedBruteForceTime')->once()->andReturn(0);
        // Not under a brute force attack
        $this->ci->session->shouldReceive('userdata')->with('bruteBan')->once()->andReturn(false);
        $this->ci->login_model->shouldReceive('lastLoginAttemptTime')->with($email)->once()->andReturn( time() );
        $this->ci->login_model->shouldReceive('countLoginAttempts')->with($email)->once()->andReturn(101);
        $this->ci->login_model->shouldReceive('isBruteForced')->with($email)->once()->andReturn(true);

        // Should store current time+15 minutes in the session
        $this->ci->session->shouldReceive('set_userdata')->with('bruteBan', time() + (60*15))->once();

        $this->assertEquals(60*15, $this->auth->isThrottled($email));
    }

    //--------------------------------------------------------------------

    /**
     * @group throttle
     */
    public function testThrottlingUnderPreviousBruteForce()
    {
        $email = 'darth@theempire.com';

        $bruteTime = (60*14) + time();
        $_SESSION['bruteBan'] = $bruteTime;

        // Not under a distributed brute force attack.
        $this->ci->login_model->shouldReceive('distributedBruteForceTime')->once()->andReturn(0);
        // Not under a brute force attack
//        $this->ci->session->shouldReceive('userdata')->with('bruteBan')->once()->andReturn( $bruteTime );
        $this->ci->login_model->shouldReceive('lastLoginAttemptTime')->with($email)->never();
        $this->ci->login_model->shouldReceive('countLoginAttempts')->with($email)->never();
        $this->ci->login_model->shouldReceive('isBruteForced')->with($email)->never();

        // Should store current time+15 minutes in the session
        $this->ci->session->shouldReceive('set_userdata')->with('bruteBan', time() + (60*15))->never();

        $this->assertEquals($bruteTime - time(), $this->auth->isThrottled($email));
        unset($_SESSION['bruteBan']);
    }

    //--------------------------------------------------------------------

    /**
     * @group throttle
     */
    public function testThrottlingUnderPreviousBruteForceWithDBrute()
    {
        $email = 'darth@theempire.com';

        $bruteTime = (60*14) + time();
        $_SESSION['bruteBan'] = $bruteTime;

        // Not under a distributed brute force attack.
        $this->ci->login_model->shouldReceive('distributedBruteForceTime')->once()->andReturn(45);
        // Not under a brute force attack
//        $this->ci->session->shouldReceive('userdata')->with('bruteBan')->once()->andReturn( $bruteTime );
        $this->ci->login_model->shouldReceive('lastLoginAttemptTime')->with($email)->never();
        $this->ci->login_model->shouldReceive('countLoginAttempts')->with($email)->never();
        $this->ci->login_model->shouldReceive('isBruteForced')->with($email)->never();

        // Should store current time+15 minutes in the session
        $this->ci->session->shouldReceive('set_userdata')->with('bruteBan', time() + (60*15))->never();

        $this->assertEquals($bruteTime - time() + 45, $this->auth->isThrottled($email));
        unset($_SESSION['bruteBan']);
    }

    //--------------------------------------------------------------------

    /**
     * @group throttle
     */
    public function testThrottlingWithAllowedAttemptsUnderDBrute()
    {
        $email = 'darth@theempire.com';

        // Not under a distributed brute force attack.
        $this->ci->login_model->shouldReceive('distributedBruteForceTime')->once()->andReturn(45);
        // Not under a brute force attack
        $this->ci->session->shouldReceive('userdata')->with('bruteBan')->once()->andReturn(false);
        $this->ci->login_model->shouldReceive('lastLoginAttemptTime')->with($email)->once()->andReturn( strtotime('-10 seconds') );
        $this->ci->login_model->shouldReceive('countLoginAttempts')->with($email)->once()->andReturn(3);

        $this->assertEquals(35, $this->auth->isThrottled($email));
    }

    //--------------------------------------------------------------------

    /**
     * @group throttle
     */
    public function testThrottlingThroughLoginMethod()
    {
        $creds = [
            'email' => 'darth@theempire.com',
            'password' => 'iwantthethrone'
        ];

        $user = [
            'id' => 54,
            'email' => 'darth@theempire.com',
            'password_hash' => password_hash('iwantthethrone', PASSWORD_DEFAULT),
            'active' => 1
        ];

        // Validate
        $this->user_model->shouldReceive('as_array')->andreturn( $this->user_model );
        $this->user_model->shouldReceive('where')->andreturn( $this->user_model );
        $this->user_model->shouldReceive('first')->andreturn( $user );

        // Not under a distributed brute force attack.
        $this->ci->login_model->shouldReceive('distributedBruteForceTime')->once()->andReturn(0);

        // Are under a brute force attack
        $this->ci->session->shouldReceive('userdata')->with('bruteBan')->once()->andReturn(45);
        $this->ci->login_model->shouldReceive('lastLoginAttemptTime')->with($creds['email'])->once()->andReturn( strtotime('-10 seconds') );
        $this->ci->login_model->shouldReceive('countLoginAttempts')->andReturn(113);

        $this->ci->login_model->shouldReceive('isBruteForced')->andReturn(true);


        $result = $this->auth->login($creds);

        // Should set the ban time in the session.
        $this->assertEquals( time() + (60 * 15), $_SESSION['bruteBan'] );
        // Login should return false.
        $this->assertFalse( $result);
    }

    //--------------------------------------------------------------------


    //--------------------------------------------------------------------
    // Utility Methods
    //--------------------------------------------------------------------

    public function testIsLoggedInReturnsFalseWhenNotLoggedIn()
    {
        $this->ci->session->shouldReceive('userdata')->with('logged_in');

        $this->assertFalse($this->auth->isLoggedIn());
    }

    //--------------------------------------------------------------------

}