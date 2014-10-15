<?= form_open() ?>

    <h2 class="form-signin-heading">Join Us!</h2>

    <?= $notice ?>

    <input type="text" name="first_name" class="form-control" placeholder="First Name" required="" autofocus="" value="<?= set_value('first_name') ?>">

    <input type="text" name="last_name" class="form-control" placeholder="Last Name" required="" value="<?= set_value('last_name') ?>" >

    <input type="email" name="email" class="form-control" placeholder="Email address" required="" value="<?= set_value('email') ?>">

    <br/>

    <input type="text" name="username" class="form-control" placeholder="Username" required="" value="<?= set_value('username') ?>">

    <input type="password" name="password" id="password" class="form-control" placeholder="Password" required="">

    <input type="password" name="pass_confirm" id="pass-confirm" class="form-control" placeholder="Password (again)" required="">


    <?= $uikit->notice('Password Strength', 'default', false, ['class' => 'pass-strength']); ?>


    <button class="btn btn-lg btn-primary btn-block" id="submit" name="submit" disabled type="submit">Sign Up</button>

<?= form_close() ?>