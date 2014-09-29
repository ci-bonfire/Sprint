<?php
/**
 * CodeIgniter
 *
 * An open source application development framework for PHP 5.2.4 or newer
 *
 * NOTICE OF LICENSE
 *
 * Licensed under the Academic Free License version 3.0
 *
 * This source file is subject to the Academic Free License (AFL 3.0) that is
 * bundled with this package in the files license_afl.txt / license_afl.rst.
 * It is also available through the world wide web at this URL:
 * http://opensource.org/licenses/AFL-3.0
 * If you did not receive a copy of the license and are unable to obtain it
 * through the world wide web, please send an email to
 * licensing@ellislab.com so we can send you a copy immediately.
 *
 * @package		CodeIgniter
 * @author		EllisLab Dev Team
 * @copyright	Copyright (c) 2008 - 2014, EllisLab, Inc. (http://ellislab.com/)
 * @license		http://opensource.org/licenses/AFL-3.0 Academic Free License (AFL 3.0)
 * @link		http://codeigniter.com
 * @since		Version 3.0
 * @filesource
 */
defined('BASEPATH') OR exit('No direct script access allowed');
?>

<?php echo \Myth\CLI::error("\n\tA PHP Error was encountered"); ?>

<?php echo \Myth\CLI::write("\tSeverity: {$severity}"); ?>
<?php echo \Myth\CLI::write("\tMessage: {$message}"); ?>
<?php echo \Myth\CLI::write("\tFilename: {$filepath}"); ?>
<?php echo \Myth\CLI::write("\tLine Number: {$line}"); ?>

<?php
    if (defined('SHOW_DEBUG_BACKTRACE') && SHOW_DEBUG_BACKTRACE === TRUE) {

        echo \Myth\CLI::write("\n\tBacktrace");

        foreach (debug_backtrace() as $error) {
            if (isset($error['file']) && strpos($error['file'], realpath(BASEPATH)) !== 0) {
                echo \Myth\CLI::write("\t\t- {$error['function']}() - Line {$error['line']} in {$error['file']}");
            }
        }
}

echo \Myth\CLI::new_line();
?>
