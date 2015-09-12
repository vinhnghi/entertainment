<?php
// No direct access to this file
defined ( '_JEXEC' ) or die ( 'Restricted Access' );

JHtml::_ ( 'formbehavior.chosen', 'select' );

$listOrder = $this->escape ( $this->filter_order );
$listDirn = $this->escape ( $this->filter_order_Dir );
?>
<form action="index.php?option=com_activity&view=types" method="post"
	id="adminForm" name="adminForm">
	<div class="row-fluid">
		<div class="span6">
			<?php echo JText::_('COM_ACTIVITY_FILTER'); ?>
			<?php
			echo JLayoutHelper::render ( 'joomla.searchtools.default', array (
					'view' => $this,
					'options' => array (
							'filtersHidden' => false 
					) 
			) );
			?>
		</div>
	</div>
	<table class="table table-striped table-hover">
		<thead>
			<tr>
				<th width="1%"><?php echo JText::_('COM_ACTIVITY_NUM'); ?></th>
				<th width="2%">
					<?php echo JHtml::_('grid.checkall'); ?>
				</th>
				<th width="74%">
					<?php echo JHtml::_('grid.sort', 'COM_ACTIVITY_NAME', 'title', $listDirn, $listOrder); ?>
				</th>
				<th width="5%">
					<?php echo JHtml::_('grid.sort', 'COM_ACTIVITY_PUBLISHED', 'published', $listDirn, $listOrder); ?>
				</th>
				<th width="8%">
					<?php echo JHtml::_('grid.sort', 'Level', 'level', $listDirn, $listOrder); ?>
				</th>
				<th width="8%">
					<?php echo JHtml::_('grid.sort', 'Parent id', 'parent_id', $listDirn, $listOrder); ?>
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
				<?php
				
				foreach ( $this->items as $i => $row ) :
					$link = JRoute::_ ( 'index.php?option=com_activity&task=type.edit&id=' . $row->id );
					?>
					<tr>
				<td><?php echo $this->pagination->getRowOffset($i); ?></td>
				<td>
							<?php echo JHtml::_('grid.id', $i, $row->id); ?>
						</td>
				<td><a href="<?php echo $link; ?>"
					title="<?php echo JText::_('COM_ACTIVITY_EDIT_ACTIVITY'); ?>">
								<?php echo $row->title; ?>
							</a></td>
				<td align="center">
							<?php echo JHtml::_('jgrid.published', $row->published, $i, 'types.', true, 'cb'); ?>
						</td>
				<td align="center">
							<?php echo (int)$row->level; ?>
						</td>
				<td align="center">
							<?php echo $row->parent_id <= 1 ? 'NULL' : $row->parent_id; ?>
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