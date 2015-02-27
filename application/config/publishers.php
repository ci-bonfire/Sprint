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

//--------------------------------------------------------------------
// FOLDERS CONFIGURATION
//--------------------------------------------------------------------
// This array collects all publishing folders into a single location
// for ease of modifying. If you only have a single base folder
// that you want to publish all of your items into, you only
// need to have the single folder listed here as each publisher should
// attach their folder structure to this.
//
// The 'key' is a name you can reference the paths in. There must always
// be an 'html' key as it is used by the docs system to be able to
// build out static documentation.
//
	$config['publishing_folders'] = [
		'html' => 'html'
	];

//--------------------------------------------------------------------
// Publishers
//--------------------------------------------------------------------
// Publishers are simply event listeners that are only able to be
// called during the 'sprint_publish' CLI task.
//
// All methods listed below MUST be callable methods. The key of
// each array item is the fully namespaced class so we can instantiate it.
// The value is the method name to call.
//
	$config['publishers'] = [
		'\Myth\Docs\DocsPublisher' => 'publish'
	];