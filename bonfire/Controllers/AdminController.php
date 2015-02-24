<?php namespace Bonfire\Controllers;

/**
 * Admin Controller
 *
 * Sets up defaults
 */
class AdminController extends \Myth\Controllers\ThemedController {

	use \Myth\Auth\AuthTrait;

	protected $theme = 'bonfire';

	protected $per_page = 25;

	//--------------------------------------------------------------------

	public function __construct()
	{
	    parent::__construct();

		$this->load->driver('session');

		$this->load->database();

		/**
		 * Automatically set a view var called 'controlbar'
		 * if a method of the same name exists in this class.
		 * This will then tell the theme how to display itself.
		 */
		if (method_exists($this, 'controlbar'))
		{
			$this->setVar( 'controlbar', $this->controlbar() );
		}
	}

	//--------------------------------------------------------------------

}