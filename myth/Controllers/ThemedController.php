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
use Myth\Themers\MetaCollection;
use Zend\Escaper\Escaper;

require_once dirname(__FILE__) .'/../Themers/escape.php';

/**
 * Class ThemedController
 *
 * @package Myth\Controllers
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

    /**
     * A MenuCollection instance
     * @var
     */
    protected $meta;

    /**
     * Whether set() should escape the output...
     * @var bool
     */
    protected $auto_escape = null;

    /**
     * An instance of ZendFrameworks Escaper
     * @var null
     */
    protected $escaper = null;

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
            throw new \RuntimeException( lang('no_themer') );
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

        // Load up our meta collection
        $this->meta = new MetaCollection( get_instance() );

        // Should we autoescape vars?
        if (is_null($this->auto_escape))
        {
            $this->auto_escape = config_item( 'theme.auto_escape' );
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
     * @param int   $cache_time
     */
    public function render($data = array(), $cache_time=0)
    {
	    if ($cache_time > 0)
	    {
		    $this->output->cache( (int)$cache_time );
	    }

        // Determine the correct theme to use
        $theme = ! empty($this->theme) ? $this->theme : config_item('theme.default_theme');
        $this->themer->setTheme($theme);

        // Determine the correct layout to use
        $layout = !empty($this->layout) ? $this->layout : null;
        $this->themer->setLayout($layout);

        // Merge any saved vars into the data
        // But first, escape the data if needed
        if ($this->auto_escape)
        {
            $data = esc($data, 'html');
        }
        $data = array_merge($data, $this->vars);

        // Make sure the MetaCollection is available in the view.
        $data['html_meta'] = $this->meta;

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
     * Will auto-escape data on the way in, unless specifically told not to.
     *
     * Uses ZendFramework's Escaper to handle the data escaping,
     * based on context. Valid contexts are:
     *      - html
     *      - htmlAttr
     *      - js
     *      - css
     *      - url
     *
     * @param string $name
     * @param mixed $value
     * @param string $context
     * @param bool $do_escape
     */
    public function setVar($name, $value = null, $context='html', $do_escape=null)
    {
        $escape = $do_escape == true ? true : $this->auto_escape;

        if (is_null($this->escaper))
        {
            $this->escaper = new Escaper(config_item('charset'));
        }

        if (is_array($name))
        {
            foreach ($name as $k => $v)
            {
                $this->vars[$k] = $escape ? esc($v, $context, $this->escaper) : $v;
            }
        }
        else
        {
            $this->vars[$name] = $escape ? esc($value, $context, $this->escaper) : $value;
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

