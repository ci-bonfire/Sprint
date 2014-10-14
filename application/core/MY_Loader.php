<?php

require_once APPPATH .'third_party/HMVC/Loader.php';

class MY_Loader extends HMVC_Loader {

    /**
     * Does the same thing that load->view does except ensures that the
     * view file is treated as a path so that it can be found outside of
     * the standard view paths.
     *
     * @param $view
     * @param array $vars
     * @param bool $return
     * @return object|void
     */
    public function view_path($view, $vars = array(), $return = FALSE)
    {
        $view .= '.php';

        // If the file can't be found, then use the regular view method...
        if (file_exists($view)) {
            return $this->_ci_load(array('_ci_path' => $view, '_ci_vars' => $this->_ci_object_to_array($vars), '_ci_return' => $return));
        }
        else {
            return $this->_ci_load(array('_ci_view' => $view, '_ci_vars' => $this->_ci_object_to_array($vars), '_ci_return' => $return));
        }
    }

    // --------------------------------------------------------------------
}