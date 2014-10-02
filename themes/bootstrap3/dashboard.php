<?= $themer->display('bootstrap:fragments/head') ?>

<?= $themer->display('bootstrap:fragments/topbar') ?>

<div class="container-fluid">

    <div class="row">

        <!-- Sidebar -->
        <div class="col-sm-3 col-md-2 sidebar">
            <ul class="nav nav-sidebar">
                <li class="active"><a href="#">Overview</a></li>
                <li><a href="#">Reports</a></li>
                <li><a href="#">Analytics</a></li>
                <li><a href="#">Export</a></li>
            </ul>
        </div>

        <!-- Main -->
        <div class="col-sm-9 col-sm-offset-3 col-md-10 col-md-offset-2 main">

            <?= $notice ?>

            <?= $view_content ?>

            <?= $themer->display('bootstrap:fragments/footer') ?>

        </div>

    </div>



</div><!-- /.container -->

