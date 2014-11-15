<?php

namespace Myth\Auth;

use Myth\Interfaces\AuthenticateInterface;
use Myth\Route;
use Myth\Events as Events;

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
 * todo: Set the error string for all error states here.
 *
 * @package Myth\Auth
 */
class LocalAuthentication implements AuthenticateInterface {

    protected $ci;

    protected $user = null;

    public $user_model = null;

    public $error = null;

    //--------------------------------------------------------------------

    public function __construct( $ci=null )
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
        $this->ci->load->model('auth/login_model');
        $this->ci->load->model('user_model', '', true);
        $this->ci->load->language('auth/auth');
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
     * @return bool|mixed
     */
    public function login($credentials, $remember=false)
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

        Events::trigger('didLogin', [$user]);

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
        $user = $this->user_model->as_array()
                                 ->where($credentials)
                                 ->first();

        if (! $user)
        {
            $this->error = lang('auth.invalid_user');
            return false;
        }

        // Now, try matching the passwords.
        $result =  password_verify($password, $user['password_hash']);

        if (! $result)
        {
            $this->error = lang('auth.invalid_password');
            return false;
        }

        // Is the user active?
        if (! $user['active'])
        {
            $this->error = lang('auth.inactive_account');
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
        $this->ci->load->helper('cookie');

        if (! Events::trigger('beforeLogout', [$this->user]))
        {
            return false;
        }

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
        $id = $this->ci->session->userdata('logged_in');

        if (! $id)
        {
            return false;
        }

        // If the user var hasn't been filled in, we need to fill it in,
        // since this method will typically be used as the only method
        // to determine whether a user is logged in or not.
        if (! $this->user)
        {
            $this->user = $this->user_model->as_array()->find_by('id', (int)$id);

            if (empty($this->user))
            {
                return false;
            }
        }

        return true;
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

        $this->ci->load->helper('cookie');

        $token = get_cookie('remember');

        // Attempt to match the token against our auth_tokens table.
        $query = $this->db->where('hash', $this->ci->login_model->hashRememberToken($token))
                          ->get('auth_tokens');

        if (! $query->num_rows())
        {
            return false;
        }

        // Grab the user
        $email = $query->row()->email;

        $user = $this->user_model->as_array()
                                 ->find_by('email', $email);

        $this->loginUser($user);

        // We only want our remember me tokens to be valid
        // for a single use.
        $this->refreshRememberCookie($user, $token);

        return true;
    }

    //--------------------------------------------------------------------

    /**
     * Registers a new user and handles activation method.
     *
     * @param $user_data
     * @return bool
     */
    public function registerUser($user_data)
    {
        // Anything special needed for Activation?
        $method = config_item('auth.activation_method');

        $user_data['active'] = $method == 'auto' ? 1 : 0;

        // If via email, we need to generate a hash
        $this->ci->load->helper('string');
        $token = random_string('alnum', 24);
        $user_data['activate_hash'] = hash('sha1', config_item('auth.salt') .$token);

        // Save the user
        if (! $id = $this->user_model->insert($user_data))
        {
            $this->error = $this->user_model->error();
            return false;
        }

        $data = [
            'user_id'   => $id,
            'email'     => $user_data['email'],
            'token'     => $token,
            'method'    => $method
        ];

        Events::trigger('didRegisterUser', [$data]);

        return true;
    }

    //--------------------------------------------------------------------

    /**
     * Used to verify the user values and activate a user so they can
     * visit the site.
     *
     * @param $data
     * @return bool
     */
    public function activateUser($data)
    {
        $post = [
            'email'         => $data['email'],
            'activate_hash' => hash('sha1', config_item('auth.salt') . $data['code'])
        ];

        $user = $this->user_model->where($post)
                                 ->first();

        if (! $user) {
            $this->error = $this->user_model->error() ? $this->user_model->error() : lang('auth.activate_no_user');

            return FALSE;
        }

        if (! $this->user_model->update($user->id, ['active' => 1, 'activate_hash' => null]) )
        {
            $this->error = $this->user_model->error();
            return false;
        }

        Events::trigger('didActivate', [(array)$user]);

        return true;
    }

    //--------------------------------------------------------------------

    /**
     * Used to allow manual activation of a user with a known ID.
     *
     * @param $id
     * @return bool
     */
    public function activateUserById($id)
    {
        if (! $this->user_model->update($id, ['active' => 1, 'activate_hash' => null]))
        {
            $this->error = $this->user_model->error();
            return false;
        }

        Events::trigger('didActivate', [$this->user_model->as_array()->find($id)]);

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
     * @param $email
     * @return mixed
     */
    public function isThrottled($email)
    {
        // Not throttling? Get outta here!
        if (! config_item('auth.allow_throttling'))
        {
            return false;
        }

        // Grab the amount of time to add if the system thinks we're
        // under a distributed brute force attack.
        $dbrute_time = $this->ci->login_model->distributedBruteForceTime();

        // If this user was found to possibly be under a brute
        // force attack, their account would have been banned
        // for 15 minutes.
        if ($time = $this->ci->session->userdata('bruteBan'))
        {
            // If the current time is less than the
            // the ban expiration, plus any distributed time
            // then the user can't login just yet.
            if ($time + $dbrute_time > time())
            {
                // The user is banned still...
                $this->error = lang('auth.bruteBan_notice');
                return ($time + $dbrute_time) - time();
            }

            // Still here? The the ban time is over...
            $this->ci->session->unset_userdata('bruteBan');
        }

        // Grab the time of last attempt and
        // determine if we're throttled by amount of time passed.
        $last_time = $this->ci->login_model->lastLoginAttemptTime($email);

        // Have any attempts been made?
        $attempts = $this->ci->login_model->countLoginAttempts($email);

        $allowed = config_item('auth.allowed_login_attempts');

        // We're not throttling if there are 0 attempts or
        // the number is less than or equal to the allowed free attempts
        if ($attempts === 0 || $attempts <= $allowed)
        {
            // Before we can say there's nothing up here,
            // we need to check dbrute time.
            $time_left = $last_time + $dbrute_time - time();

            if ($time_left > 0)
            {
                return $time_left;
            }

            return false;
        }

        // If the number of attempts is excessive (above 100) we need
        // to check the elapsed time of all of these attacks. If they are
        // less than 1 minute it's obvious this is a brute force attack,
        // so we'll set a session flag and block that user for 15 minutes.
        if ($attempts > 100 && $this->ci->login_model->isBruteForced($email))
        {
            $this->error = lang('auth.bruteBan_notice');

            $ban_time = 60 * 15;    // 15 minutes
            $this->ci->session->set_userdata('bruteBan', time() + $ban_time);
            return $ban_time;
        }

        // Get our allowed attempts out of the picture.
        $attempts = $attempts - $allowed;

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
     * Sends a password reset link email to the user associated with
     * the passed in $email.
     *
     * @param $email
     * @return mixed
     */
    public function remindUser($email)
    {
        // Is it a valid user?
        $user = $this->user_model->find_by('email', $email);

        if (! $user)
        {
            $this->error = lang('auth.invalid_email');
            return false;
        }

        // Generate/store our codes
        $this->ci->load->helper('string');
        $token = random_string('alnum', 24);
        $hash = hash('sha1', config_item('auth.salt') .$token);

        $result = $this->user_model->update($user->id, ['reset_hash' => $hash]);

        if (! $result)
        {
            $this->error = $this->user_model->error();
            return false;
        }

        Events::trigger('didRemindUser', [(array)$user, $token]);

        return true;
    }

    //--------------------------------------------------------------------

    /**
     * Validates the credentials provided and, if valid, resets the password.
     *
     * The $credentials array MUST contain a 'code' key with the string to
     * hash and check against the reset_hash.
     *
     * @param $credentials
     * @return mixed
     */
    public function resetPassword($credentials, $password, $passConfirm)
    {
        if (empty($credentials['code']))
        {
            $this->error = lang('auth.need_reset_code');
            return false;
        }

        // Generate a hash to match against the table.
        $credentials['reset_hash'] = hash('sha1', config_item('auth.salt') .$credentials['code']);
        unset($credentials['code']);

        // Is there a matching user?
        $user = $this->user_model->find_by($credentials);

        if (! $user)
        {
            $this->error = lang('auth.reset_no_user');
            return false;
        }

        // Update their password and reset their reset_hash
        $data = [
            'password'      => $password,
            'pass_confirm'  => $passConfirm,
            'reset_hash'    => null
        ];

        if (! $this->user_model->update($user->id, $data))
        {
            $this->error = $this->user_model->error();
            return false;
        }

        Events::trigger('didResetPassword', [(array)$user]);

        return true;
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
     * @param $allow_any_parent
     * @return mixed
     */
    public function useModel($model, $allow_any_parent=false)
    {
        if (! $allow_any_parent && get_parent_class($model) != 'Myth\Models\CIDbModel')
        {
            throw new \RuntimeException('Models passed into LocalAuthenticate MUST extend Myth\Models\CIDbModel');
        }

        $this->user_model =& $model;

        return $this;
    }

    //--------------------------------------------------------------------

    public function error()
    {
        if (validation_errors())
        {
            return validation_errors();
        }

        return $this->error;
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
        $this->ci->login_model->purgeLoginAttempts($email);

        // @todo record activity of login attempts purge.
        Events::trigger('didPurgeLoginAttempts', [$email]);
    }

    //--------------------------------------------------------------------

    /**
     * Purges all remember tokens for a single user. Effectively logs
     * a user out of all devices. Intended to allow users to log themselves
     * out of all devices as a security measure.
     *
     * @param $email
     */
    public function purgeRememberTokens($email)
    {
        $this->ci->login_model->purgeRememberTokens($email);

        // todo record activity of remember me purges.
        Events::trigger('didPurgeRememberTokens', [$email]);
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
            return false;
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
        $this->ci->load->helper('cookie');

        // If a token is passed in, we know we're removing the
        // old one.
        if (! empty($token))
        {
            $this->invalidateRememberCookie($user['email'], $token);
        }

        $new_token = $this->ci->login_model->generateRememberToken($user);

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
        $this->ci->login_model->deleteRememberToken($email, $token);

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
        $this->ci->session->set_userdata('logged_in', $user['id']);

        // Clear our login attempts
        $this->ci->login_model->purgeLoginAttempts($user['email']);

        // Record a new Login
        $this->ci->login_model->recordLogin($user);

        // We'll give a 20% chance to need to do a purge since we
        // don't need to purge THAT often, it's just a maintenance issue.
        // to keep the table from getting out of control.
        if (mt_rand(1, 100) < 20)
        {
            $this->ci->login_model->purgeOldRememberTokens();
        }
    }

    //--------------------------------------------------------------------


}