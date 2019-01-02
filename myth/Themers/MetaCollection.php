<?php namespace Myth\Themers;

require_once dirname(__FILE__) .'/escape.php';

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
 * Class MetaCollection
 *
 * @package Myth\Themers
 */
class MetaCollection implements MetaInterface {

    /**
     * Stores the meta values the user has set.
     *
     * @var array
     */
    protected $meta = [];

    /**
     * Stores the standard meta-value names
     * Mostly here for reference, I guess.
     *
     * @var array
     */
    protected $std_meta = [
        'application-name',
        'author',
        'copyright',
        'description',
        'generator',
        'keywords',
        'robots',
        'googlebot'
    ];

    /**
     * Stores the HTTP-Equiv meta tags
     * since they need to be rendered differently.
     *
     * @var array
     */
    protected $http_equiv_meta = [
        'cache-control',
        'content-language',
        'content-type',
        'default-style',
        'expires',
        'pragma',
        'refresh',
        'set-cookie'
    ];

    /**
     * Stores the document's character encoding.
     *
     * @var string
     */
    public $charset = 'utf-8';

    //--------------------------------------------------------------------

    public function __construct($ci)
    {
        $ci->config->load('html_meta', true);

        $config = $ci->config->item('html_meta');

        $this->meta = $config['meta'];

        $this->http_equiv_meta = array_merge($this->http_equiv_meta, $config['http-equiv']);
    }

    //--------------------------------------------------------------------


    /**
     * Sets a single meta item.
     * $alias can also be an array of key/value pairs to set.
     *
     * @param string|array $alias
     * @param null $value
     *
     * @return mixed
     */
    public function set($alias, $value=null, $escape=true)
    {
        if (is_array($alias))
        {
            foreach ($alias as $key => $val)
            {
                $this->set($key, $val);
            }

            return $this;
        }

        // Charset
        if (strtolower($alias) == 'charset')
        {
            $this->charset = $value;

            return $this;
        }

        $this->meta[ strtolower($alias) ] = $escape ? esc($value, 'htmlAttr') : $value;

        return $this;
    }

    //--------------------------------------------------------------------

    /**
     * Returns a single meta item.
     *
     * @param $alias
     *
     * @return mixed
     */
    public function get($alias)
    {
        $alias = strtolower($alias);

        return isset($this->meta[ $alias ]) ? $this->meta[$alias] : null;
    }

    //--------------------------------------------------------------------

    /**
     * Renders out all defined meta tags.
     *
     * @return mixed
     */
    public function renderTags()
    {
        if (! count($this->meta))
        {
            return null;
        }

        $output = '';

        // Character Encoding
        $output .= "\t<meta charset=\"{$this->charset}\" >";

        // Everything else
        foreach ($this->meta as $name => $content)
        {
            if (is_array($content))
            {
                $content = implode(',', $content);
            }

            if (empty($content))
            {
                continue;
            }

            // Http Equivalent meta tags.
            if (in_array($name, $this->http_equiv_meta))
            {
                $output .= "\t<meta http-equiv=\"{$name}\" content=\"{$content}\">\n";
            }
            // Standard Meta Tag
            else {
                $output .= "\t<meta name=\"{$name}\" content=\"{$content}\">\n";
            }
        }

        return $output;
    }

    //--------------------------------------------------------------------

    /**
     * Registers a new HTTP Equivalent meta tag so it can be
     * rendered out properly.
     *
     * @param $name
     *
     * @return $this
     */
    public function registerHTTPEquivTag($name)
    {
        if (is_null($name))
        {
            return $this;
        }

        $this->http_equiv_meta[] = strtolower($name);

        return $this;
    }

    //--------------------------------------------------------------------


    /**
     * Convenience implementation to set a value
     * as if it was a property of the class.
     *
     * @param $alias
     * @param null $value
     */
    public function __set($alias, $value=null)
    {
        $this->set($alias, $value);
    }

    //--------------------------------------------------------------------

    /**
     * Convenience method to access a value
     * as if it was a property of the class.
     *
     * @param $alias
     *
     * @return mixed
     */
    public function __get($alias)
    {
        return $this->get($alias);
    }

    //--------------------------------------------------------------------



}