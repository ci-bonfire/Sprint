<?php

use Myth\Modules;

class ModulesTest extends CodeIgniterTestCase {

	public function _before() {}

	//--------------------------------------------------------------------

	public function _after() {
		Modules::$registry = null;
		Modules::$routes = null;
	}

	//--------------------------------------------------------------------

	//--------------------------------------------------------------------
	// Find
	//--------------------------------------------------------------------

	public function testFindReturnsEmptyPathWithBadModule()
	{
	    list($path, $file) = Modules::find('notthere', 'nonmodule', 'controllers');

		$this->assertFalse($path);
	}

	//--------------------------------------------------------------------

	public function testFindReturnsValidPathForController()
	{
		$expected = MYTHPATH .'CIModules/auth/controllers/';

		list($path, $file) = Modules::find('Auth', 'auth', 'controllers');

		$this->assertEquals($expected, $path);
	}

	//--------------------------------------------------------------------

	public function testFindReturnsValidPathForHelpers()
	{
		$expected = MYTHPATH .'CIModules/auth/helpers/';

		list($path, $file) = Modules::find('password_helper', 'auth', 'helpers');

		$this->assertEquals($expected, $path);
	}

	//--------------------------------------------------------------------

	public function testFindReturnsValidPathForModels()
	{
		$expected = MYTHPATH .'CIModules/auth/models/';

		list($path, $file) = Modules::find('Login_model', 'auth', 'models');

		$this->assertEquals($expected, $path);
	}

	//--------------------------------------------------------------------

	public function testFindReturnsValidPathForViews()
	{
		$expected = MYTHPATH .'CIModules/auth/views/';

		list($path, $file) = Modules::find('activate_user', 'auth', 'views');

		$this->assertEquals($expected, $path);
	}

	//--------------------------------------------------------------------

	//--------------------------------------------------------------------
	// List Modules
	//--------------------------------------------------------------------

	public function testListModulesProvidesValidData()
	{
		// Limit to just core so we have control over which modules are returned.
		Modules::$locations = [MYTHPATH .'CIModules/'];

		$expected = [ 'auth', 'cron', 'database', 'docs', 'forge' ];

	    $modules = Modules::listModules();

		// Ensure we're sorted on all systems
		sort($modules, SORT_STRING);

		$this->assertEquals($expected, $modules);
	}

	//--------------------------------------------------------------------

	//--------------------------------------------------------------------
	// ControllerExists
	//--------------------------------------------------------------------
	
	public function testControllerExistsReturnsFalseWhenNotExists()
	{
	    $this->assertFalse( Modules::controllerExists('Author', 'auth') );
	}
	
	//--------------------------------------------------------------------

	public function testControllerExistsReturnsTrueWhenExists()
	{
		$this->assertTrue( Modules::controllerExists('Auth', 'auth') );
	}

	//--------------------------------------------------------------------

	public function testControllerBarfsWithBadParameters()
	{
		$this->setExpectedException('\InvalidArgumentException');

		$this->assertTrue( Modules::controllerExists(new stdClass(), 'auth') );
	}

	//--------------------------------------------------------------------

	//--------------------------------------------------------------------
	// FilePath
	//--------------------------------------------------------------------

	public function testFilePathBarfsWithBadParameters()
	{
		$this->setExpectedException('\InvalidArgumentException');

		$this->assertTrue( Modules::filePath(new stdClass(), 'auth', 'that') );
	}

	//--------------------------------------------------------------------

	public function testFilePathReturnsFalseWithNoFile()
	{
	    $this->assertFalse( Modules::filePath('auth', 'controllers', 'something') );
	}

	//--------------------------------------------------------------------

	public function testFilePathReturnsPathWithFile()
	{
		$expected = MYTHPATH .'CIModules/auth/controllers/Auth.php';

		$this->assertEquals($expected, Modules::filePath('auth', 'controllers', 'Auth.php') );
	}

	//--------------------------------------------------------------------

	//--------------------------------------------------------------------
	// FilePath
	//--------------------------------------------------------------------

	public function testPathBarfsWithBadParameters()
	{
		$this->setExpectedException('\InvalidArgumentException');

		$this->assertTrue( Modules::path(new stdClass(), 'auth') );
	}

	//--------------------------------------------------------------------

	public function testPathReturnsNullWithBadModule()
	{
		$this->assertNull( Modules::path('author') );
	}

	//--------------------------------------------------------------------

	public function testPathReturnsModulePathWithNoFolder()
	{
		$expected = MYTHPATH .'CIModules/auth/';

		$this->assertEquals($expected, Modules::path('auth') );
	}

	//--------------------------------------------------------------------

	public function testPathReturnsPathWithFile()
	{
		$expected = MYTHPATH .'CIModules/auth/controllers/';

		$this->assertEquals($expected, Modules::path('auth', 'controllers') );
	}

	//--------------------------------------------------------------------

	public function testFilesReturnsNullWithBadPath()
	{
	    $this->assertNull( Modules::files('badmodules') );
	}

	//--------------------------------------------------------------------

	public function testFilesReturnsValidList()
	{
	    $expected = [
		    'cron' => [
			    'controllers/' => [
				    'Cron.php'
			    ]
		    ]
	    ];

		$this->assertEquals($expected, Modules::files('cron'));
	}

	//--------------------------------------------------------------------

	public function testFilesReturnsValidListWithOnlyFolder()
	{
		$expected = [
			'database' => [
				'controllers/' => [
					'Database.php'
				]
			]
		];

		$this->assertEquals($expected, Modules::files('database', 'controllers'));
	}

	//--------------------------------------------------------------------
}