<div class="row">
    <?php foreach ($map as $column) :?>
    <div class="col-md-3">
    <?php foreach ($column as $section => $chapters) : ?>
        <h3><?= $section ?></h3>

        <ul class="nav">
        <?php foreach ($chapters as $link => $title) : ?>
            <li><a href="<?= $link ?>"><?= $title ?></a></li>
        <?php endforeach; ?>
        </ul>
    <?php endforeach; ?>
    </div>
    <?php endforeach; ?>
</div>