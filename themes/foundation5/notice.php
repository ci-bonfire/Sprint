<?php if (isset($notice) && ! empty($notice)) : ?>
    <div class="alert-box <?= $notice['type'] ?>" data-alert id="notice" role="alert">
        <?= $notice['message'] ?>
        <a href="#" class="close">&times;</a>
    </div>

<?php else: ?>

    <div id="notice"></div>

<?php endif; ?>