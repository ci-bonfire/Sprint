<?php
defined('BASEPATH') OR exit('No direct script access allowed');

echo \Myth\CLI::error("\n\tDatabase Error: $heading");
echo \Myth\CLI::write("$message\n");
