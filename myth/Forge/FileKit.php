<?php namespace Myth\Forge;

class FileKit {

    /**
     * Appends data to the end of a file.
     *
     * @param $file
     * @param $content
     * @return bool|int
     */
    public function append($file, $content)
    {
        if (empty($content))
        {
            return true;
        }

        // Ensure that $content has a newline at the end
        $content = rtrim($content) ."\n";

        $fh = fopen($file, 'a');
        $result = fwrite($fh, $content);
        fclose($fh);

        return $result;
    }

    //--------------------------------------------------------------------

    /**
     * Prepends string content to a file. For very large files
     * this method could have memory issues, but the primary usage
     * of source files shouldn't ever get large enough to cause issues.
     *
     * @param $file
     * @param $content
     * @return bool|int
     */
    public function prepend($file, $content)
    {
        if (empty($content))
        {
            return true;
        }

        // Ensure that $content has a newline at the end
        $content = rtrim($content) ."\n";

        $file_contents = file_get_contents($file);

        if ($file_contents === false)
        {
            throw new \RuntimeException("Unable to read from file: {$file}");
        }

        $result = file_put_contents($file, $content . $file_contents);

        return (bool)$result;
    }

    //--------------------------------------------------------------------

    /**
     * Inserts $content before the line that matches $before. NOT case-
     * sensitive.
     *
     * @param $file
     * @param $before
     * @param $content
     * @return int
     */
    public function before($file, $before, $content)
    {
        if (empty($content))
        {
            return true;
        }

        // Ensure that $content has a newline at the end
        $content = rtrim($content) ."\n";

        $lines = file($file);

        if ($lines === false)
        {
            throw new \RuntimeException("File not found: {$file}");
        }

        // Where to insert the row.
        $location = null;

        foreach ($lines as $index => $line)
        {
            if (strtolower($line) == strtolower($before) )
            {
                $location = $index;
                break;
            }
        }

        array_splice($lines, $location, 0, $content);

        $result = file_put_contents($file, $lines);

        return (bool)$result;
    }

    //--------------------------------------------------------------------

    public function after($file, $after, $content)
    {
        if (empty($content))
        {
            return true;
        }

        // Ensure that $content has a newline at the end
        $content = rtrim($content) ."\n";

        $lines = file($file);

        if ($lines === false)
        {
            throw new \RuntimeException("File not found: {$file}");
        }

        // Where to insert the row.
        $location = null;

        foreach ($lines as $index => $line)
        {
            if (strtolower($line) == strtolower($after) )
            {
                $location = $index;
                break;
            }
        }

        array_splice($lines, $location +1, 0, $content);

        $result = file_put_contents($file, $lines);

        return (bool)$result;
    }

    //--------------------------------------------------------------------

    /**
     * Replaces all instances of $search in the file with $replace.
     *
     * @param $file
     * @param $search
     * @param $replace
     * @return int
     */
    public function replaceIn($file, $search, $replace)
    {
        $file_contents = file_get_contents($file);

        if ($file_contents === false)
        {
            throw new \RuntimeException("Unable to read from file: {$file}");
        }

        $file_contents = str_replace($search, $replace, $file_contents);

        $result = file_put_contents($file, $file_contents);

        return (bool)$result;
    }

    //--------------------------------------------------------------------

    /**
     * Uses preg_replace to replace content within the file.
     *
     * @param $file
     * @param $pattern
     * @param $replace
     * @return int
     */
    public function replaceWithRegex($file, $pattern, $replace)
    {
        $file_contents = file_get_contents($file);

        if ($file_contents === false)
        {
            throw new \RuntimeException("Unable to read from file: {$file}");
        }

        $file_contents = preg_replace($pattern, $replace, $file_contents);

        $result = file_put_contents($file, $file_contents);

        return (bool)$result;
    }

    //--------------------------------------------------------------------


}