<?php

$lang['done']               = 'Done';
$lang['fail']               = 'Failed';
$lang['exists']             = 'Exists';
$lang['created']            = 'Created';
$lang['overwrote']          = 'Overwrote';
$lang['modified']           = 'Modified';
$lang['error']              = 'Error';
$lang['copied']             = 'Copied';


$lang['errors.writing_file']    = 'Unknown error writing file: %s';
$lang['errors.creating_dir']    = 'Unknown error creating directory: %s';
$lang['errors.copying_dir']     = 'Unknown error copying directory: %s';
$lang['errors.reading_file']    = "Unable to read from file: %s";
$lang['errors.file_not_found']  = "File not found: %s";
$lang['errors.cant_instantiate'] = "Unable to create instance of class: %s";
$lang['errors.dir_exists']      = 'The directory already exists: %s';

// BaseController
$lang['bad_json_encode']    = 'Resources can not be converted to JSON data.';
$lang['bad_javascript']     = 'No javascript passed to the render_js() method.';

// CLIController
$lang['cli_required']           = 'This controller must be called from the command line.';
$lang['cli.available_commands'] = "Available commands:";
$lang['cli.bad_description']    = 'Unable to locate method description.';
$lang['cli.no_help']            = 'No help available for that command.';

// ThemedController
$lang['no_themer']              = 'No Themer chosen.';

// CronManager
$lang['cron.bad_alias']         = "Invalid (empty) alias.";
$lang['cron.bad_timestring']    = "Invalid (empty) time string.";
$lang['cron.bad_task']          = "Invalid task passed for task: ";
$lang['cron.invalid_task']      = 'Not a valid task.';
$lang['cron.running_task']      = "Running task: %s...";
$lang['cron.done_with_msg']     = "Done with message:\n%s\n";
$lang['cron.not_scheduled_until']   = "'%s' Not scheduled to run until %s.";
$lang['cron.nothing_scheduled'] = "No Tasks scheduled to run currently.";

// Forge
$lang['forge.cant_find_readme'] = 'Unable to find the readme file: %s';
$lang['forge.no_themer']        = 'No themer chosen in forge config file.';
$lang['forge.no_uikit']         = 'No uikit chosen in application config file.';
$lang['forge.no_collections']   = 'No generator collections found.';
$lang['forge.dir_not_writable'] = 'The folder is not writable: %s';

// Mail
$lang['mail.invalid_service']   = "Unable to find Mail Service: %s";
$lang['mail.invalid_log_data']  = "Not enough data to log email. Must have to, subject and a message.";
$lang['mail.error_html_log']    = 'Unable to create html the email log file: %s%s.html';
$lang['mail.error_text_log']    = 'Unable to create the text email log file: %s%s.txt';
$lang['mail.cant_find_mailer']  = "Unable to locate the mailer: %s";
$lang['mail.invalid_mailer_method'] = "Mailer method does not exist: %s::%s";

// Settings
$lang['settings.bad_default']   = 'Settings cannot find the default storage system.';
$lang['settings.cant_retrieve'] = "Unable to get setting item from specified datastore (%s)";
$lang['settings.cant_retrieve'] = "Unable to delete setting item from specified datastore (%s)";

// Themers
$lang['themer.bad_folder']      = "No folder found for theme: %s.";
$lang['themer.bad_callback']    = "Method not found in View Callback - %s::%s";
