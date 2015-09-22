<?php
defined ( '_JEXEC' ) or die ();
// Including fallback code for the placeholder attribute in the search field.
JHtml::_ ( 'jquery.framework' );
JHtml::_ ( 'script', 'system/html5fallback.js', false, true );

$filters = $displayData->filterForm->getGroup ( 'filter' );
$filter_fields = $displayData->filter_fields;
?>

<div class="js-stools clearfix">
	<div class="clearfix">
		<div class="js-stools-container-bar">
			<label for="filter_search" class="element-invisible">
			<?php echo JText::_('JSEARCH_FILTER'); ?>
		</label>
			<div class="btn-wrapper input-append">
			<?php echo $filters['filter_search']->input; ?>
		</div>
		</div>
		<div class="js-stools-container-filters hidden-phone clearfix">
	<?php foreach ($filters as $fieldName => $field) : ?>
		<?php if ($fieldName != 'filter_search' && in_array($fieldName, $filter_fields)) : ?>
			<div class="js-stools-field-filter">
				<?php echo $field->input; ?>
			</div>
		<?php endif; ?>
	<?php endforeach; ?>
	</div>
	</div>
	<div class="clearfix btn-wrapper">
		<button type="submit" class="btn hasTooltip"
			title="<?php echo JHtml::tooltipText('JSEARCH_FILTER_SUBMIT'); ?>">
			<i class="icon-search"></i>
		</button>
		<button type="button" class="btn hasTooltip js-stools-btn-clear"
			onclick="jQuery(this).closest('form').find('input[type=text], textarea').val('');jQuery(this).closest('form').find('input[type=checkbox]').attr('checked', false);"
			title="<?php echo JHtml::tooltipText('JSEARCH_FILTER_CLEAR'); ?>">
				<?php echo JText::_('JSEARCH_FILTER_CLEAR');?>
				</button>
	</div>
</div>
