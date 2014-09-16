<?php

use Myth\Models\CIDbModel as CIDbModel;

class Record_model extends CIDbModel {

	protected $table_name = 'records_table';

	protected $set_created = false;
	protected $set_modified = false;

    protected $validation_rules = array(
        array(
            'field' => 'first_name',
            'label' => 'First Name',
            'rules' => 'trim'
        ),
        array(
            'field' => 'last_name',
            'label' => 'Last Name',
            'rules' => 'trim'
        ),
    );

    protected $skip_validation = true;

    protected $soft_deletes = false;

}