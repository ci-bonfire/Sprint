<table class="row">
    <tr>
        <td>
            <h1>I knew we could fix that for you!</h1>

            <p>Hey there!</p>

            <p>Someone using this email (<?= $email ?>) just reset the account password. If that was you, then disregard these instructions
                and all will be well.</p>

            <p>If you did not do this, then you should reset your password immediately by visiting the following link, and clicking the Forgot Your Password link:</p>

            <p>
                <a href="<?= $site_link ?>">
                    <?= $link ?>
                </a>
            </p>

            <p>If the link does not work, please visit the following page:</p>

            <p><?= $link ?></p>

            <p>Thanks!<br/><?= $site_name ?></p>
        </td>
    </tr>
</table>
