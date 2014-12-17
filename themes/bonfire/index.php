<?= $themer->display('bonfire:fragments/head') ?>

<div id="app-wrap">

	<?= $themer->display('bonfire:fragments/sourcebar') ?>

	<div class="main-section">

		<?= $notice ?>

		<?= $view_content ?>

	</div>

</div>

</div><!-- /.container -->

<?= $themer->display('bonfire:fragments/footer') ?>
