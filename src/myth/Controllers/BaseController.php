<?php

namespace Myth\Controllers;

class BaseController extends \CI_Controller
{
    /**
     * The type of caching to use. The default values are
     * set globally in the environment's start file, but
     * these will override if they are set.
     */
    protected $cache_type = NULL;
    protected $backup_cache = NULL;

    // If TRUE, will send back the notices view
    // through the 'render_json' method in the
    // 'fragments' array.
    protected $ajax_notices = true;

    // If set, this language file will automatically be loaded.
    protected $language_file = NULL;

    // If set, this model file will automatically be loaded.
    protected $model_file = NULL;

    //--------------------------------------------------------------------

    public function __construct()
    {
        parent::__construct();

        //--------------------------------------------------------------------
        // Cache Setup
        //--------------------------------------------------------------------

        // If the controller doesn't override cache type, grab the values from
        // the defaults set in the start file.
        if (empty($this->cache_type)) $this->cache_type = $this->config->item('cache_type');
        if (empty($this->backup_cache)) $this->backup_cache = $this->config->item('backup_cache_type');

        // Make sure that caching is ALWAYS available throughout the app
        // though it defaults to 'dummy' which won't actually cache.
        $this->load->driver('cache', array('adapter' => $this->cache_type, 'backup' => $this->backup_cache));

        //--------------------------------------------------------------------
        // Language & Model Files
        //--------------------------------------------------------------------

        if (!is_null($this->language_file)) $this->lang->load($this->language_file);

        if (!is_null($this->model_file)) {
            $this->load->database();
            $this->load->model($this->model_file);
        }

        //--------------------------------------------------------------------
        // Migrations
        //--------------------------------------------------------------------

        // Try to auto-migrate any files stored in APPPATH ./migrations
        if ($this->config->item('auto_migrate') === TRUE) {
            $this->load->library('migration');

            // We can specify a version to migrate to by appending ?migrate_to=X
            // in the URL.
            if ($mig_version = $this->input->get('migrate_to')) {
                $this->migration->version($mig_version);
            } else {
                $this->migration->latest();
            }
        }

        //--------------------------------------------------------------------
        // Profiler
        //--------------------------------------------------------------------

        // The profiler is dealt with twice so that we can set
        // things up to work correctly in AJAX methods using $this->render_json
        // and it's cousins.
        if ($this->config->item('show_profiler') == true) {
            $this->output->enable_profiler(true);
        }

        //--------------------------------------------------------------------
        // Development Environment Setup
        //--------------------------------------------------------------------
        //
        if (ENVIRONMENT == 'development') {

        }

//        $this->load->driver('Auth');
    }

    //--------------------------------------------------------------------

    //--------------------------------------------------------------------
    // "Template" Functions
    //--------------------------------------------------------------------

    /**
     * A Very simple templating system designed not for power or flexibility
     * but to use the built in features of CodeIgniter's view system to easily
     * create fast templating capabilities.
     *
     * The view is assumed to be under the views folder, under a folder with the
     * name of the controller and a view matching the name of the method.
     *
     * The theme is simply a set of files located under the views/ui folder. By default
     * a view named index.php will be used. You can specify different layouts
     * with the scope method, 'layout()'.
     *
     *      $this->layout('two_left')->render();
     *
     * You can specify a non-default view name with the scope method 'view'.
     *
     *      $this->view('another_view')->render();
     *
     * Within the template the string '{view_content}' will be replaced with the
     * contents of the view file that we're rendering.
     *
     * @param  [type]  $layout      [description]
     * @param  boolean $return_data [description]
     * @return [type]               [description]
     */
    protected function render($data = array())
    {
        // Calc our view name based on current method/controller
        $view = !empty($this->use_view) ? $this->use_view : $this->router->fetch_class() . '/' . $this->router->fetch_method();

        if (!empty($this->view_folder)) {
            $view = $this->view_folder . '/' . $view;
        }

        // Merge any saved vars into the data
        $data = array_merge($data, $this->vars);

        $theme = ! empty($this->theme) ? $this->theme : config_item('default_theme');

        // Make sure any scripts/stylesheets are available to the view
        $data['external_scripts'] = $this->external_scripts;
        $data['stylesheets'] = $this->stylesheets;

        // We'll make the view content available to the template.
        $data['view_content'] = $this->load->view($view, $data, true);

        // Build our notices from the theme's view file.
        $data['notice'] = $this->load->view("themes/{$theme}/notice", array('notice' => $this->message()), true);

        // Render our layout and we're done!
        $layout = !empty($this->use_layout) ? $this->use_layout : 'index';

        $this->load->view("themes/{$theme}/{$layout}", $data, false, true);

        // Reset our custom view attributes.
        $this->use_view = $this->use_layout = '';
    }

    //--------------------------------------------------------------------


    //--------------------------------------------------------------------
    // Simple Rendering Methods
    //--------------------------------------------------------------------

    /**
     * Renders a string of aribritrary text. This is best used during an AJAX
     * call or web service request that are expecting something other then
     * proper HTML.
     *
     * @param  string $text The text to render.
     * @param  bool $typography If TRUE, will run the text through 'Auto_typography'
     *                          before outputting to the browser.
     *
     * @return void [type]       [description]
     */
    public function renderText($text, $typography = false)
    {
        // Note that, for now anyway, we don't do any cleaning of the text
        // and leave that up to the client to take care of.

        // However, we can auto_typogrify the text if we're asked nicely.
        if ($typography === true) {
            $this->load->helper('typography');
            $text = auto_typography($text);
        }

        $this->output->enable_profiler(false)
            ->set_content_type('text/plain')
            ->set_output($text);
    }

    //--------------------------------------------------------------------

    /**
     * Converts the provided array or object to JSON, sets the proper MIME type,
     * and outputs the data.
     *
     * Do NOT do any further actions after calling this action.
     *
     * @param  mixed $json The data to be converted to JSON.
     * @throws RenderException
     * @return void [type]       [description]
     */
    public function renderJSON($json)
    {
        if (is_resource($json)) {
            throw new RenderException('Resources can not be converted to JSON data.');
        }

        // If there is a fragments array and we've enabled profiling,
        // then we need to add the profile results to the fragments
        // array so it will be updated on the site, since we disable
        // all profiling below to keep the results clean.
        if (is_array($json)) {
            if (!isset($json['fragments'])) {
                $json['fragments'] = array();
            }

            if ($this->config->item('show_profile')) {
                $this->load->library('profiler');
                $json['fragments']['#profiler'] = $this->profiler->run();
            }

            // Also, include our notices in the fragments array.
            if ($this->ajax_notices === true) {
                $json['fragments']['#notices'] = $this->load->view('theme/notice', array('notice' => $this->message()), true);
            }
        }

        $this->output->enable_profiler(false)
            ->set_content_type('application/json')
            ->set_output(json_encode($json));
    }

    //--------------------------------------------------------------------

    /**
     * Sends the supplied string to the browser with a MIME type of text/javascript.
     *
     * Do NOT do any further processing after this command or you may receive a
     * Headers already sent error.
     *
     * @param  mixed $js The javascript to output.
     * @throws RenderException
     * @return void [type]       [description]
     */
    public function renderJS($js = null)
    {
        if (!is_string($js)) {
            throw new RenderException('No javascript passed to the render_js() method.');
        }

        $this->output->enable_profiler(false)
            ->set_content_type('application/x-javascript')
            ->set_output($js);
    }

    //--------------------------------------------------------------------

    /**
     * Breaks us out of any output buffering so that any content echo'd out
     * will echo out as it happens, instead of waiting for the end of all
     * content to echo out. This is especially handy for long running
     * scripts like might be involved in cron scripts.
     *
     * @return void
     */
    public function renderRealtime()
    {
        if (ob_get_level() > 0) {
            end_end_flush();
        }
        ob_implicit_flush(true);
    }

    //--------------------------------------------------------------------

    /**
     * Integrates with the bootstrap-ajax javascript file to
     * redirect the user to a new url.
     *
     * If the URL is a relative URL, it will be converted to a full URL for this site
     * using site_url().
     *
     * @param  string $location [description]
     */
    public function ajaxRedirect($location = '')
    {
        $location = empty($location) ? '/' : $location;

        if (strpos($location, '/') !== 0 || strpos($location, '://') !== false) {
            if (!function_exists('site_url')) {
                $this->load->helper('url');
            }

            $location = site_url($location);
        }

        $this->render_json(array('location' => $location));
    }

    //--------------------------------------------------------------------

    /**
     * Attempts to get any information from php://input and return it
     * as JSON data. This is useful when your javascript is sending JSON data
     * to the application.
     *
     * @param  strign $format The type of element to return, either 'object' or 'array'
     * @param  int $depth The number of levels deep to decode
     *
     * @return mixed    The formatted JSON data, or NULL.
     */
    public function getJSON($format = 'object', $depth = 512)
    {
        $as_array = $format == 'array' ? true : false;

        return json_decode(file_get_contents('php://input'), $as_array, $depth);
    }

    //--------------------------------------------------------------------

}
