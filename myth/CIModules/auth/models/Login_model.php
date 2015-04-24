<?php
/**
 * Sprint
 *
 * A set of power tools to enhance the CodeIgniter framework and provide consistent workflow.
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 *
 * @package     Sprint
 * @author      Lonnie Ezell
 * @copyright   Copyright 2014-2015, New Myth Media, LLC (http://newmythmedia.com)
 * @license     http://opensource.org/licenses/MIT  (MIT)
 * @link        http://sprintphp.com
 * @since       Version 1.0
 */

/**
 * Class Login_model
 *
 * Provides methods for interfacing with ALL login-related information
 * for the Auth classes.
 *
 * By default it will use the 'auth_logins' for any CIDbModel-related calls,
 * but methods are included to work with 'auth_login_attempts' and 'auth_tokens' as well.
 */
class Login_model extends \Myth\Models\CIDbModel {

    protected $table_name = 'auth_logins';

    protected $set_created = false;
    protected $set_modified = false;

    //--------------------------------------------------------------------

    //--------------------------------------------------------------------
    // Login Attempts
    //--------------------------------------------------------------------

    /**
     * Records a login attempt. This is used to implement
     * throttling of user login attempts.
     *
     * @param $email
     * @return object
     */
    public function recordLoginAttempt($email)
    {
        $data = [
            'email' => $email,
            'datetime' => date('Y-m-d H:i:s')
        ];

        return $this->db->insert('auth_login_attempts', $data);
    }

    //--------------------------------------------------------------------

    /**
     * Purges all login attempt records from the database.
     *
     * @param $email
     * @return mixed
     */
    public function purgeLoginAttempts($email)
    {
        return $this->db->where('email', $email)
                        ->delete('auth_login_attempts');
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
    public function isBruteForced($email)
    {
        $start_time = date('Y-m-d H:i:s', time() - 60);

        $attempts = $this->db->where('email', $email)
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
     * @return int The time to add to any throttling.
     */
    public function distributedBruteForceTime()
    {
        if (! $time = $this->cache->get('dbrutetime'))
        {
            $time = 0;

            // Compute our daily average over the last 3 months.
            $avg_start_time = date('Y-m-d 00:00:00', strtotime('-3 months'));

            $query = $this->db->query("SELECT COUNT(*) / COUNT(DISTINCT DATE(`datetime`)) as num_rows FROM `auth_login_attempts` WHERE `datetime` >= ?", $avg_start_time);

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

            $attempts = $this->db->where('datetime >=', $today_start_time)
                                 ->count_all_results('auth_login_attempts');

            if ($attempts > (config_item('auth.dbrute_multiplier') * $average))
            {
                $time = config_item('auth.distributed_brute_add_time');
            }

            // Cache it for 3 hours.
            $this->cache->save('dbrutetime', $time, 60*60*3);
        }

        return $time;
    }

    //--------------------------------------------------------------------

    //--------------------------------------------------------------------
    // Logins
    //--------------------------------------------------------------------

    /**
     * Records a successful login. This stores in a table so that a
     * history can be pulled up later if needed for security analyses.
     *
     * @param $user
     */
    public function recordLogin($user)
    {
        $data = [
            'user_id'    => (int)$user['id'],
            'datetime'   => date('Y-m-d H:i:s'),
            'ip_address' => $this->input->ip_address()
        ];

        return $this->db->insert('auth_logins', $data);
    }

    //--------------------------------------------------------------------

    //--------------------------------------------------------------------
    // Tokens
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
    public function generateRememberToken($user)
    {
        $this->load->helper('string');

        return str_replace('@', '.', $user['email']) .'|' . random_string('alnum', 128);
    }

    //--------------------------------------------------------------------

    /**
     * Hashes the token for the Remember Me Functionality.
     *
     * @param $token
     * @return string
     */
    public function hashRememberToken($token)
    {
        return sha1(config_item('auth.salt') . $token);
    }

    //--------------------------------------------------------------------

    /**
     * Deletes a single token that matches the email/token combo.
     *
     * @param $email
     * @param $token
     * @return mixed
     */
    public function deleteRememberToken($email, $token)
    {
        $where = [
            'email' => $email,
            'hash'  => $this->hashRememberToken($token)
        ];

        $this->db->delete('auth_tokens', $where);
    }

    //--------------------------------------------------------------------

    /**
     * Removes all persistent login tokens (RememberMe) for a single user
     * across all devices they may have logged in with.
     *
     * @param $email
     * @return mixed
     */
    public function purgeRememberTokens($email)
    {
        return $this->db->delete('auth_tokens', ['email' => $email]);
    }

    //--------------------------------------------------------------------


    /**
     * Purges the 'auth_tokens' table of any records that are too old
     * to be of any use anymore. This equates to 1 week older than
     * the remember_length set in the config file.
     */
    public function purgeOldRememberTokens()
    {
        if (! config_item('auth.allow_remembering'))
        {
            return;
        }

        $date = time() - config_item('auth.remember_length') - 604800; // 1 week
        $date = date('Y-m-d 00:00:00', $date);

        $this->db->where('created <=', $date)
                 ->delete('auth_tokens');
    }

    //--------------------------------------------------------------------

    /**
     * Gets the timestamp of the last attempted login for this user.
     *
     * @param $email
     * @return int|null
     */
    public function lastLoginAttemptTime($email)
    {
        $query = $this->db->where('email', $email)
                          ->order_by('datetime', 'desc')
                          ->limit(1)
                          ->get('auth_login_attempts');

        if (! $query->num_rows())
        {
            return 0;
        }

        return strtotime($query->row()->datetime);
    }

    //--------------------------------------------------------------------

    /**
     * Returns the number of failed login attempts for a single email.
     *
     * @param $email
     * @return int
     */
    public function countLoginAttempts($email)
    {
        return $this->db->where('email', $email)
                        ->count_all_results('auth_login_attempts');
    }

    //--------------------------------------------------------------------

}
