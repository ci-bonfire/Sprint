<h2>Edit <?= $single_name ?></h2>

@= form_open(); ?>

<?= $uikit->row([], function() use($uikit, $fields)
{

	$sizes = [
		's' => 12,
		'm' => 6,
		'l' => 4
	];
	echo $uikit->column( [ 'sizes' => $sizes ], function () use ( $uikit, $fields )
	{

		foreach ( $fields as $field )
		{

			echo $uikit->inputWrap( humanize( $field['name'] ), NULL, function () use ( $uikit, $field )
			{

				switch ( $field['type'] )
				{
					case 'text':
						echo "\t\t\t<input type='text' name='{$field['name']}' class='form-control' value='@= set_value('" . $field["name"] . "', \$item->" . $field['name'] . " ) ?>' />\n";
						break;
					case 'number':
						echo "\t\t\t<input type='number' name='{$field['name']}' class='form-control' value='@= set_value('" . $field["name"] . "', \$item->" . $field['name'] . " ) ?>' />\n";
						break;
					case 'date':
						echo "\t\t\t<input type='date' name='{$field['name']}' class='form-control' value='@= set_value('" . $field["name"] . "', \$item->" . $field['name'] . " ) ?>' />\n";
						break;
					case 'datetime':
						echo "\t\t\t<input type='datetime' name='{$field['name']}' class='form-control' value='@= set_value('" . $field["name"] . "', \$item->" . $field['name'] . " ) ?>' />\n";
						break;
					case 'time':
						echo "\t\t\t<input type='time' name='{$field['name']}' class='form-control' value='@= set_value('" . $field["name"] . "', \$item->" . $field['name'] . " ) ?>' />\n";
						break;
					case 'textarea':
						echo "\t\t\t<textarea  class='form-control' name='{$field['name']}'>@= set_value('" . $field["name"] . "') ?></textarea>\n";
						break;
				}

			} );
		}
	} );
} );
?>

<input type="submit" name="submit" class="btn btn-primary" value="Save <?= $single_name ?>" />
&nbsp;or&nbsp;
<a href="@= site_url('<?= $lower_name ?>') ?>">Cancel</a>

@= form_close(); ?>
