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

use Myth\Docs\DocSearchInterface;

/**
 * Class docSearch
 *
 * Implements basic search capabilities for Bonfire docs. Includes application,
 * core bonfire, and module docs.
 */
class Search implements DocSearchInterface
{

    /**
     * Minimum characters that can be submitted for a search.
     *
     * @var int
     */
    protected $min_chars = 3;

    /**
     * Maximum characters that can be submitted for a search.
     *
     * @var int
     */
    protected $max_chars = 30;

    /**
     * Valid file extensions we can search in.
     *
     * @var string
     */
    protected $allowed_file_types = 'html|htm|php|php4|php5|txt|md';

    /**
     * Which files should we skip over during our search?
     *
     * @var array
     */
    protected $skip_files = ['.', '..', '_404.md', '_toc.ini'];

    /**
     * How much of each file should we read.
     * Use lower values for faster searches.
     *
     * @var int
     */
    protected $byte_size = 51200;

    /**
     * Number of words long (approximately)
     * the result excerpt should be.
     *
     * @var int
     */
    protected $excerpt_length = 60;

    /**
     * The maximum number of results allowed from a single file.
     *
     * @var int
     */
    protected $max_per_file = 1;

    protected $doc_folders = array();

    protected $formatters = array();

    //--------------------------------------------------------------------

    /**
     * The entry point for performing a search of the documentation.
     *
     * @param null $terms
     * @param array $folders
     *
     * @return array|null
     */
    public function search($terms = null, $folders = [])
    {
        if (empty($terms) || empty($folders)) {
            return null;
        }

        $results = [];
        $this->doc_folders = $folders;

        foreach ($folders as $group => $folder) {
            $results = array_merge($results, $this->searchFolder($terms, $folder, $group));
        }

        return $results;
    }

    //--------------------------------------------------------------------

    //--------------------------------------------------------------------
    // Private Methods
    //--------------------------------------------------------------------


    /**
     * Searches a single directory worth of files.
     *
     * @param $term
     * @param $folder
     * @param $group_name
     *
     * @return array The results.
     */
    protected function searchFolder($term, $folder, $group_name)
    {
        $results = [];

        $map = $this->directory_map($folder, 2);

        $map = $this->flattenMap($map);

        // Make sure we have something to work with.
        if (! is_array($map) || (is_array($map) && ! count($map))) {
            return [];
        }

        // Loop over each file and search the contents for our term.
        foreach ($map as $dir => $file) {
            $file_count = 0;

            if (in_array($file, $this->skip_files)) {
                continue;
            }

            // Is it a folder?
            if (is_array($file) && count($file)) {
                $results = array_merge($results, $this->searchFolder($term, $folder . '/' . $dir, $group_name));
                continue;
            }

            // Make sure it's the right file type...
            if (! preg_match("/({$this->allowed_file_types})/i", $file)) {
                continue;
            }

            $path = is_string($dir) ? $folder . '/' . $dir . '/' . $file : $folder . '/' . $file;
            $term_html = htmlentities($term);

            // Read in the file text
            $handle = fopen($path, 'r');
            $text = fread($handle, $this->byte_size);

            // Do we have a match in here somewhere?
            $found = stristr($text, $term) || stristr($text, $term_html);

            if (! $found) {
                continue;
            }

            // Escape our terms to safely use in a preg_match
            $excerpt = strip_tags($text);
            $term = preg_quote($term);
            $term = str_replace("/", "\/", "{$term}");
            $term_html = preg_quote($term_html);
            $term_html = str_replace("/", "\/", "{$term_html}");

            // Add the item to our results with extracts.
            if (preg_match_all(
                "/((\s\S*){0,3})($term|$term_html)((\s?\S*){0,3})/i",
                $excerpt,
                $matches,
                PREG_OFFSET_CAPTURE | PREG_SET_ORDER
            )) {
                foreach ($matches as $match) {
                    if ($file_count >= $this->max_per_file) {
                        continue;
                    }
                    $result_url = '/docs/' . $group_name . '/' . str_replace('.md', '', $file);

                    foreach ($this->doc_folders as $alias => $folder) {
                        $result_url = str_replace($folder, $alias, $result_url);
                    }

                    $results[] = [
                        'title' => $this->extractTitle($excerpt, $file),
                        'file' => $folder . '/' . $file,
                        'url' => $result_url,
                        'extract' => $this->buildExtract($excerpt, $term, $match[0][0])
                    ];

                    $file_count++;
                }
            }
        }

        return $results;
    }

    //--------------------------------------------------------------------

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
    public function registerFormatter($callback_name = '', $cascade = false)
    {
        if (empty($callback_name)) return;

        $this->formatters[] = array($callback_name => $cascade);

        return $this;
    }

    //--------------------------------------------------------------------

    /**
     * Runs the text through the registered formatters.
     *
     * @param $str
     * @return mixed
     */
    public function format($str)
    {
        if (! is_array($this->formatters)) return $str;

        foreach ($this->formatters as $formatter) {
            $method = key($formatter);
            $cascade = $formatter[$method];

            $str = call_user_func($method, $str);

            if (! $cascade) return $str;
        }

        return $str;
    }

    //--------------------------------------------------------------------


    //--------------------------------------------------------------------
    // Protected Methods
    //--------------------------------------------------------------------

    /**
     * Converts an array generated by directory_map into a flat array of
     * folders, removing any nested folders and adding them to the path.
     *
     * @param $map
     * @param $prefix   Used to recursively add the folder name...
     * @return mixed
     */
    protected function flattenMap($map, $prefix = '')
    {
        if (! is_array($map) || ! count($map)) {
            return $map;
        }

        $return = [];

        foreach ($map as $folder => $files) {

            // If it's a folder name and an array of files
            // then call this method recursively to flatten it out.
            if (is_array($files)) {
                $return = array_merge($return, $this->flattenMap($files, $prefix . $folder));
                continue;
            }

            // Else, add our prefix (if any) to the filename...
            $return[] = $prefix . $files;
        }

        return $return;
    }

    //--------------------------------------------------------------------

    /**
     * Handles extracting the text surrounding our match and basic match formatting.
     *
     * @param $excerpt
     * @param $term
     * @param $match_string
     *
     * @return string
     */
    protected function buildExtract($excerpt, $term, $match_string)
    {
        // Find the character positions within the string that our match was found at.
        // That way we'll know from what positions before and after this we want to grab it in.
        $start_offset = stripos($excerpt, $match_string);

        // Modify the start and end positions based on $this->excerpt_length / 2.
        $buffer = floor($this->excerpt_length / 2);

        // Adjust our start position
        $start_offset = $start_offset - $buffer;
        if ($start_offset < 0) {
            $start_offset = 0;
        }

        $extract = substr($excerpt, $start_offset);

        $extract = strip_tags($this->format($extract));

        $extract = $this->firstXWords($extract, $this->excerpt_length);

        // Wrap the search term in a span we can style.
        $extract = str_ireplace($term, '<span class="term-hilight">' . $term . '</span>', $extract);

        return $extract;
    }

    //--------------------------------------------------------------------

    /**
     * Extracts the title from a bit of markdown formatted text. If it doesn't
     * have an h1 or h2, then it uses the filename.
     *
     * @param $excerpt
     * @param $file
     * @return string
     */
    protected function extractTitle($excerpt, $file)
    {
        $title = '';

        // Easiest to work if this is split into lines.
        $lines = explode("\n", $excerpt);

        if (is_array($lines) && count($lines)) {
            foreach ($lines as $line) {
                if (strpos($line, '# ') === 0 || strpos($line, '## ') === 0) {
                    $title = trim(str_replace('#', '', $line));
                    break;
                }
            }
        }

        // If it's empty, we'll use the filename.
        if (empty($title)) {
            $title = str_replace('_', ' ', $file);
            $title = str_replace('.md', ' ', $title);
            $title = ucwords($title);
        }

        return $title;
    }
    //--------------------------------------------------------------------

    /**
     * Create a Directory Map
     *
     * Reads the specified directory and builds an array
     * representation of it. Sub-folders contained with the
     * directory will be mapped as well.
     *
     * @param    string $source_dir Path to source
     * @param    int $directory_depth Depth of directories to traverse
     *                        (0 = fully recursive, 1 = current dir, etc)
     * @param    bool $hidden Whether to show hidden files
     * @return    array
     */
    protected function directory_map($source_dir, $directory_depth = 0, $hidden = FALSE)
    {
        if ($fp = @opendir($source_dir)) {
            $filedata = array();
            $new_depth = $directory_depth - 1;
            $source_dir = rtrim($source_dir, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR;

            while (FALSE !== ($file = readdir($fp))) {
                // Remove '.', '..', and hidden files [optional]
                if ($file === '.' OR $file === '..' OR ($hidden === FALSE && $file[0] === '.')) {
                    continue;
                }

                is_dir($source_dir . $file) && $file .= DIRECTORY_SEPARATOR;

                if (($directory_depth < 1 OR $new_depth > 0) && is_dir($source_dir . $file)) {
                    $filedata[$file] = $this->directory_map($source_dir . $file, $new_depth, $hidden);
                } else {
                    $filedata[] = $file;
                }
            }

            closedir($fp);
            return $filedata;
        }

        return FALSE;
    }

    //--------------------------------------------------------------------

    /**
     * Gets the first 'X' words of a string.
     *
     * @param $str
     * @param int $wordCount
     * @return string
     */
    protected function firstXWords($str, $wordCount = 10)
    {
        return implode(
            '',
            array_slice(
                preg_split(
                    '/([\s,\.;\?\!]+)/',
                    $str,
                    $wordCount * 2 + 1,
                    PREG_SPLIT_DELIM_CAPTURE
                ),
                0,
                $wordCount * 2 - 1
            )
        );
    }

    //--------------------------------------------------------------------

}
