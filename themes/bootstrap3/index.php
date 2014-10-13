<?php
    if (empty($navbar_style)) $themer->set('navbar_style', 'navbar-static');
    if (empty($containerClass)) $themer->set('containerClass', 'container');
?>
<?= $themer->display('bootstrap:fragments/head') ?>

<?= $themer->display('bootstrap:fragments/topbar') ?>

<div class="container">

    <?= $notice ?>

    <?= $view_content ?>

</div><!-- /.container -->

<?= $themer->display('bootstrap:fragments/footer') ?>
