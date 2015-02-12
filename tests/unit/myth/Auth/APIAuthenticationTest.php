<?php

use Myth\Api\Auth\APIAuthentication as Authenticate;
use \Mockery as m;

class APIAuthenticationTests extends CodeIgniterTestCase {

	protected $ci;

	protected $user_model;

	protected $auth;

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

	//--------------------------------------------------------------------
	// Common
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
//
//	//--------------------------------------------------------------------
//
//	public function testBasicAuthReturnsFalseWithInvalidUser()
//	{
//		$_SERVER['PHP_AUTH_USER'] = 'baduser';
//		$_SERVER['PHP_AUTH_PW']  = 'nocookies';
//
//		$this->auth->user_model->shouldReceive('as_array')->once()->andReturn( $this->auth->user_model );
//		$this->auth->user_model->shouldReceive('where')->once()->andReturn( $this->auth->user_model );
//		$this->auth->user_model->shouldReceive('first')->once()->andReturn( false );
//
//		$this->assertFalse( $this->auth->tryBasicAuthentication() );
//	}
//
//	//--------------------------------------------------------------------
//
//	public function testBasicAuthReturnsUserWithValidUser()
//	{
//		$_SERVER['PHP_AUTH_USER'] = 'baduser';
//		$_SERVER['PHP_AUTH_PW']  = 'nocookies';
//
//		$user = [
//			'id' => 12,
//			'email'	=> 'baduser',
//			'password_hash' => password_hash('nocookies', PASSWORD_DEFAULT),
//			'active' => 1
//		];
//
//		$this->auth->user_model->shouldReceive('as_array')->once()->andReturn( $this->auth->user_model );
//		$this->auth->user_model->shouldReceive('where')->once()->andReturn( $this->auth->user_model );
//		$this->auth->user_model->shouldReceive('first')->once()->andReturn( $user );
//		$this->ci->login_model->shouldReceive('recordLoginAttempt');
//
//		$this->assertEquals($user, $this->auth->tryBasicAuthentication() );
//		$this->assertEquals(12, $this->auth->id());
//	}

	//--------------------------------------------------------------------

	//--------------------------------------------------------------------
	// Digest
	//--------------------------------------------------------------------

	public function testDigestFailsWithNoCredentials()
	{
		$this->assertFalse( $this->auth->tryDigestAuthentication() );

		$header = $this->ci->output->get_header('WWW-Authenticate');

		$matches = [];
		preg_match_all('@(nonce|opaque|realm)=[\'"]?([^\'",]+)@', $header, $matches);
		$matches = array_combine($matches[1], $matches[2]);

		$this->assertNotEmpty( $header );
		$this->assertNotEmpty( $matches['nonce'] );
		$this->assertNotEmpty( $matches['opaque'] );
		$this->assertNotEmpty( $matches['realm'] );
	}

	//--------------------------------------------------------------------

	public function testDigestBasicFlow()
	{
		// first things - hit the server and get our nonce and such...
		$this->auth->tryDigestAuthentication();

		$header = $this->ci->output->get_header('WWW-Authenticate');

		$matches = [];
		preg_match_all('@(nonce|opaque|realm)=[\'"]?([^\'",]+)@', $header, $matches);
		$matches = array_combine($matches[1], $matches[2]);

		// Build the header to send back.
		$user = [
			'id' => 12,
			'email'	=> 'baduser',
			'password_hash' => password_hash('nocookies', PASSWORD_DEFAULT),
			'active' => 1
		];

		$cnonce = $matches['nonce'];
		$uri = 'http://local.dev/test/something';
		$A1 = md5($user['email'] .':'. $matches['realm'] .':'. 'nocookies');
		$A2 = md5('GET:'. $uri);
		$response = md5($A1 .':'. $matches['nonce'] .':1:'. $cnonce .':auth:'.  $A2);

		$user['api_key'] = $A1;

		$header_string = sprintf('Authorization: Digest username="%s", realm="%s", nonce="%s", nc="1", cnonce="%s", opaque="%s", qop="auth", uri="%s", response="%s"',
			$user['email'], $matches['realm'], $matches['nonce'], $cnonce, $matches['opaque'], $uri, $response);

		// Try it again, for realz
		$_SERVER['PHP_AUTH_DIGEST'] = $header_string;
		$_SERVER['REQUEST_METHOD'] = 'GET';

		$this->auth->user_model->shouldReceive('as_array')->once()->andReturn( $this->auth->user_model );
		$this->auth->user_model->shouldReceive('find_by')->once()->andReturn( $user );

		$ruser = $this->auth->tryDigestAuthentication();

		$this->assertTrue( is_array($ruser));
		$this->assertEquals(12, $this->auth->id());
	}

	//--------------------------------------------------------------------
}