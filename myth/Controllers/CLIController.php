<?php

namespace Myth\Controllers;
use Myth\CLI;

/**
 * Class CLIInterface
 *
 * Provides helpers for creating simple interactive CLI tools.
 *
 * @package Myth\Controllers
 */
class CLIController extends \CI_Controller {

    //--------------------------------------------------------------------

    /**
     * Ensures that we are running on the CLI, and collects basic settings
     * like collecting our command line arguments into a pretty array.
     */
    public function __construct()
    {
        parent::__construct();

        // Make sure the CLI library is loaded and ready.
        $cli = new CLI();
        $cli::_init();
    }

    //--------------------------------------------------------------------



}