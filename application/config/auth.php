<?php

//--------------------------------------------------------------------
// Allow Persistent Login Cookies (Remember me)
//--------------------------------------------------------------------
// While every attempt has been made to create a very strong protection
// with the remember me system, there are some cases (like when you
// need extreme protection, like dealing with users financials) that
// you might not want the extra risk associated with this cookie-based
// solution.
//
    $config['auth.allow_remembering'] = true;

//--------------------------------------------------------------------
// Remember Me Salt
//--------------------------------------------------------------------
// This string is used to salt the hashes when storing RememberMe
// tokens in the database.
// If you are using Remember Me functionality, you should consider
// changing this value to be unique to your site.
//
    $config['auth.salt'] = 'ASimpleSalt';

//--------------------------------------------------------------------
// Remember Length
//--------------------------------------------------------------------
// The amount of time, in seconds, that you want a login to last for.
// Defaults to 2 weeks.
//
// Common values are:
//      1 hour   - 3600
//      1 day    - 86400
//      1 week   - 604800
//      2 weeks  - 1209600
//      3 weeks  - 1814400
//      1 month  - 2419200
//      2 months - 4838400
//      3 months - 7257600
//      6 months - 14515200
//      1 year   - 29030400
//
    $config['auth.remember_length'] = 1209600;
