<?= form_open( current_url(), ['id' => 'login_form']); ?>

    <h2 class="form-signin-heading"><?= lang('auth.signin') ?></h2>

    <?= $notice ?>

    <input type="email" name="email" class="form-control" placeholder="<?= lang('auth.email') ?>" required="" autofocus="" value="<?= set_value('email') ?>" >

    <input type="password" name="password" class="form-control" placeholder="<?= lang('auth.password') ?>" required="" >

    <label for="remember">
        <input type="checkbox" name="remember" value="1" <?= set_checkbox('remember', 1) ?> >
        <?= lang('auth.remember_label') ?>
    </label>

    <input type="submit" class="btn btn-lg btn-primary btn-block" id="submit" name="submit" value="<?= lang('auth.signin') ?>">

    <br/>
    <p><?= lang('auth.need_account') ?></p>
    <p><?= lang('auth.forgot_pass') ?></p>

<?= form_close(); ?>