<?php

namespace Myth\Auth\Models;

use Myth\Models\CIDbModel;

class FlatPermissionsModel extends CIDbModel {
	
	protected $table_name = 'auth_permissions';

	protected $soft_deletes = false;

	protected $set_created = false;

	protected $set_modified = false;

	protected $protected_attributes = ['id', 'submit'];

	protected $validation_rules = [
		[
			'field' => 'name',
			'label' => 'Name',
			'rules' => 'trim|max_length[255]|xss_clean'
		],
		[
			'field' => 'description',
			'label' => 'Description',
			'rules' => 'trim|max_length[255]|xss_clean'
		],
	];

	protected $insert_validate_rules = [
		'name'      => 'required|is_unique[auth_groups.name]'
	];

	protected $before_insert = [];
	protected $before_update = [];
	protected $after_insert  = [];
	protected $after_update  = [];


	protected $fields = [];

	//--------------------------------------------------------------------

	/**
	 * Checks to see if a user, or one of their groups, has a specific
	 * permission.
	 *
	 * @param $user_id
	 * @param $permission_id
	 *
	 * @return bool
	 */
	public function doesUserHavePermission($user_id, $permission_id)
	{
		$permissions = $this->join('auth_groups_permissions', 'auth_groups_permissions.permission_id = auth_permissions.id', 'inner')
							->join('auth_groups_users', 'auth_groups_users.group_id = auth_groups_permissions.group_id', 'inner')
							->where('auth_groups_users.user_id', (int)$user_id)
							->as_array()
							->find_all();

		if (! $permissions)
		{
			return false;
		}

		$ids = array_column($permissions, 'permission_id');

		return in_array($permission_id, $ids);
	}

	//--------------------------------------------------------------------


}