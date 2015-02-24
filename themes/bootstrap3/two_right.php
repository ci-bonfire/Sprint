<?= $themer->display('bootstrap:fragments/head') ?>

<?= $themer->display('bootstrap:fragments/topbar') ?>

<div class="<?= $containerClass ?>">

    <div class="row">

        <!-- Main -->
        <div class="col-sm-9 col-md-10">

            <?= $notice ?>

            <?= $view_content ?>

        </div>

        <!-- Sidebar -->
        <div class="col-sm-3 col-md-2">
            <ul class="nav nav-sidebar">
                <li class="active"><a href="#">Overview</a></li>
                <li><a href="#">Reports</a></li>
                <li><a href="#">Analytics</a></li>
                <li><a href="#">Export</a></li>
            </ul>
        </div>

    </div>

</div><!-- /.container -->

<?= $themer->display('bootstrap:fragments/footer') ?>
