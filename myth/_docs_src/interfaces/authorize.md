# AuthorizationInterface
This interface defines the methods required when implementing a new type of Authorization. Depending on the type of authorization provided, some methods might not make sense. In those cases, that method should return `true`, so as not to stop script execution checks.

## Basic Actions

### inGroup()
Checks to see if a user is in a group. Groups can be either a string, with the name of the group, an INT with the ID of the group, or an array of strings/ids that the user must belong to ONE of. (It's an OR check not an AND check).

The first parameter is the string/array of groups to check if the user is in. The second parameter is the ID of the user to check.

	if (! $auth->inGroup( ['admin', 'moderators'], 124 ) ) {
		redirect('busted');
	}

Should return `null` if invalid group or user id.

Should return `false` if the user in not part of that group, or `true` if they are.

### hasPermission()
Similar to `inGroup`, this check's to see if a user has a certain permission. However, this permission could be across any of the groups they are part of or even a private permission assigned just to the user, not a group. The first parameter is the permission to check. This can be either the ID or the name of the permission. The second parameter is the ID of the user.

	if (! $auth->hasPermission( 'overthrow.emperor', 124) ) {
		redirect('busted');
	}

Should return `null` if an invalid permission, otherwise return `true` or `false` to answer the question.

### addUserToGroup()
Makes a user part of a group. The first parameter is the ID of the user. The second is the group to add the user to. This should accept either a primary key of the group or a string that is the group name. 

	$auth->addUserToGroup(124, 'overlords');

Should trigger the `beforeAddUserToGroup` and `afterAddUserToGroup` events.

Should return `null` if invalid user_id or invalid group. Should return `false` and set the error if unable to add the user to the group.

### removeUserFromGroup()
Removes a user from a single group. The first parameter is the ID of the user. The second paramter is the group. The group can be either the ID or the name.

	$this->removeUserFromGroup(124, 'rebels');

Should trigger the `beforeRemoveUserFromGroup` and `afterRemoveUserFromGroup` events.

Should return null for invalid user or groups, otherwise `true` or `false` based on the status of the removal.

### addPermissionToGroup()
Supports adding a single permission to a group. The first parameter is the permission, which can be either the ID or the name. The second parameter is the group, which can be either its ID or it's name.

	$this->addPermissionToGroup('overthrow.emperor', 'rebels');

Should return `null` for invalid permission or group, else return `true` or `false` based on the success of the action.

### removePermissionFromGroup()
Removes a single permission from a single group. The first parameter is the permission, which can be either the ID or the name. The second parameter is the group, which can be either its ID or it's name.

	$this->removePermissionFromGroup('overthrow.emperor', 'rebels');

Should return `null` for invalid permission or group, else return `true` or `false` based on the success of the action.

### addPermissionToUser()
Allows assigning a "private permission" that is set at the user level, not the group level. The first parameter is the permission, which can be either the ID or the name. The second parameter is the ID of the user.

Should trigger the `beforeAddPermissionToUser` and `afterAddPermissionToUser` events.

Should return `null` for invalid permission or user, otherwise return `true` or `false` based on the success of the action.

### removePermissionFromUser()
Removes a single private permission from a single user. The first parameter is the permission, which can be either the ID or the name. The second parameter is the ID of the user.

Should trigger the `beforeRemovePermissionFromUser` and `afterRemovePermissionFromUser` events.

Should return `null` for invalid permission or user, otherwise return `true` or `false` based on the success of the action.

### doesUserHavePermission()
Checks to see if a user has a private permission assigned. This is called automatically by the `hasPermission` method so it is not typically called standalone, but should be available in case it is needed. The first parameter is the ID of the user. The second parameter is the permission, which can be either the ID or the name.


## Groups

### group()
Returns the details of a single group. The only parameter is the group which can be either the ID or the name. 

	$group = $auth->group( 'rebels' );

### groups()
Like above, but returns details on all groups in the system.

### createGroup()
Responsible for saving a single new group. The first parameter is the name of the group. The second parameter is the description (max 255 characters) for the group.

Should return the group's ID on success, and or false on fail.

### deleteGroup()
Deletes a single group. The only parameter is the ID of the group. This method is NOT allowed to use string values to represent the group.

Should return either `true` or `false` on success/fail.

### updateGroup()
Saves changes to an existing group. The first parameter is the ID of the group. The second parameter is the name of the group. The third parameter is the description (max 255 characters).

Should return `true` or `false`.



## Permissions

### permission()
Returns the details of a single permission. The only parameter is the permission which can be either the ID or the name. 

### groups()
Like above, but returns details on all permissions in the system.

### createPermission()
Responsible for saving a single new permission. The first parameter is the name of the permission. The second parameter is the description (max 255 characters) for the permission.

Should return the permission's ID on success, and or false on fail.

### deletePermission()
Deletes a single permission. The only parameter is the ID of the permission. This method is NOT allowed to use string values to represent the permission.

Should return either `true` or `false` on success/fail.

### updatePermission()
Saves changes to an existing permission. The first parameter is the ID of the permission. The second parameter is the name of the permission. The third parameter is the description (max 255 characters).

Should return `true` or `false`.



## Utility Methods

### error()
Should return a string with any/all errors that have been encountered during the script execution.

