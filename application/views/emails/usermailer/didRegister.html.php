<table class="row">
    <tr>
        <td>
            <h1>Your New Here, Right?</h1>

            <p>Hey there!</p>

            <p>Someone using this email (<?= $email ?>) just signed up for an account at <a href="<?= $site_link ?>"><?= $site_name ?></a>. If that was not you, then disregard these instructions
                and all will be well.</p>

            <p>If that was you - then click the link below to activate your account:</p>

            <p>
                <a href="<?= $link ."?e={$email}&code={$token}" ?>">
                    <?= $link ."?e={$email}&code={$token}" ?>
                </a>
            </p>

            <p>If the link does not work, please visit the following page: <b><?= $link ?></b> and enter the following token when asked:</p>

            <p><?= $token ?></p>

            <p>Thanks!<br/><?= $site_name ?></p>
        </td>
    </tr>
</table>
