<?php namespace Bonfire;

class Module {

	protected $ci;

	protected $active_module = false;

	//--------------------------------------------------------------------

	public function __construct()
	{
	    $this->ci =& get_instance();

		if (! empty($this->name))
		{
			$this->active_module = $this->name;
		}

		// Give the module a chance to set itself up.
		if (defined('IN_ADMIN'))
		{
			$this->doInitAdmin();
		}
		else
		{
			$this->doInit();
		}
	}

	//--------------------------------------------------------------------

	/**
	 * Runs initialization methods, like hooking into the menus, tapping
	 * into hooks, etc.
	 */
	public function doInit()
	{
		if (method_exists($this, 'init'))
		{
			$this->init();
		}
	}

	//--------------------------------------------------------------------

	/**
	 * Runs initialization methods, like hooking into the menus, tapping
	 * into hooks, etc. for Admin area
	 */
	public function doInitAdmin()
	{
		if (method_exists($this, 'initAdmin'))
		{
			$this->initAdmin();
		}
	}

	//--------------------------------------------------------------------
}