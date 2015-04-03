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

$descriptions = [
    'localize install' => ['install', 'Creates the migration file for localization table.'],
    'localize theme' => ['theme <theme> [lang]', 'Copies the themes view files into a localized subfolder'],
    'localize controller' => ['controller <name> [<lang>]', 'Copies the controllers view files into a localized subfolder'],
];

$long_description = <<<EOT
NAME
	localize - Tools for localizing your application

SYNOPSIS
	localize install

DESCRIPTION
    The localize generator contains a handful of tools meant to assist you in rapidly
    localizing/translating your application.

    INSTALL:
    The 'install' command will create the migration file for the translations table.

    Usage: sprint forge localize install

    THEME:
    Will copy all of the files of the specified theme into a subfolder with that language's
    name, ready for you to edit/translate.

    Usage: sprint forge localize theme {theme_name}

    CONTROLLER:
    Will copy all of the view files for a standard controller's folder into a subfolder within
    the views folder for the specified language. Note: this does NOT work with modules at
    this time.

    Usage: sprint forge localize controller {controller_name}


OPTIONS
	none
EOT;
