<?php if (! empty($notice)) : ?>
<div class="alert alert-<?= $notice['type'] ?>">
    <?= $notice['message'] ?>
</div>
<?php endif; ?>