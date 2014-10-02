<?php

class Layouts extends \Myth\Controllers\ThemedController {

    protected $theme = 'bootstrap';

    //--------------------------------------------------------------------

    public function __construct()
    {
        parent::__construct();

        $this->load->driver('session');
    }

    //--------------------------------------------------------------------

    public function index()
    {
        $layout = $this->session->userdata('layout') ? $this->session->userdata('layout') : 'index';
        $theme = $this->session->userdata('layout') ? $this->session->userdata('theme') : 'bootstrap';

        $this->layout = $layout;
        $this->theme = $theme;

        if ($theme == 'foundation')
        {
            $this->addStyle( site_url('themes/foundation5/css/theme.css') );
        }

        $this->setMessage('Status messages would go here. Luckily everything is great, now!', 'success');

        $this->setVar('layout_title', str_replace('_', ' ', $layout));
        $this->setVar('layout_desc', $this->describe($layout));
        $this->setVar('theme', $this->theme);
        $this->render();
    }

    //--------------------------------------------------------------------

    public function set_layout($layout)
    {
        $this->session->set_userdata('layout', $layout);

        redirect('tests/layouts');
    }

    //--------------------------------------------------------------------

    public function set_theme($theme)
    {
        $this->session->set_userdata('theme', $theme);

        redirect('tests/layouts');
    }

    //--------------------------------------------------------------------

    private function describe($layout)
    {
        $description = '';

        $this->setVar('navbar_style', 'navbar-static-top');

        switch ($layout)
        {
            case 'index':
                $description = 'A basic, single-column, fixed-width layout.';
                $this->setVar('containerClass', 'container');
                break;
            case 'full_single':
                $description = 'A full-width, single-column layout.';
                $this->setVar('containerClass', 'container-fluid');
                break;
            case 'two_left_fixed':
                $description = 'A fixed-width, 2-column layout with sidebar on the left.';
                $this->setVar('containerClass', 'container');
                $this->layout = 'two_left';
                break;
            case 'two_right_fixed':
                $description = 'A fixed-width, 2-column layout with sidebar on the right.';
                $this->setVar('containerClass', 'container');
                $this->layout = 'two_right';
                break;
            case 'two_left_full':
                $description = 'A full-width, 2-column layout with sidebar on the left.';
                $this->setVar('containerClass', 'container-fluid');
                $this->layout = 'two_left';
                break;
            case 'two_right_full':
                $description = 'A full-width, 2-column layout with sidebar on the right.';
                $this->setVar('containerClass', 'container-fluid');
                $this->layout = 'two_right';
                break;
            case 'dashboard':
                $description = 'A full-width, two-column layout perfect for dashboards.';
                $this->setVar('containerClass', 'container-fluid');
                $this->setVar('navbar_style', 'navbar-fixed-top');

                $theme = $this->theme == 'bootstrap' ? 'bootstrap3' : 'foundation5';
                $this->addStyle( site_url("themes/{$theme}/css/dashboard.css") );
                break;
            case 'login':
                $description = 'A fixed-width page perfect for login.';
                $this->setVar('containerClass', 'container');
                $this->layout = 'login';
                break;
            default:
                $description = 'Some kind of layout, surely.';
                $this->setVar('containerClass', 'container');
        }

        return $description;
    }

    //--------------------------------------------------------------------

}