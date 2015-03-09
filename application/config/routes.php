<?php
defined('BASEPATH') OR exit('No direct script access allowed');


$routes = new \Myth\Route();

/*
| -------------------------------------------------------------------------
| URI ROUTING
| -------------------------------------------------------------------------
| This file lets you re-map URI requests to specific controller functions.
|
| Typically there is a one-to-one relationship between a URL string
| and its corresponding controller class/method. The segments in a
| URL normally follow this pattern:
|
|	example.com/class/method/id/
|
| In some instances, however, you may want to remap this relationship
| so that a different class/function is called than the one
| corresponding to the URL.
|
| Please see the user guide for complete details:
|
|	http://codeigniter.com/user_guide/general/routing.html
|
| -------------------------------------------------------------------------
| RESERVED ROUTES
| -------------------------------------------------------------------------
|
| There are three reserved routes:
|
|	$route['default_controller'] = 'welcome';
|
| This route indicates which controller class should be loaded if the
| URI contains no data. In the above example, the "welcome" class
| would be loaded.
|
|	$route['404_override'] = 'errors/page_missing';
|
| This route will tell the Router which controller/method to use if those
| provided in the URL cannot be matched to a valid route.
|
|	$route['translate_uri_dashes'] = FALSE;
|
| This is not exactly a route, but allows you to automatically route
| controller and method names that contain dashes. '-' isn't a valid
| class or method name character, so it requires translation.
| When you set this option to TRUE, it will replace ALL dashes in the
| controller and method URI segments.
|
| Examples:	my-controller/index	-> my_controller/index
|		my-controller/my-method	-> my_controller/my_method
*/
$route['default_controller'] = 'home';
$route['404_override'] = '';
$route['translate_uri_dashes'] = FALSE;


//--------------------------------------------------------------------
// Auth-Related Routes
//--------------------------------------------------------------------

$routes->any('join', 'auth/register', ['as' => 'register']);
$routes->any('login', 'auth/login', ['as' => 'login']);
$routes->get('logout', 'auth/logout', ['as' => 'logout']);
$routes->any('forgot_password', 'auth/forgot_password', ['as' => 'forgot_pass']);
$routes->any('reset_password', 'auth/reset_password', ['as' => 'reset_pass']);
$routes->any('change_password', 'auth/change_password', ['as' => 'change_pass']);
$routes->any('activate_user', 'auth/activate_user', ['as' => 'activate_user']);
$routes->get('password_check/(:any)', 'auth/password_check/$1');

$routes->block('auth/(:any)');


//--------------------------------------------------------------------

// Auto-generated routes go here

//--------------------------------------------------------------------

// Make sure CI's Router gets the array they expect.
$route = $routes->map($route);
