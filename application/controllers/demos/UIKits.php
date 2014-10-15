<?php

class UIKits extends \Myth\Controllers\ThemedController {

    protected $uikit = null;
    protected $uikit_name = null;

    //--------------------------------------------------------------------

    public function __construct($uikit='Bootstrap3')
    {
        // Determine the UIKit to use based on the $_GET['uikit'] variable.
        if (isset($_GET['uikit']))
        {
            switch ($_GET['uikit'])
            {
                case 'Bootstrap3':
                    $this->uikit_name = 'Bootstrap3';
                    $this->uikit = new \Myth\UIKits\Bootstrap();
                    break;
                case 'Foundation5':
                    $this->uikit_name = 'Foundation5';
                    $this->uikit = new \Myth\UIKits\Foundation();
                    break;
            }
        }

        parent::__construct();
    }

    //--------------------------------------------------------------------

    public function index()
    {
        switch ($this->uikit_name)
        {
            case 'Bootstrap3':
                $data['uikit'] = new \Myth\UIKits\Bootstrap();
                $data['stylesheet'] = 'http://maxcdn.bootstrapcdn.com/bootstrap/3.2.0/css/bootstrap.min.css';
                $data['scripts'] = '<script type="text/javascript" src="http://maxcdn.bootstrapcdn.com/bootstrap/3.2.0/js/bootstrap.min.js"></script><script type="text/javascript">$(".dropdown-toggle").dropdown();</script>';
                break;
            case 'Foundation5':
                $data['uikit'] = new \Myth\UIKits\Foundation();
                $data['stylesheet'] = '//cdnjs.cloudflare.com/ajax/libs/foundation/5.4.6/css/foundation.min.css';
                $data['scripts'] = '<script type="text/javascript" src="//cdnjs.cloudflare.com/ajax/libs/foundation/5.4.6/js/foundation.min.js"></script><script type="javascript">$(document).foundation();</script>';
                break;
        }

        $this->load->view('demos/uikits/index', $data);
    }

    //--------------------------------------------------------------------
    
    
}