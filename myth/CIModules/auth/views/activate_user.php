<?= form_open(); ?>
 
    <h2 class="form-signin-heading"><?= lang('auth.activate_account') ?></h2>

    <?= $notice ?>

    <input type="email" name="email" class="form-control" placeholder="<?= lang('auth.email') ?>" required="" autofocus="" value="<?= set_value('email', $email) ?>" >

    <input type="text" name="code" class="form-control" placeholder="<?= lang('auth.pass_code') ?>" required="" autofocus="" value="<?= set_value('code', $code) ?>" >

    <button class="btn btn-lg btn-primary btn-block" id="submit" name="submit" type="submit"><?= lang('auth.activate') ?></button>

<?= form_close(); ?>