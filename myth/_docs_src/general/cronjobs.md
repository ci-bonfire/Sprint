# Cron Jobs

The cron system allows you to have many tasks that run at scheduled times. This provides a single controller action to call from your web server's cron system or Scheduled Tasks system.

## Setting Up Your Server
How you setup your server depends greatly on the operating system your web server is running. Instead of providing detailed instructions for many different operating systems, I will provide links to instructions for several common operating systems.

You need to know two items: 1) the CLI command to have the cronjob run and 2) the interval to have it run. 

The script that will need to run is your `webroot folder` followed by the `index file` and `cron controller`. It would look something like the following, though you'll have to verify for your exact setup.

	/home/www/mysite/index.php cron run

The interval should be equal to the smallest frequency of any tasks that need to be run. This is often either every minute or every 5 minutes, depending on the types of tasks being run. This does not mean that all of the cron tasks will be ran every 5 minutes. Instead, it calls the cron controller which determines which tasks should be run. Some may run every 5 minutes, like sending out emails, while some may happen once a day, like cleaning out old login attempts.

### OS-Specific Instructions

- [Ubuntu](https://help.ubuntu.com/community/CronHowto)
- [CentOS](https://www.centos.org/docs/5/html/Deployment_Guide-en-US/ch-autotasks.html)
- [RedHat](https://access.redhat.com/documentation/en-US/Red_Hat_Enterprise_Linux/5/html/Deployment_Guide/ch-autotasks.html)
- [Windows](https://www.drupal.org/node/31506)
- [Mac OS X](http://rossb.biz/blog/2011/os-x-cron-jobs-a-simple-tutorial/)

## Running the Tasks
In order to tell the system to process all of the tasks and run the ones that are scheduled, you use the `run` command.

	$ php sprint cron run

### Event: afterCron
Once the cronjob has been run, it will fire an [Event](general/events) to allow other parties to take an action. By default, an action is provided (but disabled) to send an email with the output of the cron job to the email listed in `application/config/application.php` as `site.auth_email`. This is best used for debugging, though doesn't hurt for monitoring purposes, but can clog up inboxes depending on the frequency of the cron job.

	\Myth\Events::trigger('afterCron', [$output]);

## Specifying Tasks To Run

All tasks are configured in a simple configuration file. The system will automatically read in `application/config/cron.php` when run. Any tasks in that file are registered with the system and then checked to see which tasks should run now, if any.

The tasks are defined by calling `CronManager::schedule()`. The first parameter is an alias that the job can be referred to by later, in case you need to remove it, find next or previous scheduled dates for it, or just need to grab for your own use.

The second parameter is the `schedule`. This is an English string that describes how often the task can run. More details below.

The third parameter is either any [callable function](http://php.net/manual/en/language.types.callable.php) or any CodeIgniter-accessible `library:method` combination.

	use Myth\Cron\CronManager as CronManager;

	// With simple callback
	CronManager::schedule('send emails', '5 minutes', 'send_email_function');
	// With static class method call
	CronManager::schedule('send emails', '5 minutes', [ 'EmailQueue', 'processQueue' ]);
	// With object method call
	$queue = new EmailQueue();
	CronManager::schedule('send emails', '5 minutes', [ $queue, 'processQueue' ] );
	// With static class method call
	CronManager::schedule('send emails', '5 minutes', 'EmailQueue::processQueue');
	// With Closure
	CronManager::schedule('send emails', '5 minutes', function() {...} );
	// With CodeIgniter library
	CronManager::schedule('send emails', '5 minutes', 'module/library:method');

### The Schedule String
The schedule string can support a number of ways to schedule a task. The string uses a subset of the [relative datetime formats](http://php.net/manual/en/datetime.formats.relative.php) with some additional processing to make sure things happen as expected. This table provides examples of all of the types of schedules that make sense for your Cron Tasks.

_Note that any string can be used, but they will not all create conditions that are practical. For example, trying to specify something to happen every `second month` will create a next run date of `2 months from now`, which will never evaluate to true._

Example			| Description
----------------|---------------------------------------------
5 minutes		| Runs a task every 5 minutes, like 10:45, 10:50, etc.
2 days			| Runs a task very early every two days
second Monday	| The second Monday of each month.
thursday		| Every Thursday
weekday			| Midnight every weekday
monday 3am		| every Monday at 3:00am
weekdays 5am	| every weekday at 3:00am
back of 3am		| every day at 3:15am
front of 3am	| every day at 2:45am

Please note that some combinations will not work. If it isn't shown on this table, it hasn't been tested. If you require other schedule strings to be supported, please post an issue (and a pull request would be even better!).

## CLI Tools
The cron module provides a few CLI-based tools to help you verify when your cronjobs are scheduled to run.

### Show Tasks
Lists the available tasks defined in the sytem.

	$ php sprint cron show

	Available Tasks:
		task1
		task2

### Show All Tasks
The `list all` task will spit out a table with all scheduled tasks and their next and previous run times.

	$ php sprint cron show all

	Task				Next Run				Previous Run
	--------------------------------------------------------------------------
	task1				Thu 2014-10-30 20:45	Thu 2014-10-30 20:50
	task with a long...	Thu 2014-10-30 20:45	Thu 2014-10-30 20:50

### Show A Single Task
By passing a task name to the `show` command instead of `all` you can limit the results to a single task.

$ php index.php cron show task1

	Task				Next Run				Previous Run
	--------------------------------------------------------------------------
	task1				Thu 2014-10-30 20:45	Thu 2014-10-30 20:50

### Suspend A Task
You can suspend a task from running temporarily with the `suspend` command. This does not edit your config file, so the task is still there, and normal execution can be resumed later. This is handy for when you need to do some debugging on a live server, or need to do some other maintenance. You can suspend the task, do your maintenance, and then resume it when it's safe to run. Also keeps the database safe during potential upgrades.

The only parameter is the name of the task.

	$ php sprint cron suspend task1

### Resume A Task
Resuming a task that has been suspended is a simple process with the `resume`.

	$ php sprint cron resumeTask task1

### Stop Cron From Running
You can suspend the cronjobs from running completely with the `disable` command. This will stop the cron system from running any tasks until you enable it again. The value is stored in the [Settings](general/settings) system. 

	$ php sprint cron disable

This can be restarted again with the `enable` command.

	$ php sprint cron enable
