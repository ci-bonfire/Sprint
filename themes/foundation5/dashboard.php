<?= $themer->display('foundation:fragments/_vars') ?>

<?= $themer->display('foundation:fragments/head') ?>

<div class="fixed">
    <?= $themer->display('foundation:fragments/topbar') ?>
</div>

<div class="row container-fluid">

    <!-- Sidebar -->
    <div class="small-3 medium-2 columns sidebar">
        <ul class="side-nav">
            <li class="active"><a href="#">Overview</a></li>
            <li><a href="#">Reports</a></li>
            <li><a href="#">Analytics</a></li>
            <li><a href="#">Export</a></li>
        </ul>
    </div>

    <!-- Main -->
    <div class="small-9 small-offset-3 medium-10 medium-offset-2 columns main">

        <?= $notice ?>

        <?= $view_content ?>

        <?= $themer->display('foundation:fragments/footer') ?>

    </div>

</div>


