<?php
// No direct access to this file
defined ( '_JEXEC' ) or die ( 'Restricted Access' );

JHtml::_ ( 'formbehavior.chosen', 'select' );
JHtml::_ ( 'behavior.core' );
JHtml::_ ( 'bootstrap.tooltip' );

$listOrder = $this->escape ( $this->filter_order );
$listDirn = $this->escape ( $this->filter_order_Dir );

//
$num_row_item = 3;
$item_width = 100 / $num_row_item - 2;
?>
<script type='text/javascript'>
function getSelectedTalents() {
	var checkedBoxes = jQuery('input[type=checkbox]:checked');
	var selectedTalents = [];
	if (checkedBoxes) {
		var k=0;
		for (var i=0;i<checkedBoxes.length;i++) {
			var id = parseInt( jQuery(checkedBoxes[i]).val() );
			if (id) {
				for (j=k;j<talents.length;j++) {
					if	(talents[j].id == id) {
						selectedTalents.push(talents[j]);
						k=j;
						break;
					}
				}
			}
		}
	}
	return selectedTalents;
}
var talents= <?php echo json_encode($this->items);?>;
var elId= '<?php echo JFactory::getApplication()->input->get('elId', 'activitytalents');?>';
</script>
<form method="post" id="adminForm" name="adminForm">
	<div class="row-fluid js-stools">
		<?php echo JText::_('Filter'); ?>
		<?php
		echo JLayoutHelper::render ( 'joomla.searchtools.default', array (
				'view' => $this,
				'options' => array (
						'filtersHidden' => false 
				) 
		) );
		?>
		<div class="btn-wrapper">
			<?php echo JHtml::_('grid.checkall'); ?><span class="checkbox-label checkall-label">Check
				All</span>
			<button type="button"
				onclick="window.parent.addTalents(elId,getSelectedTalents());"
				class="btn btn-small btn-success">
				<span class="icon-new icon-white"></span>Add
			</button>
		</div>
	</div>
	<div class="item-page com_talent_content com_talent_list_content">
		<?php
		if (! empty ( $this->items )) :
			foreach ( $this->items as $i => $row ) :
				$row->index = $i;
				$image = SiteTalentHelper::getIntroImage ( $row->images );
				?>
	<div class="com_talent_list_item" style="width: <?php echo $item_width?>%">
			<div class="com_talent_list_item_title">
				<?php echo JHtml::_('grid.id', $i, $row->id)?>
				<span class="checkbox-label"><a href="<?php echo $link ?>"><?php echo $row->title ?></a></span>
			</div>
		<?php if ($image):?>
		<div class="com_talent_list_item_image_intro">
				<img alt="<?php echo $image->alt?>" src="<?php echo $image->src?>">
			</div>
			<div class="clearfix"></div>
		<?php endif; ?>
		<?php echo SiteTalentHelper::getTalentDetailsHtml($row); ?>
		<div class="clearfix"></div>
			<div class="com_talent_list_item_introtext"><?php echo SiteTalentHelper::truncate( strip_tags($row->introtext), 20);?></div>
		</div>
	<?php if ($i%$num_row_item==$num_row_item-1):?>
	<div class="clearfix"></div>
	<?php endif; ?>
	<?php endforeach; ?>
	<?php endif; ?>
	</div>
	<input type="hidden" name="task" value="" /> <input type="hidden"
		name="boxchecked" value="0" /> <input type="hidden"
		name="filter_order" value="<?php echo $listOrder; ?>" /> <input
		type="hidden" name="filter_order_Dir" value="<?php echo $listDirn; ?>" />
	<?php echo JHtml::_('form.token'); ?>
</form>