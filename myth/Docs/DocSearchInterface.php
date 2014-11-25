<?php

namespace Myth\Docs;

interface DocSearchInterface {

    /**
     * The entry point for performing a search of the documentation.
     *
     * @param null  $terms
     * @param array $folders
     *
     * @return array|null
     */
    public function search($terms = null, $folders = []);

    //--------------------------------------------------------------------

    /**
     * Stores the name of the callback method to run to convert the source
     * files to viewable files. By default, this should be used to register
     * a Mardown Extended formatter with the system, but could be used to
     * extend the
     *
     * @param string $callback_name
     * @param bool   $cascade       // If FALSE the formatting of a component ends here. If TRUE, will be passed to next formatter.
     * @return $this
     */
    public function registerFormatter($callback_name='', $cascade=false);

    //--------------------------------------------------------------------

    /**
     * Runs the text through the registered formatters.
     *
     * @param $str
     * @return mixed
     */
    public function format($str);

    //--------------------------------------------------------------------

}