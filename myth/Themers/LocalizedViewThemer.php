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

use Myth\Modules as Modules;
use Myth\Themers\ThemerInterface;

class ViewThemer implements ThemerInterface
{

    protected $theme = '';

    protected $default_theme = null;

	protected $active_theme = null;

    protected $layout = 'index';

    protected $view = '';

    protected $vars = [];

    protected $folders = [];

    protected $variants = [];

    protected $current_variant = '';

	protected $parse_views = false;

    protected $ci;

    //--------------------------------------------------------------------

    public function __construct($ci)
    {
        $this->ci = $ci;

	    $this->parse_views = config_item('theme.parse_views');
    }

    //--------------------------------------------------------------------

    /**
     * The main entryway into rendering a view. This is called from the
     * controller and is generally the last method called.
     *
     * @param string $layout        If provided, will override the default layout.
     * @param int    $cache_time    The number of seconds to cache the output for.
     * @return mixed
     */
    public function render($layout = null)
    {
        // Make the template engine available within the views.
        $this->vars['themer'] = $this;

        // Render our current view content
        $this->vars['view_content'] = $this->content();

        $theme = empty($this->theme) ? $this->default_theme : $this->theme;

        if (! isset($this->folders[$theme])) {
            throw new \LogicException( sprintf( lang('theme.bad_folder'), $theme ) );
        }

	    $this->active_theme = $theme;

        // Make the path available within views.
        $this->vars['theme_path'] = $this->folders[$theme];

        return $this->display($this->folders[$theme] . '/' . $this->layout);
    }

    //--------------------------------------------------------------------

	/**
	 * Used within the template layout file to render the current content.
	 * This content is typically used to display the current view.
	 *
	 * @param int $cache_time
	 *
	 * @return mixed
	 */
    public function content()
    {
        // Calc our view name based on current method/controller
        $dir = $this->ci->router->fetch_directory();

        foreach (Modules::$locations as $key => $offset) {

            if (stripos($dir, 'module') !== false) {
//                $dir = str_replace($offset, '', $dir);
                $dir = str_replace('controllers/', '', $dir);
            }
        }

        if (substr($dir, -strlen($this->ci->router->fetch_module() .'/')) == $this->ci->router->fetch_module() . '/') {
            $dir = '';
        }

        $view = ! empty($this->view) ? $this->view :
            $dir . $this->ci->router->fetch_class() . '/' . $this->ci->router->fetch_method();

        return $this->display($view);
    }

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
     * If a variant has been specified, it will be added to the end
     * of the view name before looking for the file.
     *
     * @param string $view
     * @param array  $data
     * @param int    $cache_time Number of minutes to cache the page for
     * @param string $cache_name A custom name for the cached file.
     * @return mixed
     */
    public function display($view, $data = array(), $cache_time = 0, $cache_name=null)
    {
	    if (empty($cache_name))
	    {
		    $cache_name = 'theme_view_' . $view . '_' . $this->ci->router->fetch_class() . '_' . $this->ci->router->fetch_method();
		    $cache_name = str_replace( '/', '_', $cache_name );
	    }

	    if ($cache_time == 0 || ! $output = $this->ci->cache->get($cache_name))
	    {
		    $theme        = NULL;
		    $variant_view = NULL;

		    // Pull out the theme from the view, if given.
		    if ( strpos( $view, ':' ) !== FALSE )
		    {
			    list( $theme, $view ) = explode( ':', $view );

			    $theme = str_replace('{theme}', $this->active_theme, $theme);
		    }

		    if ( ! empty( $theme ) && isset( $this->folders[ $theme ] ) )
		    {
			    $view = rtrim( $this->folders[ $theme ], '/' ) . '/' . $view;
		    }

		    if (! is_array($data))
		    {
			    $data = [];
		    }

		    $data = array_merge( $this->vars, $data );

		    // if using a variant, add it to the view name.
		    if ( ! empty( $this->current_variant ) )
		    {
			    $variant_view = $view . $this->variants[ $this->current_variant ];

			    $output = $this->loadView($variant_view, $data);
		    }

		    // If that didn't find anything, then try it without a variant
		    if ( empty( $output ) )
		    {
			    $output = $this->loadView($view, $data);
		    }

		    // Cache it
		    if ((int)$cache_time > 0)
		    {
			    $this->ci->cache->save($cache_name, $output, (int)$cache_time * 60);
		    }
	    }

	    // Parse views?
	    if ($this->parse_views)
	    {
		    $this->ci->load->library('parser');

		    // Any class objects will cause failure
		    // so get rid of those bad boys....
		    unset($data['uikit'], $data['themer']);

		    $output = $this->ci->parser->parse_string($output, $data, true);
	    }

        return $output;
    }

    //--------------------------------------------------------------------

    /**
     * Runs a callback method and returns the contents to the view.
     *
     * @param $command
     * @param int $cache_time
     * @param string $cache_name    // Number of MINUTES to cache output
     * @return mixed|void
     */
    public function call($command, $cache_time=0, $cache_name=null)
    {
	    if (empty($cache_name))
	    {
		    $cache_name = 'theme_call_' . md5( $command );
	    }

        if (! $output = $this->ci->cache->get($cache_name)) {
            $parts = explode(' ', $command);

            list($class, $method) = explode(':', array_shift($parts));

            // Prepare our parameter list to send to the callback
            // by splitting $parts on equal signs.
            $params = array();

            foreach ($parts as $part) {
                $p = explode('=', $part);

                if (empty($p[0]) || empty($p[1]))
                {
                    continue;
                }

                $params[ $p[0] ] = $p[1];
            }

            // Let PHP try to autoload it through any available autoloaders
            // (including Composer and user's custom autoloaders). If we
            // don't find it, then assume it's a CI library that we can reach.
            if (class_exists($class)) {
                $class = new $class();
            } else {
                $this->ci->load->library($class);
                $class =& $this->ci->$class;
            }

            if (! method_exists($class, $method)) {
                throw new \RuntimeException( sprintf( lang('themer.bad_callback'), $class, $method ) );
            }

            // Call the class with our parameters
            $output = $class->{$method}($params);

            // Cache it
            if ((int)$cache_time > 0)
            {
                $this->ci->cache->save($cache_name, $output, (int)$cache_time * 60);
            }
        }

        return $output;
    }

    //--------------------------------------------------------------------

    /**
     * Sets the active theme to use. This theme should be
     * relative to one of the 'theme_paths' folders.
     *
     * @param $theme
     */
    public function setTheme($theme)
    {
        $this->theme = $theme;
    }

    //--------------------------------------------------------------------

    /**
     * Returns the current theme.
     *
     * @return mixed|string
     */
    public function theme()
    {
        return $this->theme;
    }

    //--------------------------------------------------------------------

    /**
     * Sets the default theme to use if another isn't specified.
     *
     * @param $theme
     * @return mixed|void
     */
    public function setDefaultTheme($theme)
    {
        $this->default_theme = $theme;
    }

    //--------------------------------------------------------------------


    /**
     * Sets the current view file to render.
     *
     * @param $file
     * @return mixed
     */
    public function setView($file)
    {
        $this->view = $file;
    }

    //--------------------------------------------------------------------

    /**
     * Returns the current view.
     *
     * @return mixed|string
     */
    public function view()
    {
        return $this->view;
    }

    //--------------------------------------------------------------------

    /**
     * Sets the current layout to be used. MUST be the name of one of
     * the layout files within the current theme. Case-sensitive.
     *
     * @param $file
     * @return mixed
     */
    public function setLayout($file)
    {
        if (empty($file)) return;

        $this->layout = $file;
    }

    //--------------------------------------------------------------------

    /**
     * Returns the current layout.
     *
     * @return mixed|string
     */
    public function layout()
    {
        return $this->layout;
    }

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
     * @param mixed $value
     * @return $this
     */
    public function set($key, $value = null)
    {
        if (is_array($key)) {
            $this->vars = array_merge($this->vars, $key);
            return;
        }

        $this->vars[$key] = $value;

        return $this;
    }

    //--------------------------------------------------------------------

    /**
     * Returns a value that has been previously set().
     *
     * @param $key
     * @return mixed
     */
    public function get($key)
    {
        return isset($this->vars[$key]) ? $this->vars[$key] : null;
    }

    //--------------------------------------------------------------------

    /**
     * Determines whether or not the view should be parsed with the
     * CodeIgniter's parser.
     *
     * @param bool $parse
     * @return mixed
     */
    public function parseViews($parse = false)
    {
	    $this->parse_views = $parse;

        return $this;
    }

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
    public function addThemePath($alias, $path)
    {
        $this->folders[$alias] = $path;

        return $this;
    }

    //--------------------------------------------------------------------

    /**
     * Removes a single theme path.
     *
     * @param $alias
     * @return $this
     */
    public function removeThemePath($alias)
    {
        unset($this->folders[$alias]);

        return $this;
    }

    //--------------------------------------------------------------------

    /**
     * Returns the path to the active/default theme's folder.
     *
     * @return string|null
     */
    public function getThemePath()
    {
        $theme = empty($this->theme) ? $this->default_theme : $this->theme;

        if (! isset($this->folders[ $theme ]))
        {
            return null;
        }

        return $this->folders[$theme];
    }

    //--------------------------------------------------------------------



    //--------------------------------------------------------------------
    // Variants
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
     * @return $this
     */
    public function setVariant($variant)
    {
        if (isset($this->variants[$variant])) {
            $this->current_variant = $variant;
        }

        return $this;
    }
    //--------------------------------------------------------------------

    /**
     * Adds a new variant to the system.
     *
     * @param $name
     * @param $postfix
     * @return $this|mixed
     */
    public function addVariant($name, $postfix)
    {
        $this->variants[$name] = $postfix;

        return $this;
    }

    //--------------------------------------------------------------------

    /**
     * Removes a variant from the system.
     *
     * @param $name
     * @return $this|mixed
     */
    public function removeVariant($name)
    {
        if (isset($this->variants[$name])) {
            unset($this->variants[$name]);
        }

        return $this;
    }

    //--------------------------------------------------------------------
    // Private Methods
    //--------------------------------------------------------------------

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

use Myth\Modules as Modules;
use Myth\Themers\ThemerInterface;

class LocalizedViewThemer implements ThemerInterface
{

    protected $theme = '';

    protected $default_theme = null;

	protected $active_theme = null;

    protected $layout = 'index';

    protected $view = '';

    protected $vars = [];

    protected $folders = [];

    protected $variants = [];

    protected $current_variant = '';

	protected $parse_views = false;

    protected $ci;

    //--------------------------------------------------------------------

    public function __construct($ci)
    {
        $this->ci = $ci;

	    $this->parse_views = config_item('theme.parse_views');
    }

    //--------------------------------------------------------------------

    /**
     * The main entryway into rendering a view. This is called from the
     * controller and is generally the last method called.
     *
     * @param string $layout        If provided, will override the default layout.
     * @param int    $cache_time    The number of seconds to cache the output for.
     * @return mixed
     */
    public function render($layout = null)
    {
        // Make the template engine available within the views.
        $this->vars['themer'] = $this;

        // Render our current view content
        $this->vars['view_content'] = $this->content();

        $theme = empty($this->theme) ? $this->default_theme : $this->theme;

        if (! isset($this->folders[$theme])) {
            throw new \LogicException( sprintf( lang('theme.bad_folder'), $theme ) );
        }

	    $this->active_theme = $theme;

        // Make the path available within views.
        $this->vars['theme_path'] = $this->folders[$theme];

        return $this->display($this->folders[$theme] . '/' . $this->layout);
    }

    //--------------------------------------------------------------------

	/**
	 * Used within the template layout file to render the current content.
	 * This content is typically used to display the current view.
	 *
	 * @param int $cache_time
	 *
	 * @return mixed
	 */
    public function content()
    {
        // Calc our view name based on current method/controller
        $dir = $this->ci->router->fetch_directory();

        foreach (Modules::$locations as $key => $offset) {

            if (stripos($dir, 'module') !== false) {
//                $dir = str_replace($offset, '', $dir);
                $dir = str_replace('controllers/', '', $dir);
            }
        }

        if (substr($dir, -strlen($this->ci->router->fetch_module() .'/')) == $this->ci->router->fetch_module() . '/') {
            $dir = '';
        }

        $view = ! empty($this->view) ? $this->view :
            $dir . $this->ci->router->fetch_class() . '/' . $this->ci->router->fetch_method();

        return $this->display($view);
    }

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
     * If a variant has been specified, it will be added to the end
     * of the view name before looking for the file.
     *
     * @param string $view
     * @param array  $data
     * @param int    $cache_time Number of minutes to cache the page for
     * @param string $cache_name A custom name for the cached file.
     * @return mixed
     */
    public function display($view, $data = array(), $cache_time = 0, $cache_name=null)
    {
	    if (empty($cache_name))
	    {
		    $cache_name = 'theme_view_' . $view . '_' . $this->ci->router->fetch_class() . '_' . $this->ci->router->fetch_method();
		    $cache_name = str_replace( '/', '_', $cache_name );
	    }

	    if ($cache_time == 0 || ! $output = $this->ci->cache->get($cache_name))
	    {
		    $theme        = NULL;
		    $variant_view = NULL;

		    // Pull out the theme from the view, if given.
		    if ( strpos( $view, ':' ) !== FALSE )
		    {
			    list( $theme, $view ) = explode( ':', $view );

			    $theme = str_replace('{theme}', $this->active_theme, $theme);
		    }

		    if ( ! empty( $theme ) && isset( $this->folders[ $theme ] ) )
		    {
			    $view = rtrim( $this->folders[ $theme ], '/' ) . '/' . $view;
		    }

		    if (! is_array($data))
		    {
			    $data = [];
		    }

		    $data = array_merge( $this->vars, $data );

		    // if using a variant, add it to the view name.
		    if ( ! empty( $this->current_variant ) )
		    {
			    $variant_view = $view . $this->variants[ $this->current_variant ];

			    $output = $this->loadView($variant_view, $data);
		    }

		    // If that didn't find anything, then try it without a variant
		    if ( empty( $output ) )
		    {
			    $output = $this->loadView($view, $data);
		    }

		    // Cache it
		    if ((int)$cache_time > 0)
		    {
			    $this->ci->cache->save($cache_name, $output, (int)$cache_time * 60);
		    }
	    }

	    // Parse views?
	    if ($this->parse_views)
	    {
		    $this->ci->load->library('parser');

		    // Any class objects will cause failure
		    // so get rid of those bad boys....
		    unset($data['uikit'], $data['themer']);

		    $output = $this->ci->parser->parse_string($output, $data, true);
	    }

        return $output;
    }

    //--------------------------------------------------------------------

    /**
     * Runs a callback method and returns the contents to the view.
     *
     * @param $command
     * @param int $cache_time
     * @param string $cache_name    // Number of MINUTES to cache output
     * @return mixed|void
     */
    public function call($command, $cache_time=0, $cache_name=null)
    {
	    if (empty($cache_name))
	    {
		    $cache_name = 'theme_call_' . md5( $command );
	    }

        if (! $output = $this->ci->cache->get($cache_name)) {
            $parts = explode(' ', $command);

            list($class, $method) = explode(':', array_shift($parts));

            // Prepare our parameter list to send to the callback
            // by splitting $parts on equal signs.
            $params = array();

            foreach ($parts as $part) {
                $p = explode('=', $part);

                if (empty($p[0]) || empty($p[1]))
                {
                    continue;
                }

                $params[ $p[0] ] = $p[1];
            }

            // Let PHP try to autoload it through any available autoloaders
            // (including Composer and user's custom autoloaders). If we
            // don't find it, then assume it's a CI library that we can reach.
            if (class_exists($class)) {
                $class = new $class();
            } else {
                $this->ci->load->library($class);
                $class =& $this->ci->$class;
            }

            if (! method_exists($class, $method)) {
                throw new \RuntimeException( sprintf( lang('themer.bad_callback'), $class, $method ) );
            }

            // Call the class with our parameters
            $output = $class->{$method}($params);

            // Cache it
            if ((int)$cache_time > 0)
            {
                $this->ci->cache->save($cache_name, $output, (int)$cache_time * 60);
            }
        }

        return $output;
    }

    //--------------------------------------------------------------------

    /**
     * Sets the active theme to use. This theme should be
     * relative to one of the 'theme_paths' folders.
     *
     * @param $theme
     */
    public function setTheme($theme)
    {
        $this->theme = $theme;
    }

    //--------------------------------------------------------------------

    /**
     * Returns the current theme.
     *
     * @return mixed|string
     */
    public function theme()
    {
        return $this->theme;
    }

    //--------------------------------------------------------------------

    /**
     * Sets the default theme to use if another isn't specified.
     *
     * @param $theme
     * @return mixed|void
     */
    public function setDefaultTheme($theme)
    {
        $this->default_theme = $theme;
    }

    //--------------------------------------------------------------------


    /**
     * Sets the current view file to render.
     *
     * @param $file
     * @return mixed
     */
    public function setView($file)
    {
        $this->view = $file;
    }

    //--------------------------------------------------------------------

    /**
     * Returns the current view.
     *
     * @return mixed|string
     */
    public function view()
    {
        return $this->view;
    }

    //--------------------------------------------------------------------

    /**
     * Sets the current layout to be used. MUST be the name of one of
     * the layout files within the current theme. Case-sensitive.
     *
     * @param $file
     * @return mixed
     */
    public function setLayout($file)
    {
        if (empty($file)) return;

        $this->layout = $file;
    }

    //--------------------------------------------------------------------

    /**
     * Returns the current layout.
     *
     * @return mixed|string
     */
    public function layout()
    {
        return $this->layout;
    }

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
     * @param mixed $value
     * @return $this
     */
    public function set($key, $value = null)
    {
        if (is_array($key)) {
            $this->vars = array_merge($this->vars, $key);
            return;
        }

        $this->vars[$key] = $value;

        return $this;
    }

    //--------------------------------------------------------------------

    /**
     * Returns a value that has been previously set().
     *
     * @param $key
     * @return mixed
     */
    public function get($key)
    {
        return isset($this->vars[$key]) ? $this->vars[$key] : null;
    }

    //--------------------------------------------------------------------

    /**
     * Determines whether or not the view should be parsed with the
     * CodeIgniter's parser.
     *
     * @param bool $parse
     * @return mixed
     */
    public function parseViews($parse = false)
    {
	    $this->parse_views = $parse;

        return $this;
    }

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
    public function addThemePath($alias, $path)
    {
        $this->folders[$alias] = $path;

        return $this;
    }

    //--------------------------------------------------------------------

    /**
     * Removes a single theme path.
     *
     * @param $alias
     * @return $this
     */
    public function removeThemePath($alias)
    {
        unset($this->folders[$alias]);

        return $this;
    }

    //--------------------------------------------------------------------

    /**
     * Returns the path to the active/default theme's folder.
     *
     * @return string|null
     */
    public function getThemePath()
    {
        $theme = empty($this->theme) ? $this->default_theme : $this->theme;

        if (! isset($this->folders[ $theme ]))
        {
            return null;
        }

        return $this->folders[$theme];
    }

    //--------------------------------------------------------------------



    //--------------------------------------------------------------------
    // Variants
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
     * @return $this
     */
    public function setVariant($variant)
    {
        if (isset($this->variants[$variant])) {
            $this->current_variant = $variant;
        }

        return $this;
    }
    //--------------------------------------------------------------------

    /**
     * Adds a new variant to the system.
     *
     * @param $name
     * @param $postfix
     * @return $this|mixed
     */
    public function addVariant($name, $postfix)
    {
        $this->variants[$name] = $postfix;

        return $this;
    }

    //--------------------------------------------------------------------

    /**
     * Removes a variant from the system.
     *
     * @param $name
     * @return $this|mixed
     */
    public function removeVariant($name)
    {
        if (isset($this->variants[$name])) {
            unset($this->variants[$name]);
        }

        return $this;
    }

    //--------------------------------------------------------------------
    // Private Methods
    //--------------------------------------------------------------------

    /**
     * Handles the actual loading of a view file, and checks for any
     * overrides in themes, etc.
     *
     * @param $view
     * @param $data
     *
     * @return string
     */
    private function loadView($view, $data)
    {
        // First - does it exist in the current theme?
        $theme = ! empty($this->active_theme) ? $this->active_theme : $this->default_theme;
        $theme = ! empty($this->folders[$theme]) ? $this->folders[$theme] : $theme;
        $theme = rtrim($theme, '/ ') .'/';
		
		// Set localization idioms
		$theme_lang_idom = str_replace(FCPATH .'themes/', FCPATH .'themes/'. CURRENT_LANGUAGE .'/', $theme);
		$view_lang_idom = str_replace(FCPATH .'themes/', FCPATH .'themes/'. CURRENT_LANGUAGE .'/', $view);

		if(defined('CURRENT_LANGUAGE') && file_exists($theme_lang_idom ."{$view}.php"))
		{
			$output = $this->ci->load->view_path( $theme_lang_idom . $view, $data, TRUE );
		}
		
		elseif (file_exists($theme ."{$view}.php"))
        {
            $output = $this->ci->load->view_path( $theme . $view, $data, TRUE );
        }
		
		elseif(defined('CURRENT_LANGUAGE') && realpath($view_lang_idom .".php"))
		{
			$output = $this->ci->load->view_path( $view_lang_idom, $data, TRUE );
		}
		
        // Next, if it's a real file with path, then load it
        elseif ( realpath( $view . '.php' ) )
        {
            $output = $this->ci->load->view_path( $view, $data, TRUE );
        }

        // Otherwise, treat it as a standard view, which means
        // application/views will override any modules. (See HMVC/Loader)
        else
        {
        	if(defined('CURRENT_LANGUAGE')) $view = CURRENT_LANGUAGE.'/'.$view;
            $output = $this->ci->load->view( $view, $data, TRUE );
        }

        return $output;
    }

}
