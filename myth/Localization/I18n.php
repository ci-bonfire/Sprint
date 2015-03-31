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
			define("CURRENT_LANGUAGE", $languages[$lang_segment]);
			$this->config->set_item('language', CURRENT_LANGUAGE);
            // Remove that element from the URI segments.
            array_shift($segments);
		}

        return $segments;
	}

    //--------------------------------------------------------------------

}
