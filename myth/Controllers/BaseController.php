<?php

namespace Myth\Controllers;

/**
 * The following properties are used to provide autocomplete for IDE's.
 *
 * Thanks to:  https://gist.github.com/topdown/1697338
 *
 * @property \CI_DB_query_builder    $db
 * @property \CI_DB_utility          $dbutil
 * @property \CI_DB_forge            $dbforge
 * @property \CI_Benchmark           $benchmark
 * @property \CI_Calendar            $calendar
 * @property \CI_Cart                $cart
 * @property \CI_Config              $config
 * @property \CI_Controller          $controller
 * @property \CI_Email               $email
 * @property \CI_Encrypt             $encrypt
 * @property \CI_Exceptions          $exceptions
 * @property \CI_Form_validation     $form_validation
 * @property \CI_Ftp                 $ftp
 * @property \CI_Hooks               $hooks
 * @property \CI_Image_lib           $image_lib
 * @property \CI_Input               $input
 * @property \CI_Lang                $lang
 * @property \CI_Loader              $load
 * @property \CI_Log                 $log
 * @property \CI_Model               $model
 * @property \CI_Output              $output
 * @property \CI_Pagination          $pagination
 * @property \CI_Parser              $parser
 * @property \CI_Profiler            $profiler
 * @property \CI_Router              $router
 * @property \CI_Session             $session
 * @property \CI_Sha1                $sha1
 * @property \CI_Table               $table
 * @property \CI_Trackback           $trackback
 * @property \CI_Typography          $typography
 * @property \CI_Unit_test           $unit_test
 * @property \CI_Upload              $upload
 * @property \CI_URI                 $uri
 * @property \CI_User_agent          $user_agent
 * @property \CI_Xmlrpc              $xmlrpc
 * @property \CI_Xmlrpcs             $xmlrpcs
 * @property \CI_Zip                 $zip
 * @property \CI_Javascript          $javascript
 * @property \CI_Jquery              $jquery
 * @property \CI_Utf8                $utf8
 * @property \CI_Security            $security
 */
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

        $this->setupCache();

        $this->autoload();

        $this->autoMigrate();

        $this->setupProfiler();

        log_message('debug', get_class($this) .' controller loaded.');
    }

    //--------------------------------------------------------------------

    //--------------------------------------------------------------------
    // Setup Methods
    //--------------------------------------------------------------------
    // These methods are used during the initial constructor, but split out
    // here so that child controllers can easily override individual methods
    // if they need to customize that aspect of the startup.

    /**
     * Gets the cache up and running. The site-wide cache settings can be
     * set in the application config file. Each controller can override these
     * settings using the 'cache_type' and 'backup_cache' class vars.
     */
    protected function setupCache()
    {
        // If the controller doesn't override cache type, grab the values from
        // the defaults set in the start file.
        if (empty($this->cache_type)) $this->cache_type = $this->config->item('cache_type');
        if (empty($this->backup_cache)) $this->backup_cache = $this->config->item('backup_cache_type');

        // Make sure that caching is ALWAYS available throughout the app
        // though it defaults to 'dummy' which won't actually cache.
        $this->load->driver('cache', array('adapter' => $this->cache_type, 'backup' => $this->backup_cache));
    }

    //--------------------------------------------------------------------

    /**
     * Handles any autoloading of files, like language or model files,
     * that can be used throughout the controller.
     */
    protected function autoload()
    {
        if (!is_null($this->language_file)) $this->lang->load($this->language_file);

        if (!is_null($this->model_file)) {
            $this->load->database();
            $this->load->model($this->model_file);
        }
    }

    //--------------------------------------------------------------------

    /**
     * If settings allow, will auto-migrate the system to the latest
     * available migrations.
     */
    protected function autoMigrate()
    {
        $migrations = config_item('auto_migrate');

        if (! is_array($migrations) || ! count($migrations)) return;

        $this->load->library('migration');

        // Run all of our migrations for each group.
        foreach ($migrations as $group)
        {
            $this->migration->latest($group);
        }
    }

    //--------------------------------------------------------------------

    /**
     * Handles setting up the profiler.
     */
    protected function setupProfiler()
    {
        // The profiler is dealt with twice so that we can set
        // things up to work correctly in AJAX methods using $this->render_json
        // and it's cousins.
        if ($this->config->item('show_profiler') == true) {
            $this->output->enable_profiler(true);
        }
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
