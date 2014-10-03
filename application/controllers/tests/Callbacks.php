<?php

class Callbacks extends \Myth\Controllers\ThemedController {

    public function index()
    {
        $this->setVar('navbar_style', 'navbar-static');
        $this->setVar('containerClass', 'container');
        $this->render();
    }

    //--------------------------------------------------------------------


}