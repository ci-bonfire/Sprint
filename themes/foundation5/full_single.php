<?= $themer->display('foundation:fragments/_vars') ?>

<?= $themer->display('foundation:fragments/head') ?>

<?= $themer->display('foundation:fragments/topbar') ?>

<div class="row container-fluid padded">

    <?= $notice ?>

    <?= $view_content ?>

</div><!-- /.container -->

<?= $themer->display('foundation:fragments/footer') ?>
