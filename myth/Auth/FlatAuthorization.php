<?php namespace Myth\Auth;

/**
 * Sprint
 *
 * A set of power tools to enhance the CodeIgniter framework and provide consistent workflow.
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 *
 * @package     Sprint
 * @author      Lonnie Ezell
 * @copyright   Copyright 2014-2015, New Myth Media, LLC (http://newmythmedia.com)
 * @license     http://opensource.org/licenses/MIT  (MIT)
 * @link        http://sprintphp.com
 * @since       Version 1.0
 */

use Myth\Auth\Flat\FlatGroupsModel;
use Myth\Auth\Flat\FlatPermissionsModel;

class FlatAuthorization implements AuthorizeInterface {

	protected $groupModel;

	protected $permissionModel;

	protected $error = NULL;

	//--------------------------------------------------------------------

	public function __construct($groupModel = null, $permModel = null)
	{
		$this->groupModel      = ! empty($groupModel) ? $groupModel : new FlatGroupsModel();
		$this->permissionModel = ! empty($permModel)  ? $permModel  : new FlatPermissionsModel();

		get_instance()->load->language('auth/auth');
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
	public function inGroup( $groups, $user_id )
	{
		if ( ! is_array( $groups ) )
		{
			$groups = [ $groups ];
		}

		if (empty($user_id))
		{
			return null;
		}

		$user_groups = $this->groupModel->getGroupsForUser( (int) $user_id );

		if ( ! $user_groups )
		{
			return FALSE;
		}

		foreach ( $groups as $group )
		{
			if (is_numeric($group))
			{
				$ids = array_column($user_groups, 'id');
				if (in_array($group, $ids))
				{
					return true;
				}
			}

			else if (is_string($group))
			{
				$ids = array_column($user_groups, 'name');
				if (in_array($group, $ids))
				{
					return true;
				}
			}
		}

		return FALSE;
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
	public function hasPermission( $permission, $user_id )
	{
		if (empty($permission) || (! is_string($permission) && ! is_numeric($permission)) )
		{
			return null;
		}

		if (empty($user_id) || ! is_numeric($user_id))
		{
			return null;
		}

		// Get the Permission ID
		$permission_id = $this->getPermissionID($permission);

		if ( ! is_numeric( $permission_id ) )
		{
			return false;
		}

		return $this->permissionModel->doesUserHavePermission( (int)$user_id, (int)$permission_id );
	}

	//--------------------------------------------------------------------

	/**
	 * Makes a member a part of a group.
	 *
	 * @param $user_id
	 * @param $group // Either ID or name
	 *
	 * @return bool
	 */
	public function addUserToGroup( $user_id, $group )
	{
		if (empty($user_id) || ! is_numeric($user_id))
		{
			return null;
		}

		if (empty($group) || (! is_numeric($group) && ! is_string($group) ) )
		{
			return null;
		}

		if (! \Myth\Events::trigger('beforeAddUserToGroup', [$user_id, $group]))
		{
			return false;
		}

		$group_id = $this->getGroupID($group);

		// Group ID
		if ( ! is_numeric( $group_id ) )
		{
			return false;
		}

		if ( ! $this->groupModel->addUserToGroup( (int)$user_id, (int)$group_id ) )
		{
			$this->error = $this->groupModel->error();

			return FALSE;
		}

		\Myth\Events::trigger('didAddUserToGroup', [$user_id, $group]);

		return TRUE;
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
	public function removeUserFromGroup( $user_id, $group )
	{
		if (empty($user_id) || ! is_numeric($user_id))
		{
			return null;
		}

		if (empty($group) || (! is_numeric($group) && ! is_string($group) ) )
		{
			return null;
		}

		if (! \Myth\Events::trigger('beforeRemoveUserFromGroup', [$user_id, $group]))
		{
			return false;
		}

		$group_id = $this->getGroupID($group);

		// Group ID
		if ( ! is_numeric( $group_id ) )
		{
			return false;
		}

		if ( ! $this->groupModel->removeUserFromGroup( $user_id, $group_id ) )
		{
			$this->error = $this->groupModel->error();

			return FALSE;
		}

		\Myth\Events::trigger('didRemoveUserFromGroup', [$user_id, $group]);

		return TRUE;
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
	public function addPermissionToGroup( $permission, $group )
	{
		$permission_id = $this->getPermissionID($permission);
		$group_id      = $this->getGroupID($group);

		// Permission ID
		if ( ! is_numeric( $permission_id ) )
		{
			return false;
		}

		// Group ID
		if ( ! is_numeric( $group_id ) )
		{
			return false;
		}

		// Remove it!
		if ( ! $this->groupModel->addPermissionToGroup( $permission_id, $group_id ) )
		{
			$this->error = $this->groupModel->error();

			return FALSE;
		}

		return TRUE;
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
	public function removePermissionFromGroup( $permission, $group )
	{
		$permission_id = $this->getPermissionID($permission);
		$group_id      = $this->getGroupID($group);

		// Permission ID
		if ( ! is_numeric( $permission_id ) )
		{
			return false;
		}

		// Group ID
		if ( ! is_numeric( $group_id ) )
		{
			return false;
		}

		// Remove it!
		if ( ! $this->groupModel->removePermissionFromGroup( $permission_id, $group_id ) )
		{
			$this->error = $this->groupModel->error();

			return FALSE;
		}

		return TRUE;
	}

	//--------------------------------------------------------------------

	/**
	 * Assigns a single permission to a user, irregardless of permissions
	 * assigned by roles. This is saved to the user's meta information.
	 *
	 * @param int|string $permission
	 * @param int        $user_id
	 */
	public function addPermissionToUser( $permission, $user_id )
	{

	}

	//--------------------------------------------------------------------

	/**
	 * Removes a single permission from a user. Only applies to permissions
	 * that have been assigned with addPermissionToUser, not to permissions
	 * inherited based on groups they belong to.
	 *
	 * @param int/string $permission
	 * @param int        $user_id
	 */
	public function removePermissionFromUser( $permission, $user_id )
	{

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
	public function group( $group )
	{
		if ( is_numeric( $group ) )
		{
			return $this->groupModel->find( (int) $group );
		}

		return $this->groupModel->find_by( 'name', $group );
	}

	//--------------------------------------------------------------------

	/**
	 * Grabs an array of all groups.
	 *
	 * @return array of objects
	 */
	public function groups($with_counts = false)
	{
		$groups = $this->groupModel->find_all();

		if (! $groups) return $groups;

		if ($with_counts)
		{
			foreach ($groups as &$group)
			{
				$group->count = get_instance()->db->where('group_id', $group->id)->count_all_results('auth_groups_users');
			}
		}

		return $groups;
	}

	//--------------------------------------------------------------------

	/**
	 * @param $name
	 * @param string $description
	 *
	 * @return mixed
	 */
	public function createGroup( $name, $description = '' )
	{
		$data = [
			'name'        => $name,
			'description' => $description
		];

		$id = $this->groupModel->insert( $data );

		if ( is_numeric( $id ) )
		{
			return (int) $id;
		}

		$this->error = $this->groupModel->error();

		return FALSE;
	}

	//--------------------------------------------------------------------

	/**
	 * Deletes a single group.
	 *
	 * @param int $group_id
	 *
	 * @return bool
	 */
	public function deleteGroup( $group_id )
	{
		if ( ! $this->groupModel->delete( $group_id ) )
		{
			$this->error = $this->groupModel->error();

			return FALSE;
		}

		return TRUE;
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
	public function updateGroup( $id, $name, $description = '' )
	{
		$data = [
			'name' => $name
		];

		if ( ! empty( $description ) )
		{
			$data['description'] = $description;
		}

		if ( ! $this->groupModel->update( (int) $id, $data ) )
		{
			$this->error = $this->groupModel->error();

			return FALSE;
		}

		return TRUE;
	}

	//--------------------------------------------------------------------

	/**
	 * Given a group, will return the group ID. The group can be either
	 * the ID or the name of the group.
	 *
	 * @param int|string $group
	 *
	 * @return int|false
	 */
	protected function getGroupID( $group )
	{
		if (is_numeric($group))
		{
			return (int)$group;
		}

		$g = $this->groupModel->find_by( 'name', $group );

		if ( ! $g )
		{
			$this->error = lang('auth.group_not_found');

			return FALSE;
		}

		return (int)$g->id;
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
	public function permission( $permission )
	{
		if ( is_numeric( $permission ) )
		{
			return $this->permissionModel->find( (int) $permission );
		}

		return $this->permissionModel->find_by( 'LOWER(name)', strtolower( $permission ) );
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
	public function createPermission( $name, $description = '' )
	{
		$data = [
			'name'        => $name,
			'description' => $description
		];

		$id = $this->permissionModel->insert( $data );

		if ( is_numeric( $id ) )
		{
			return (int) $id;
		}

		$this->error = $this->permissionModel->error();

		return FALSE;
	}

	//--------------------------------------------------------------------

	/**
	 * Deletes a single permission and removes that permission from all groups.
	 *
	 * @param $permission
	 *
	 * @return mixed
	 */
	public function deletePermission( $permission_id )
	{
		if ( ! $this->permissionModel->delete( $permission_id ) )
		{
			$this->error = $this->permissionModel->error();

			return FALSE;
		}

		// Remove the permission from all groups
		$this->groupModel->removePermissionFromAllGroups( $permission_id );

		return TRUE;
	}

	//--------------------------------------------------------------------

	/**
	 * Updates the details for a single permission.
	 *
	 * @param int $id
	 * @param string $name
	 * @param string $description
	 *
	 * @return bool
	 */
	public function updatePermission( $id, $name, $description = '' )
	{
		$data = [
			'name' => $name
		];

		if ( ! empty( $description ) )
		{
			$data['description'] = $description;
		}

		if ( ! $this->permissionModel->update( (int) $id, $data ) )
		{
			$this->error = $this->permissionModel->error();

			return FALSE;
		}

		return TRUE;
	}

	//--------------------------------------------------------------------

	/**
	 * Verifies that a permission (either ID or the name) exists and returns
	 * the permission ID.
	 *
	 * @param int|string $permission
	 *
	 * @return int|null
	 */
	protected function getPermissionID( $permission )
	{
		// If it's a number, we're done here.
		if (is_numeric($permission))
		{
			return (int)$permission;
		}

		// Otherwise, pull it from the database.
		$p = $this->permissionModel->find_by( 'name', $permission );

		if ( ! $p )
		{
			$this->error = lang('auth.permission_not_found');

			return FALSE;
		}

		return (int)$p->id;
	}

	//--------------------------------------------------------------------

}