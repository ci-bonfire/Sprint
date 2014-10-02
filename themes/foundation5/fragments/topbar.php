<nav class="top-bar" data-topbar role="navigation">
    <ul class="title-area">
        <li class="name">
            <h1><a href="<?= site_url('tests/layouts') ?>">SprintPHP</a></h1>
        </li>
        <!-- Remove the class "menu-icon" to get rid of menu icon. Take out "Menu" to just have icon alone -->
        <li class="toggle-topbar menu-icon"><a href="#"><span>Menu</span></a></li>
    </ul>

    <section class="top-bar-section">
        <!-- Right Nav Section -->
        <ul class="right">
            <li class="has-dropdown">
                <a href="#">Themes</a>
                <ul class="dropdown">
                    <li <?= $this->session->userdata('theme') == 'bootstrap' ? 'class="active"' : '' ?>>
                        <a href="<?= site_url('tests/layouts/set_theme/bootstrap') ?>">Bootstrap 3</a>
                    </li>
                    <li <?= $this->session->userdata('theme') == 'foundation' ? 'class="active"' : '' ?>>
                        <a href="<?= site_url('tests/layouts/set_theme/foundation') ?>">Foundation 5</a>
                    </li>
                </ul>
            </li>

            <li class="has-dropdown">
                <a href="#">Layouts</a>
                <ul class="dropdown">
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

    </section>
</nav>
