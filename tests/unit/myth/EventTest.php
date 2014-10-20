<?php

namespace Myth;

use \Myth\Events as Events;

class EventTest extends \CodeIgniterTestCase
{
    /**
     * @var \UnitTester
     */
    protected $tester;

    protected function _before()
    {
        Events::removeAllListeners();
    }

    protected function _after()
    {
    }

    //--------------------------------------------------------------------

    public function testListeners()
    {
        $callback1 = function() {};
        $callback2 = function() {};

        Events::on('foo', $callback1, EVENTS_PRIORITY_HIGH);
        Events::on('foo', $callback2, EVENTS_PRIORITY_NORMAL);

        $this->assertEquals([$callback2, $callback1], Events::listeners('foo'));
    }

    //--------------------------------------------------------------------

    public function testHandleEvent()
    {
        $result = null;

        Events::on('foo', function($arg) use(&$result) {
            $result = $arg;
        });

        $this->assertTrue(Events::trigger('foo', ['bar']) );

        $this->assertEquals('bar', $result);
    }

    //--------------------------------------------------------------------

    public function testCancelEvent()
    {
        $result = 0;

        // This should cancel the flow of events, and leave
        // $result = 1.
        Events::on('foo', function($arg) use (&$result) {
            $result = 1;
            return false;
        });
        Events::on('foo', function($arg) use (&$result) {
            $result = 2;
        });

        $this->assertFalse(Events::trigger('foo', ['bar']));
        $this->assertEquals(1, $result);
    }

    //--------------------------------------------------------------------

    public function testPriority()
    {
        $result = 0;

        Events::on('foo', function($arg) use (&$result) {
            $result = 1;
            return false;
        }, EVENTS_PRIORITY_NORMAL);
        // Since this has a higher priority, it will
        // run first.
        Events::on('foo', function($arg) use (&$result) {
            $result = 2;
            return false;
        }, EVENTS_PRIORITY_HIGH);

        $this->assertFalse(Events::trigger('foo', ['bar']));
        $this->assertEquals(2, $result);
    }

    //--------------------------------------------------------------------

    public function testPriorityWithMultiple()
    {
        $result = [];

        Events::on('foo', function() use (&$result) {
            $result[] = 'a';
        }, EVENTS_PRIORITY_NORMAL);

        Events::on('foo', function() use (&$result) {
            $result[] = 'b';
        }, EVENTS_PRIORITY_LOW);

        Events::on('foo', function() use (&$result) {
            $result[] = 'c';
        }, EVENTS_PRIORITY_HIGH);

        Events::on('foo', function() use (&$result) {
            $result[] = 'd';
        }, 75);

        Events::trigger('foo');
        $this->assertEquals(['c', 'd', 'a', 'b'], $result);
    }

    //--------------------------------------------------------------------

    public function testRemoveListener()
    {
        $result = false;

        $callback = function() use (&$result)
        {
            $result = true;
        };

        Events::on('foo', $callback);

        Events::trigger('foo');
        $this->assertTrue($result);

        $result = false;
        $this->assertTrue( Events::removeListener('foo', $callback) );

        Events::trigger('foo');
        $this->assertFalse($result);
    }

    //--------------------------------------------------------------------

    public function testRemoveListenerTwice()
    {
        $result = false;

        $callback = function() use (&$result)
        {
            $result = true;
        };

        Events::on('foo', $callback);

        Events::trigger('foo');
        $this->assertTrue($result);

        $result = false;
        $this->assertTrue( Events::removeListener('foo', $callback) );
        $this->assertFalse( Events::removeListener('foo', $callback) );

        Events::trigger('foo');
        $this->assertFalse($result);
    }

    //--------------------------------------------------------------------

    public function testRemoveUnknownListener()
    {
        $result = false;

        $callback = function() use (&$result)
        {
            $result = true;
        };

        Events::on('foo', $callback);

        Events::trigger('foo');
        $this->assertTrue($result);

        $result = false;
        $this->assertFalse( Events::removeListener('bar', $callback) );

        Events::trigger('foo');
        $this->assertTrue($result);
    }

    //--------------------------------------------------------------------




}