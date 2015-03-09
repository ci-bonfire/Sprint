<?= form_open(); ?>

    <h2 class="form-signin-heading"><?= lang('auth.change_password') ?></h2>

    <?= $notice ?>

    <p><?= lang('auth.force_change_note') ?></p>

    <input type="password" name="current_pass" class="form-control" placeholder="<?= lang('auth.current_password') ?>" required="" autofocus="" >

    <br/>

    <p><?= lang('auth.new_password') ?></p>

    <input type="password" name="password" id="password" class="form-control" placeholder="<?= lang('auth.password') ?>" required="">

    <input type="password" name="pass_confirm" id="pass-confirm" class="form-control" placeholder="<?= lang('auth.pass_confirm') ?>" required="">

    <?= $uikit->notice(lang('auth.password_strength'), 'default', false, ['class' => 'pass-strength']); ?>

    <button class="btn btn-lg btn-primary btn-block" id="submit" name="submit" type="submit"><?= lang('auth.send') ?></button>

<?= form_close(); ?>