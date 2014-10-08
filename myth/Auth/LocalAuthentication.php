<?php

namespace Myth\Auth;

use Myth\Interfaces\AuthenticateInterface;

/**
 * Class LocalAuthentication
 *
 * Provides most of the Authentication that web applications would need,
 * at least as far as local authentication goes. It does NOT provide
 * social authentication through third-party applications.
 *
 * The system attempts to incorporate as many of the ideas and best practices
 * set forth in the following documents:
 *
 *  - http://stackoverflow.com/questions/549/the-definitive-guide-to-form-based-website-authentication
 *  - https://www.owasp.org/index.php/Guide_to_Authentication
 *
 * @package Myth\Auth
 */
class LocalAuthentication implements AuthenticateInterface {

    protected $ci;

    protected $user = null;

    public $user_model = null;

    public $error = null;

    //--------------------------------------------------------------------

    public function __construct( &$ci=null )
    {

        if ($ci)
        {
            $this->ci= $ci;
        }
        else
        {
            $this->ci =& get_instance();
        }

        // Get our compatibility password file loaded up.
        if (! function_exists('password_hash'))
        {
            require_once dirname(__FILE__) .'password.php';
        }

        if (empty($this->ci->session))
        {
            $this->ci->load->driver('session');
            $this->ci->session->select_driver( config_item('sess_driver') );
        }

        $this->ci->config->load('auth');

        $this->ci->load->helper('cookie');
    }

    //--------------------------------------------------------------------

    /**
     * Attempt to log a user into the system.
     *
     * $credentials is an array of key/value pairs needed to log the user in.
     * This is often email/password, or username/password.
     *
     * @param array $credentials
     * @param bool $remember
     * @param null $redirect
     * @return bool|mixed
     */
    public function login($credentials, $remember=false, $redirect=null)
    {
        $user = $this->validate($credentials, true);

        if (! $user)
        {
            $this->user = null;
            return $user;
        }

        $this->loginUser($user);

        if ($remember)
        {
            $this->rememberUser($user);
        }

        return true;
    }

    //--------------------------------------------------------------------

    /**
     * Validates user login information without logging them in.
     *
     * $credentials is an array of key/value pairs needed to log the user in.
     * This is often email/password, or username/password.
     *
     * @param array $credentials
     * @param bool  $return_user
     * @return mixed
     */
    public function validate($credentials, $return_user=false)
    {
        // Can't validate without a password.
        if (empty($credentials['password']) || count($credentials) < 2)
        {
            return null;
        }
        $password = $credentials['password'];
        unset($credentials['password']);

        // Can we find a user with those credentials?
        $user = $this->user_model->asArray()
                                 ->where($credentials)
                                 ->first();

        if (! $user)
        {
            return false;
        }

        // Now, try matching the passwords.
        $result =  password_verify($password, $user['password_hash']);

        if (! $result)
        {
            return false;
        }

        return $return_user ? $user : true;
    }

    //--------------------------------------------------------------------

    /**
     * Logs a user out and removes all session information.
     *
     * @return mixed
     */
    public function logout()
    {
        // Destroy the session
        $this->ci->session->sess_destroy();

        // Take care of any rememberme functionality.
        if (config_item('auth.allow_remembering')) {
            $token = get_cookie('remember');

            $this->invalidateRememberCookie($this->user['email'], $token);
        }
    }

    //--------------------------------------------------------------------

    /**
     * Checks whether a user is logged in or not.
     *
     * @return bool
     */
    public function isLoggedIn()
    {
        if ($this->ci->session->userdata('logged_in'))
        {
            return true;
        }

        return false;
    }

    //--------------------------------------------------------------------

    /**
     * Attempts to log a user in based on the "remember me" cookie.
     *
     * @return bool
     */
    public function viaRemember()
    {
        if (! config_item('auth.allow_remembering'))
        {
            return false;
        }

        $token = get_cookie('remember');

        // Attempt to match the token against our auth_tokens table.
        $query = $this->db->where('hash', $this->hashRememberToken($token))
                          ->get('auth_tokens');

        if (! $query->num_rows())
        {
            return false;
        }

        // Grab the user
        $email = $query->row()->email;

        $user = $this->user_model->asArray()
                                 ->find_by('email', $email);

        $this->loginUser($user);

        // We only want our remember me tokens to be valid
        // for a single use.
        $this->refreshRememberCookie($user, $token);

        return true;
    }

    //--------------------------------------------------------------------

    /**
     * Grabs the current user object. Returns NULL if nothing found.
     *
     * @return object|null
     */
    public function user()
    {
        return $this->user;
    }

    //--------------------------------------------------------------------

    /**
     * A convenience method to grab the current user's ID.
     *
     * @return int|null
     */
    public function id()
    {
        if (! is_array($this->user) || empty($this->user['id']))
        {
            return null;
        }

        return (int)$this->user['id'];
    }

    //--------------------------------------------------------------------

    /**
     * Checks to see if the user is currently being throttled.
     *
     *  -If they are NOT, will return FALSE.
     *  - If they ARE, will return the number of seconds until they can try again.
     *
     * @param $userId
     * @return mixed
     */
    public function isThrottled($email)
    {
        // Not throttling? Get outta here!
        if (! config_item('auth.allow_throttling'))
        {
            return false;
        }

        // If this user was found to possibly be under a brute
        // force attack, their account would have been banned
        // for 15 minutes.
        if ($time = $this->ci->session->userdata('bruteBan'))
        {
            if ($time > time())
            {
                // The user is banned still...
                $this->error = "Your account has had excessive login attempts. To protect the account you must wait 15 minutes before another attempt can be made.";
                return true;
            }

            // Still here? The the ban time is over...
            $this->ci->session->unset_userdata('bruteBan');
        }

        // Have any attempts been made?
        $attempts = $this->ci->db->where('email', $email)
                                 ->count_all_results();

        $allowed = config_item('auth.allowed_login_attempts');

        // We're not throttling if there are 0 attempts or
        // the number is less than or equal to the allowed free attempts
        if ($attempts === 0 || $attempts <= $attempts)
        {
            return false;
        }

        // If the number of attempts is excessive (above 100) we need
        // to check the elapsed time of all of these attacks. If they are
        // less than 1 minute it's obvious this is a brute force attack,
        // so we'll set a session flag and block that user for 15 minutes.
        if ($attempts > 100 && $this->isBruteForced($email))
        {
            $this->error = "Your account has had excessive login attempts. To protect the account you must wait 15 minutes before another attempt can be made.";

            $ban_time = 60 * 15;    // 15 minutes
            $this->ci->session->set_userdata('bruteBan', time() + $ban_time);
            return $ban_time;
        }

        // Check the time of last attempt and
        // determine if we're throttled by amount of time passed.
        $query = $this->ci->db->where('email', $email)
                              ->order_by('datetime', 'desc')
                              ->limit(1)
                              ->get('auth_login_attempts');

        // Get a timestamp of the last attempt
        $last_time = strtotime($query->row()->datetime);

        // Get our allowed attempts out of the picture.
        $attempts = $attempts - $allowed;

        $dbrute_time = $this->distributedBruteForceTime();

        $max_time = config_item('auth.max_throttle_time');

        $add_time = pow(2, $attempts);

        if ($add_time > $max_time)
        {
            $add_time = $max_time;
        }

        $next_time = $last_time + $add_time + $dbrute_time;

        $current = time();

        // We are NOT throttled if we are already
        // past the allowed time.
        if ($current > $next_time)
        {
            return false;
        }

        return $next_time - $current;
    }

    //--------------------------------------------------------------------

    /**
     * Sends a password reminder email to the user associated with
     * the passed in $email.
     *
     * @param $email
     * @return mixed
     */
    public function remindUser($email)
    {

    }

    //--------------------------------------------------------------------

    /**
     * Validates the credentials provided and, if valid, resets the password.
     *
     * @param $credentials
     * @return mixed
     */
    public function resetPassword($credentials)
    {

    }

    //--------------------------------------------------------------------

    /**
     * Provides a way for implementations to allow new statuses to be set
     * on the user. The details will vary based upon implementation, but
     * will often allow for banning or suspending users.
     *
     * @param $newStatus
     * @param null $message
     * @return mixed
     */
    public function changeStatus($newStatus, $message=null)
    {

    }

    //--------------------------------------------------------------------

    /**
     * Allows the consuming application to pass in a reference to the
     * model that should be used.
     *
     * The model MUST extend Myth\Models\CIDbModel.
     *
     * @param $model
     * @return mixed
     */
    public function useModel($model)
    {
        if (get_parent_class($model) != 'CIDbModel')
        {
            throw new \RuntimeException('Models passed into LocalAuthenticate MUST extend Myth\Models\CIDbModel');
        }

        $this->user_model =& $model;

        return $this;
    }

    //--------------------------------------------------------------------

    //--------------------------------------------------------------------
    // Protected Methods
    //--------------------------------------------------------------------

    protected function rememberUser($user)
    {
        if (! config_item('auth.allow_remembering'))
        {
            log_message('debug', 'Auth library set to refuse "Remember Me" functionality.');
        }

        $this->refreshRememberCookie($user);
    }

    //--------------------------------------------------------------------

    /**
     * Invalidates the current rememberme cookie/database entry, creates
     * a new one, stores it and returns the new value.
     *
     * @param array $user
     * @param null $token
     * @return mixed
     */
    protected function refreshRememberCookie($user, $token=null)
    {
        // If a token is passed in, we know we're removing the
        // old one.
        if (! empty($token))
        {
            $this->invalidateRememberCookie($user['email'], $token);
        }

        $new_token = $this->generateRememberToken($user);

        // Save the token to the database.
        $data = [
            'email' => $user['email'],
            'hash' => sha1(config_item('auth.salt') . $new_token),
            'created' => date('Y-m-d H:i:s')
        ];

        $this->ci->db->insert('auth_tokens', $data);

        // Create the cookie
        set_cookie(
            'remember',                             // Cookie Name
            $new_token,                             // Value
            config_item('auth.remember_length'),    // # Seconds until it expires
            config_item('cookie_domain'),
            config_item('cookie_path'),
            config_item('cookie_prefix'),
            false,                                  // Only send over HTTPS?
            true                                    // Hide from Javascript?
        );

        return $new_token;
    }

    //--------------------------------------------------------------------

    /**
     * Deletes any current remember me cookies and database entries.
     *
     * @param $email
     * @param $token
     * @return string The new token (not the hash).
     */
    protected function invalidateRememberCookie($email, $token)
    {
        // Remove from the database
        $where = [
            'email' => $email,
            'hash'  => $this->hashRememberToken($token)
        ];

        $this->ci->db->delete('auth_tokens', $where);

        // Remove the cookie
        delete_cookie(
            'remember',
            config_item('cookie_domain'),
            config_item('cookie_path'),
            config_item('cookie_prefix')
        );
    }

    //--------------------------------------------------------------------

    /**
     * Generates a new token for the rememberme cookie.
     *
     * The token is based on the user's email address (since everyone will have one)
     * with the '@' turned to a '.', followed by a pipe (|) and a random 128-character
     * string with letters and numbers.
     *
     * @param $user
     * @return mixed
     */
    protected function generateRememberToken($user)
    {
        $this->ci->load->helper('string');

        return str_replace('@', '.', $user['email']) .'|' . random_string('alnum', 128);
    }

    //--------------------------------------------------------------------

    /**
     * Hases the token for the Remember Me Functionality.
     *
     * @param $token
     * @return string
     */
    protected function hashRememberToken($token)
    {
        return sha1(config_item('auth.salt') . $token);
    }

    //--------------------------------------------------------------------

    /**
     * Purges the 'auth_tokens' table of any records that are too old
     * to be of any use anymore. This equates to 1 week older than
     * the remember_length set in the config file.
     */
    protected function purgeOldRememberTokens()
    {
        if (! config_item('auth.allow_remembering'))
        {
            return;
        }

        $date = time() - config_item('auth.remember_length') - strtotime('-1 week');
        $date = date('Y-m-d 00:00:00', $date);

        $this->ci->db->where('created <=', $date)
                     ->delete('auth_tokens');
    }

    //--------------------------------------------------------------------

    /**
     * Handles the nitty gritty of actually logging our user into the system.
     * Does NOT perform the authentication, just sets the system up so that
     * it knows we're here.
     *
     * @param $user
     */
    protected function loginUser($user)
    {
        // Save the user for later access
        $this->user = $user;

        // Let the session know that we're logged in.
        $this->ci->session->set_userdata('logged_in', true);

        // Clear our login attempts
        $this->purgeLoginAttempts($user['email']);

        // Record a new Login
        $this->recordLogin($user);

        // We'll give a 20% chance to need to do a purge since we
        // don't need to purge THAT often, it's just a maintenance issue.
        // to keep the table from getting out of control.
        if (mt_rand(1, 100) < 20)
        {
            $this->purgeOldRememberTokens();
        }
    }

    //--------------------------------------------------------------------

    //--------------------------------------------------------------------
    // Login Records
    //--------------------------------------------------------------------

    /**
     * Purges all login attempt records from the database.
     *
     * @param $email
     */
    public function purgeLoginAttempts($email)
    {
        $this->ci->db->where('email', $email)
                     ->delete('auth_login_attempts');
    }

    //--------------------------------------------------------------------

    /**
     * Records a login attempt. This is used to implement
     * throttling of user login attempts.
     *
     * @param $email
     */
    protected function recordLoginAttempt($email)
    {
        $data = [
            'email' => $email,
            'datetime' => date('Y-m-d H:i:s')
        ];

        $this->ci->db->insert('auth_login_attempts', $data);
    }

    //--------------------------------------------------------------------

    /**
     * Checks to see if how many login attempts have been attempted in the
     * last 60 seconds. If over 100, it is considered to be under a
     * brute force attempt.
     *
     * @param $email
     * @return bool
     */
    protected function isBruteForced($email)
    {
        $start_time = date('Y-m-d H:i:s', time() - 60);

        $attempts = $this->ci->db->where('email', $email)
                                 ->where('datetime >=', $start_time)
                                 ->count_all_results('auth_login_attempts');

        return $attempts > 100;
    }

    //--------------------------------------------------------------------

    /**
     * Attempts to determine if the system is under a distributed
     * brute force attack.
     *
     * To determine if we are in under a brute force attack, we first
     * find the average number of bad logins per day that never converted to
     * successful logins over the last 3 months. Then we compare
     * that to the the average number of logins in the past 24 hours.
     *
     * If the number of attempts in the last 24 hours is more than X (see config)
     * times the average, then institute additional throttling.
     *
     * @return int  The time to add to any throttling.
     */
    protected function distributedBruteForceTime()
    {
        if (! $time = $this->ci->cache->get('dbrutetime'))
        {
            $time = 0;

            // Compute our daily average over the last 3 months.
            $avg_start_time = date('Y-m-d 00:00:00', strtotime('-3 months'));

            $query = $this->ci->db->query("SELECT COUNT(*) / COUNT(DISTINCT DATE(`datetime`)) as num_rows FROM `auth_login_attempts` WHERE `datetime` >= ?", $avg_start_time);
            if (! $query->num_rows())
            {
                $average = 0;
            }
            else
            {
                $average = $query->row()->num_rows;
            }

            // Get the total in the last 24 hours
            $today_start_time = date('Y-m-d H:i:s', strtotime('-24 hours'));

            $attempts = $this->ci->db->where('datetime >=', $today_start_time)
                                     ->count_all_results('auth_login_attempts');

            if ($attempts > (config_item('auth.dbrute_multiplier') * $average) ) {
                $time = config_item('auth.distributed_brute_add_time');
            }

            // Cache it for 3 hours.
            $this->ci->cache->set('dbrutetime', $time, 60*60*3);
        }

        return $time;
    }

    //--------------------------------------------------------------------



    /**
     * Records a successful login. This stores in a table so that a
     * history can be pulled up later if needed for security analyses.
     *
     * @param $user
     */
    protected function recordLogin($user)
    {
        $data = [
            'user_id'   => (int)$user['id'],
            'datetime'  => date('Y-m-d H:i:s')
        ];

        $this->ci->db->insert('auth_logins', $data);
    }

    //--------------------------------------------------------------------
}