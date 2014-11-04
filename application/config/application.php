<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

//--------------------------------------------------------------------
// Auto Migrate?
//--------------------------------------------------------------------
// We can automatically run any outstanding migrations in the core,
// the application and the modules themselves if this is set to TRUE.
//
    $config['auto_migrate'] = array(
        // 'app',   // Comment this line out to turn off auto-migrations.
    );

//--------------------------------------------------------------------
// Site Information
//--------------------------------------------------------------------
//
    $config['site.name']        = 'Sprint PHP';
    $config['site.auth_email']  = 'test@example.com';

//--------------------------------------------------------------------
// Authentication
//--------------------------------------------------------------------
//
    $config['auth.allowed_drivers'] = array('auth_sprintauth');

    $config['auth.default_driver']  = 'sprintauth';

//--------------------------------------------------------------------
// Profiler
//--------------------------------------------------------------------
//
    $config['show_profiler'] = true;

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
// Use Eldarion AJAX?
//--------------------------------------------------------------------
// If TRUE, the eldarion ajax library will be integrated into the
// BaseController for some additional helpers. If FALSE, then you
// will have to replace the functionality on your own.
//
    $config['use_eldarion'] = true;

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
