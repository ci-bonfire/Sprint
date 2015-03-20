<nav class="top-bar" data-topbar role="navigation">
    <ul class="title-area">
        <li class="name">
            <h1><a href="<?= site_url() ?>">SprintPHP</a></h1>
        </li>
        <!-- Remove the class "menu-icon" to get rid of menu icon. Take out "Menu" to just have icon alone -->
        <li class="toggle-topbar menu-icon"><a href="#"><span>Menu</span></a></li>
    </ul>

    <section class="top-bar-section">
        <!-- Right Nav Section -->
        <ul class="right">
            <li>
                <?php if (! empty($_SESSION['logged_in']) ) : ?>
                    <a href="<?= site_url( \Myth\Route::named('logout') ) ?>">Logout</a>
                <?php else : ?>
                    <a href="<?= site_url( \Myth\Route::named('login') ) ?>">Login</a>
                <?php endif; ?>
            </li>
        </ul>

    </section>
</nav>
