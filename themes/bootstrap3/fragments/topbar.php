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
            <a class="navbar-brand" href="<?= site_url('tests/layouts') ?>">SprintPHP Samples</a>

                <ul class="nav navbar-nav navbar-right">
                    <li <?= $this->uri->segment(2) == 'callbacks' ? 'class="active"' : '' ?>>
                        <a href="<?= site_url('tests/callbacks') ?>">Callbacks</a>
                    </li>

                    <li class="dropdown">
                        <a href="#" class="dropdown-toggle" data-toggle="dropdown">Themes <span class="caret"></span></a>
                        <ul class="dropdown-menu" role="menu">
                            <li <?= (isset($theme) && $theme == 'bootstrap') || empty($theme) ? 'class="active"' : '' ?>>
                                <a href="<?= site_url('tests/layouts/set_theme/bootstrap') ?>">Bootstrap 3</a>
                            </li>
                            <li <?= isset($theme) && $theme == 'foundation' ? 'class="active"' : '' ?>>
                                <a href="<?= site_url('tests/layouts/set_theme/foundation') ?>">Foundation 5</a>
                            </li>
                        </ul>
                    </li>

                    <li class="dropdown">
                        <a href="#" class="dropdown-toggle" data-toggle="dropdown">Layouts <span class="caret"></span></a>
                        <ul class="dropdown-menu" role="menu">
                            <li <?= isset($layout) && $layout == 'index' || ! isset($layout) ? 'class="active"' : '' ?>>
                                <a href="<?= site_url('tests/layouts/set_layout/index') ?>">Default</a>
                            </li>
                            <li <?= isset($layout) && $layout == 'full_single' ? 'class="active"' : '' ?>>
                                <a href="<?= site_url('tests/layouts/set_layout/full_single') ?>">Full-width 1-column</a>
                            </li>
                            <li <?= isset($layout) && $layout == 'two_left_fixed' ? 'class="active"' : '' ?>>
                                <a href="<?= site_url('tests/layouts/set_layout/two_left_fixed') ?>">2-Columns, Left Sidebar, Fixed Width</a>
                            </li>
                            <li <?= isset($layout) && $layout == 'two_right_fixed' ? 'class="active"' : '' ?>>
                                <a href="<?= site_url('tests/layouts/set_layout/two_right_fixed') ?>">2-Columns, Right Sidebar, Fixed Width</a>
                            </li>
                            <li <?= isset($layout) && $layout == 'two_left_full' ? 'class="active"' : '' ?>>
                                <a href="<?= site_url('tests/layouts/set_layout/two_left_full') ?>">2-Columns, Left Sidebar, Full Width</a>
                            </li>
                            <li <?= isset($layout) && $layout == 'two_right_full' ? 'class="active"' : '' ?>>
                                <a href="<?= site_url('tests/layouts/set_layout/two_right_full') ?>">2-Columns, Right Sidebar, Full Width</a>
                            </li>
                            <li <?= isset($layout) && $layout == 'dashboard' ? 'class="active"' : '' ?>>
                                <a href="<?= site_url('tests/layouts/set_layout/dashboard') ?>">Dashboard</a>
                            </li>
                            <li <?= isset($layout) && $layout == 'login' ? 'class="active"' : '' ?>>
                                <a href="<?= site_url('tests/layouts/set_layout/login') ?>">Login Page</a>
                            </li>
                        </ul>
                    </li>
                </ul>
        </div>

    </div>
</header>