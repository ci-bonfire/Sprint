<?php

use Myth\Cron\CronManager as CronManager;

/**
 * Cron Specification File.
 *
 * This file should contain the complete list of all scheduled tasks (cron jobs)
 * that your site might need to perform. You can include other files from within
 * this file if you desire a different organization to your tasks.
 *
 * Cron jobs are specified by calling CronManager::schedule().
 *
 * Example:
 *      CronManager::schedule('taskName', 'interval', callable() );
 *
 * See the docs for details.
 */

CronManager::schedule('task1', '1 minutes', function () { return true; });
CronManager::schedule('task2 with a really long name that wont show well', '5 minutes', function () { return true; });

// Process the mail queue every 5 minutes
CronManager::schedule('process_mail_queue', '5 minutes', '\Myth\Mail\Mail::process');

