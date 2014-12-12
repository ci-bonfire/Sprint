<?php

namespace Myth\Auth;

use Myth\Auth\Models\FlatGroupsModel;
use Myth\Auth\Models\FlatPermissionsModel;

class FlatAuthorization implements AuthorizeInterface {

	protected $groupModel;

	protected $permissionModel;

	protected $error = null;

	//--------------------------------------------------------------------

	public function __construct()
	{
		$this->groupModel = new FlatGroupsModel();
		$this->permissionModel  = new FlatPermissionsModel();
	}

	//--------------------------------------------------------------------

	public function error()
	{
	    return $this->error;
	}

	//--------------------------------------------------------------------


	//--------------------------------------------------------------------
	// Actions
	//--------------------------------------------------------------------

	/**
	 * Checks to see if a user is in a group.
	 *
	 * Groups can be either a string, with the name of the group, an INT
	 * with the ID of the group, or an array of strings/ids that the
	 * user must belong to ONE of. (It's an OR check not an AND check)
	 *
	 * @param $groups
	 *
	 * @return bool
	 */
	public function inGroup($groups, $user_id)
	{
		if (! is_array($groups))
		{
			$groups = [ $groups ];
		}

		// todo Allow inGroup to accept group names also.
		$user_groups = $this->groupModel->getGroupsForUser( (int)$user_id);

		if (! $user_groups)
		{
			return false;
		}

		$group_ids = array_column($user_groups, 'group_id');

		$in_a_group = false;

		foreach ($groups as $id)
		{
			if (in_array($id, $group_ids))
			{
				return true;
			}
		}

		return $in_a_group;
	}

	//--------------------------------------------------------------------

	/**
	 * Checks a user's groups to see if they have the specified permission.
	 *
	 * @param int|string $permission
	 * @param int|string $user_id
	 *
	 * @return mixed
	 */
	public function hasPermission($permission, $user_id)
	{
		$permission_id = $permission;

		// Permission ID
		if (! is_numeric($permission_id))
		{
			$p = $this->permissionModel->find_by('name', $permission);

			if (! $p)
			{
				$this->error = 'Unable to locate that Permission.';
				return false;
			}

			$permission_id = $p->id;
			unset($p);
		}

		return $this->permissionModel->doesUserHavePermission($user_id, $permission_id);
	}

	//--------------------------------------------------------------------

	/**
	 * Makes a member a part of a group.
	 *
	 * @param $user_id
	 * @param $group        // Either ID or name
	 *
	 * @return bool
	 */
	public function addUserToGroup($user_id, $group)
	{
		$group_id = $group;

		// Group ID
		if (! is_numeric($group_id))
		{
			$g = $this->groupModel->find_by('name', $group);

			if (! $g)
			{
				$this->error = 'Unable to locate that Group.';
				return false;
			}

			$group_id = $g->id;
			unset($g);
		}

		if (! $this->groupModel->addUserToGroup($user_id, $group_id))
		{
			$this->error = $this->groupModel->error();
			return false;
		}

		return true;
	}

	//--------------------------------------------------------------------

	/**
	 * Removes a single user from a group.
	 *
	 * @param $user_id
	 * @param $group
	 *
	 * @return mixed
	 */
	public function removeUserFromGroup($user_id, $group)
	{
		$group_id = $group;

		// Group ID
		if (! is_numeric($group_id))
		{
			$g = $this->groupModel->find_by('name', $group);

			if (! $g)
			{
				$this->error = 'Unable to locate that Group.';
				return false;
			}

			$group_id = $g->id;
			unset($g);
		}

		if (! $this->groupModel->removeUserFromGroup($user_id, $group_id))
		{
			$this->error = $this->groupModel->error();
			return false;
		}

		return true;
	}

	//--------------------------------------------------------------------

	/**
	 * Adds a single permission to a single group.
	 *
	 * @param int|string $permission
	 * @param int|string $group
	 *
	 * @return mixed
	 */
	public function addPermissionToGroup($permission, $group)
	{
		$permission_id = $permission;
		$group_id = $group;

		// Permission ID
		if (! is_numeric($permission_id))
		{
			$p = $this->permissionModel->find_by('name', $permission);

			if (! $p)
			{
				$this->error = 'Unable to locate that Permission.';
				return false;
			}

			$permission_id = $p->id;
			unset($p);
		}

		// Group ID
		if (! is_numeric($group_id))
		{
			$g = $this->groupModel->find_by('name', $group);

			if (! $g)
			{
				$this->error = 'Unable to locate that Group.';
				return false;
			}

			$group_id = $g->id;
			unset($g);
		}

		// Remove it!
		if (! $this->groupModel->addPermissionToGroup($permission_id, $group_id))
		{
			$this->error = $this->groupModel->error();
			return false;
		}

		return true;
	}

	//--------------------------------------------------------------------

	/**
	 * Removes a single permission from a group.
	 *
	 * @param int|string $permission
	 * @param int|string $group
	 *
	 * @return mixed
	 */
	public function removePermissionFromGroup($permission, $group)
	{
		$permission_id = $permission;
		$group_id = $group;

		// Permission ID
		if (! is_numeric($permission_id))
		{
			$p = $this->permissionModel->find_by('name', $permission);

			if (! $p)
			{
				$this->error = 'Unable to locate that Permission.';
				return false;
			}

			$permission_id = $p->id;
			unset($p);
		}

		// Group ID
		if (! is_numeric($group_id))
		{
			$g = $this->groupModel->find_by('name', $group);

			if (! $g)
			{
				$this->error = 'Unable to locate that Group.';
				return false;
			}

			$group_id = $g->id;
			unset($g);
		}

		// Remove it!
		if (! $this->groupModel->removePermissionFromGroup($permission_id, $group_id))
		{
			$this->error = $this->groupModel->error();
			return false;
		}

		return true;
	}

	//--------------------------------------------------------------------

	//--------------------------------------------------------------------
	// Groups
	//--------------------------------------------------------------------

	/**
	 * Grabs the details about a single group.
	 *
	 * @param $group
	 *
	 * @return object|null
	 */
	public function group($group)
	{
		if (is_numeric($group))
		{
			return $this->groupModel->find( (int)$group );
		}

		return $this->groupModel->find_by('name', $group);
	}

	//--------------------------------------------------------------------

	/**
	 * Grabs an array of all groups.
	 *
	 * @return array of objects
	 */
	public function groups()
	{
		return $this->groupModel->find_all();
	}

	//--------------------------------------------------------------------

	/**
	 * @param $name
	 * @param string $description
	 *
	 * @return mixed
	 */
	public function createGroup($name, $description='')
	{
		$data = [
			'name'          => $name,
			'description'   => $description
		];

		$id = $this->groupModel->insert($data);

		if (is_numeric($id))
		{
			return (int)$id;
		}

		$this->error = $this->groupModel->error();

		return false;
	}

	//--------------------------------------------------------------------

	/**
	 * Deletes a single group.
	 *
	 * @param int $group_id
	 *
	 * @return bool
	 */
	public function deleteGroup($group_id)
	{
		if (! $this->groupModel->delete($group_id))
		{
			$this->error = $this->groupModel->error();
			return false;
		}

		return true;
	}

	//--------------------------------------------------------------------

	/**
	 * Updates a single group's information.
	 *
	 * @param $id
	 * @param $name
	 * @param string $description
	 *
	 * @return mixed
	 */
	public function updateGroup($id, $name, $description='')
	{
		$data = [
			'name'  => $name
		];

		if (! empty($description))
		{
			$data['description'] = $description;
		}

		if (! $this->groupModel->update( (int)$id, $data))
		{
			$this->error = $this->groupModel->error();
			return false;
		}

		return true;
	}

	//--------------------------------------------------------------------

	//--------------------------------------------------------------------
	// Permissions
	//--------------------------------------------------------------------

	/**
	 * Returns the details about a single permission.
	 *
	 * @param int|string $permission
	 *
	 * @return object|null
	 */
	public function permission($permission)
	{
		if (is_numeric($permission))
		{
			return $this->permissionModel->find( (int)$permission );
		}

		return $this->permissionModel->find_by('LOWER(name)', strtolower($permission) );
	}

	//--------------------------------------------------------------------

	/**
	 * Returns an array of all permissions in the system.
	 *
	 * @return mixed
	 */
	public function permissions()
	{
		return $this->permissionModel->find_all();
	}

	//--------------------------------------------------------------------

	/**
	 * Creates a single permission.
	 *
	 * @param $name
	 * @param string $description
	 *
	 * @return mixed
	 */
	public function createPermission($name, $description='')
	{
		$data = [
			'name'          => $name,
			'description'   => $description
		];

		$id = $this->permissionModel->insert($data);

		if (is_numeric($id))
		{
			return (int)$id;
		}

		$this->error = $this->permissionModel->error();

		return false;
	}

	//--------------------------------------------------------------------

	/**
	 * Deletes a single permission and removes that permission from all groups.
	 *
	 * @param $permission
	 *
	 * @return mixed
	 */
	public function deletePermission($permission_id)
	{
		if (! $this->permissionModel->delete($permission_id))
		{
			$this->error = $this->permissionModel->error();
			return false;
		}

		// Remove the permission from all groups
		$this->groupModel->removePermissionFromAllGroups($permission_id);

		return true;
	}

	//--------------------------------------------------------------------

	/**
	 * Updates the details for a single permission.
	 *
	 * @param int    $id
	 * @param string $name
	 * @param string $description
	 *
	 * @return bool
	 */
	public function updatePermission($id, $name, $description='')
	{
		$data = [
			'name'  => $name
		];

		if (! empty($description))
		{
			$data['description'] = $description;
		}

		if (! $this->permissionModel->update( (int)$id, $data))
		{
			$this->error = $this->permissionModel->error();
			return false;
		}

		return true;
	}

	//--------------------------------------------------------------------
}