<?php

use Myth\Cron\CronManager as CronManager;

/**
 * Class CronTaskTest
 *
 */
class CronManagerTest extends CodeIgniterTestCase {

    public function _before() {
        CronManager::reset();
    }

    //--------------------------------------------------------------------

    public function _after() { }

    //--------------------------------------------------------------------

    //--------------------------------------------------------------------
    // Schedule
    //--------------------------------------------------------------------

    public function testThrowsExceptionOnInvalidTask()
    {
        $thrown = false;

        try {
            CronManager::schedule('task1', '5 minutes', 45);
        }
        catch (\RuntimeException $e)
        {
            $thrown = true;
        }

        $this->assertTrue($thrown);
    }

    //--------------------------------------------------------------------

    public function testThrowsExceptionOnEmptyAlias()
    {
        $thrown = false;

        try {
            CronManager::schedule('', '5 minutes', 'library:method');
        }
        catch (\RuntimeException $e)
        {
            $thrown = true;
        }

        $this->assertTrue($thrown);
    }

    //--------------------------------------------------------------------

    public function testThrowsExceptionOnEmptyTime()
    {
        $thrown = false;

        try {
            CronManager::schedule('task1', '', 'library:method');
        }
        catch (\RuntimeException $e)
        {
            $thrown = true;
        }

        $this->assertTrue($thrown);
    }

    //--------------------------------------------------------------------

    //--------------------------------------------------------------------
    // Task
    //--------------------------------------------------------------------

    public function testTaskRetrievesTheRightTask()
    {
        CronManager::schedule('task1', '5 minutes', 'library:method');

        $expected = new \Myth\Cron\CronTask('5 minutes', 'library:method');

        $this->assertEquals($expected, CronManager::task('task1'));
    }

    //--------------------------------------------------------------------

    public function testTaskReturnsNullOnNoneFound()
    {
        CronManager::schedule('task1', '5 minutes', 'library:method');

        $expected = new \Myth\Cron\CronTask('5 minutes', 'library:method');

        $this->assertNull(CronManager::task('task2'));
    }

    //--------------------------------------------------------------------

    public function testTaskRetrievesTheRightTaskWithMultiple()
    {
        CronManager::schedule('task1', '5 minutes', 'library:method');
        CronManager::schedule('task2', '6 minutes', 'library:method');
        CronManager::schedule('task3', '7 minutes', 'library:method');

        $expected = new \Myth\Cron\CronTask('6 minutes', 'library:method');

        $this->assertEquals($expected, CronManager::task('task2'));
    }

    //--------------------------------------------------------------------

    //--------------------------------------------------------------------
    // Tasks
    //--------------------------------------------------------------------

    public function testStoresTasksCorrectly()
    {
        CronManager::schedule('task1', '5 minutes', 'library:method');

        $expected = [
            'task1' => new \Myth\Cron\CronTask('5 minutes', 'library:method')
        ];

        $this->assertEquals($expected, CronManager::tasks());
    }

    //--------------------------------------------------------------------

    public function testTasksReturnNullWithNoTasks()
    {
        $this->assertNull(CronManager::tasks());
    }

    //--------------------------------------------------------------------

    //--------------------------------------------------------------------
    // Remove
    //--------------------------------------------------------------------

    public function testRemoveActuallyRemovesTask()
    {
        CronManager::schedule('task1', '5 minutes', 'library:method');
        CronManager::schedule('task2', '6 minutes', 'library:method');
        CronManager::schedule('task3', '7 minutes', 'library:method');

        CronManager::remove('task2');

        $tasks = CronManager::tasks();

        $this->assertFalse(isset($tasks['task2']));
        $this->assertEquals(2, count($tasks));
    }

    //--------------------------------------------------------------------

    public function testRemoveReturnsNullOnInvalidTask()
    {
        CronManager::schedule('task1', '5 minutes', 'library:method');
        CronManager::schedule('task2', '6 minutes', 'library:method');
        CronManager::schedule('task3', '7 minutes', 'library:method');

        $this->assertNull(CronManager::remove('task5'));
    }

    //--------------------------------------------------------------------

    public function testRemoveReturnsTrueOnValidTask()
    {
        CronManager::schedule('task1', '5 minutes', 'library:method');
        CronManager::schedule('task2', '6 minutes', 'library:method');
        CronManager::schedule('task3', '7 minutes', 'library:method');

        $this->assertTrue(CronManager::remove('task2'));
    }

    //--------------------------------------------------------------------

    //--------------------------------------------------------------------
    // List All
    //--------------------------------------------------------------------

    public function testListAllReturnsNullWithNotTasks()
    {
        $this->assertNull(CronManager::listAll());
    }
    
    //--------------------------------------------------------------------
    
    
    public function testListAllReturnsArrayItemsForEachTask()
    {
        CronManager::schedule('task1', '5 minutes', 'library:method');
        CronManager::schedule('task2', '6 minutes', 'library:method');
        CronManager::schedule('task3', '7 minutes', 'library:method');

        $all = CronManager::listAll();

        $this->assertTrue(isset($all['task1']));
        $this->assertTrue(isset($all['task2']));
        $this->assertTrue(isset($all['task3']));
    }

    //--------------------------------------------------------------------

    public function testListAllReturnsCorrectEntriesForItems()
    {
        CronManager::schedule('task1', '5 minutes', 'library:method');
        CronManager::schedule('task2', '6 minutes', 'library:method');
        CronManager::schedule('task3', '7 minutes', 'library:method');

        $all = CronManager::listAll();

        $this->assertTrue(isset($all['task1']['next_run']));
        $this->assertTrue(isset($all['task1']['prev_run']));
        $this->assertTrue(isset($all['task1']['task']));
    }

    //--------------------------------------------------------------------

    public function testListAllReturnsRelativeTimesInPresent()
    {
        CronManager::schedule('task1', '5 minutes', 'library:method');
        CronManager::schedule('task2', '6 minutes', 'library:method');
        CronManager::schedule('task3', '7 minutes', 'library:method');

        $time = time();

        $all = CronManager::listAll( $time );

        $this->assertTrue($all['task1']['next_run'] >= strtotime(date('Y-m-d H:i', $time)));
        $this->assertTrue($all['task1']['prev_run'] < $time);
    }

    //--------------------------------------------------------------------

    public function testListAllReturnsRelativeTimesInPast()
    {
        CronManager::schedule('task1', '5 minutes', 'library:method');
        CronManager::schedule('task2', '6 minutes', 'library:method');
        CronManager::schedule('task3', '7 minutes', 'library:method');

        $time = strtotime('-5 days');

        $all = CronManager::listAll( $time );

        // Must do fancy check since findTimeInterval rounds to current minute...
        $this->assertTrue($all['task1']['next_run'] >= strtotime(date('Y-m-d H:i', $time)));
        $this->assertTrue($all['task1']['prev_run'] < $time);
    }

    //--------------------------------------------------------------------

    //--------------------------------------------------------------------
    // Run
    //--------------------------------------------------------------------

    public function testRunActuallyRuns()
    {
        CronManager::schedule('task1', '5 minutes', function () { return true; });

        $current_time = strtotime('10:45:05am');

        $result = CronManager::run('task1', false,  $current_time);

        $this->assertTrue( strpos($result, 'task1') !== false );
        $this->assertTrue( strpos($result, 'Done') !== false );
    }

    //--------------------------------------------------------------------

    public function testRunActuallyRunsOnlyScheduled()
    {
        CronManager::schedule('task1', '5 minutes', function () { return true; });
        CronManager::schedule('task2', '24 minutes', function () { return true; });

        $current_time = strtotime('10:45:05');

        $result = CronManager::run(null, false, $current_time);

        $this->assertTrue( strpos($result, 'task1') !== false );
        $this->assertFalse( strpos($result, 'Running task: task2') );
    }

    //--------------------------------------------------------------------

    public function testRunActuallyRunsCollectsHistory()
    {
        CronManager::schedule('task1', '5 minutes', function () { return true; });
        CronManager::schedule('task2', '5 minutes', function () { return true; });

        $current_time = strtotime('10:45:05');

        $result = CronManager::run(null, false, $current_time);

        $this->assertTrue( strpos($result, 'task1') !== false );
        $this->assertTrue( strpos($result, 'Done') !== false );
        $this->assertTrue( strpos($result, 'task2') !== false );
    }

    //--------------------------------------------------------------------

    public function testRunActuallyRunsSingleTask()
    {
        CronManager::schedule('task1', '5 minutes', function () { return true; });
        CronManager::schedule('task2', '5 minutes', function () { return true; });

        $current_time = strtotime('10:45:05');

        $result = CronManager::run('task1', false, $current_time);

        $this->assertTrue( strpos($result, 'task1') !== false );
        $this->assertTrue( strpos($result, 'Done') !== false );
        $this->assertTrue( strpos($result, 'task2') === false );
    }

    //--------------------------------------------------------------------
}