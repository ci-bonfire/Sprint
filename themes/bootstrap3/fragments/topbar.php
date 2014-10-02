<header class="navbar navbar-inverse <?= $navbar_style ?>" role="banner">
    <div class="<?= $containerClass ?>">

        <div class="navbar-header">
            <button type="button" class="navbar-toggle" data-toggle="collapse" data-target="#main-nav-collapse">
                <span class="sr-only">Toggle navigation</span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
            </button>
        </div>


        <div class="collapse navbar-collapse" id="main-nav-collapse">
            <a class="navbar-brand" href="<?= site_url('tests/layouts') ?>">SprintPHP</a>

                <ul class="nav navbar-nav navbar-right">
                    <li class="dropdown">
                        <a href="#" class="dropdown-toggle" data-toggle="dropdown">Themes <span class="caret"></span></a>
                        <ul class="dropdown-menu" role="menu">
                            <li <?= $this->session->userdata('theme') == 'bootstrap' ? 'class="active"' : '' ?>>
                                <a href="<?= site_url('tests/layouts/set_theme/bootstrap') ?>">Bootstrap 3</a>
                            </li>
                            <li <?= $this->session->userdata('theme') == 'foundation' ? 'class="active"' : '' ?>>
                                <a href="<?= site_url('tests/layouts/set_theme/foundation') ?>">Foundation 5</a>
                            </li>
                        </ul>
                    </li>

                    <li class="dropdown">
                        <a href="#" class="dropdown-toggle" data-toggle="dropdown">Layouts <span class="caret"></span></a>
                        <ul class="dropdown-menu" role="menu">
                            <li <?= $this->session->userdata('layout') == 'index' || ! $this->session->userdata('layout') ? 'class="active"' : '' ?>>
                                <a href="<?= site_url('tests/layouts/set_layout/index') ?>">Default</a>
                            </li>
                            <li <?= $this->session->userdata('layout') == 'full_single' ? 'class="active"' : '' ?>>
                                <a href="<?= site_url('tests/layouts/set_layout/full_single') ?>">Full-width 1-column</a>
                            </li>
                            <li <?= $this->session->userdata('layout') == 'two_left_fixed' ? 'class="active"' : '' ?>>
                                <a href="<?= site_url('tests/layouts/set_layout/two_left_fixed') ?>">2-Columns, Left Sidebar, Fixed Width</a>
                            </li>
                            <li <?= $this->session->userdata('layout') == 'two_right_fixed' ? 'class="active"' : '' ?>>
                                <a href="<?= site_url('tests/layouts/set_layout/two_right_fixed') ?>">2-Columns, Right Sidebar, Fixed Width</a>
                            </li>
                            <li <?= $this->session->userdata('layout') == 'two_left_full' ? 'class="active"' : '' ?>>
                                <a href="<?= site_url('tests/layouts/set_layout/two_left_full') ?>">2-Columns, Left Sidebar, Full Width</a>
                            </li>
                            <li <?= $this->session->userdata('layout') == 'two_right_full' ? 'class="active"' : '' ?>>
                                <a href="<?= site_url('tests/layouts/set_layout/two_right_full') ?>">2-Columns, Right Sidebar, Full Width</a>
                            </li>
                            <li <?= $this->session->userdata('layout') == 'dashboard' ? 'class="active"' : '' ?>>
                                <a href="<?= site_url('tests/layouts/set_layout/dashboard') ?>">Dashboard</a>
                            </li>
                            <li <?= $this->session->userdata('layout') == 'login' ? 'class="active"' : '' ?>>
                                <a href="<?= site_url('tests/layouts/set_layout/login') ?>">Login Page</a>
                            </li>
                        </ul>
                    </li>
                </ul>
        </div>

    </div>
</header>