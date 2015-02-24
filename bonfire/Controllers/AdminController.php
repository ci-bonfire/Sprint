<?php namespace Bonfire\Controllers;

/**
 * Admin Controller
 *
 * Sets up defaults
 */
class AdminController extends \Myth\Controllers\ThemedController {

	use \Myth\Auth\AuthTrait;

	protected $theme = 'bonfire';

	//--------------------------------------------------------------------

	public function __construct()
	{
	    parent::__construct();

		$this->load->driver('session');
	}

	//--------------------------------------------------------------------



}