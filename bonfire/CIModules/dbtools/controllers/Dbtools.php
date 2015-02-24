<?php

class Dbtools extends \Bonfire\Controllers\AdminController {

	//--------------------------------------------------------------------

	public function index()
	{
	    $this->render();
	}

	//--------------------------------------------------------------------

	public function controlbar()
	{
		$data = [
			'tables' => $this->db->list_tables()
		];

	    return $this->load->view('_controlbar', $data, true);
	}

	//--------------------------------------------------------------------

	public function list_table($table_name)
	{
		$return['html'] = '<p>'. $table_name .'</p>';

		$this->renderJSON($return);
	}

	//--------------------------------------------------------------------



}