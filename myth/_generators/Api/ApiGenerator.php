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

use Myth\CLI as CLI;

/**
 * Class ApiGenerator
 *
 * Asks the user a series of questions and creates any needed files based
 * upon the authentication type.
 *
 * Auth Type |  Files Created
 * ----------+----------------------------------
 * basic        none
 * digest       migration, modifies adds event to config
 * keys         migration, modifies adds event to config
 */
class ApiGenerator extends \Myth\Forge\BaseGenerator {

	protected $auth_type;

	//--------------------------------------------------------------------

	public function run($segments=[], $quiet=false)
	{
		CLI::write("Available Auth Types: ". CLI::color('basic, digest', 'yellow') );

		$this->auth_type = trim( CLI::prompt('Auth type') );

		switch ($this->auth_type)
		{
			case 'basic':
				$this->setupBasic();
				$this->readme('readme_basic.txt');
				break;
			case 'digest':
				$this->setupDigest();
				$this->readme('readme_digest.txt');
				break;
		}
	}

	//--------------------------------------------------------------------

	private function setupBasic()
	{
		$this->setAuthType('basic');

		$this->setupLogging();
	}

	//--------------------------------------------------------------------

	private function setupDigest()
	{
		$this->makeMigration('migration', 'Add_digest_key_to_users');

		$this->setAuthType('digest');

		$this->setupLogging();
	}

	//--------------------------------------------------------------------

	//--------------------------------------------------------------------
	// Utility Methods
	//--------------------------------------------------------------------

	private function setupLogging()
	{
		$shouldWe = CLI::prompt('Enable Request Logging?', ['y', 'n']);

		if (strtolower($shouldWe) != 'y')
		{
			return;
		}

		$this->makeMigration('log_migration', 'Create_api_log_table');

		// Update the config setting
		$content = "config['api.enable_logging']    = true;";
		$this->injectIntoFile(APPPATH .'config/api.php', $content, ['regex' => "/config\['api.enable_logging']\s+=\s+[a-zA-Z]+;/u"] );
	}

	//--------------------------------------------------------------------


	private function makeMigration( $tpl, $name )
	{
		// Create the migration
		$this->load->library('migration');

		$destination = $this->migration->determine_migration_path('app', true);

		$file = $this->migration->make_name( $name );

		$destination = rtrim($destination, '/') .'/'. $file;

		if (! $this->copyTemplate( $tpl, $destination, [], true) )
		{
			CLI::error('Error creating migration file.');
		}

		$this->setAuthType('digest');
	}

	//--------------------------------------------------------------------

	/**
	 * Modifies the config/api.php file and sets the Auth type
	 *
	 * @param $type
	 */
	private function setAuthType( $type )
	{
		$content = "config['api.auth_type']    = '{$type}';";

		$this->injectIntoFile(APPPATH .'config/api.php', $content, ['regex' => "/config\['api.auth_type']\s+=\s+'[a-zA-Z]+';/u"] );
	}

	//--------------------------------------------------------------------

}
