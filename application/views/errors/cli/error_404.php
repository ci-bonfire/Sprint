<?php
defined('BASEPATH') OR exit('No direct script access allowed');

echo \Myth\CLI::error("\n\t404 ERROR: $heading");
echo \Myth\CLI::write("$message\n");
echo \Myth\CLI::write( \Myth\CLI::cli_string() );