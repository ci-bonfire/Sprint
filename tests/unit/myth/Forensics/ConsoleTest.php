<?php

use Myth\Forensics\Console;

class ConsoleTest extends CodeIgniterTestCase {

	public function _before() {
		Console::reset();
	}

	//--------------------------------------------------------------------

	public function _after() { }

	//--------------------------------------------------------------------

	public function testLogInsertsEmpty()
	{
	    Console::log();

		$logs = Console::getLogs();

		$this->assertEquals(1, count($logs['console']) );
		$this->assertEquals(1, count($logs['log_count']) );
		$this->assertEquals('empty', $logs['console'][0]['data']);
	}
	
	//--------------------------------------------------------------------

	public function testLogInsertsCorrectData()
	{
		Console::log('testing');

		$logs = Console::getLogs();

		$this->assertEquals('testing', $logs['console'][0]['data']);
	}

	//--------------------------------------------------------------------

	public function testLogMemoryLogsCurrentUsageWithEmpty()
	{
	    Console::logMemory();

		$logs = Console::getLogs();

		$this->assertEquals('memory', $logs['console'][0]['type']);
		$this->assertEquals('PHP', $logs['console'][0]['name']);
		$this->assertTrue(is_numeric($logs['console'][0]['data']) );
		$this->assertTrue($logs['console'][0]['data'] > 0 );

	}

	//--------------------------------------------------------------------

	public function testLogMemoryLogsMemoryOfObject()
	{
	    $test = 'This is a simple test string';
		$mem  = strlen(serialize($test));

		Console::logMemory($test, 'teststring');

		$logs = Console::getLogs();

		$this->assertEquals($mem, $logs['console'][0]['data']);
		$this->assertEquals('teststring', $logs['console'][0]['name']);
	}

	//--------------------------------------------------------------------

}