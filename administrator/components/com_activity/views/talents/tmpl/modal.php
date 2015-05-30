<?php
// No direct access to this file
defined ( '_JEXEC' ) or die ( 'Restricted Access' );

JHtml::_ ( 'formbehavior.chosen', 'select' );

$listOrder = $this->escape ( $this->filter_order );
$listDirn = $this->escape ( $this->filter_order_Dir );
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
<form
	action="index.php?option=com_activity&view=talents&layout=modal&tmpl=component"
	method="post" id="adminForm" name="adminForm">
	<div class="row-fluid js-stools">
		<?php echo JText::_('COM_ACTIVITY_FILTER'); ?>
		<?php
		echo JLayoutHelper::render ( 'joomla.searchtools.default', array (
				'view' => $this 
		) );
		?>
		<div class="btn-wrapper">
			<button type="button"
				onclick="window.parent.ActivityTalents.addItems(elId,getSelectedTalents());"
				class="btn btn-small btn-success">
				<span class="icon-new icon-white"></span>Add Talent
			</button>
		</div>
	</div>
	<table class="table table-striped table-hover">
		<thead>
			<tr>
				<th width="1%"><?php echo JText::_('COM_ACTIVITY_NUM'); ?></th>
				<th width="2%">
				<?php echo JHtml::_('grid.checkall'); ?>
			</th>
				<th width="95%">
				<?php echo JHtml::_('grid.sort', 'COM_ACTIVITY_NAME', 'title', $listDirn, $listOrder); ?>
			</th>
				<th width="2%">
				<?php echo JHtml::_('grid.sort', 'COM_ACTIVITY_ID', 'id', $listDirn, $listOrder); ?>
			</th>
			</tr>
		</thead>
		<tfoot>
			<tr>
				<td colspan="5">
					<?php echo $this->pagination->getListFooter(); ?>
				</td>
			</tr>
		</tfoot>
		<tbody>
			<?php if (!empty($this->items)) : ?>
				<?php foreach ($this->items as $i => $row) :?>
					<tr>
				<td><?php echo $this->pagination->getRowOffset($i); ?></td>
				<td>
							<?php echo JHtml::_('grid.id', $i, $row->id); ?>
						</td>
				<td>
							<?php echo $row->title; ?>
						</td>
				<td align="center">
							<?php echo $row->id; ?>
						</td>
			</tr>
				<?php endforeach; ?>
			<?php endif; ?>
		</tbody>
	</table>
	<input type="hidden" name="task" value="" /> <input type="hidden"
		name="boxchecked" value="0" /> <input type="hidden"
		name="filter_order" value="<?php echo $listOrder; ?>" /> <input
		type="hidden" name="filter_order_Dir" value="<?php echo $listDirn; ?>" />
	<?php echo JHtml::_('form.token'); ?>
</form>