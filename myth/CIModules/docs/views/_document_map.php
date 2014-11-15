<?php if (isset($docMap)) : ?>
    <div class="doc-map">
        <h3><?php echo lang('docs_in_this_chapter') ?></h3>

        <ul class="nav">
        <?php foreach ($docMap as $row) : ?>
            <li>
                <a href="<?= $row['link'] ?>"><?= $row['name'] ?></a>

                <?php if (isset($row['items']) && is_array($row['items']) && count($row['items'])) : ?>
                <ul class="nav">
                <?php foreach ($row['items'] as $item) : ?>
                    <li>
                        <a href="<?= $item['link'] ?>"><?= $item['name'] ?></a>
                    </li>
                <?php endforeach; ?>
                </ul>
                <?php endif; ?>
            </li>
        <?php endforeach; ?>
        </ul>
    </div>
<?php endif; ?>