<?php
// No direct access to this file
defined ( '_JEXEC' ) or die ( 'Restricted Access' );

JHtml::_ ( 'formbehavior.chosen', 'select' );
//
$user = JFactory::getUser ();
$agent = SiteTalentHelper::getAgentByUserId ( $user->id );
//
$listOrder = $this->escape ( $this->filter_order );
$listDirn = $this->escape ( $this->filter_order_Dir );
?>
<form method="post" id="adminForm" name="adminForm">
	<table class="table table-striped table-hover com_talent_list_item">
		<thead>
			<tr>
				<th width="15%"><?php echo JText::_('COM_TALENT_IMAGE') ?></th>
				<th width="30%"><?php echo JText::_('COM_TALENT_DETAIL') ?></th>
				<th><?php echo JHtml::_('grid.sort', 'COM_TALENT_INTRO', 'title', $listDirn, $listOrder) ?></th>
			</tr>
		</thead>
		<tfoot>
			<tr>
				<td colspan="3">
					<?php echo $this->pagination->getListFooter()?>
				</td>
			</tr>
		</tfoot>
		<tbody>
			<?php if (!empty($this->items)) : ?>
				<?php
				foreach ( $this->items as $i => $row ) :
					$link = 'index.php?option=com_talent&view=talent&id=' . $row->id;
					$image = SiteTalentHelper::getIntroImage ( $row->images );
					?>
			<tr>
				<td><?php if ($image):?><img alt="<?php echo $image->alt?>"
					src="<?php echo $image->src?>"><?php endif ?></td>
				<td><?php $row->index = $i; echo SiteTalentHelper::getTalentDetailsHtml($row)?></td>
				<td><a href="<?php echo $link ?>" target="_blank"
					title="<?php echo $row->title ?>">
								<?php echo $row->title?>
							</a>
					<div><?php echo $row->introtext?></div></td>
			</tr>
				<?php endforeach ?>
			<?php endif ?>
		</tbody>
	</table>
	<input type="hidden" name="return"
		value="<?php echo $this->return_page; ?>" /> <input type="hidden"
		name="task" value="" /> <input type="hidden" name="boxchecked"
		value="0" /> <input type="hidden" name="filter_order"
		value="<?php echo $listOrder ?>" /> <input type="hidden"
		name="filter_order_Dir" value="<?php echo $listDirn ?>" />
	<?php echo JHtml::_('form.token')?>
</form>