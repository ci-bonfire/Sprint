# Testing Your Apps

Sprint provides the powerful [Codeception](http://codeception.com/) test suite out of the box, and it's what we use to test the framework itself.

## Running Tests
All tests are run from the command line with standard Codeception commands:

	// Runs all unit tests
	$ php codecept.phar run unit

## Unit Tests
All Unit Test classes should extend the `CodeIgniterTestCase` class. This class is automatically loaded when the test suite loads up, and allows your tests to access the CodeIgniter "super object" as a class var, `$this->ci`.  In addition, you can work with the traditional CodeIgniter objects just like you're in a controller.

	class SimpleTest extends CodeIgniterTestCase {

		protected function _before() {
			...
		}
		
		protected function _after() {
			...
		}
		
		public function testSomethingHere() {

			$this->load->library('typography');

			$this->assertTrue($x, $y);
		}
	}

## Test Environment
When running unit tests, Sprint automatically sets the Environment to `testing`. This allows you specify default configuration settings to be used during tests. By default, a database configuration is already setup that uses an in-memory sqlite3 database for faster test execution. Depending on your application's needs, you might need to customize this to more closely match your server setup.

## Helper Methods
The `MythTester` trait contains a handful of methods to make testing your application easier. This trait is already used by the CodeIgniterTestCase.

### Migrating the Database
Will run the specified [migration group](database/migrations#migration_groups) to the latest available migration.

	$this->migrate();

By default, this will migrate the `app` group, which is your main application. However, if you're testing a module you can specify that by passing the group name in as the first parameter.

	$this->migrate('some_group');

### Dropping database tables
You can drop one or more tables from the database with the `dropTables` method.

	// Drop a single table
	$this->dropTables('tableA');

	// Drop 2 tables
	$this->dropTables( ['tableA', 'tableB'] );

	// Drop ALL tables
	$this->dropTables();

### Seeding the Database
To seed the database with information needed for testing you can create a special [Seeder](database/seeding) to populate your test database with the exact state you need it for running your tests against. This might include a known set of users with different permissions to test them against, for example.

	$this->seed('TestSeeder');

### Logging A User In
Often, you will need to act as a certain user in order to get your tests different situations. You can do this by passing an array that represents the current user to the `beUser` method.

	$user = [
		'name'  => 'Darth Vader',
		'email' => 'darth@theempire.com'
	];
	$this->beUser($user);

This only works with the LocalAuthentication driver currently.
