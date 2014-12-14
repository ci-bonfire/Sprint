<?php

use \Mockery as m;

class FlatAuthorizationTest extends CodeIgniterTestCase {

	public $auth;

	public $groupModel;
	public $permModel;

	//--------------------------------------------------------------------

	public function _before()
	{
		$this->groupModel = m::mock('\Myth\Auth\FlatGroupModel');
		$this->permModel  = m::mock('\Myth\Auth\FlatPermissionModel');

	    $this->auth = new \Myth\Auth\FlatAuthorization($this->groupModel, $this->permModel);
	}

	//--------------------------------------------------------------------

	public function _after()
	{

	}

	//--------------------------------------------------------------------

	//--------------------------------------------------------------------
	// InGroup
	//--------------------------------------------------------------------

	public function testInGroupReturnsFalseWithNoGroupsFound()
	{
		$this->groupModel->shouldReceive('getGroupsForUser')->once()->with(1);

	    $this->assertFalse( $this->auth->inGroup('admin', 1) );
	}

	//--------------------------------------------------------------------

	public function testInGroupReturnsFalseWithNullGroups()
	{
		$this->groupModel->shouldReceive('getGroupsForUser')->once()->with(1);

		$this->assertFalse( $this->auth->inGroup(null, 1) );
	}

	//--------------------------------------------------------------------

	public function testInGroupReturnsNullWithEmptyUser()
	{
		$this->groupModel->shouldReceive('getGroupsForUser')->once()->with(1);

		$this->assertNull( $this->auth->inGroup('admin', null) );
	}

	//--------------------------------------------------------------------

	public function testInGroupReturnsFalseIfNotInGroupString()
	{
		$groups = [
			[
				'id' => 1,
				'name' => 'admin',
				'description' => ''
			],
			[
				'id' => 2,
				'name' => 'moderators',
				'description' => ''
			],
		];

		$this->groupModel->shouldReceive('getGroupsForUser')->once()->with(1)->andReturn( $groups );

		$this->assertFalse( $this->auth->inGroup('dummy', 1) );
	}

	//--------------------------------------------------------------------

	public function testInGroupReturnsFalseIfNotInGroupId()
	{
		$groups = [
			[
				'id' => 1,
				'name' => 'admin',
				'description' => ''
			],
			[
				'id' => 2,
				'name' => 'moderators',
				'description' => ''
			],
		];

		$this->groupModel->shouldReceive('getGroupsForUser')->once()->with(1)->andReturn( $groups );

		$this->assertFalse( $this->auth->inGroup(3, 1) );
	}

	//--------------------------------------------------------------------

	public function testInGroupReturnsTrueIfInGroupString()
	{
		$groups = [
			[
				'id' => 1,
				'name' => 'admin',
				'description' => ''
			],
			[
				'id' => 2,
				'name' => 'moderators',
				'description' => ''
			],
		];

		$this->groupModel->shouldReceive('getGroupsForUser')->once()->with(1)->andReturn( $groups );

		$this->assertTrue( $this->auth->inGroup('admin', 1) );
	}

	//--------------------------------------------------------------------

	public function testInGroupReturnsTrueIfInGroupStringArray()
	{
		$groups = [
			[
				'id' => 1,
				'name' => 'admin',
				'description' => ''
			],
			[
				'id' => 2,
				'name' => 'moderators',
				'description' => ''
			],
		];

		$this->groupModel->shouldReceive('getGroupsForUser')->once()->with(1)->andReturn( $groups );

		$this->assertTrue( $this->auth->inGroup(['admin', 'moderators'], 1) );
	}

	//--------------------------------------------------------------------

	public function testInGroupReturnsTrueIfInGroupId()
	{
		$groups = [
			[
				'id' => 1,
				'name' => 'admin',
				'description' => ''
			],
			[
				'id' => 2,
				'name' => 'moderators',
				'description' => ''
			],
		];

		$this->groupModel->shouldReceive('getGroupsForUser')->once()->with(1)->andReturn( $groups );

		$this->assertTrue( $this->auth->inGroup(1, 1) );
	}

	//--------------------------------------------------------------------

	public function testInGroupReturnsTrueIfInGroupIdArray()
	{
		$groups = [
			[
				'id' => 1,
				'name' => 'admin',
				'description' => ''
			],
			[
				'id' => 2,
				'name' => 'moderators',
				'description' => ''
			],
		];

		$this->groupModel->shouldReceive('getGroupsForUser')->once()->with(1)->andReturn( $groups );

		$this->assertTrue( $this->auth->inGroup([1, 4], 1) );
	}

	//--------------------------------------------------------------------

	//--------------------------------------------------------------------
	// Has Permission
	//--------------------------------------------------------------------

	public function testHasPermissionReturnsFalseWithNoPermissionsFoundID()
	{
		$this->permModel->shouldReceive('doesUserHavePermission')->once()->with(1, 3)->andReturn(false);

		$this->assertFalse( $this->auth->hasPermission(3, 1) );
	}

	//--------------------------------------------------------------------

	public function testHasPermissionReturnsFalseWithNoPermissionsFoundString()
	{
		$this->permModel->shouldReceive('find_by')->once()->with('name', 'viewBlog')->andReturn(false);
		$this->permModel->shouldReceive('doesUserHavePermission')->once()->with(1, 3)->andReturn(false);

		$this->assertFalse( $this->auth->hasPermission('viewBlog', 1) );
	}

	//--------------------------------------------------------------------

	public function testHasPermissionReturnsTrueWithPermissionsFoundID()
	{
		$this->permModel->shouldReceive('doesUserHavePermission')->once()->with(1, 3)->andReturn(true);

		$this->assertTrue( $this->auth->hasPermission(3, 1) );
	}

	//--------------------------------------------------------------------

	public function testHasPermissionReturnsNullWithNoUserID()
	{
		$this->permModel->shouldReceive('doesUserHavePermission')->once()->with(1, 3)->andReturn(false);

		$this->assertNull( $this->auth->hasPermission(3, null) );
		$this->assertNull( $this->auth->hasPermission(3, 0) );
	}

	//--------------------------------------------------------------------

	public function testHasPermissionReturnsNullWithStringUserID()
	{
		$this->permModel->shouldReceive('doesUserHavePermission')->once()->with(1, 3)->andReturn(false);

		$this->assertNull( $this->auth->hasPermission(3, 'fred') );
	}

	//--------------------------------------------------------------------

	public function testHasPermissionReturnsNullWithObjectPermission()
	{
		$this->permModel->shouldReceive('doesUserHavePermission')->once()->with(1, 3)->andReturn(false);

		$this->assertNull( $this->auth->hasPermission( new stdClass(), 1) );
	}

	//--------------------------------------------------------------------

	public function testHasPermissionReturnsNullWithEmptyPermission()
	{
		$this->permModel->shouldReceive('doesUserHavePermission')->once()->with(1, 3)->andReturn(false);

		$this->assertNull( $this->auth->hasPermission( null, 1) );
		$this->assertNull( $this->auth->hasPermission( 0, 1) );
	}

	//--------------------------------------------------------------------

	//--------------------------------------------------------------------
	// Add User To Group
	//--------------------------------------------------------------------

	public function testAddUserToGroupReturnsNullWithEmptyUserId()
	{
//		$this->permModel->shouldReceive('doesUserHavePermission')->once()->with(1, 3)->andReturn(false);

		$this->assertNull( $this->auth->addUserToGroup( null, 1) );
	}

	//--------------------------------------------------------------------

	public function testAddUserToGroupReturnsNullWithStringUserId()
	{
		$this->assertNull( $this->auth->addUserToGroup( 'fred', 1) );
	}

	//--------------------------------------------------------------------

	public function testAddUserToGroupReturnsNullWithEmptyGroup()
	{
		$this->assertNull( $this->auth->addUserToGroup( 1, null) );
		$this->assertNull( $this->auth->addUserToGroup( 1, 0) );
		$this->assertNull( $this->auth->addUserToGroup( 1, '') );
	}

	//--------------------------------------------------------------------

	public function testAddUserToGroupReturnsNullWithObjectGroup()
	{
		$this->assertNull( $this->auth->addUserToGroup( 1, new stdClass()) );
	}

	//--------------------------------------------------------------------

	public function testAddUserToGroupReturnsFalseWhenCannotConvertStringGroupName()
	{
		$g = new stdClass();
		$g->id = 1;

		$this->groupModel->shouldReceive('find_by')->once()->with('name', 'wilma')->andReturn( false );
		$this->permModel->shouldReceive('doesUserHavePermission')->once()->with(1, 3)->andReturn(false);

		$this->assertFalse( $this->auth->addUserToGroup( 1, 'wilma') );
	}

	//--------------------------------------------------------------------

	public function testAddUserToGroupReturnsTrueString()
	{
		$g = new stdClass();
		$g->id = 1;

		$this->groupModel->shouldReceive('find_by')->once()->with('name', 'wilma')->andReturn( $g );
		$this->groupModel->shouldReceive('addUserToGroup')->once()->andReturn( true );
		$this->permModel->shouldReceive('doesUserHavePermission')->once()->with(1, 3)->andReturn(false);

		$this->assertTrue( $this->auth->addUserToGroup( 1, 'wilma') );
	}

	//--------------------------------------------------------------------

	//--------------------------------------------------------------------
	// Remove User From Group
	//--------------------------------------------------------------------

	public function testRemoveUserFromGroupReturnsNullWithEmptyUserId()
	{
		$this->assertNull( $this->auth->removeUserFromGroup( null, 1) );
	}

	//--------------------------------------------------------------------

	public function testRemoveUserFromGroupReturnsNullWithStringUserId()
	{
		$this->assertNull( $this->auth->removeUserFromGroup( 'fred', 1) );
	}

	//--------------------------------------------------------------------

	public function testRemoveUserFromGroupReturnsNullWithEmptyGroup()
	{
		$this->assertNull( $this->auth->removeUserFromGroup( 1, null) );
		$this->assertNull( $this->auth->removeUserFromGroup( 1, 0) );
		$this->assertNull( $this->auth->removeUserFromGroup( 1, '') );
	}

	//--------------------------------------------------------------------

	public function testRemoveUserFromGroupReturnsNullWithObjectGroup()
	{
		$this->assertNull( $this->auth->removeUserFromGroup( 1, new stdClass()) );
	}

	//--------------------------------------------------------------------
}