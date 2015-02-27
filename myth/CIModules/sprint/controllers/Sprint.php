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

use Myth\CLI;
use Myth\Events;

class Sprint extends \Myth\Controllers\CLIController {

	/**
	 * Displays the current version string for Sprint.
	 */
	public function version()
	{
		$output = 'Running Sprint v'. CLI::color(SPRINT_VERSION, 'yellow');

		if (defined('BONFIRE_VERSION'))
		{
			$output .= ' and Bonfire v'. CLI::color(BONFIRE_VERSION, 'yellow');
		}

		CLI::write($output);
	}

	//--------------------------------------------------------------------

	/**
	 * Provides a way for the application to build out static
	 * elements. Paths, and the Event listeners that execute
	 * the publishers are located in config/publishers.php.
	 *
	 * This is called from the CLI:
	 *      $ php sprint publish
	 */
	public function publish()
	{
	    define('SPRINT_PUBLISHING', true);

		$this->load->config('publishers');

		$publishers = config_item('publishers');
		$folders    = config_item('publishing_folders');

		if (! is_array($publishers) || ! count($publishers))
		{
			throw new \RuntimeException('You must provide at least one publisher.');
		}

		foreach ($publishers as $class_name => $method)
		{
			if (! class_exists($class_name))
			{
				CLI::write("\tInvalid publisher class: ". $class_name, 'red');
				continue;
			}

			$instance = new $class_name();

			if (! method_exists($instance, $method))
			{
				CLI::write("\tInvalid publisher method: {$class_name}::{$method}", 'red');
				continue;
			}

			CLI::write("\trunning ". CLI::color("{$class_name}::{$method}", 'yellow') );

			$instance->$method( $folders );

			unset($instance);
		}

		CLI::write("Done");
	}

	//--------------------------------------------------------------------


}