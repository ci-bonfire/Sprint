<?= form_open(); ?>

    <h2 class="form-signin-heading"><?= lang('auth.forgot') ?></h2>

    <?= $notice ?>

    <p><?= lang('auth.forgot_note') ?></p>

    <input type="email" name="email" class="form-control" placeholder="<?= lang('auth.email') ?>" required="" autofocus="" value="<?= set_value('email') ?>" >

    <button class="btn btn-lg btn-primary btn-block" id="submit" name="submit" type="submit"><?= lang('auth.send') ?></button>

<?= form_close(); ?>