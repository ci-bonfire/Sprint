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
    
    /**
     * Does the same thing that load->view does but also checks lang idoms
     * and modules view paths.
     *
     * @param $view
     * @param array $vars
     * @param bool $return
     * @return object|void
     */
    public function view_path_with_modules($view, $vars = array(), $return = FALSE)
    {
        // Checking APPATH/views/{lang_idiom}/
        if (defined('CURRENT_LANGUAGE') && file_exists(APPPATH .'views/'. CURRENT_LANGUAGE . "/{$view}.php"))
        {
            return parent::view(CURRENT_LANGUAGE . '/'. $view, $vars, $return);
        }
        
        // Checking APPATH/views/
        if (file_exists(APPPATH .'views/'. $view .'.php'))
        {
            return parent::view($view, $vars, $return);
        }
        
        // Checking modules
        if(list($module, $class) = $this->detect_module($view))
        {
            $view_paths = $this->_ci_view_paths;
            // Checking if module was added
            if (!in_array($module, $this->_ci_modules)) 
            {
                $this->add_module($module);
                $view_paths = $this->_ci_view_paths;
                $this->remove_module();
            }
            
            foreach($view_paths as $path => $value)
            {
                // Checking {module}/views/{lang_idiom}/            
                if(defined('CURRENT_LANGUAGE') && file_exists($path. CURRENT_LANGUAGE . '/' . $class .'.php')) return $this->_ci_load(array('_ci_path' => $path. CURRENT_LANGUAGE . '/' . $class .'.php', '_ci_vars' => $this->_ci_object_to_array($vars), '_ci_return' => $return));
                // Checking {module}/views/
                if(file_exists($path. $class .'.php')) return $this->_ci_load(array('_ci_path' => $path. $class .'.php', '_ci_vars' => $this->_ci_object_to_array($vars), '_ci_return' => $return));
            }
        }
        
        // Else leave as default
        return parent::view($view, $vars, $return);
    }

    // --------------------------------------------------------------------

}
