<?= $themer->display('foundation:fragments/head') ?>

<?= $themer->display('foundation:fragments/topbar') ?>

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
    .form-signin input[type="email"] {
        margin-bottom: -1px;
        border-bottom-right-radius: 0;
        border-bottom-left-radius: 0;
    }
    .form-signin input[type="password"] {
        margin-bottom: 10px;
        border-top-left-radius: 0;
        border-top-right-radius: 0;
    }

</style>

<div class="container">

    <form class="form-signin" role="form">

        <h2 class="form-signin-heading">Please sign in</h2>

        <?= $notice ?>

        <input type="email" class="form-control" placeholder="Email address" required="" autofocus="">

        <input type="password" class="form-control" placeholder="Password" required="">

        <label class="checkbox">
            <input type="checkbox" value="remember-me"> Remember me
        </label>

        <button class="btn btn-lg btn-primary btn-block" type="submit">Sign in</button>

    </form>

</div><!-- /.container -->



<?= $themer->display('foundation:fragments/footer') ?>
