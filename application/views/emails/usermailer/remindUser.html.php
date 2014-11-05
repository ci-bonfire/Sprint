<table class="row">
    <tr>
        <td>
            <h1>Huh. I think we can fix that.</h1>
            
            <p>Hey there!</p>
            
            <p>Someone using this email (<?= $email ?>) just requested password reset instructions. If that was not you, then disregard these instructions
                and all will be well.</p>
            
            <p>If you do need to reset your password, please visit the following link:</p>
            
            <p>
                <a href="<?= $link ."?e={$email}&code={$code}" ?>">
                    <?= $link ."?e={$email}&code={$code}" ?>
                </a>
            </p>

            <p>If the link does not work, please visit the following page: <b><?= $link ?></b> and enter the following code when asked:</p>

            <p><?= $code ?></p>

            <p>Thanks!<br><?= $site_name ?></p>
        </td>
    </tr>
</table>
