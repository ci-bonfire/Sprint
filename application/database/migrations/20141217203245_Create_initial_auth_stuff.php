<?php

/**
 * Migration: Create Initial Auth Stuff
 *
 * Created by: SprintPHP
 * Created on: 2014-12-17 20:32:45 pm
 */
class Migration_create_initial_auth_stuff extends CI_Migration {

    public function up ()
    {
		$authorize = new \Myth\Auth\FlatAuthorization();

	    $authorize->createGroup('admins', 'Site Administrators');
	    $authorize->createGroup('members', 'Site Members');

	    // Temporary user
	    // todo Remove this temporary information when an installer is in place
	    $this->load->model('auth/user_model');

	    $user = [
		    'email'         => 'lonnieje@gmail.com',
		    'username'      => 'admin',
		    'display_name'  => 'Lonnie',
		    'password'      => 'password',
		    'pass_confirm'  => 'password',
		    'active'        => 1,
		    'first_name'    => 'Lonnie',
		    'last_name'     => 'Ezell'
	    ];

	    $id = $this->user_model->skip_validation()->insert($user);

	    $authorize->addUserToGroup($id, 'admins');
    }

    //--------------------------------------------------------------------

    public function down ()
    {

    }

    //--------------------------------------------------------------------

}