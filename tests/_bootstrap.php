<?php
// This is global bootstrap for autoloading

// Include our Composer autoloader so we can
// get to our myth/* files
include 'src/vendor/autoload.php';

// Tell CI to shutup about the BASEPATH
define('BASEPATH', true);

function log_message() {}

