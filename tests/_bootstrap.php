<?php
// This is global bootstrap for autoloading

// Include our Composer autoloader so we can
// get to our myth/* files
include 'vendor/autoload.php';

// The path to the codeigniter application index.php folder
define ('ROOT', getcwd() .'/');

//--------------------------------------------------------------------
// Load up CodeIgniter so that we can use it!
//--------------------------------------------------------------------

// Show 404 will block us here, so override the damn function
// so can move on.
function show_404($page = '', $log_error = TRUE) {
    // By default we log this, but allow a dev to skip it
    if ($log_error)
    {
        log_message('error', 'Not Found: '.$page);
    }
}

//--------------------------------------------------------------------

ob_start();
    include(ROOT . 'index.php');
    $ci =& get_instance();
ob_end_clean();


//--------------------------------------------------------------------

/**
 * Class CodeIgniterTestCase
 *
 * Use this for testing anything that needs $ci access.
 *
 */
class CodeIgniterTestCase extends \Codeception\TestCase\Test {

    protected $ci;

    //--------------------------------------------------------------------

    public function __construct()
    {
        parent::__construct();

        $this->ci =& get_instance();
    }

    //--------------------------------------------------------------------

    public function __get($var)
    {
        return $this->ci->$var;
    }

    //--------------------------------------------------------------------

}

//--------------------------------------------------------------------
