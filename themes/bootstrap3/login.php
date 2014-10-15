<?php
    if (empty($navbar_style)) $themer->set('navbar_style', 'navbar-static');
    if (empty($containerClass)) $themer->set('containerClass', 'container');
?>
<?= $themer->display('bootstrap:fragments/head') ?>

<?= $themer->display('bootstrap:fragments/topbar') ?>

<style>
    body {
        padding-bottom: 40px;
        background-color: #eee;
    }

    .form-signin {
        max-width: 330px;
        padding: 15px;
        margin: 0 auto 40px auto;
    }
    .form-signin .form-signin-heading,
    .form-signin .checkbox {
        margin-bottom: 10px;
    }
    .form-signin .checkbox {
        font-weight: normal;
    }
    .form-signin .form-control {
        position: relative;
        height: auto;
        -webkit-box-sizing: border-box;
        -moz-box-sizing: border-box;
        box-sizing: border-box;
        padding: 10px;
        font-size: 16px;
    }
    .form-signin .form-control:focus {
        z-index: 2;
    }
    .form-signin input {
        margin-bottom: 10px;
    }
    .pass-strength {
        min-height: 1.5em;
    }

</style>

<div class="container">

    <div class="form-signin">
        <?= $view_content ?>
    </div>

</div><!-- /.container -->



<?= $themer->display('bootstrap:fragments/footer') ?>
