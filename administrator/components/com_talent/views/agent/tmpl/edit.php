<?php
// No direct access
defined ( '_JEXEC' ) or die ( 'Restricted access' );
JHtml::_ ( 'behavior.formvalidation' );
?>
<form
	action="<?php echo JRoute::_('index.php?option=com_talent&layout=edit&id=' . (int) $this->item->id); ?>"
	method="post" name="adminForm" id="adminForm" class="form-validate">
	<div class="form-horizontal">
	<?php echo JHtml::_('bootstrap.startTabSet', 'myTab', array('active' => 'user_details')); ?>
		<?php echo JHtml::_('bootstrap.addTab', 'myTab', 'user_details', JText::_('Talent details', true)); ?>
		<div class="row-fluid form-horizontal-desktop">
			<div class="span6">
				<?php foreach ($this->form->getFieldset('user_details_basic') as $field) : ?>
				<?php echo $field->getControlGroup(); ?>
				<?php endforeach; ?>
				<?php foreach ($this->form->getFieldset('user_details_extra') as $field) : ?>
				<?php echo $field->getControlGroup(); ?>
				<?php endforeach; ?>
			</div>
		</div>
		<?php echo JHtml::_('bootstrap.endTab'); ?>
		<?php echo JHtml::_('bootstrap.addTab', 'myTab', 'general', JText::_('Description', true)); ?>
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
					<?php echo $this->form->getInput('agenttext'); ?>
				</fieldset>
			</div>
		</div>
		<?php echo JHtml::_('bootstrap.endTab'); ?>
		
		<?php echo JHtml::_('bootstrap.addTab', 'myTab', 'publishing', JText::_('Publishing', true)); ?>
		<div class="row-fluid form-horizontal-desktop">
			<div class="span6">
				<?php echo JLayoutHelper::render('joomla.edit.metadata', $this); ?>
				<?php echo JLayoutHelper::render('joomla.edit.global', $this); ?>
			</div>
		</div>
		<?php echo JHtml::_('bootstrap.endTab'); ?>
		
	
	<?php echo JHtml::_('bootstrap.endTabSet'); ?>
	</div>
	<input type="hidden" name="task" value="agent.edit" />
	
	<?php echo JHtml::_('form.token'); ?>
</form>