<h2>Create A <?= $single_name ?></h2>

@php form_open(); ?>

<?php foreach ($fields as $field) : ?>

	<?= $uikit->inputWrap( humanize($field['name']), null, function() use($uikit, $field) {

			switch ($field['type'])
			{
				case 'text':
					echo "\t\t\t<input type='text' name='{$field['name']}' />\n";
					break;
				case 'number':
					echo "\t\t\t<input type='number' name='{$field['name']}' />\n";
					break;
				case 'date':
					echo "\t\t\t<input type='date' name='{$field['name']}' />\n";
					break;
				case 'datetime':
					echo "\t\t\t<input type='datetime' name='{$field['name']}' />\n";
					break;
				case 'time':
					echo "\t\t\t<input type='time' name='{$field['name']}' />\n";
					break;
				case 'textarea':
					echo "\t\t\t<textarea name='{$field['name']}'></textarea>\n";
					break;
			}

	} ); ?>

<?php endforeach; ?>

	<input type="submit" name="submit" value="Create <?= $single_name ?>" />
	&nbsp;or&nbsp;
	<a href="@= site_url('<?= $lower_name ?>') ?>">Cancel</a>

@php form_close(); ?>