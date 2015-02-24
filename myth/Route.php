<?php namespace Myth;
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
 * Route class provides methods to be used within the routes config file
 * to enable a simpler syntax for some of the non-CI native methods.
 *
 * Thanks to Jamie Rumbelow and his wonderful Pigeon routing class for the
 * ideas for teh HTTP Verb-based routing in use here.
 *
 * @package Bonfire
 * @since   1.0
 */
class Route
{

    // Our routes, ripe for the picking.
    public $routes = array();

    // Holds key/value pairs of named routes
    public static $names = array();

    // Used for grouping routes together.
    public $group = null;

    // Holds the 'areas' of the site.
    public static $areas = array();

    // The default controller to use in case
    // 'default_controller' is not in the routes file.
    protected $default_home = 'home';

    // The default constraint to use in route building
    protected $default_constraint = 'any';

    protected $constraints = [
        'any'  => '(:any)',
        'num'  => '(:num)',
        'id'   => '(:num)',
        'name' => "([a-zA-Z']+)"
    ];

    protected $current_subdomain = null;

    //--------------------------------------------------------------------

    /**
     * Combines the routes that we've defined with the Route class with the
     * routes passed in. This is intended to be used  after all routes have been
     * defined to merge CI's default $route array with our routes.
     *
     * Example:
     *     $route['default_controller'] = 'home';
     *     Route::resource('posts');
     *     $route = Route::map($route);
     *
     * @param array $routes
     * @internal param array $route The array to merge
     * @return array         The merge route array.
     */
    public function map($routes = array())
    {
        $controller = isset($routes['default_controller']) ? $routes['default_controller'] : $this->default_home;

        $routes = array_merge($routes, $this->routes);

        foreach ($routes as $from => $to) {
            $routes[$from] = str_ireplace('{default_controller}', $controller, $to);
        }

        return $routes;
    }

    //--------------------------------------------------------------------

    /**
     * A single point to the basic routing. Can be used in place of CI's $route
     * array if desired. Used internally by many of the methods.
     *
     * Available options are currently:
     *      'as'        - remembers the route via a name that can be called outside of it.
     *      'offset'    - Offsets and parameters ($1, $2, etc) in routes by the specified amount.
     *                    Useful while doing versioning of API's, etc.
     *
     * Example:
     *      $route->any('news', 'posts/index');
     *
     * @param string $from
     * @param string $to
     * @param array  $options
     * @return void
     */
    public function any($from, $to, $options = array())
    {
        $this->create($from, $to, $options);
    }

    //--------------------------------------------------------------------

    /**
     * Sets the default constraint to be used in the system. Typically
     * for use with the 'resources' method.
     *
     * @param $constraint
     */
    public function setDefaultConstraint($constraint)
    {
        if (array_key_exists($constraint, $this->constraints)) {
            $this->default_constraint = $constraint;
        }
    }

    //--------------------------------------------------------------------

    /**
     * Registers a new constraint to be used internally. Useful for creating
     * very specific regex patterns, or simply to allow your routes to be
     * a tad more readable.
     *
     * Example:
     *      $route->registerConstraint('hero', '(^.*)');
     *
     *      $route->any('home/{hero}', 'heroes/journey');
     *
     *      // Route then looks like:
     *      $route['home/(^.*)'] = 'heroes/journey';
     *
     * @param      $name
     * @param      $pattern
     * @param bool $overwrite
     */
    public function registerConstraint($name, $pattern, $overwrite = false)
    {
        // Ensure consistency
        $name    = trim($name, '{} ');
        $pattern = '(' . trim($pattern, '() ') . ')';

        // Not here? Add it and leave...
        if (! array_key_exists($name, $this->constraints)) {
            $this->constraints[$name] = $pattern;

            return;
        }

        // Here? Then it exists. Should we overwrite it?
        if ($overwrite) {
            $this->constraints[$name] = $pattern;
        }
    }

    //--------------------------------------------------------------------

    //--------------------------------------------------------------------
    // Named Routes
    //--------------------------------------------------------------------

    /**
     * Returns the value of a named route. Useful for getting named
     * routes for use while building with site_url() or in templates
     * where you don't need to instantiate the route class.
     *
     * Example:
     *      $route->any('news', 'posts/index', ['as' => 'blog']);
     *
     *      // Returns http://mysite.com/news
     *      site_url( Route::named('blog') );
     *
     * @param  [type] $name [description]
     * @return [type]       [description]
     */
    public static function named($name)
    {
        if (isset(self::$names[$name])) {
            return self::$names[$name];
        }

        return null;
    }

    //--------------------------------------------------------------------

    //--------------------------------------------------------------------
    // Grouping Routes
    //--------------------------------------------------------------------

    /**
     * Group a series of routes under a single URL segment. This is handy
     * for grouping items into an admin area, like:
     *
     * Example:
     *     $route->group('admin', function() {
     *            $route->resources('users');
     *     });
     *
     * @param  string   $name     The name to group/prefix the routes with.
     * @param  \Closure $callback An anonymous function that allows you route inside of this group.
     * @return void
     */
    public function group($name, \Closure $callback)
    {
        $old_group = $this->group;

        // To register a route, we'll set a flag so that our router
        // so it will see the groupname.
        $this->group = ltrim($old_group . '/' . $name, '/');

        call_user_func($callback);

        // Make sure to clear the group name so we don't accidentally
        // group any ones we didn't want to.
        $this->group = $old_group;
    }

    //--------------------------------------------------------------------

    //--------------------------------------------------------------------
    // HTTP Verb-based routing
    //--------------------------------------------------------------------
    // Routing works here because, as the routes config file is read in,
    // the various HTTP verb-based routes will only be added to the in-memory
    // routes if it is a call that should respond to that verb.
    //
    // The options array is typically used to pass in an 'as' or var, but may
    // be expanded in the future. See the docblock for 'any' method above for
    // current list of globally available options.
    //

    /**
     * Specifies a single route to match for multiple HTTP Verbs.
     *
     * Example:
     *  $route->match( ['get', 'post'], 'users/(:num)', 'users/$1);
     *
     * @param array $verbs
     * @param       $from
     * @param       $to
     * @param array $options
     */
    public function match($verbs = [], $from, $to, $options = [])
    {
        foreach ($verbs as $verb) {
            $verb = strtolower($verb);

            $this->{$verb}($from, $to, $options);
        }
    }

    //--------------------------------------------------------------------

    /**
     * Specifies a route that is only available to GET requests.
     *
     * @param       $from
     * @param       $to
     * @param array $options
     */
    public function get($from, $to, $options = [])
    {
        if (isset($_SERVER['REQUEST_METHOD']) && $_SERVER['REQUEST_METHOD'] == 'GET') {
            $this->create($from, $to, $options);
        }
    }

    //--------------------------------------------------------------------

    /**
     * Specifies a route that is only available to POST requests.
     *
     * @param       $from
     * @param       $to
     * @param array $options
     */
    public function post($from, $to, $options = [])
    {
        if (isset($_SERVER['REQUEST_METHOD']) && $_SERVER['REQUEST_METHOD'] == 'POST') {
            $this->create($from, $to, $options);
        }
    }

    //--------------------------------------------------------------------

    /**
     * Specifies a route that is only available to PUT requests.
     *
     * @param       $from
     * @param       $to
     * @param array $options
     */
    public function put($from, $to, $options = [])
    {
        if (isset($_SERVER['REQUEST_METHOD']) && $_SERVER['REQUEST_METHOD'] == 'PUT') {
            $this->create($from, $to, $options);
        }
    }

    //--------------------------------------------------------------------

    /**
     * Specifies a route that is only available to DELETE requests.
     *
     * @param       $from
     * @param       $to
     * @param array $options
     */
    public function delete($from, $to, $options = [])
    {
        if (isset($_SERVER['REQUEST_METHOD']) && $_SERVER['REQUEST_METHOD'] == 'DELETE') {
            $this->create($from, $to, $options);
        }
    }

    //--------------------------------------------------------------------

    /**
     * Specifies a route that is only available to HEAD requests.
     *
     * @param       $from
     * @param       $to
     * @param array $options
     */
    public function head($from, $to, $options = [])
    {
        if (isset($_SERVER['REQUEST_METHOD']) && $_SERVER['REQUEST_METHOD'] == 'HEAD') {
            $this->create($from, $to, $options);
        }
    }

    //--------------------------------------------------------------------

    /**
     * Specifies a route that is only available to PATCH requests.
     *
     * @param       $from
     * @param       $to
     * @param array $options
     */
    public function patch($from, $to, $options = [])
    {
        if (isset($_SERVER['REQUEST_METHOD']) && $_SERVER['REQUEST_METHOD'] == 'PATCH') {
            $this->create($from, $to, $options);
        }
    }

    //--------------------------------------------------------------------

    /**
     * Specifies a route that is only available to OPTIONS requests.
     *
     * @param       $from
     * @param       $to
     * @param array $options
     */
    public function options($from, $to, $options = [])
    {
        if (isset($_SERVER['REQUEST_METHOD']) && $_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
            $this->create($from, $to, $options);
        }
    }

    //--------------------------------------------------------------------

    /**
     * Creates a collections of HTTP-verb based routes for a controller.
     *
     * Possible Options:
     *      'controller'    - Customize the name of the controller used in the 'to' route
     *      'module'        - Prepend a module name to the generate 'to' routes
     *      'constraint'    - The regex used by the Router. Defaults to '(:any)'
     *
     * Example:
     *      $route->resources('photos');
     *
     *      // Generates the following routes:
     *      HTTP Verb | Path        | Action        | Used for...
     *      ----------+-------------+---------------+-----------------
     *      GET         /photos             index           display a list of photos
     *      GET         /photos/new         creation_form   return an HTML form for creating a new photo
     *      GET         /photos/{id}        show            display a specific photo
     *      GET         /photos/{id}/edit   editing_form    return an HTML form for editing the photo
     *      POST        /photos             create          create a new photo
     *      PUT         /photos/{id}        update          update an existing photo
     *      DELETE      /photos/{id}/delete delete          delete an existing photo
     *
     * @param  string $name    The name of the controller to route to.
     * @param  array  $options An list of possible ways to customize the routing.
     */
    public function resources($name, $options = [])
    {
        // In order to allow customization of the route the
        // resources are sent to, we need to have a new name
        // to store the values in.
        $new_name = $name;

        // If a new controller is specified, then we replace the
        // $name value with the name of the new controller.
        if (isset($options['controller'])) {
            $new_name = $options['controller'];
        }

        // If a new module was specified, simply put that path
        // in front of the controller.
        if (isset($options['module'])) {
            $new_name = $options['module'] . '/' . $new_name;
        }

        // In order to allow customization of allowed id values
        // we need someplace to store them.
        $id = isset($this->constraints[$this->default_constraint]) ? $this->constraints[$this->default_constraint] :
            '(:any)';

        if (isset($options['constraint'])) {
            $id = $options['constraint'];
        }

        $this->get($name, $new_name . '/list_all', $options);
        $this->get($name . '/new', $new_name . '/creation_form', $options);
        $this->get($name . '/' . $id . '/edit', $new_name . '/editing_form/$1', $options);
        $this->get($name . '/' . $id, $new_name . '/show/$1', $options);
        $this->post($name, $new_name . '/create', $options);
        $this->put($name . '/' . $id, $new_name . '/update/$1', $options);
        $this->delete($name . '/' . $id, $new_name . '/delete/$1', $options);
        $this->options($name, $new_name . '/index', $options);
    }

    //--------------------------------------------------------------------

    /**
     * Lets the system know about different 'areas' within the site, like
     * the admin area, that maps to certain controllers.
     *
     * @param  string $area       The name of the area.
     * @param  string $controller The controller name to look for.
     * @param         $options
     */
    public function area($area, $controller = null, $options = [])
    {
        // No controller? Match the area name.
        $controller = is_null($controller) ? $area : $controller;

        // Save the area so we can recognize it later.
        self::$areas[$area] = $controller;

        // Create routes for this area.
        $this->create($area . '/(:any)/(:any)/(:any)/(:any)/(:any)', '$1/' . $controller . '/$2/$3/$4/$5', $options);
        $this->create($area . '/(:any)/(:any)/(:any)/(:any)', '$1/' . $controller . '/$2/$3/$4', $options);
        $this->create($area . '/(:any)/(:any)/(:any)', '$1/' . $controller . '/$2/$3', $options);
        $this->create($area . '/(:any)/(:any)', '$1/' . $controller . '/$2', $options);
        $this->create($area . '/(:any)', '$1/' . $controller, $options);
    }

    //--------------------------------------------------------------------

    /**
     * Returns the name of the area based on the controller name.
     *
     * @param  string $controller The name of the controller
     * @return string             The name of the corresponding area
     */
    public static function getAreaName($controller)
    {
        foreach (self::$areas as $area => $cont) {
            if ($controller == $cont) {
                return $area;
            }
        }

        return null;
    }

    //--------------------------------------------------------------------

    /**
     * Allows you to easily block access to any number of routes by setting
     * that route to an empty path ('').
     *
     * Example:
     *     Route::block('posts', 'photos/(:num)');
     *
     *     // Same as...
     *     $route['posts']          = '';
     *     $route['photos/(:num)']  = '';
     */
    public function block()
    {
        $paths = func_get_args();

        if (! is_array($paths) || ! count($paths)) {
            return;
        }

        foreach ($paths as $path) {
            $this->create($path, '');
        }
    }

    //--------------------------------------------------------------------

    /**
     * Empties all named and un-named routes from the system.
     *
     * @return void
     */
    public function reset()
    {
        $this->routes = array();
        $this->names  = array();
        $this->group  = null;
        $this->areas  = array();
    }

    //--------------------------------------------------------------------

    //--------------------------------------------------------------------
    // Private Methods
    //--------------------------------------------------------------------

    /**
     * Does the heavy lifting of creating an actual route. You must specify
     * the request method(s) that this route will work for. They can be separated
     * by a pipe character "|" if there is more than one.
     *
     * @param  string $from
     * @param  array  $to
     * @param array   $options
     *
     * @return array          The built route.
     */
    private function create($from, $to, $options = array())
    {
        $prefix = is_null($this->group) ? '' : $this->group . '/';

        $from = $prefix . $from;

        // Are we saving the name for this one?
        if (isset($options['as']) && !empty($options['as'])) {
            self::$names[$options['as']] = $from;
        }

        // Limiting to subdomains?
        if (isset($options['subdomain']) && !empty($options['subdomain'])) {
            // If we don't match the current subdomain, then
            // we don't need to add the route.
            if (!$this->checkSubdomains($options['subdomain'])) {
                return;
            }
        }

        // Are we offsetting the parameters?
        // If so, take care of them here in one
        // fell swoop.
        if (isset($options['offset'])) {
            // Get a constant string to work with.
            $to = preg_replace('/(\$\d+)/', '$X', $to);

            for ($i = (int)$options['offset'] + 1; $i < (int)$options['offset'] + 7; $i ++) {
                $to = preg_replace_callback(
                    '/\$X/',
                    function ($m) use ($i) {
                        return '$' . $i;
                    },
                    $to,
                    1
                );
            }
        }

        // Convert any custom constraints to the CI/pattern equivalent
        foreach ($this->constraints as $name => $pattern) {
            $from = str_replace('{' . $name . '}', $pattern, $from);
        }

        $this->routes[$from] = $to;
    }

    //--------------------------------------------------------------------

    /**
     * Compares the subdomain(s) passed in against the current subdomain
     * on this page request.
     *
     * @param $subdomains
     * @return bool
     */
    private function checkSubdomains($subdomains)
    {
        if (is_null($this->current_subdomain)) {
            $this->determineCurrentSubdomain();
        }

        if (!is_array($subdomains)) {
            $subdomains = array($subdomains);
        }

        $matched = false;

        array_walk(
            $subdomains,
            function ($subdomain) use (&$matched) {
                if ($subdomain == $this->current_subdomain || $subdomain == '*') {
                    $matched = true;
                }
            }
        );

        return $matched;
    }

    //--------------------------------------------------------------------

    /**
     * Examines the HTTP_HOST to get a best match for the subdomain. It
     * won't be perfect, but should work for our needs.
     */
    private function determineCurrentSubdomain()
    {
        $parsedUrl = parse_url($_SERVER['HTTP_HOST']);

        $host = explode('.', $parsedUrl['host']);

        // If we only have 2 parts, then we don't have a subdomain.
        // This won't be totally accurate, since URL's like example.co.uk
        // would still pass, but it helps to separate the chaff...
        if (!is_array($host) || count($host) == 2) {
            // Set it to false so we don't make it back here again.
            $this->current_subdomain = false;
            return;
        }

        // Now, we'll simply take the first element of the array. This should
        // be fine even in cases like example.co.uk, since they won't be looking
        // for 'example' when they try to match the subdomain, in most all cases.
        $this->current_subdomain = array_shift($host);
    }
    //--------------------------------------------------------------------

}
