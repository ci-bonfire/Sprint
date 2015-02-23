<?php 

use Myth\Mail\Mail;
use \Mockery as m;
use Myth\Mail\Queue;

class MailTest extends CodeIgniterTestCase {
	
	public function _before() {
		// Make sure we're not sending real emails.
		$this->ci->config->set_item('mail.default_service', '\Myth\Mail\LogMailService');

		// Better yet, just pretend to send...
		$this->ci->config->set_item('mail.pretend', true);
	}
	
	//--------------------------------------------------------------------
	
	public function _after() {
		// Clean up the email log folder
		$this->emptyLogFolder();
	}
	
	//--------------------------------------------------------------------
	
	//--------------------------------------------------------------------
	// Deliver
	//--------------------------------------------------------------------
	
	public function testExceptionOnInvalidMailer()
	{
	    $cmd = "CronnerMail:results";

		$this->setExpectedException('\RuntimeException');

		Mail::deliver($cmd);
	}
	
	//--------------------------------------------------------------------

	public function testDeliverReturnsFalseWithBadMethod()
	{
		$cmd = "CronMailer:resultsorsomething";

		$this->setExpectedException('\BadMethodCallException');

		Mail::deliver($cmd);
	}

	//--------------------------------------------------------------------

	public function testDeliverReturnsTrueOnSuccess()
	{
		$cmd = "CronMailer:results";

		$this->assertTrue( Mail::deliver($cmd) );
	}

	//--------------------------------------------------------------------

	//--------------------------------------------------------------------
	// Queue
	//--------------------------------------------------------------------
	
	public function testQueueReturnsFalseOnError()
	{
	    $model = m::mock('\Myth\Mail\Queue');

		$model->shouldReceive('insert')->once()->andReturn(false);

		$result = Mail::queue('mailer_name', ['some', 'stuff'], [], $model);

		$this->assertFalse($result);
	}
	
	//--------------------------------------------------------------------

	public function testQueueReturnsIDOnSuccess()
	{
		$model = m::mock('\Myth\Mail\Queue');

		$model->shouldReceive('insert')->once()->andReturn(13);

		$result = Mail::queue('mailer_name', ['some', 'stuff'], [], $model);

		$this->assertEquals(13, $result);
	}

	//--------------------------------------------------------------------
	
	//--------------------------------------------------------------------
	// Process
	//--------------------------------------------------------------------

	public function testProcessReturnsTrueWhenNothingToDo()
	{
		$model = m::mock('\Myth\Mail\Queue');

		$model->shouldReceive('find_many_by')->andReturn(false);

		$this->assertTrue( Mail::process(50, $model) );
	}

	//--------------------------------------------------------------------

	public function testProcessReturnsDoneString()
	{
		$model = m::mock('\Myth\Mail\Queue');

		$queue = [];
		$item  = new stdClass();
		$item->mailer  = 'CronMailer:results';
		$item->params  = serialize([]);
		$item->options = serialize([]);
		$queue[]  = $item;

		$model->shouldReceive('find_many_by')->andReturn($queue);

		$result = Mail::process(50, $model);

		$this->assertTrue(strpos($result, 'Done') !== false);
	}

	//--------------------------------------------------------------------
	
	//--------------------------------------------------------------------
	// Private Methods
	//--------------------------------------------------------------------


	private function emptyLogFolder( )
	{
		if (is_dir(APPPATH ."logs/email"))
		{
			@array_map('unlink', glob(APPPATH ."logs/email/{,.}*", GLOB_BRACE));
			rmdir( APPPATH . "logs/email" );
		}
	}

	//--------------------------------------------------------------------

}