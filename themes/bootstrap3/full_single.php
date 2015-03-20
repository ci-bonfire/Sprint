<?= $themer->display('bootstrap:fragments/head') ?>

<?= $themer->display('bootstrap:fragments/topbar') ?>

<div class="container-fluid">

    <?= $notice ?>

    <?= $view_content ?>

</div><!-- /.container -->

<?= $themer->display('bootstrap:fragments/footer') ?>
