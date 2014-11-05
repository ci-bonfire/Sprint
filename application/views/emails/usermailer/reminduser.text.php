Hey there!

Someone using this email (<?= $email ?>) just requested password reset instructions. If that was not you, then disregard these instructions
and all will be well.

If you do need to reset your password, please visit the following link:

<?= $link ."?e={$email}&code={$code}" ?>

If the link does not work, please visit the following page: <?= $link ?> and enter the following code when asked:

<?= $code ?>

Thanks!
<?= $site_name ?>