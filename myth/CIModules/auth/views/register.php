<?= form_open() ?>

    <h2 class="form-signin-heading"><?= lang('auth.register') ?></h2>

    <?= $notice ?>

    <input type="text" name="first_name" class="form-control" placeholder="<?= lang('auth.first_name') ?>" required="" autofocus="" value="<?= set_value('first_name') ?>">

    <input type="text" name="last_name" class="form-control" placeholder="<?= lang('auth.last_name') ?>" required="" value="<?= set_value('last_name') ?>" >

    <input type="email" name="email" class="form-control" placeholder="<?= lang('auth.email') ?>" required="" value="<?= set_value('email') ?>">

    <br/>

    <input type="text" name="username" class="form-control" placeholder="<?= lang('auth.username') ?>" required="" value="<?= set_value('username') ?>">

    <input type="password" name="password" id="password" class="form-control" placeholder="<?= lang('auth.password') ?>" required="">

    <input type="password" name="pass_confirm" id="pass-confirm" class="form-control" placeholder="<?= lang('auth.pass_confirm') ?>" required="">


    <?= $uikit->notice(lang('auth.password_strength'), 'default', false, ['class' => 'pass-strength']); ?>


    <button class="btn btn-lg btn-primary btn-block" id="submit" name="submit" disabled type="submit"><?= lang('auth.register') ?></button>

    <br/>
    <p><?= lang('auth.have_account') ?></p>

<?= form_close() ?>