<?php if (isset($notice) && ! empty($notice)) : ?>
    <div class="alert alert-<?= $notice['type'] ?> alert-dismissible" role="alert">
        <button type="button" class="close" data-dismiss="alert"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
        <?= $notice['message'] ?>
    </div>
<?php endif; ?>