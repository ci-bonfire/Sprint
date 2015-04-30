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
if (!defined('BASEPATH')) exit('No direct script access allowed');

//--------------------------------------------------------------------
// Auto Migrate?
//--------------------------------------------------------------------
// We can automatically run any outstanding migrations in the core,
// the application and the modules themselves if this is set to TRUE.
//
    $config['auto_migrate'] = array(
//         'app',   // Comment this line out to turn off auto-migrations.
    );

//--------------------------------------------------------------------
// Site Information
//--------------------------------------------------------------------
//
    $config['site.name']        = 'Sprint PHP';
    $config['site.auth_email']  = 'test@example.com';

//--------------------------------------------------------------------
// Profiler
//--------------------------------------------------------------------
//
    $config['show_profiler'] = true;

//--------------------------------------------------------------------
// PHP Error
//--------------------------------------------------------------------
// If enabled, will use a custom error screen on PHP errors instead
// of a generic screen. Only works in the development environment,
// for all other environments it is ignored.
//
	$config['use_php_error'] = true;

//--------------------------------------------------------------------
// Caching
//--------------------------------------------------------------------
// Sets the default types of caching used throughout the site. Possible
// choices are:
//      - apc
//      - file
//      - memcached
//      - dummy
//
// If you don't wish to use any caching in your environment, set it
// to dummy.
//
// The cache types can be overriedden as class values within each
// controller.
//
    $config['cache_type']           = 'dummy';
    $config['backup_cache_type']    = 'dummy';

//--------------------------------------------------------------------
// Themer
//--------------------------------------------------------------------
// Sets the Themer Engine to use.
//
    $config['active_themer'] = '\Myth\Themers\ViewThemer';

//--------------------------------------------------------------------
// Theme Paths
//--------------------------------------------------------------------
// The aliases and paths to the theme folders. The key of each element
// is the alias name. This is used to reference within the 'display'
// method of the
    $config['theme.paths'] = array(
        'bootstrap'  => FCPATH .'themes/bootstrap3',
        'foundation' => FCPATH .'themes/foundation5',
        'docs'       => FCPATH .'themes/docs',
        'email'      => FCPATH .'themes/email'
    );

//--------------------------------------------------------------------
// Variants
//--------------------------------------------------------------------
// Variants are different versions of the view files that can be used.
// These are used by Themers to serve up different versions of
// the view files based on the device type that is looking at the page.
//
// The key is the name the variant is referenced by.
// The value is the string that is added to the view name.
//
    $config['theme.variants'] = array(
        'phone'  => '+phone',
        'tablet' => '+tablet',
    );

//--------------------------------------------------------------------
// AutoDetect Variants?
//--------------------------------------------------------------------
// If TRUE, the ThemedController (and children) will automatically
// attempt to determine whether the user is using a desktop,
// mobile phone, or tablet to browse the site. This is then set
// in the Themer so it will attempt to use variant files.
//
    $config['theme.autodetect_variant'] = true;

//--------------------------------------------------------------------
// Default Theme
//--------------------------------------------------------------------
// This is the name of the folder that holds the default theme parts.
// This can be overridden in each controller via the $theme class variable.
//
    $config['theme.default_theme'] = 'bootstrap';

//--------------------------------------------------------------------
// Use A UIKit?
//--------------------------------------------------------------------
// If defined, this should be the full name (with namespaces) of
// the UIKit to use within your views.
//
    $config['theme.uikit'] = '\Myth\UIKits\Bootstrap';

//--------------------------------------------------------------------
// Parse Views?
//--------------------------------------------------------------------
// If TRUE, all files ran through the themers display() method will
// be passed through the parser, in addition to any other processing.
//
	$config['theme.parse_views'] = false;

//--------------------------------------------------------------------
// Auto Escape Vars
//--------------------------------------------------------------------
// If TRUE, will auto escape any vars set using set_var() or passed
// in a $data array to one of the theme methods.
//
// This can be overridden on a per-call basis.
//
    $config['theme.auto_escape'] = true;

//--------------------------------------------------------------------
// Settings Stores
//--------------------------------------------------------------------
// Lists the Settings stores to use. Must include full namespace to
// the class, if applicable. List the stores in order of descending
// priority. If a value is not found in one store, it will continue
// running through the stores until it is found.
//
// The 'key' is the alias the store can be referenced by later.
// The 'value' is the fully namespaced class name for the store.
//
    $config['settings.stores'] = [
        'db'        => '\Myth\Settings\DatabaseStore',
        'config'    => '\Myth\Settings\ConfigStore'
    ];

//--------------------------------------------------------------------
// Default Settigns Store
//--------------------------------------------------------------------
// The default datastore to use if none is specified. This primarily
// is used for saving items and findBy, but does apply to all.
//
    $config['settings.default_store'] = 'db';


//--------------------------------------------------------------------
// Default MailService
//--------------------------------------------------------------------
// The default MailService to use when sending any emails through
// Myth\Mail\Mail commands. Must include the full namespace of the class.
//
    $config['mail.default_service'] = '\Myth\Mail\LogMailService';

//--------------------------------------------------------------------
// Mail: Pretend to send
//--------------------------------------------------------------------
// When set to TRUE, this setting tells Sprint to pretend to send
// emails and simply return a successful send. Useful during
// certain stages of testing.
//
    $config['mail.pretend'] = FALSE;
