<h2><?= $plural_name ?></h2>

@php if ( ! empty($rows) && is_array($rows) && count($rows) ) : ?>

	<?= $uikit->notice('Unable to find any records.', 'warning'); ?>

@php else : ?>

	@php $this->table->generate( $rows ); ?>

@php endif; ?>