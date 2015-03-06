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

/**
 * BUILD TOOL
 *
 * This is a simple tool to create the releases for Sprint. The primary tasks are:
 *      - Create a zip file of final state of code ready for upload
 *      - Remove our tests folder
 *      - Remove any logs and cache items from the app that it might have
 *      - remove this build folder
 */

use Myth\CLI;

$start_time = microtime(true);

// Ensure errors are on
error_reporting(-1);
ini_set('display_errors', 1);

// Load our autoloader
require __DIR__ .'/../vendor/autoload.php';

// Load configuration
require 'build_config.php';

// Folder definitions
define('BASEPATH', __DIR__ .'/');

//--------------------------------------------------------------------
// Determine the build script to run
//--------------------------------------------------------------------

$release = CLI::segment(1);

if (empty($release))
{
	$release = CLI::prompt("Which script", array_keys($config['builds']) );
}

if (! array_key_exists($release, $config['builds']))
{
	CLI::error('Invalid build specified: '. $release);
	exit(1);
}

//--------------------------------------------------------------------
// Instantiate the class and run it
//--------------------------------------------------------------------

$class_name = $config['builds'][$release];


if (! file_exists(BASEPATH ."scripts/{$class_name}.php"))
{
	CLI::error('Unable to find build script: '. $class_name .'.php');
	exit(1);
}

require BASEPATH ."lib/BaseBuilder.php";
require BASEPATH ."scripts/{$class_name}.php";

$builder = new $class_name( $config['destinations'][$release] );

if (! is_object($builder))
{
	CLI::error('Unable to make new class: '. $class_name);
	exit(1);
}

// run it!
CLI::write("Running builder `{$release}` ({$class_name})...", 'yellow');

$builder->run();

//--------------------------------------------------------------------
// Show closing comments
//--------------------------------------------------------------------

$end_time = microtime(true);
$elapsed_time = number_format($end_time - $start_time, 4);

CLI::write('Done in '. $elapsed_time .' seconds', 'green');