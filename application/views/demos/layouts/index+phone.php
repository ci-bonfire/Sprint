<?php if ($theme == 'bootstrap') : ?>

    <div class="jumbotron">
        <h1><?= ucwords($layout_title) ?></h1>

        <p><?= $layout_desc ?></p>
    </div>

<?php else: ?>

    <div class="panel">
        <h1><?= ucwords($layout_title) ?></h1>

        <p><?= $layout_desc ?></p>
    </div>

<?php endif; ?>

<p>This View is intended for mobile phones, based on the variant.</p>