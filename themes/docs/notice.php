<?php if (isset($notice) && ! empty($notice)) : ?>
    <div class="alert alert-<?= $notice['type'] ?>">
        <?= $notice['message'] ?>
    </div>
<?php endif; ?>
