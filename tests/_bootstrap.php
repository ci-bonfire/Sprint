<?php
// This is global bootstrap for autoloading

// Set the environment variable so we know that we're in test mode
putenv('TESTING=true');

require_once dirname(__FILE__) .'/MythTester.php';

require_once dirname(__FILE__) .'/CodeIgniterTestCase.php';