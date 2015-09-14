<?php
// No direct access
defined ( '_JEXEC' ) or die ( 'Restricted access' );
JHtml::_ ( 'behavior.formvalidation' );
?>
<form
	action="<?php echo JRoute::_('index.php?option=com_talent&layout=edit&id=' . (int) $this->item->id); ?>"
	method="post" name="adminForm" id="adminForm" class="form-validate">
	<?php echo JLayoutHelper::render('joomla.edit.title_alias', $this); ?>
	<div class="form-horizontal">
	<?php echo JHtml::_('bootstrap.startTabSet', 'myTab', array('active' => 'general')); ?>
		<?php echo JHtml::_('bootstrap.addTab', 'myTab', 'general', JText::_('Content', true)); ?>
		<div class="row-fluid form-horizontal-desktop">
			<div class="span9">
				<?php foreach ($this->form->getGroup('images') as $field) : ?>
				<?php if ($field->getAttribute('start') == true):?>
				<div class="span6">
				<?php endif;?>
						<?php echo $field->getControlGroup(); ?>
				<?php if ($field->getAttribute('end') == true):?>
				</div>
				<?php endif;?>
				<?php endforeach; ?>
				<fieldset class="adminform">
					<?php echo $this->form->getInput('favoritetext'); ?>
				</fieldset>
			</div>
			<div class="span3">
				<?php echo JLayoutHelper::render('joomla.edit.global', $this); ?>
				<?php echo $this->form->getInput('agent_id'); ?>
			</div>
		</div>
		<?php echo JHtml::_('bootstrap.endTab'); ?>
	
		<?php echo JHtml::_('bootstrap.addTab', 'myTab', 'publishing', JText::_('Publishing', true)); ?>
		<div class="row-fluid form-horizontal-desktop">
				<?php echo JLayoutHelper::render('joomla.edit.publishingdata', $this); ?>
				<?php echo JLayoutHelper::render('joomla.edit.metadata', $this); ?>
		</div>
		<?php echo JHtml::_('bootstrap.endTab'); ?>
		
	<?php echo JHtml::_('bootstrap.endTabSet'); ?>
	</div>
	<input type="hidden" name="task" value="favorite.edit" />
	
	<?php echo JHtml::_('form.token'); ?>
</form>