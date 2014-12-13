<?php namespace Myth\Controllers;
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