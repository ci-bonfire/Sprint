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

class LocalizedViewThemer extends  ViewThemer
{

    //--------------------------------------------------------------------
    // Protected Methods
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
    protected function loadView($view, $data)
    {
        // First - does it exist in the current theme?
        $theme = ! empty($this->active_theme) ? $this->active_theme : $this->default_theme;
        $theme = ! empty($this->colon_theme) ? $this->colon_theme : $theme;
        $theme = ! empty($this->folders[$theme]) ? $this->folders[$theme] : $theme;
        $theme = rtrim($theme, '/ ') .'/';
        
        // First check if I18n idiom is set and add it to theme
        if (defined('CURRENT_LANGUAGE') && file_exists($theme . CURRENT_LANGUAGE ."/{$view}.php"))
        {
            $output = $this->ci->load->view_path( $theme . CURRENT_LANGUAGE . '/' . $view, $data, TRUE );
        }
        
        // Next normal check
        else if (file_exists($theme ."{$view}.php"))
        {
            $output = $this->ci->load->view_path( $theme . $view, $data, TRUE );
        }

        // Next, if it's a real file with path, then load it
        elseif ( realpath( $view . '.php' ) )
        {
            $output = $this->ci->load->view_path( $view, $data, TRUE );
        }

        // Otherwise, treat it as a standard view, which means
        // application/views will override any modules (See HMVC/Loader) 
        // but with checking of lang idiom.
        else
        {
            $output = $this->ci->load->view_path_with_modules( $view, $data, TRUE );
        }

        return $output;
    }

    //--------------------------------------------------------------------

}
