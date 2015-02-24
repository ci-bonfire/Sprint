<?php
/**
 * Sprint
 *
 * A set of power tools to enhance the CodeIgniter framework and provide consistent workflow.
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 *
 * @package     Sprint
 * @author      Lonnie Ezell
 * @copyright   Copyright 2014-2015, New Myth Media, LLC (http://newmythmedia.com)
 * @license     http://opensource.org/licenses/MIT  (MIT)
 * @link        http://sprintphp.com
 * @since       Version 1.0
 */
if (!defined('BASEPATH')) exit('No direct script access allowed');

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
