<!-- ControlBar -->
<div class="scrollview controlbar-wrap">
	<div class="controlbar">

		<header>
			<h2><i class="fa fa-lg fa-group"></i> Manage Users</h2>
		</header>

		<div class="group">
			<h3>Groups</h3>

			<?php if (isset($groups) && is_array($groups)) : ?>
			<ul>
				<?php foreach ($groups as $group) : ?>
					<li>
						<a href="#">
							<?= ucwords( str_replace('_', ' ', $group->name) ) ?>
							<?php if (! empty($group->count)) : ?>
								<span class="label"><?= (int)$group->count ?></span>
							<?php endif; ?>
						</a>
					</li>
				<?php endforeach; ?>
			</ul>
			<?php endif; ?>

			<div class="text-center">
				<a class="flat button info" href="#">Manage Groups</a>
			</div>
		</div>



	</div>
</div>