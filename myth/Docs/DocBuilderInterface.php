<?php namespace Myth\Docs;
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
 * Class DocBuilder
 *
 * Handles the brunt of building documentation from Markdown formatted files.
 *
 * @category Documentation
 * @author   Lonnie Ezell <lonnie@newmythmedia.com>
 */
interface DocBuilderInterface
{
    /**
     * Does the actual work of reading in and parsing the help file.
     * If a folder Nickname (see addDocFolder() ) is passed as the second parameter,
     * it will limit it's search to that single folder. If nothing is passed, it will
     * search through all of the folders in the order they were given to the library,
     * until it finds the first one.
     *
     * @param string $path The 'path' of the file (relative to the docs
     *                                 folder. Usually from the URI)
     * @param string $restrictToFolder (Optional) The folder nickname
     *
     * @return string
     */
    public function readPage($path, $restrictToFolder = null);

    /**
     * Parses the contents. Currently runs through the Markdown Extended
     * parser to convert to HTML.
     *
     * @param $str
     * @return mixed
     */
    public function parse($str);

    /**
     * Perform a few housekeeping tasks on a page, like rewriting URLs to full
     * URLs, not relative, ensuring they link correctly, etc.
     *
     * @param      $content
     * @param null $site_url
     * @param null $current_url
     * @return string   The post-processed HTML.
     */
    public function postProcess($content, $site_url = null, $current_url = null);

    /**
     * Allows users to define the classes that are attached to
     * generated tables.
     *
     * @param null $classes
     * @return $this
     */
    public function setTableClasses($classes = null);

    /**
     * Given the contents to render, will build a list of links for the sidebar
     * out of the headings in the file.
     *
     * Note: Will ONLY use h2 and h3 to build the links from.
     *
     * Note: The $content passed in WILL be modified by adding named anchors
     * that match up with the locations.
     *
     * @param string $content The HTML to analyse for headings.
     * @return string
     */
    public function buildDocumentMap(&$content);

    /**
     * Stores the name of the callback method to run to convert the source
     * files to viewable files. By default, this should be used to register
     * a Mardown Extended formatter with the system, but could be used to
     * extend the
     *
     * @param string $callback_name
     * @param bool $cascade // If FALSE the formatting of a component ends here. If TRUE, will be passed to next formatter.
     * @return $this
     */
    public function registerFormatter($callback_name = '', $cascade = false);

    /**
     * Runs the text through the registered formatters.
     *
     * @param $str
     * @return mixed
     */
    public function format($str);

    /**
     * Retrieves the list of files in a folder and preps the name and filename
     * so it's ready for creating the HTML.
     *
     * @param  String $folder The path to the folder to retrieve.
     *
     * @return Array  An associative array @see parse_ini_file for format
     * details.
     */
    public function buildTOC($folder);

    /**
     * Returns the current docFolders array.
     *
     * @return array
     */
    public function docFolders();

    /**
     * Registers a path to be used when searching for documentation files.
     *
     * @param $name     A nickname to reference it by later.
     * @param $path     The server path to the folder.
     * @return $this
     */
    public function addDocFolder($name, $path);

    /**
     * Removes a folder from the folders we scan for documentation files
     * within.
     *
     * @param $name
     * @return $this
     */
    public function removeDocFolder($name);
}