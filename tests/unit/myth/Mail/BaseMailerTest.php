<?php

use Myth\Mail\BaseMailer;
use \Mockery as m;

class BaseMailerTest extends CodeIgniterTestCase {

	protected $mailer;

	protected $service;

	//--------------------------------------------------------------------

	public function _before()
	{
		$this->service = m::mock('\Myth\Mail\LogMailService');

		$this->mailer = new BaseMailer();

		$this->ci->config->set_item('mail.pretend', false);
	}

	//--------------------------------------------------------------------

	public function _after() {
		unset($this->mailer);

		if (is_dir(APPPATH ."logs/email"))
		{
			@array_map('unlink', glob(APPPATH ."logs/email/{,.}*", GLOB_BRACE));
			rmdir( APPPATH . "logs/email" );
		}
	}

	//--------------------------------------------------------------------

	public function testAttach()
	{
		$filename = 'somefile';
		$disposition = 'inline';
		$mime = 'text/plain';

		$this->service->shouldReceive('attach')->once()->with([$filename, $disposition, $filename, $mime]);

		$this->mailer->attach($filename, $disposition, $filename, $mime);
	}

	//--------------------------------------------------------------------

	public function testHeader()
	{
		$header = 'custom';
		$value = 'customValue';

		$this->service->shouldReceive('attach')->once()->with([$header, $value]);

		$this->mailer->header($header, $value);
	}

	//--------------------------------------------------------------------

	public function testSendBasics()
	{
		$options = [
			'from' => 'someone@lovesyou.com',
			'reply_to' => 'someonelse@lovesyou.com',
			'cc' => 'carbon@copy.com',
			'bcc' => 'blind@copy.com',
			'theme' => 'email',
			'layout' => 'index',
			'message' => 'It Feels Like Today',
			'service' => $this->service
		];
		$to = 'someone';
		$subject = 'Something To Be';

		$this->mailer->setOptions($options);

		$this->service->shouldReceive('to')->once()->with($to);
		$this->service->shouldReceive('from')->once()->with($options['from']);
		$this->service->shouldReceive('subject')->once()->with($subject);
		$this->service->shouldReceive('cc')->once()->with($options['cc']);
		$this->service->shouldReceive('bcc')->once()->with($options['bcc']);
		$this->service->shouldReceive('reply_to')->once()->with($options['reply_to']);
		$this->service->shouldReceive('text_message')->once()->with($options['message']);
		$this->service->shouldReceive('send')->once()->andReturn(true);

		$this->assertTrue( $this->mailer->send($to, $subject) );
	}

	//--------------------------------------------------------------------

	/**
	 * @group single
	 */
	public function testSendWithText()
	{
		$options = [
			'from' => 'someone@lovesyou.com',
			'reply_to' => 'someonelse@lovesyou.com',
			'cc' => 'carbon@copy.com',
			'bcc' => 'blind@copy.com',
			'theme' => 'email',
			'layout' => 'index',
		];
		$to = 'someone';
		$subject = 'Something To Be';

		$this->ci->config->set_item('mail.default_service', '\Myth\Mail\LogMailService');
		$mailer = new BaseMailer($options);

		// Create a temp HTML file so that we can test the
		$folder = 'views/emails/basemailer/';
		$view = $folder .'testSendWithText';
		$path = APPPATH ."{$view}.text.php";
		$this->ci->load->helper('file');
		@mkdir(APPPATH . $folder);

		write_file($path, 'Something goes here');

		$this->assertTrue( $mailer->send($to, $subject) );

		// Get rid of the temp file.
		@unlink($path);
		@rmdir(APPPATH . $folder);
	}

	//--------------------------------------------------------------------

	public function testSendWithHTML()
	{
		$options = [
			'from' => ['someone@lovesyou.com', 'Someone'],
			'reply_to' => ['someonelse@lovesyou.com', 'Someone Else'],
			'cc' => 'carbon@copy.com',
			'bcc' => 'blind@copy.com',
			'theme' => 'email',
			'layout' => 'index',
		];
		$to = 'someone';
		$subject = 'Something To Be';

		$this->ci->config->set_item('mail.default_service', '\Myth\Mail\LogMailService');
		$mailer = new BaseMailer($options);

		// Create a temp HTML file so that we can test the
		$folder = 'views/emails/basemailer/';
		$view = $folder .'testSendWithHTML';
		$path = APPPATH ."{$view}.html.php";
		$this->ci->load->helper('file');
		@mkdir(APPPATH . $folder);

		write_file($path, 'Something goes here');

		$this->assertTrue( $mailer->send($to, $subject) );

		// Get rid of the temp file.
		@unlink($path);
		@rmdir(APPPATH . $folder);
	}

	//--------------------------------------------------------------------

	public function testSendReturnsTrueWhenPretending()
	{
		$this->ci->config->set_item('mail.pretend', true);

		$to = 'someone';
		$subject = 'Something To Be';

		$this->assertTrue($this->mailer->send($to, $subject));
	}

	//--------------------------------------------------------------------
}