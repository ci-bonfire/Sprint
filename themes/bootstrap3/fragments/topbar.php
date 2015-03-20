<header class="navbar navbar-inverse navbar-static" role="banner">
    <div class="container">

        <div class="navbar-header">
            <button type="button" class="navbar-toggle" data-toggle="collapse" data-target="#main-nav-collapse">
                <span class="sr-only">Toggle navigation</span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
            </button>
        </div>


        <div class="collapse navbar-collapse" id="main-nav-collapse">
            <a class="navbar-brand" href="<?= site_url() ?>">SprintPHP</a>

                <ul class="nav navbar-nav navbar-right">
                    <li>
                        <?php if (! empty($_SESSION['logged_in']) ) : ?>
                            <a href="<?= site_url( \Myth\Route::named('logout') ) ?>">Logout</a>
                        <?php else : ?>
                            <a href="<?= site_url( \Myth\Route::named('login') ) ?>">Login</a>
                        <?php endif; ?>
                    </li>
                </ul>
        </div>

    </div>
</header>