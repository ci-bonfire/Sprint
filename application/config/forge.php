<?php

//--------------------------------------------------------------------
// Allowed Environments
//--------------------------------------------------------------------
// Before any _generators are run, the current environment will be
// tested to verify it's an allowed environment.
//
    $config['forge.allowed_environments'] = [
        'development',
        'travis'
    ];

//--------------------------------------------------------------------
// Themer to Use
//--------------------------------------------------------------------
// Define the themer to use when rendering our template files.
// This should include the fully namespaced classname.
//
    $config['forge.themer'] = '\Myth\Themers\ViewThemer';

//--------------------------------------------------------------------
// Generator Collections
//--------------------------------------------------------------------
// Defines the locations to look for generator and their templates. These will
// be searched in the order listed in the array. This allows you to
// customize just one or two files for this project or your company
// styles and still have all other templates from the Sprint group.
//
// The 'keys' are aliases that can be used to reference the view from.
//
    $config['forge.collections'] = [
        'sprint'    => MYTHPATH .'_generators/'
    ];