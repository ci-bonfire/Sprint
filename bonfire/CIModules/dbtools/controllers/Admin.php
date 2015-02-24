<?php

class Admin extends \Bonfire\Controllers\AdminController {

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

	    return $this->load->view('admin/_controlbar', $data, true);
	}

	//--------------------------------------------------------------------

	public function list_table($table_name)
	{
		$page = $this->input->get('page');

		$data = [
			'table' => $table_name,
			'fields' => $this->db->list_fields($table_name),
			'total' => $this->db->count_all($table_name),
			'rows'  => $this->getRows($table_name, $page)
		];

		$return['html'] = $this->load->view('admin/_table', $data, true);

		$this->renderJSON($return);
	}

	//--------------------------------------------------------------------

	protected function getRows($table, $page=1)
	{
	    $query = $this->db->limit($this->per_page, ($page * $this->per_page) )
		                  ->get($table);

		if (! $query->num_rows())
		{
			return null;
		}

		return $query->result();
	}

	//--------------------------------------------------------------------



}