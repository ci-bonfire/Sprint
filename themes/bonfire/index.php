<?= $themer->display('bonfire:fragments/head') ?>

<div id="app-wrap">

	<?= $themer->display('bonfire:fragments/sourcebar') ?>

	<?php if (! empty($controlbar)) : ?>
		<?= $controlbar ?>
	<?php endif; ?>

	<div class="main-section <?php if (! empty($controlbar)) echo 'with-controlbar' ?>">

		<?= $notice ?>

		<?= $view_content ?>

	</div>

</div>

</div><!-- /.container -->

<?= $themer->display('bonfire:fragments/footer') ?>
