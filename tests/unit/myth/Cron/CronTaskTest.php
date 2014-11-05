<?php

use Myth\Cron\CronTask as CronTask;

/**
 * Class CronTaskTest
 *
 */
class CronTaskTest extends CodeIgniterTestCase {

    public function _before() { }

    //--------------------------------------------------------------------

    public function _after() { }

    //--------------------------------------------------------------------

    public function testCreationStoresVars()
    {
        $schedule = '2 seconds';
        $myTask   = 'library:method';

        $task = new CronTask($schedule, $myTask);

        $this->assertEquals($schedule, $task->schedule());
        $this->assertEquals($myTask, $task->task());
    }

    //--------------------------------------------------------------------

    //--------------------------------------------------------------------
    // Schedule Type
    //--------------------------------------------------------------------

    public function testScheduleTypeWithDay()
    {
        $schedule = 'thursday';
        $myTask   = 'library:method';

        $task = new CronTask($schedule, $myTask);

        $this->assertEquals('increment', $task->determineScheduleType('sunday'));
        $this->assertEquals('increment', $task->determineScheduleType('monday'));
        $this->assertEquals('increment', $task->determineScheduleType('tuesday'));
        $this->assertEquals('increment', $task->determineScheduleType('Wednesday'));
        $this->assertEquals('increment', $task->determineScheduleType('thursday'));
        $this->assertEquals('increment', $task->determineScheduleType('Friday'));
        $this->assertEquals('increment', $task->determineScheduleType('saturday'));
        $this->assertEquals('increment', $task->determineScheduleType('sun'));
        $this->assertEquals('increment', $task->determineScheduleType('mon'));
        $this->assertEquals('increment', $task->determineScheduleType('tue'));
        $this->assertEquals('increment', $task->determineScheduleType('wed'));
        $this->assertEquals('increment', $task->determineScheduleType('thu'));
        $this->assertEquals('increment', $task->determineScheduleType('Fri'));
        $this->assertEquals('increment', $task->determineScheduleType('SAT'));
        $this->assertEquals('increment', $task->determineScheduleType('weekday'));
        $this->assertEquals('increment', $task->determineScheduleType('weekdays'));
        $this->assertEquals('increment', $task->determineScheduleType('midnight'));
        $this->assertEquals('increment', $task->determineScheduleType('noon'));
        $this->assertEquals('increment', $task->determineScheduleType('back of'));
        $this->assertEquals('increment', $task->determineScheduleType('front of'));
        $this->assertEquals('increment', $task->determineScheduleType('first day of'));
        $this->assertEquals('increment', $task->determineScheduleType('last day of'));
    }

    //--------------------------------------------------------------------

    public function testScheduleTypeWithOrdinal()
    {
        $schedule = 'thursday';
        $myTask   = 'library:method';

        $task = new CronTask($schedule, $myTask);

        $this->assertEquals('ordinal', $task->determineScheduleType('first monday'));
        $this->assertEquals('ordinal', $task->determineScheduleType('second monday'));
        $this->assertEquals('ordinal', $task->determineScheduleType('Third monday'));
        $this->assertEquals('ordinal', $task->determineScheduleType('fourth monday'));
        $this->assertEquals('ordinal', $task->determineScheduleType('fifth monday'));
        $this->assertEquals('ordinal', $task->determineScheduleType('sixth monday'));
        $this->assertEquals('ordinal', $task->determineScheduleType('seventh monday'));
        $this->assertEquals('ordinal', $task->determineScheduleType('eighth monday'));
        $this->assertEquals('ordinal', $task->determineScheduleType('ninth monday'));
        $this->assertEquals('ordinal', $task->determineScheduleType('tenth monday'));
        $this->assertEquals('ordinal', $task->determineScheduleType('eleventh monday'));
        $this->assertEquals('ordinal', $task->determineScheduleType('twelfth monday'));
    }

    //--------------------------------------------------------------------

    public function testScheduleTypeWithTime()
    {
        $schedule = 'thursday';
        $myTask   = 'library:method';

        $task = new CronTask($schedule, $myTask);

        $this->assertEquals('time', $task->determineScheduleType('5 minutes'));
    }

    //--------------------------------------------------------------------

    public function testScheduleTypeReturnsNUllWithOther()
    {
        $schedule = 'thursday';
        $myTask   = 'library:method';

        $task = new CronTask($schedule, $myTask);

        $this->assertNull($task->determineScheduleType('minutes'));
    }

    //--------------------------------------------------------------------

    //--------------------------------------------------------------------
    // Find Date Interval
    //--------------------------------------------------------------------

    public function testFindDateIntervalWithSeconds()
    {
        $schedule = '10 seconds';
        $myTask   = 'library:method';

        $task = new CronTask($schedule, $myTask);

        $result = $task->findDateInterval($schedule, 'next');

        $this->assertEquals(0, $result % 10);
    }

    //--------------------------------------------------------------------

    public function testFindDateIntervalWithMinutes()
    {
        $schedule = '10 minutes';
        $myTask   = 'library:method';

        $task = new CronTask($schedule, $myTask);

        $result = $task->findDateInterval($schedule, 'next');

        $this->assertEquals(0, $result % 60);
    }

    //--------------------------------------------------------------------

    public function testFindDateIntervalWithMinutesAndCurrentTime()
    {
        $schedule = '5 minutes';
        $myTask   = 'library:method';

        $task = new CronTask($schedule, $myTask);

        $current_time = strtotime('10:45am');

        $result = $task->findDateInterval($schedule, 'next', $current_time);

        $this->assertEquals(date('Y-m-d H:i', $current_time), date('Y-m-d H:i', $result));

    }

    //--------------------------------------------------------------------

    public function testFindDateIntervalWithDays()
    {
        $schedule = '2 days';
        $myTask   = 'library:method';

        $task = new CronTask($schedule, $myTask);

        $result = $task->findDateInterval($schedule, 'next');

        $this->assertEquals(0, $result % (60 * 60 * 24 * 2) );
    }

    //--------------------------------------------------------------------

    public function testFindDateIntervalWithWeeks()
    {
        $schedule = '2 weeks';
        $myTask   = 'library:method';

        $task = new CronTask($schedule, $myTask);

        $result = $task->findDateInterval($schedule, 'next');

        $this->assertEquals(0, $result % (60 * 60 * 24 * 14) );
    }

    //--------------------------------------------------------------------

    public function testFindDateIntervalWithPreviousMinutes()
    {
        $schedule = '10 minutes';
        $myTask   = 'library:method';

        $task = new CronTask($schedule, $myTask);

        $result = $task->findDateInterval($schedule, 'prev');

        $this->assertEquals(0, $result % 60);
        $this->assertTrue($result < time());
    }

    //--------------------------------------------------------------------

    public function testFindDateIntervalWithPreviousMinutesInPast()
    {
        $schedule = '10 minutes';
        $myTask   = 'library:method';

        $current_time = strtotime('-5 days');

        $task = new CronTask($schedule, $myTask);

        $result = $task->findDateInterval($schedule, 'prev', $current_time);

        $this->assertEquals(0, $result % 60);
        $this->assertTrue($result < $current_time);
    }

    //--------------------------------------------------------------------

    public function testFindDateIntervalWithPreviousDays()
    {
        $schedule = '2 days';
        $myTask   = 'library:method';

        $task = new CronTask($schedule, $myTask);

        $result = $task->findDateInterval($schedule, 'prev');

        $this->assertEquals(0, $result % (60 * 60 * 24 * 2) );
        $this->assertTrue($result < time());
    }

    //--------------------------------------------------------------------

    //--------------------------------------------------------------------
    // Previous Ordinal Dates
    //--------------------------------------------------------------------

    public function testPrevOrdinalWithDay()
    {
        $schedule = 'second Monday';
        $myTask = 'library:method';

        $task = new CronTask($schedule, $myTask);

        $current_time = strtotime('Oct 29, 2014');
        $result = $task->findPreviousOrdinal($schedule, $current_time);

        $this->assertEquals( date('Y-m-d', strtotime('Oct 13, 2014')), date('Y-m-d', $result) );
    }

    //--------------------------------------------------------------------

    /*
     * NOTE: Second Month will add two months from now,
     * which means that it can never be ran with cron.
     */
//    public function testPrevOrdinalWithMonth()
//    {
//        $schedule = 'second month';
//        $myTask = 'library:method';
//
//        $task = new CronTask($schedule, $myTask);
//
//        $current_time = strtotime('Oct 29, 2014');
//        $result = $task->findPreviousOrdinal($schedule, $current_time);
//
//        var_dump(date('Y-m-d', strtotime('second month')));
//        var_dump(date('Y-m-d', strtotime('February 1, 2014')));
//        die(var_dump(date('Y-m-d', $result)));
//
//        $this->assertEquals( date('Y-m-d', strtotime('February 1, 2014')), date('Y-m-d', $result) );
//    }

    //--------------------------------------------------------------------

    //--------------------------------------------------------------------
    // Next Run Date
    //--------------------------------------------------------------------

    public function testNextRunDateWithSeconds()
    {
        $schedule = '7 seconds';
        $myTask   = 'library:method';

        $task = new CronTask($schedule, $myTask);

        $this->assertEquals(0, $task->nextRunDate() % 7);
    }

    //--------------------------------------------------------------------

    public function testNextRunDateWith5Minutes()
    {
        $schedule = '5 minutes';
        $myTask   = 'library:method';

        $task = new CronTask($schedule, $myTask);

        $time = $task->nextRunDate();

        $this->assertEquals(0, $task->nextRunDate() % 300);
    }

    //--------------------------------------------------------------------

    public function testNextRunDateWithWeekDay()
    {
        $schedule = 'thursday';
        $myTask   = 'library:method';

        $task = new CronTask($schedule, $myTask);

        $current_time = time();

        $this->assertEquals(strtotime('thursday'), $task->nextRunDate());
    }

    //--------------------------------------------------------------------

    public function testNextRunDateWithWeekDays()
    {
        $schedule = 'weekday';
        $myTask   = 'library:method';

        $task = new CronTask($schedule, $myTask);

        $this->assertEquals(strtotime('next monday'), $task->nextRunDate('next monday'));
    }

    //--------------------------------------------------------------------

    public function testNextRunDateWithSecondDay()
    {
        $schedule = 'second Monday';
        $myTask   = 'library:method';

        $task = new CronTask($schedule, $myTask);
        $next = $this->calcDateInterval('second', 'Monday');

        $result = $task->nextRunDate();

        $this->assertEquals('Mon', date('D', $result));
        $this->assertTrue($result > time());
    }

    //--------------------------------------------------------------------

    //--------------------------------------------------------------------
    // Previous Run Date
    //--------------------------------------------------------------------

    public function testPrevRunDateWithSeconds()
    {
        $schedule = '7 seconds';
        $myTask   = 'library:method';

        $task = new CronTask($schedule, $myTask);

        $current_time = time();
        $result = $task->previousRunDate($current_time);

        $this->assertEquals(0, $result % 7);
        $this->assertTrue($result < $current_time);
    }

    //--------------------------------------------------------------------

    public function testPrevRunDateWith5Minutes()
    {
        $schedule = '5 minutes';
        $myTask   = 'library:method';

        $task = new CronTask($schedule, $myTask);

        $current_time = time();
        $result = $task->previousRunDate($current_time);

        $this->assertEquals(0, $result % 300);
        $this->assertTrue($result < $current_time);
    }

    //--------------------------------------------------------------------

    public function testPrevRunDateWith5MinutesInPast()
    {
        $schedule = '5 minutes';
        $myTask   = 'library:method';

        $task = new CronTask($schedule, $myTask);

        $current_time = strtotime('-5 days');
        $result = $task->previousRunDate($current_time);

        $this->assertEquals(0, $result % 300);
        $this->assertTrue($result < $current_time);
    }

    //--------------------------------------------------------------------

    public function testPrevRunDateWithWeekDay()
    {
        $schedule = 'thursday';
        $myTask   = 'library:method';

        $task = new CronTask($schedule, $myTask);

        $current_time = time();
        $result = $task->previousRunDate($current_time);

        $this->assertEquals(strtotime('-1 thursday'), $result);
        $this->assertTrue($result < $current_time);
    }

    //--------------------------------------------------------------------

    public function testPrevRunDateWithWeekDays()
    {
        $schedule = 'weekday';
        $myTask   = 'library:method';

        $task = new CronTask($schedule, $myTask);

        $current_time = strtotime('last tuesday');
        $result = $task->previousRunDate($current_time);

        $this->assertEquals(date('D Y-m-d H:i', strtotime('last monday')), date('D Y-m-d H:i', $result));
        $this->assertTrue($result < $current_time);
    }

    //--------------------------------------------------------------------

    public function testPrevRunDateWithSecondDay()
    {
        $schedule = 'second Monday';
        $myTask   = 'library:method';

        $task = new CronTask($schedule, $myTask);

        $current_time = strtotime('October 29, 2014');
        $result = $task->previousRunDate($current_time);

        $this->assertEquals( date('Y-m-d', strtotime('Oct 13 2014')), date('Y-m-d', $result));
        $this->assertTrue($result < $current_time);
    }

    //--------------------------------------------------------------------

    public function testPreviousRunDateWithDayOfWeek()
    {
        $schedule = 'Monday';
        $myTask   = 'library:method';

        $task = new CronTask($schedule, $myTask);

        $current_time = strtotime('October 29, 2014');
        $result = $task->previousRunDate($current_time);

        $this->assertEquals( date('Y-m-d', strtotime('Oct 27 2014')), date('Y-m-d', $result));
        $this->assertTrue($result < $current_time);
    }

    //--------------------------------------------------------------------

    //--------------------------------------------------------------------
    // Is Due?
    //--------------------------------------------------------------------

    public function testIsDueReturnsFalseWhenNotDue()
    {
        $schedule = 'Monday';
        $myTask   = 'library:method';

        $task = new CronTask($schedule, $myTask);

        $current_time = strtotime('Tuesday');

        $this->assertFalse($task->isDue($current_time));
    }

    //--------------------------------------------------------------------

    public function testIsDueReturnsTrueIfDue()
    {
        $schedule = 'Monday';
        $myTask   = 'library:method';

        $task = new CronTask($schedule, $myTask);

        $current_time = strtotime('Monday');

        $this->assertTrue($task->isDue($current_time));
    }

    //--------------------------------------------------------------------

    public function testIsDueReturnsTrueWithTimeInterval()
    {
        $schedule = '5 minutes';
        $myTask   = 'library:method';

        $task = new CronTask($schedule, $myTask);

        $current_time = strtotime('10:45am');

        $this->assertTrue($task->isDue($current_time));
    }

    //--------------------------------------------------------------------

    //--------------------------------------------------------------------
    // Various Schedule String Tests
    //--------------------------------------------------------------------

    public function testScheduleStringWithTimeOfDay()
    {
        $schedule = 'Monday 5am';
        $myTask   = 'library:method';

        $task = new CronTask($schedule, $myTask);

        $current_time = strtotime('October 27 2014 4am');

        $this->assertEquals(strtotime('October 27 2014 5am'), $task->nextRunDate($current_time));
    }

    //--------------------------------------------------------------------

    public function testScheduleStringWithTimeOfWeekDay()
    {
        $schedule = 'weekdays 5am';
        $myTask   = 'library:method';

        $task = new CronTask($schedule, $myTask);

        $current_time = strtotime('October 27 2014 4am');

        $this->assertEquals(strtotime('October 27 2014 5am'), $task->nextRunDate($current_time));
    }

    //--------------------------------------------------------------------

    public function testScheduleStringWithBackOfHour()
    {
        $schedule = 'back of 5am';
        $myTask   = 'library:method';

        $task = new CronTask($schedule, $myTask);

        $current_time = strtotime('October 27 2014 4am');

        $this->assertEquals(strtotime('October 27 2014 5:15am'), $task->nextRunDate($current_time));
    }

    //--------------------------------------------------------------------


    //--------------------------------------------------------------------

    //--------------------------------------------------------------------
    // Private Methods
    //--------------------------------------------------------------------

    private function calcDateInterval($interval="second", $day="Saturday")
    {
        $now = date("U");
        $monthyear = date("F Y");
        $next = date('U', strtotime("{$monthyear} {$interval} {$day}"));
        if ($now > $next)
        {
            $monthyear = date("F Y", strtotime("next month") );
            $next = date('U', strtotime("{$monthyear} {$interval} {$day}"));
        }

        return strtotime($next);
    }

    //--------------------------------------------------------------------

}