<?php

class Admin extends \Bonfire\Controllers\AdminController {

	public function index()
	{
	    $this->render();
	}

	//--------------------------------------------------------------------

	public function controlbar()
	{
		$data = [
			'groups' => $this->authorize->groups(true)
		];

	    return $this->load->view('admin/_controlbar', $data, true);
	}

	//--------------------------------------------------------------------



}