<?php namespace Myth\Localization;
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

class I18n {

    /**
     * An instance of CI_Config.
     *
     * @var
     */
    protected $config;

    /**
     * Stores the current ISO code,
     * if a match was found.
     *
     * @var null
     */
    protected $iso_code = null;

    //--------------------------------------------------------------------

    public function __construct()
    {
        $this->config =& load_class('Config', 'core');
    }

    //--------------------------------------------------------------------

	/**
     * Compares the first element in $segments against
     * the array of allowed languages in config_item 'i18n.languages'
     * to determine the language the site is being requested in.
     *
	 * Set CURRENT_LANGUAGE constant, if found.
	 *
	 * @param 	array	&$segments
     *
	 * @return	void
	 */
	public function setLanguage($segments)
	{
        if (empty($segments[1]))
        {
            return $segments;
        }

		// Getting languages array from config
		$languages = $this->config->item('i18n.languages');

		$lang_segment = $segments[1];

        if (array_key_exists($lang_segment, $languages) )
		{
            if (! defined('CURRENT_LANGUAGE'))
            {
                define( 'CURRENT_LANGUAGE', $languages[ $lang_segment ] );
            }
			$this->config->set_item('language', CURRENT_LANGUAGE);

            // Store the ISO code for later use
            $this->iso_code = $lang_segment;

			// Reset indexes of segments array, now index starts at 0
			$segments = array_values($segments); 
			// Remove ISO code from the URI segments
			unset($segments[0]);
		}

        return $segments;
	}

    //--------------------------------------------------------------------

    /**
     * MUST be ran AFTER setLanguage() and removes the locale code
     * from the URI string.
     *
     * @param $str
     *
     * @return mixed
     */
    public function cleanURIString($str)
    {
        // If iso_code is empty, then there
        // is no current language set.
        if (empty($this->iso_code))
        {
            return $str;
        }

        if (strpos($str, $this->iso_code) === 0)
        {
            $str = substr($str, strlen($this->iso_code));
            $str = ltrim($str, '/');
        }

        return $str;
    }

    //--------------------------------------------------------------------


}
