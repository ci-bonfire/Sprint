<?php

if (! function_exists('isStrongPassword'))
{
    /**
     * Works with Myth\Auth\Password to enforce a strong password.
     * Uses settings from the auth config file.
     *
     * @param $password
     */
    function isStrongPassword($password)
    {
        $min_strength = config_item('auth.min_password_strength');
        $use_dict = config_item('auth.use_dictionary');

        if (! \Myth\Auth\Password::isStrongPassword($password, $min_strength, $use_dict))
        {
            get_instance()->form_validation->set_message('password', '{Field} must be a stronger password.');
            return false;
        }

        return true;
    }
}