<?php
defined('BASEPATH') OR exit('No direct script access allowed');
?>

<?php echo \Myth\CLI::error("\n\tAn uncaught Exception was encountered"); ?>

<?php echo \Myth\CLI::write("\Type: {get_class($exception)}"); ?>
<?php echo \Myth\CLI::write("\Message: {$message}"); ?>
<?php echo \Myth\CLI::write("\Filename: {$exception->getFile()}"); ?>
<?php echo \Myth\CLI::write("\ine Number: {$exception->getLine()}"); ?>

<?php
	if (defined('SHOW_DEBUG_BACKTRACE') && SHOW_DEBUG_BACKTRACE === TRUE) {

		echo \Myth\CLI::write("\n\tBacktrace");

		foreach ($exception->getTrace() as $error) {
			if (isset($error['file']) && strpos($error['file'], realpath(BASEPATH)) !== 0) {
				echo \Myth\CLI::write("\t\t- {$error['function']}() - Line {$error['line']} in {$error['file']}");
			}
		}
	}

echo \Myth\CLI::new_line();
?>
