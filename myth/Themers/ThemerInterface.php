<?php namespace Myth\Themers;
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
 * ThemerInterface
 *
 * Provides a consistent manner to implement different theme engines
 * without having to make sweeping changes to your application.
 */
interface ThemerInterface
{
    /**
     * The main entryway into rendering a view. This is called from the
     * controller and is generally the last method called.
     *
     * @param string $layout If provided, will override the default layout.
     */
    public function render($layout = null);

    //--------------------------------------------------------------------

    /**
     * Used within the template layout file to render the current content.
     * This content is typically used to display the current view.
     */
    public function content();

    //--------------------------------------------------------------------

    /**
     * Sets the active theme to use. This theme should be
     * relative to one of the 'theme_paths' folders.
     *
     * @param $theme
     */
    public function setTheme($theme);

    //--------------------------------------------------------------------

    /**
     * Returns the current theme.
     *
     * @return mixed|string
     */
    public function theme();

    //--------------------------------------------------------------------

    /**
     * Sets the default theme to use.
     *
     * @param $theme
     * @return mixed
     */
    public function setDefaultTheme($theme);

    //--------------------------------------------------------------------

    /**
     * Sets the current view file to render.
     *
     * @param $file
     * @return mixed
     */
    public function setView($file);

    //--------------------------------------------------------------------

    /**
     * Returns the current view.
     *
     * @return mixed|string
     */
    public function view();

    //--------------------------------------------------------------------

    /**
     * Sets the current layout to be used. MUST be the name of one of
     * the layout files within the current theme. Case-sensitive.
     *
     * @param $file
     * @return mixed
     */
    public function setLayout($file);

    //--------------------------------------------------------------------

    /**
     * Returns the active layout.
     *
     * @return mixed
     */
    public function layout();

    //--------------------------------------------------------------------

    /**
     * Stores one or more pieces of data to be passed to the views when
     * they are rendered out.
     *
     * If both $key and $value are ! empty, then it will treat it as a
     * key/value pair. If $key is an array of key/value pairs, then $value
     * is ignored and each element of the array are made available to the
     * view as if it was a single $key/$value pair.
     *
     * @param string|array $key
     * @param mixed        $value
     */
    public function set($key, $value = null);

    //--------------------------------------------------------------------

    /**
     * Returns a value that has been previously set().
     *
     * @param $key
     * @return mixed
     */
    public function get($key);

    //--------------------------------------------------------------------

    /**
     * Determines whether or not the view should be parsed with the
     * CodeIgniter's parser.
     *
     * @param bool $parse
     * @return mixed
     */
    public function parseViews($parse = false);

    //--------------------------------------------------------------------

    /**
     * Theme paths allow you to have multiple locations for themes to be
     * stored. This might be used for separating themes for different sub-
     * applications, or a core theme and user-submitted themes.
     *
     * @param $alias The name the theme can be referenced by
     * @param $path  A new path where themes can be found.
     *
     * @return mixed
     */
    public function addThemePath($alias, $path);

    //--------------------------------------------------------------------

    /**
     * Removes a single theme path.
     *
     * @param $alias
     */
    public function removeThemePath($alias);
    //--------------------------------------------------------------------

    /**
     * Loads a view file. Useful to control caching. Intended for use
     * from within view files.
     *
     * You can specify that a view should belong to a theme by prefixing
     * the name of the theme and a colon to the view name. For example,
     * "admin:header" would try to display the "header.php" file within
     * the "admin" theme.
     *
     * @param string    $view
     * @param array     $data
     * @param int       $cache_time  The number of MINUTES to cache the output
     * @return mixed
     */
    public function display($view, $data = array(), $cache_time = 0);

    //--------------------------------------------------------------------

    /**
     * Sets the variant used when creating view names. These variants can
     * be anything, but by default are used to render specific templates
     * for desktop, tablet, and phone. The name of the variant is added
     * to the view name, joined by a "+" symbol.
     *
     * Example:
     *      $this->setVariant('phone');
     *      $this->display('header');
     *
     *      Tries to display "views/header+phone.php"
     *
     * @param $variant
     * @return mixed
     */
    public function setVariant($variant);

    //--------------------------------------------------------------------

    /**
     * Adds a new variant to the system.
     *
     * @param $name
     * @param $postfix
     * @return mixed
     */
    public function addVariant($name, $postfix);

    //--------------------------------------------------------------------

    /**
     * Removes a variant from the system.
     *
     * @param $name
     * @param $postfix
     * @return mixed
     */
    public function removeVariant($name);

    //--------------------------------------------------------------------

    /**
     * Runs a callback method and returns the contents to the view.
     *
     * @param $command
     * @param int $cache_minutes
     * @return mixed
     */
    public function call($command, $cache_minutes=0);

    //--------------------------------------------------------------------

}