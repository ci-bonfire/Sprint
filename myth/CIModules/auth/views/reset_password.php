<?= form_open(); ?>
    <h2 class="form-signin-heading"><?= ucwords( lang('auth.forgot') ) ?></h2>

<?= $notice ?>

    <p><?= lang('auth.reset_note') ?></p>

    <input type="email" name="email" class="form-control" placeholder="<?= lang('auth.email') ?>" required="" autofocus="" value="<?= set_value('email', $email) ?>" >

    <input type="text" name="code" class="form-control" placeholder="<?= lang('auth.pass_code') ?>" required="" autofocus="" value="<?= set_value('code', $code) ?>" >


    <br/>

    <p><?= lang('auth.new_password') ?></p>

    <input type="password" name="password" id="password" class="form-control" placeholder="<?= lang('auth.password') ?>" required="">

    <input type="password" name="pass_confirm" id="pass-confirm" class="form-control" placeholder="<?= lang('auth.pass_confirm') ?>" required="">


    <?= $uikit->notice(lang('auth.password_strength'), 'default', false, ['class' => 'pass-strength']); ?>

    <button class="btn btn-lg btn-primary btn-block" id="submit" name="submit" type="submit"><?= lang('auth.send') ?></button>
<?= form_close(); ?>