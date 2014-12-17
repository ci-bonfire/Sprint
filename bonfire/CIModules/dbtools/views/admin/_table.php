<h2><?= $table ?></h2>

<?php if (! empty($fields)) : ?>
<div style="width: 100%; overflow-x: scroll">
	<table style="width: 100%">
		<thead>
			<tr>
			<?php foreach ($fields as $field) : ?>
				<th><?= $field ?></th>
			<?php endforeach; ?>
			</tr>
		</thead>
		<tbody>
		<?php if (! empty($rows) && is_array($rows) && count($rows)) : ?>
			<?php foreach ($rows as $row) : ?>
			<tr>
			<?php foreach ($fields as $field) : ?>
				<td><?= htmlentities( $row->{$field} ) ?></td>
			<?php endforeach; ?>
			</tr>
			<?php endforeach; ?>
		<?php else : ?>
			<tr><td colspan="<?= count($fields) ?>">
			<div class="alert-box info">
				Unable to find any data for <?= $table ?>
			</div>
			</td></tr>
		<?php endif; ?>
		</tbody>
	</table>
</div>
<?php endif; ?>