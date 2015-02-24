<?php if (isset($notice) && ! empty($notice)) : ?>
    <div class="alert-box <?= $notice['type'] ?>" data-alert role="alert">
        <?= $notice['message'] ?>
        <a href="#" class="close">&times;</a>
    </div>
<?php endif; ?>