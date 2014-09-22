<div id="toc">
    <ul class="nav">
        <?php if ( ! empty($docs) && is_array($docs)) : ?>
            <?php foreach ($docs as $file => $name) : ?>
                <?php if (is_array($name)) : ?>
                    <li class='parent'>
                        <div class="nav-header"><?php echo $file; ?></div>
                        <ul class="nav">
                            <?php foreach ($name as $line => $namer) : ?>
                            <li><?php echo anchor($docsDir . str_replace($docsExt, '', $line), $namer); ?></li>
                            <?php endforeach; ?>
                        </ul>
                    </li>
                <?php else : ?>
                    <li><?php echo anchor($docsDir . str_replace($docsExt, '', $file), $name); ?></li>
                <?php endif ?>
            <?php endforeach ?>
        <?php else : ?>
            <p class="text-center"><?php echo lang('docs_not_found'); ?></p>
        <?php endif ?>

        <?php if ( ! empty($module_docs) && is_array($module_docs)) : ?>
            <li class="parent"><div class="nav-header"><?php e(lang('docs_title_modules')); ?></div></li>

            <!-- Module Specific Docs -->
            <?php foreach ($module_docs as $module => $mod_files) : ?>
                <?php if (count($mod_files)) : ?>
                    <li class="parent">
                        <div class='nav-header'><?php echo $module; ?></div>
                        <ul class='nav'>
                        <?php foreach ($mod_files as $fileName => $title) : ?>
                            <li><?php echo anchor(site_url($docsDir . '/' . str_replace($docsExt, '', $fileName)), ucwords($title)); ?></li>
                        <?php endforeach; ?>
                        </ul>
                    </li>
                <?php else : ?>
                    <li class='parent'><?php echo anchor(site_url($docsDir . '/' . str_replace($docsExt, '', $module)), ucwords(str_replace('_', ' ', $module))); ?></li>
                <?php endif ?>
            <?php endforeach ?>
        <?php endif ?>
    </ul>
</div>