<?php
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
