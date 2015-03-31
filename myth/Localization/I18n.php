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
	 * Set CURRENT_LANGUAGE constant, shift language from Uri segments
	 *
	 * @param 	array	&$segments
	 * @return	void
	 */
	public function setLanguage(&$segments)
	{
		// Load config/application.php
		$this->config->load('application');
		// Getting i18n
		$i18n = $this->config->item('i18n');
		// Getting languages array from config
		$languages = $this->config->item('i18n.languages');
		
		$lang_segment = $segments[1];		
		if($i18n && array_key_exists($lang_segment, $languages))
		{
			define("CURRENT_LANGUAGE", $languages[$lang_segment]);
			$this->config->set_item('language', CURRENT_LANGUAGE);
			array_shift($segments);
		}
	}
   
}
