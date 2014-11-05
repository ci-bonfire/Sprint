Hey there!

Someone using this email (<?= $email ?>) just signed up for an account at <?= $site_name ?>. If that was not you, then disregard these instructions
and all will be well.

If that was you - then click the link below to activate your account:

<?= $link ."?e={$email}&code={$token}" ?>

If the link does not work, please visit the following page: <?= $link ?> and enter the following token when asked:

<?= $token ?>

Thanks!
<?= $site_name ?>