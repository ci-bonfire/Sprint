<?php

namespace Myth\Controllers;

/**
 * Class ThemedController
 *
 * @package Bonfire\Libraries\Controllers
 */
class ThemedController extends BaseController
{
    /**
     * Stores data variables to be sent to the view.
     * @var array
     */
    protected $vars = array();

    /**
     * Stores current status message.
     * @var
     */
    protected $message;

    /**
     * The UIKit to make available to the template views.
     * @var string
     */
    protected $uikit = '';

    /**
     * An instance of an active Themer to use.
     * @var null
     */
    protected $themer = null;

    /**
     * Allows per-controller override of theme.
     * @var null
     */
    protected $theme = null;

    /**
     * Per-controller override of the current layout file.
     * @var null
     */
    protected $layout = null;

    /**
     * Stores an array of javascript files.
     * @var array
     */
    protected $external_scripts = array();

    /**
     * Stores an array of CSS stylesheets.
     * @var array
     */
    protected $stylesheets = array();

    //--------------------------------------------------------------------

    /**
     * Constructor takes care of getting the template engine up and running
     * and bound to our DI object, as well as any other preliminary needs,
     * like detecting the variant to use, etc.
     */
    public function __construct()
    {
        parent::__construct();

        // Setup our Template Engine
        $themer = config_item('active_themer');

        if (empty($themer)) {
            throw new \RuntimeException('No Themer chosen.');
        }

        $this->themer = new $themer( get_instance() );

        // Register our paths with the themer
        $paths = config_item('theme.paths');

        foreach ($paths as $key => $path) {
            $this->themer->addThemePath($key, $path);
        }

        // Set our default theme.
        $this->themer->setDefaultTheme( config_item('theme.default_theme') );

        // Register our variants with the engine.
        $variants = config_item('theme.variants');

        foreach ($variants as $key => $value) {
            $this->themer->addVariant($key, $value);
        }

        $this->detectVariant();

        // Ensure that our UIKit is loaded up if we're using one.
        $uikit = config_item('theme.uikit');

        if ($uikit)
        {
            $this->uikit = new $uikit();
        }
    }

    //--------------------------------------------------------------------

    /**
     * Provides a common interface with the other rendering methods to
     * set the output of the method. Uses the current instance of $this->template.
     * Ensures that any data we've stored through $this->setVar() are present
     * and includes the status messages into the data.
     *
     * @param array $data
     */
    public function render($data = array())
    {
        // Determine the correct theme to use
        $theme = ! empty($this->theme) ? $this->theme : config_item('theme.default_theme');
        $this->themer->setTheme($theme);

        // Determine the correct layout to use
        $layout = !empty($this->layout) ? $this->layout : null;
        $this->themer->setLayout($layout);

        // Merge any saved vars into the data
        $data = array_merge($data, $this->vars);

        // Include our UIKit so views can use it
        if (! empty($this->uikit)) {
            $data['uikit'] = $this->uikit;
        }

        // Build our notices from the theme's view file.
        $data['notice'] = $this->themer->display($this->themer->theme() . ':notice', ["notice" => $this->message()]);

        // Make sure any scripts/stylesheets are available to the view
        $data['external_scripts'] = $this->external_scripts;
        $data['stylesheets'] = $this->stylesheets;

        $this->themer->set($data);

        $this->output->set_content_type('html')
                     ->set_output($this->themer->render());
    }

    //--------------------------------------------------------------------

    /**
     * Sets a data variable to be sent to the view during the render() method.
     *
     * @param string $name
     * @param mixed $value
     */
    public function setVar($name, $value = null)
    {
        if (is_array($name)) {
            foreach ($name as $k => $v) {
                $this->vars[$k] = $v;
            }
        } else {
            $this->vars[$name] = $value;
        }
    }

    //--------------------------------------------------------------------

    //--------------------------------------------------------------------
    // Status Messages
    //--------------------------------------------------------------------

    /**
     * Sets a status message (for displaying small success/error messages).
     * This is used in place of the session->flashdata functions since you
     * don't always want to have to refresh the page to show the message.
     *
     * @param string $message The message to save.
     * @param string $type The string to be included as the CSS class of the containing div.
     */
    public function setMessage($message = '', $type = 'info')
    {
        if (! empty($message)) {
            if (isset($this->session)) {
                $this->session->set_flashdata('message', $type . '::' . $message);
            }

            $this->message = array(
                'type' => $type,
                'message' => $message
            );
        }
    }

    //--------------------------------------------------------------------

    /**
     * Retrieves the status message to display (if any).
     *
     * @param  string $message [description]
     * @param  string $type [description]
     * @return array
     */
    public function message($message = '', $type = 'info')
    {
        $return = array(
            'message' => $message,
            'type' => $type
        );

        // Does session data exist?
        if (empty($message) && class_exists('CI_Session')) {
            $message = $this->session->flashdata('message');

            if (! empty($message)) {
                // Split out our message parts
                $temp_message = explode('::', $message);
                $return['type'] = $temp_message[0];
                $return['message'] = $temp_message[1];

                unset($temp_message);
            }
        }

        // If message is empty, we need to check our own storage.
        if (empty($message)) {
            if (empty($this->message['message'])) {
                return '';
            }

            $return = $this->message;
        }

        // Clear our session data so we don't get extra messages on rare occasions.
        if (class_exists('CI_Session')) {
            $this->session->set_flashdata('message', '');
        }

        return $return;
    }

    //--------------------------------------------------------------------

    //--------------------------------------------------------------------
    // Utility Methods
    //--------------------------------------------------------------------

    /**
     * Detects whether the item is being displayed on a desktop, phone,
     * or tablet device.
     */
    protected function detectVariant()
    {
        // Variant Detection and setup
        if (config_item('autodetect_variant') === true) {
            $detect = new \Mobile_Detect();

            if ($detect->isMobile()) {
                $this->template->setVariant('phone');
            } else if ($detect->isTablet()) {
                $this->template->setVariant('tablet');
            }
        }
    }

    //--------------------------------------------------------------------

    //--------------------------------------------------------------------
    // 'Asset' functions
    //--------------------------------------------------------------------

    /**
     * Adds an external javascript file to the 'external_scripts' array.
     *
     * @param [type] $filename [description]
     */
    public function addScript($filename)
    {
        if (strpos($filename, 'http') === FALSE) {
            $filename = base_url() . 'assets/js/' . $filename;
        }

        $this->external_scripts[] = $filename;
    }

    //--------------------------------------------------------------------

    /**
     * Adds an external stylesheet file to the 'stylesheets' array.
     */
    public function addStyle($filename)
    {
        if (strpos($filename, 'http') === FALSE) {
            $filename = base_url() . 'assets/css/' . $filename;
        }

        $this->stylesheets[] = $filename;
    }

    //--------------------------------------------------------------------
}