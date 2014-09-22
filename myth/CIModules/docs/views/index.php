<?php if (empty($content)) : ?>
    <div class="alert alert-info" style="margin-top: 40px;">
        <?php if (isset($notice)) : ?>
            <?= $notice; ?>
        <?php else: ?>
            <?php echo lang('docs_not_found'); ?>
        <?php endif; ?>
    </div>

<?php else: ?>

    <div class="page">
        <?php echo $content; ?>
    </div>

<?php endif; ?>
