<?php

//--------------------------------------------------------------------
// PERSISTENT LOGINS
//--------------------------------------------------------------------


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



//--------------------------------------------------------------------
// THROTTLING
//--------------------------------------------------------------------


//--------------------------------------------------------------------
// Throttling Rules
//--------------------------------------------------------------------
// Allow throttling of login and password resets?
// Throttling exponentially increases the time between allowed login
// attempts.
//
    $config['auth.allow_throttling'] = true;

//--------------------------------------------------------------------
// Max Throttling Time
//--------------------------------------------------------------------
// What is the max amount of time allowed between logins? This should
// be balanced with the protection of brute force or DOS attacks,
// against a user forgetting their password.
//
// This is the number of SECONDS max.
//
    $config['auth.max_throttle_time'] = 45;

//--------------------------------------------------------------------
// Start Throttling After
//--------------------------------------------------------------------
// Throttling will start after X number of attempts. Before this,
// the user can make attempts like normal.
//
    $config['auth.allowed_login_attempts'] = 5;

//--------------------------------------------------------------------
// Distributed Brute Force Checks
//--------------------------------------------------------------------
// The amount to multiply the average daily logins over the last 3 months
// by to determine if we might be under a distributed brute force attempt.
//
    $config['auth.dbrute_multiplier'] = 3;

//--------------------------------------------------------------------
// Additional Suspension Time for Distributed Brute Force Attempts
//--------------------------------------------------------------------
// This is the number of SECONDS that will be added to all login
// attempts that are being throttled.
//
    $config['auth.distributed_brute_add_time'] = 45;


//--------------------------------------------------------------------
// PASSWORDS
//--------------------------------------------------------------------

//--------------------------------------------------------------------
// Minimum Entropy (password strength)
//--------------------------------------------------------------------
// The minimum password strength that a password must meet to be
// considered a strong-enough value. While the formula is a bit complex
// you can use the following guidelines:
//      - 18 bits of entropy = minimum for ANY website.
//      - 25 bits of entropy = minimum for a general purpose web service used relatively widely (e.g. Hotmail).
//      - 30 bits of entropy = minimum for a web service with business critical applications (e.g. SAAS).
//      - 40 bits of entropy = minimum for a bank or other financial service.
//
    $config['auth.min_password_strength'] = 18;

//--------------------------------------------------------------------
// Use Dictionary
//--------------------------------------------------------------------
// Should the passwords be compared against an English-language
// dictionary to eliminate common words and their variations that would
// be pretty simply for a hacking attempt to guess?
//
    $config['auth.use_dictionary'] = true;
