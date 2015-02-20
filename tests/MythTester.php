<?php
/**
 * Sprint
 *
 * A set of power tools to enhance the CodeIgniter framework and provide consistent workflow.
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 *
 * @package     Sprint
 * @author      Lonnie Ezell
 * @copyright   Copyright 2014-2015, New Myth Media, LLC (http://newmythmedia.com)
 * @license     http://opensource.org/licenses/MIT  (MIT)
 * @link        http://sprintphp.com
 * @since       Version 1.0
 */

trait MythTester {

	/**
	 * Drops one or more tables from the database.
	 * If $tables is null, will drop all tables from
	 * the database. Otherwise, will only drop the tables
	 * listed in the array.
	 *
	 * @param array $tables
	 *
	 * @return bool
	 */
	public function dropTables($tables=null)
	{
		$ci =& get_instance();

		// Ensure database is loaded
		if (empty($ci->db))
		{
			$ci->load->database();
		}

		if (! empty($tables) && is_string($tables))
		{
			$tables = [$tables];
		}

		// If no tables were passed in, grab
		// a list of ALL tables in the database to delete.
		if (empty($tables) || ! is_array($tables))
		{
			$tables = $ci->db->list_tables();
		}

		if (! count($tables))
		{
			return true;
		}

		$ci->load->dbforge();

		$success = true;

		foreach ($tables as $t)
		{
			try
			{
				if ( ! $ci->dbforge->drop_table( $t, TRUE ) )
				{
					$success = FALSE;
				}
			} catch (\Exception $e)
			{
				$success = false;
			}
		}

	    return $success;
	}

	//--------------------------------------------------------------------

	/**
	 * Runs all of the available migrations for a single 'group'.
	 *
	 * @param string $type
	 */
	public function migrate($type='app')
	{
	    $ci =& get_instance();

		// Ensure database is loaded
		if (empty($ci->db))
		{
			$ci->load->database();
		}

		$ci->load->library('migration');

		$ci->migration->latest($type);
	}

	//--------------------------------------------------------------------


	/**
	 * Will run the listed Seed against the current database.
	 *
	 * @param $name
	 */
	public function seed($name)
	{
		$ci =& get_instance();

		// Ensure database is loaded
		if (empty($ci->db))
		{
			$ci->load->database();
		}

		$ci->load->library('database/seeder');

		$ci->seeder->call($name);
	}

	//--------------------------------------------------------------------

	/**
	 * Set the current authenticated user.
	 *
	 * $user can be either an INT with the user ID as it is in the current database,
	 * or an array of user information to set as the current user.
	 *
	 * NOTE: This does not enter any information in the database and is only
	 * valid for the current request.
	 *
	 * @param array $user
	 * @param object $authInstance  An instance of LocalAuthentication
	 */
	public function beUser(array $user, $authInstance)
	{
	    $this->invokeMethod($authInstance, 'loginUser', [$user]);
	}
	
	//--------------------------------------------------------------------

	/**
	 * Call the protected/private method of a class.
	 *
	 * source: https://jtreminio.com/2013/03/unit-testing-tutorial-part-3-testing-protected-private-methods-coverage-reports-and-crap/
	 *
	 * @param $object           Instantiated object that we will run the method on.
	 * @param $method_name      Method name to call
	 * @param array $parameters Array of paremeters to pass.
	 *
	 * @return mixed
	 */
	public function invokeMethod(&$object, $method_name, array $parameters = [])
	{
		$reflection = new \ReflectionClass(get_class($object));
		$method = $reflection->getMethod($method_name);
		$method->setAccessible(true);

		return $method->invokeArgs($object, $parameters);
	}
	
	//--------------------------------------------------------------------
	
	
}