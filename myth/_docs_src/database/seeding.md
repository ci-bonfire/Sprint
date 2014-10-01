# Database Seeds
Seeds allow you to populate your database with test data using simple Seed classes. All seeds are stored in `application/database/seeds`. They can be named however you want to name them, but will typically follow a convention, like `UserDataSeeder`, `LocationSeeder` or `ClientSeeder`. By default, a `TestSeeder` is provided for you to jump in and extend. From this class you can use the `call()` method to run other seeders, controlling the seed order and staying organized. You also have access to the `$ci` superglobal, and the `$db` and `$dbforge` objects. 

## Creating a Seeder
A seeder should extend the `Seeder` class and contain the `run()` method. Everything else is pretty flexible. You could:

- use the `$db` object and pull in an SQL dump and run it through the system
- read CSV files and populate the database that way.
- fill in a table using random data generated using [Faker](https://github.com/fzaninotto/Faker)
- and more...

```php
class TestSeeder extends Seeder {
	public function run()
	{
		$this->call('UserSeeder');
	}
}
	
class UserSeeder extends Seeder {
	public function run()
	{
		$this->db->truncate('users');
		
		$this->db->insert('users', array('username'=>'DarthV') );
	}
}
```

## Running the Seeders
Seeders must be run from the command line, using the database tools, and passing the name of the seed class as the last argument.

	php index.php database seed TestSeeder

	

<small>Thanks to Laravel for the inspiration for this.</small>