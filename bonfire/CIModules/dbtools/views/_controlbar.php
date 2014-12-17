<!-- ControlBar -->
<div class="scrollview controlbar-wrap">
	<div class="controlbar">

		<header>
			<h2><i class="fa fa-lg fa-database"></i> Database Tools</h2>
		</header>

		<div class="group">
			<h3>Tools</h3>

			<ul class="with-icon">
				<li class="active"><a href="#">
						<i class="fa fa-database"></i>
						Browse Database
					</a>
				</li>
				<li><a href="#">
						<i class="fa fa-files-o"></i>
						Manage Backups
					</a>
				</li>
			</ul>
		</div>

		<div class="group">
			<h3>Tables</h3>

			<?php if (! empty($tables)) : ?>

				<ul>
				<?php foreach ($tables as $table) : ?>
					<li>
						<a href="<?= site_url('dbtools/list_table/'. $table) ?>" class="ajax" data-replace-inner="#data-wrap"><?= $table ?></a>
					</li>
				<?php endforeach; ?>
				</ul>

			<?php else: ?>
				<div data-alert class="alert-box info">
					No tables found.
					<br/><small>Maybe check your database connection?</small>
				</div>
			<?php endif; ?>

		</div>

	</div>
</div>