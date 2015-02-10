<?php

use Myth\Api\Auth\APIAuthentication as Authenticate;
use \Mockery as m;

include APPPATH .'models/User_model.php';

class APIAuthenticationTests extends CodeIgniterTestCase {

	//--------------------------------------------------------------------

	public function __construct()
	{
		parent::__construct();
	}

	//--------------------------------------------------------------------


	public function _before()
	{
		$this->user_model = m::mock('User_model');

		$this->ci = get_instance();

		$this->ci->load->model('auth/login_model');

		$this->ci->login_model = m::mock('Login_model');

		$this->auth = new Authenticate( $this->ci );
		$this->auth->useModel($this->user_model, true);

	}

	//--------------------------------------------------------------------

	public function _after()
	{

	}

	//--------------------------------------------------------------------

//	public function testBlocksIPBlacklist()
//	{
//		$this->ci->config->set_item('api.ip_blacklist', '127.0.0.1, 0.0.0.0');
//
//		$this->setExpectedException('\Exception', 'IP Address is denied.');
//
//		$this->auth->checkIPBlacklist();
//	}
//
//	//--------------------------------------------------------------------
//
//	public function testBasicAuthFailsWithNoCredentials()
//	{
//	    $this->assertFalse( $this->auth->tryBasicAuthentication() );
//		$this->assertNotNull( $this->ci->output->get_header('WWW-Authenticate: Basic realm="'. config_item('api.realm') .'"'));
//	}

	//--------------------------------------------------------------------

	public function testBasicAuthReturnsFalseWithInvalidUser()
	{
		$_SERVER['PHP_AUTH_USER'] = 'baduser';
		$_SERVER['PHP_AUTH_PW']  = 'nocookies';

		$this->auth->user_model->shouldReceive('as_array')->once()->andReturn( $this->auth->user_model );
		$this->auth->user_model->shouldReceive('where')->once()->andReturn( $this->auth->user_model );
		$this->auth->user_model->shouldReceive('first')->once()->andReturn( false );

		$this->assertFalse( $this->auth->tryBasicAuthentication() );
	}
	
	//--------------------------------------------------------------------

	public function testBasicAuthReturnsUserWithValidUser()
	{
		$_SERVER['PHP_AUTH_USER'] = 'baduser';
		$_SERVER['PHP_AUTH_PW']  = 'nocookies';

		$user = [
			'id' => 12,
			'email'	=> 'baduser',
			'password_hash' => password_hash('nocookies', PASSWORD_DEFAULT),
			'active' => 1
		];

		$this->auth->user_model->shouldReceive('as_array')->once()->andReturn( $this->auth->user_model );
		$this->auth->user_model->shouldReceive('where')->once()->andReturn( $this->auth->user_model );
		$this->auth->user_model->shouldReceive('first')->once()->andReturn( $user );
		$this->ci->login_model->shouldReceive('recordLoginAttempt');

		$this->assertEquals($user, $this->auth->tryBasicAuthentication() );
		$this->assertEquals(12, $this->auth->id());
	}

	//--------------------------------------------------------------------

}