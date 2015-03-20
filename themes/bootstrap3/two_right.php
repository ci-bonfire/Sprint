<?php
    if (empty($navbar_style)) $themer->set('navbar_style', 'navbar-static');
    if (empty($containerClass))
    {
        $themer->set('containerClass', 'container');
        $containerClass = 'container';
    }
?>

<?= $themer->display('bootstrap:fragments/head') ?>

<?= $themer->display('bootstrap:fragments/topbar') ?>

<div class="<?= $containerClass ?>">

    <div class="row">

        <!-- Main -->
        <div class="col-sm-8 col-md-9">

            <?= $notice ?>

            <?= $view_content ?>

        </div>

        <!-- Sidebar -->
        <div class="col-sm-4 col-md-3">

            <?= $themer->display('{theme}:sidebar'); ?>

        </div>

    </div>

</div><!-- /.container -->

<?= $themer->display('bootstrap:fragments/footer') ?>
