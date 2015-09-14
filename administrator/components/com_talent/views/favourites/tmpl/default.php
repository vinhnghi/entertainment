<?php
// No direct access to this file
defined ( '_JEXEC' ) or die ( 'Restricted Access' );

JHtml::_ ( 'formbehavior.chosen', 'select' );

$listOrder = $this->escape ( $this->filter_order );
$listDirn = $this->escape ( $this->filter_order_Dir );
?>
<form action="index.php?option=com_talent&view=favourites" method="post"
	id="adminForm" name="adminForm">
	<div class="row-fluid">
		<div class="span6">
			<?php echo JText::_('COM_TALENT_FILTER'); ?>
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
				<th width="1%"><?php echo JText::_('COM_TALENT_NUM'); ?></th>
				<th width="2%">
				<?php echo JHtml::_('grid.checkall'); ?>
			</th>
				<th>
				<?php echo JHtml::_('grid.sort', 'COM_TALENT_NAME', 'title', $listDirn, $listOrder); ?>
			</th>
				<th width="10%">
				<?php echo JHtml::_('grid.sort', 'COM_TALENT_AGENT', 'agent', $listDirn, $listOrder); ?>
			</th>
				<th width="5%">
				<?php echo JHtml::_('grid.sort', 'COM_TALENT_PUBLISHED', 'published', $listDirn, $listOrder); ?>
			</th>
				<th width="2%">
				<?php echo JHtml::_('grid.sort', 'COM_TALENT_ID', 'id', $listDirn, $listOrder); ?>
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
					$link = JRoute::_ ( 'index.php?option=com_talent&task=favourite.edit&id=' . $row->id );
					?>
					<tr>
				<td><?php echo $this->pagination->getRowOffset($i); ?></td>
				<td>
							<?php echo JHtml::_('grid.id', $i, $row->id); ?>
						</td>
				<td><a href="<?php echo $link; ?>"
					title="<?php echo JText::_('COM_TALENT_EDIT_FAVOURITE'); ?>">
								<?php echo $row->title; ?>
							</a></td>
				<td align="center">
							<?php echo $row->agent; ?>
						</td>
				<td align="center">
							<?php echo JHtml::_('jgrid.published', $row->published, $i, 'favourites.', true, 'cb'); ?>
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