<?php

/**
 * Class FlatAuthorizationSeeder
 *
 * Inserts sample groups and permissions to work
 * with.
 */
class FlatAuthorizationSeeder extends Seeder {

	public function run()
	{
	    $flat = new \Myth\Auth\FlatAuthorization();

		$flat->createPermission('viewPosts', 'View the blog posts.');
		$flat->createPermission('managePosts', 'Manage the blog posts.');
		$flat->createPermission('viewUsers', 'View the users.');
		$flat->createPermission('manageUsers', 'Edit the users.');

		$flat->createGroup('admin', 'Site Administrators');
		$flat->createGroup('moderators', 'Site Moderators');
		$flat->createGroup('users', 'Site Users');

		$flat->addPermissionToGroup('viewPosts', 'admin');
		$flat->addPermissionToGroup('managePosts', 'admin');
		$flat->addPermissionToGroup('viewUsers', 'admin');
		$flat->addPermissionToGroup('manageUsers', 'admin');

		$flat->addPermissionToGroup('viewPosts', 'moderators');
		$flat->addPermissionToGroup('viewUsers', 'moderators');
	}

	//--------------------------------------------------------------------

}