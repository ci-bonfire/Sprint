<?php namespace Myth\Forensics;
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

/*
	Class: Console
	
	Provides several additional logging features designed to work 
	with the Forensics Profiler.
	
	Inspired by ParticleTree's PHPQuickProfiler. (http://particletree.com)
	
	Package: 
		Forensics
		
	Author: 
		Lonnie Ezell (http://lonnieezell.com)
		
	License:
		MIT 
*/
class Console {

	/*
		Var: $logs
		Contains all of the logs that are collected.
	*/
	private static $logs = array(
		'console'		=> array(),
		'log_count'		=> 0,
		'memory_count'	=> 0,
	);

	/*	
		Var: $ci
		An instance of the CI super object.
	 */
	private static $ci;

	//--------------------------------------------------------------------
	
	/*
		Method: __construct()
		
		This constructor is here purely for CI's benefit, as this is a
		static class.
		
		Return:
			void
	 */
	public function __construct() 
	{			
		self::init();
		
		log_message('debug', 'Forensics Console library loaded');
	}
	
	//--------------------------------------------------------------------
	
	/*
		Method: init()
		
		Grabs an instance of CI and gets things ready to run.
	*/
	public static function init() 
	{
		self::$ci =& get_instance();
	}
	
	//--------------------------------------------------------------------
	
	/*
		Method: log()
		
		Logs a variable to the console. 
		
		Parameters:
			$data	- The variable to log.
	*/
	public static function log($data=null) 
	{
		if ($data !== 0 && empty($data)) 
		{ 
			$data = 'empty';
		}
		
		$log_item = array(
			'data' => $data,
			'type' => 'log'
		);
		
		self::addToConsole('log_count', $log_item);
	}
	
	//--------------------------------------------------------------------
	
	/*
		Method: logMemory()
		
		Logs the memory usage a single variable, or the entire script.
		
		Parameters:
			$object	- The object to store the memory usage of.
			$name	- The name to be displayed in the console.
	*/
	public static function logMemory($object=false, $name='PHP')
	{
		$memory = memory_get_usage();
		
		if ($object) 
		{
			$memory = strlen(serialize($object));
		}
		
		$log_item = array(
			'data' => $memory,
			'type' => 'memory',
			'name' => $name,
			'data_type' => gettype($object)
		);

		self::addToConsole('memory_count', $log_item);
	}
	
	//--------------------------------------------------------------------
	
	/*
		Method: getLogs()
		
		Returns the logs array for use in external classes. (Namely the
		Forensics Profiler.
	*/
	public static function getLogs()
	{
		return self::$logs;
	}
	
	//--------------------------------------------------------------------

	public function reset()
	{
	    self::$logs = array(
		    'console'		=> array(),
		    'log_count'		=> 0,
		    'memory_count'	=> 0,
	    );
	}

	//--------------------------------------------------------------------



	//--------------------------------------------------------------------
	// !PRIVATE METHODS
	//--------------------------------------------------------------------
	
	protected static function addToConsole($log=null, $item=null)
	{
		if (empty($log) || empty($item)) 
		{ 
			return;
		}
		
		self::$logs['console'][]	= $item;
		self::$logs[$log] 			+= 1;
	}
	
	//--------------------------------------------------------------------
	
}

// End Console class
