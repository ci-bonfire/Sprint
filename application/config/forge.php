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