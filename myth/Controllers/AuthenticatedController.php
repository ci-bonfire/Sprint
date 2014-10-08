<?php

namespace Myth\Controllers;

class AuthenticatedController extends BaseController {

    protected $auth = null;

    protected $restrict_to_roles = array();

    //--------------------------------------------------------------------

    /**
     * Responsible for loading an instance of our selected Auth library,
     * attempting autologin, and ensuring the current user has the correct
     * role.
     */
    public function __construct()
    {
        parent::__construct();

        $auth = config_item('active_auth_library');

        if (empty($auth)) {
            throw new \RuntimeException('No Authentication System chosen.');
        }

        $this->auth = new $auth( get_instance() );

        $this->attempt_autologin();

        $this->restrict();
    }

    //--------------------------------------------------------------------

    protected function attempt_autologin() {

    }

    //--------------------------------------------------------------------

    protected function restrict()
    {

    }

    //--------------------------------------------------------------------

}