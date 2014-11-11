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

        $strong = \Myth\Auth\Password::isStrongPassword($password, $min_strength, $use_dict);

        if (! $strong)
        {
            if (isset(get_instance()->form_validation)) {
                get_instance()->form_validation->set_message('isStrongPassword', lang('auth.pass_not_strong'));
            }
            return false;
        }

        return true;
    }
}
