<?php

namespace Myth;

/**
 * Since registerglobals doesn't exist after 5.4, you cannot get access to the CFG object
 * when running from the CLI. So, grab the config file and get our module location manually
 * here, then discard when we're done. It's a bit hacky, but since modules have to be
 * available within the Router class, before get_instance() is available we'll have to
 * live with it for now.
 */
include APPPATH . 'config/config.php';

if ( isset( $config ) )
{
	if ( is_array( $config['modules_locations'] ) )
	{
		Modules::$locations = $config['modules_locations'];
	}
	else
	{
		Modules::$locations = array( APPPATH . 'modules/' => '../modules/' );
	}

	unset( $config );
}

/* PHP5 spl_autoload */
spl_autoload_register( '\Myth\Modules::autoload' );

/**
 * This file has been copied from the original location and revised to work with CodeIgniter 3,
 * as well as add additional capabilities to, by Lonnie Ezell.
 */

/**
 * Modular Extensions - HMVC
 *
 * Adapted from the CodeIgniter Core Classes
 * @link    http://codeigniter.com
 *
 * Description:
 * This library provides functions to load and instantiate controllers
 * and module controllers allowing use of modules and the HMVC design pattern.
 *
 * Install this file as application/third_party/MX/Modules.php
 *
 * @copyright    Copyright (c) 2011 Wiredesignz
 * @version    5.4
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
 **/
class Modules {
	public static $routes, $registry, $locations;

	//--------------------------------------------------------------------

	/**
	 * Load a module file
	 *
	 * @param $file
	 * @param $path
	 * @param string $type
	 * @param bool $result
	 *
	 * @return bool
	 */
	public static function load_file( $file, $path, $type = 'other', $result = TRUE )
	{

		$file     = str_replace( '.php', '', $file );
		$location = $path . $file . '.php';

		if ( $type === 'other' )
		{
			if ( class_exists( $file, FALSE ) )
			{
				log_message( 'debug', "File already loaded: {$location}" );

				return $result;
			}
			include_once $location;
		}
		else
		{

			/* load config or language array */
			include $location;

			if ( ! isset( $$type ) OR ! is_array( $$type ) )
			{
				show_error( "{$location} does not contain a valid {$type} array" );
			}

			$result = $$type;
		}
		log_message( 'debug', "File loaded: {$location}" );

		return $result;
	}

	//--------------------------------------------------------------------

	/**
	 * Find a file
	 *
	 * Scans for files located within modules directories.
	 * Also scans application directories for models, plugins and views.
	 * Generates fatal error if file not found.
	 *
	 * @param   string $file The name of the file to find.
	 * @param   string $module The name of the module or modules to look in for the file.
	 * @param   string $base The path within the module to look for the file.
	 *
	 * @return  array           [ {full_path_to_file}, {file} ] or FALSE
	 */
	public static function find( $file, $module, $base )
	{

		// Find the actual file name. It will always be the last element.
		$segments = explode( '/', $file );
		$file     = array_pop( $segments );
		$file_ext = ( pathinfo( $file, PATHINFO_EXTENSION ) ) ? $file : $file . '.php';

		// Put the pieces back to get the path.
		$path = implode( '/', $segments ) . '/';
		$base = rtrim( $base, '/' ) . '/';

		// Look in any possible module locations based on the string segments.
		$modules = array();
		if ( ! empty( $module ) )
		{
			$modules[ $module ] = $path;
		}

		// Collect the modules from the segments
		if ( ! empty( $segments ) )
		{
			$modules[ array_shift( $segments ) ] = ltrim( implode( '/', $segments ) . '/', '/' );
		}

		foreach ( self::$locations as $location => $offset )
		{

			foreach ( $modules as $module => $subpath )
			{
				// Combine the elements to make an actual path to the file
				$fullpath = str_replace( '//', '/', "{$location}{$module}/{$base}{$subpath}" );

				// If it starts with a '/' assume it's a full path already
				if ( substr( $path, 0, 1 ) == '/' && strlen( $path ) > 1 )
				{
					$fullpath = $path;
				}

				// Libraries are a special consideration since they are
				// frequently ucfirst.
				if ( $base == 'libraries/' AND is_file( $fullpath . ucfirst( $file_ext ) ) )
				{
					return array( $fullpath, ucfirst( $file ) );
				}

				if ( is_file( $fullpath . $file_ext ) )
				{
					return array( $fullpath, $file );
				}
			}
		}

		return array( FALSE, $file );
	}

	//--------------------------------------------------------------------

	/**
	 * Returns a list of all modules in the system.
	 *
	 * @return  array                   A list of all modules in the system.
	 */
	public static function listModules()
	{
		if ( ! function_exists( 'directory_map' ) )
		{
			require BASEPATH . 'helpers/directory.php';
		}

		$map = array();

		foreach ( self::$locations as $folder )
		{

			$dirs = directory_map( $folder, 1 );
			if ( ! is_array( $dirs ) )
			{
				$dirs = array();
			}

			$map = array_merge( $map, $dirs );
		}

		// Clean out any html or php files
		if ( $count = count( $map ) )
		{
			for ( $i = 0; $i < $count; $i ++ )
			{
				if ( strpos( $map[ $i ], '.html' ) !== FALSE || strpos( $map[ $i ], '.php' ) !== FALSE )
				{
					unset( $map[ $i ] );
				}
			}
		}

		return $map;
	}

	//--------------------------------------------------------------------

	/**
	 * Determines whether a controller exists for a module.
	 *
	 * @param   string $controller The name of the controller to look for (without the .php)
	 * @param   string $module The name of module to look in.
	 *
	 * @return boolean
	 */
	public static function controllerExists( $controller = NULL, $module = NULL )
	{
		if ( empty( $controller ) || empty( $module ) )
		{
			return FALSE;
		}

		// Look in all module paths
		foreach ( self::$locations as $folder )
		{
			if ( is_file( "{$folder}{$module}/controllers/{$controller}.php" ) )
			{
				return TRUE;
			}
		}

		return FALSE;
	}

	//--------------------------------------------------------------------

	/**
	 * Finds the path to a module's file.
	 *
	 * @param   string $module The name of the module to find.
	 * @param   string $folder The folder within the module to search for the file (ie. controllers).
	 * @param   string $file The name of the file to search for.
	 *
	 * @return  string          The full path to the file, or false if the file was not found
	 */
	public static function filePath( $module = NULL, $folder = NULL, $file = NULL )
	{
		if ( empty( $module ) || empty( $folder ) || empty( $file ) )
		{
			return FALSE;
		}

		$folders = Modules::folders();
		foreach ( $folders as $module_folder )
		{
			$test_file = "{$module_folder}{$module}/{$folder}/{$file}";
			if ( is_file( $test_file ) )
			{
				return $test_file;
			}
		}

		return FALSE;
	}

	//--------------------------------------------------------------------

	/**
	 * Returns the path to the module and it's specified folder.
	 *
	 * @param $module string The name of the module (must match the folder name)
	 * @param $folder string The folder name to search for. (Optional)
	 *
	 * @return string The path, relative to the front controller, or false if the folder was not found
	 */
	public static function path( $module = NULL, $folder = NULL )
	{
		foreach ( self::$locations as $module_folder )
		{
			if ( is_dir( $module_folder . $module ) )
			{
				if ( ! empty( $folder ) && is_dir( "{$module_folder}{$module}/{$folder}" ) )
				{
					return "{$module_folder}{$module}/{$folder}";
				}

				return $module_folder . $module . '/';
			}
		}

		return FALSE;
	}

	//--------------------------------------------------------------------

	/**
	 * Returns an associative array of files within one or more modules.
	 *
	 * @param $module_name string If not NULL, will return only files from that module.
	 * @param $module_folder string If not NULL, will return only files within that folder of each module (ie 'views')
	 * @param $exclude_core boolean Whether we should ignore all core modules.
	 *
	 * @return array An associative array, like: array('module_name' => array('folder' => array('file1', 'file2')))
	 */
	public static function files( $module_name = NULL, $module_folder = NULL )
	{
		if ( ! function_exists( 'directory_map' ) )
		{
			require BASEPATH . 'helpers/directory.php';
		}

		$files = array();

		foreach ( self::$locations as $path )
		{

			// Only map the whole modules directory if $module_name isn't passed
			if ( empty( $module_name ) )
			{
				$modules = directory_map( $path );
			}
			// Only map the $module_name directory if it exists
			elseif ( is_dir( $path . $module_name ) )
			{
				$path                    = $path . $module_name;
				$modules[ $module_name ] = directory_map( $path );
			}

			// If the element is not an array, it's a file, so ignore it.
			// Otherwise it is assumed to be a module.
			if ( empty( $modules ) || ! is_array( $modules ) )
			{
				continue;
			}

			foreach ( $modules as $mod_name => $values )
			{
				if ( is_array( $values ) )
				{
					// Add just the specified folder for this module
					if ( ! empty( $module_folder ) && isset( $values[ $module_folder ] ) && count( $values[ $module_folder ] ) )
					{
						$files[ $mod_name ] = array(
							$module_folder => $values[ $module_folder ],
						);
					}
					// Add the entire module
					elseif ( empty( $module_folder ) )
					{
						$files[ $mod_name ] = $values;
					}
				}
			}
		}

		return count( $files ) ? $files : FALSE;
	}

	//--------------------------------------------------------------------

	/**
	 * Parse module routes.
	 *
	 * @param $module
	 * @param $uri
	 *
	 * @return array
	 */
	public static function parse_routes( $module, $uri )
	{

		/* load the route file */
		if ( ! isset( self::$routes[ $module ] ) )
		{
			if ( list( $path ) = self::find( 'routes', $module, 'config/' ) AND $path )
			{
				self::$routes[ $module ] = self::load_file( 'routes', $path, 'route' );
			}
		}

		if ( ! isset( self::$routes[ $module ] ) )
		{
			return;
		}

		/* parse module routes */
		foreach ( self::$routes[ $module ] as $key => $val )
		{

			$key = str_replace( array( ':any', ':num' ), array( '.+', '[0-9]+' ), $key );

			if ( preg_match( '#^' . $key . '$#', $uri ) )
			{
				if ( strpos( $val, '$' ) !== FALSE AND strpos( $key, '(' ) !== FALSE )
				{
					$val = preg_replace( '#^' . $key . '$#', $val, $uri );
				}

				return explode( '/', $module . '/' . $val );
			}
		}
	}

	//--------------------------------------------------------------------

	//--------------------------------------------------------------------
	// Autoloader
	//--------------------------------------------------------------------

	/**
	 * Library base class autoload.
	 *
	 * @param $class
	 */
	public static function autoload( $class )
	{

		/* don't autoload CI_ prefixed classes or those using the config subclass_prefix */
		if ( strstr( $class, 'CI_' ) OR strstr( $class, config_item( 'subclass_prefix' ) ) )
		{
			return;
		}

		/* autoload Modular Extensions MX core classes */
		if ( strstr( $class, 'MX_' ) AND is_file( $location = dirname( __FILE__ ) . '/' . substr( $class, 3 ) . '.php' ) )
		{
			include_once $location;

			return;
		}

		/* autoload core classes */
		if ( is_file( $location = APPPATH . 'core/' . $class . '.php' ) )
		{
			include_once $location;

			return;
		}

		/* autoload library classes */
		if ( is_file( $location = APPPATH . 'libraries/' . $class . '.php' ) )
		{
			include_once $location;

			return;
		}
	}

	//--------------------------------------------------------------------

	//--------------------------------------------------------------------
	// Deprecated Methods
	//--------------------------------------------------------------------

	/**
	 * Run a module controller method
	 * Output from module is buffered and returned.
	 *
	 * DEPRECATED. The loading of module controllers is discouraged, since maintainability
	 * and testability is enhanced by keeping the controllers lean and doing all of the
	 * work within libraries or models.
	 *
	 * @param $module
	 */
	public static function run( $module )
	{

		$method = 'index';

		if ( ( $pos = strrpos( $module, '/' ) ) != FALSE )
		{
			$method = substr( $module, $pos + 1 );
			$module = substr( $module, 0, $pos );
		}

		if ( $class = self::load( $module ) )
		{

			if ( method_exists( $class, $method ) )
			{
				ob_start();
				$args   = func_get_args();
				$output = call_user_func_array( array( $class, $method ), array_slice( $args, 1 ) );
				$buffer = ob_get_clean();

				return ( $output !== NULL ) ? $output : $buffer;
			}
		}

		log_message( 'error', "Module controller failed to run: {$module}/{$method}" );
	}

	//--------------------------------------------------------------------

	/**
	 * Load a module controller.
	 *
	 * DEPRECATED. The loading of module controllers is discouraged, since maintainability
	 * and testability is enhanced by keeping the controllers lean and doing all of the
	 * work within libraries or models.
	 *
	 * @param $module
	 *
	 * @return mixed
	 */
	public static function load( $module )
	{

		( is_array( $module ) ) ? list( $module, $params ) = each( $module ) : $params = NULL;

		/* get the requested controller class name */
		$alias = strtolower( basename( $module ) );

		/* create or return an existing controller from the registry */
		if ( ! isset( self::$registry[ $alias ] ) )
		{

			/* find the controller */
			list( $class ) = CI::$APP->router->locate( explode( '/', $module ) );

			/* controller cannot be located */
			if ( empty( $class ) )
			{
				return;
			}

			/* set the module directory */
			$path = APPPATH . 'controllers/' . CI::$APP->router->fetch_directory();

			/* load the controller class */
			$class = $class . CI::$APP->config->item( 'controller_suffix' );
			self::load_file( $class, $path );

			/* create and register the new controller */
			$controller               = ucfirst( $class );
			self::$registry[ $alias ] = new $controller( $params );
		}

		return self::$registry[ $alias ];
	}

	//--------------------------------------------------------------------
}
