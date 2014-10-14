<?= form_open(); ?>

    <h2 class="form-signin-heading">Sign In</h2>

    <?= $notice ?>

    <input type="email" name="email" class="form-control" placeholder="Email" required="" autofocus="" value="<?= set_value('email') ?>" >

    <input type="password" name="password" class="form-control" placeholder="Password" required="" >

    <label for="remember">
        <input type="checkbox" name="remember" value="1" <?= set_checkbox('remember', 1) ?> >
        Remember me on this device
    </label>

    <button class="btn btn-lg btn-primary btn-block" id="submit" name="submit" type="submit">Sign In</button>

<?= form_close(); ?>