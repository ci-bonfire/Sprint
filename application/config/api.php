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
if (!defined('BASEPATH')) exit('No direct script access allowed');

//--------------------------------------------------------------------
// Authentication Type
//--------------------------------------------------------------------
// Which type of authentication to use. Some, like HTTP Basic, still
// uses the standard users/passwords of any existing users, providing
// API access automatically to all users.
//
// Allowed values: 'basic', 'digest'
//
	$config['api.auth_type']    = 'basic';

//--------------------------------------------------------------------
// Realm
//--------------------------------------------------------------------
// The "realm" that the authentication is protecting. Typically only
// a single value per site. Only supported by basic and digest
// authentication.
//
	$config['api.realm'] = 'Unnamed';

//--------------------------------------------------------------------
// Credential Field
//--------------------------------------------------------------------
// This is the field in the users table that is considered to be
// the username of the client. This will typically be either
// 'username' or 'email'.
//
	$config['api.auth_field']   = 'email';

//--------------------------------------------------------------------
// IP Blacklists
//--------------------------------------------------------------------
// A comma-separated list of IP addresses that will not be allowed access
// to the API under any circumstances.
//
	$config['api.ip_blacklist_enabled']    = false;
	$config['api.ip_blacklist']            = '';

//--------------------------------------------------------------------
// IP Whitelists
//--------------------------------------------------------------------
// A comma-separated list of IP address that are the ONLY IP addresses
// allowed to access the site. Any other IP's will be rejected.
//
	$config['api.ip_whitelist_enabled'] = false;
	$config['aip.ip_whitelist']         = '';

//--------------------------------------------------------------------
// AJAX Requests Only?
//--------------------------------------------------------------------
// If TRUE, the API will be restricted to only allow calls through
// AJAX calls. All other traditional calls will be rejected.
//
	$config['api.ajax_only'] = false;