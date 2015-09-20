<?php
// No direct access
defined ( '_JEXEC' ) or die ( 'Restricted access' );
JHtml::_ ( 'behavior.formvalidation' );

?>
<form method="post" name="adminForm" id="adminForm"
	class="form-validate">
	<div class="btn-toolbar">
		<div class="btn-group">
			<button type="button" class="btn btn-primary"
				onclick="Joomla.submitbutton('talent.apply')">
				<span class="icon-ok"></span><?php echo JText::_('JSAVE')?>
				</button>
		</div>
	</div>

	<div class="form-horizontal">
	<?php echo JHtml::_('bootstrap.startTabSet', 'myTab', array('active' => 'user_details')); ?>
		<?php echo JHtml::_('bootstrap.addTab', 'myTab', 'user_details', JText::_('Talent details', true)); ?>
		<div class="row-fluid form-horizontal-desktop">
			<div class="span5">
				<?php foreach ($this->form->getFieldset('user_details_basic') as $field) : ?>
				<?php echo $field->getControlGroup(); ?>
				<?php endforeach; ?>
			</div>
			<div class="span5">
				<?php foreach ($this->form->getFieldset('user_details_extra') as $field) : ?>
				<?php echo $field->getControlGroup(); ?>
				<?php endforeach; ?>
			</div>
			<div class="clearfix">
				<?php echo JLayoutHelper::render('joomla.edit.global', $this); ?>
			</div>
		</div>
		<?php echo JHtml::_('bootstrap.endTab'); ?>
		<?php echo JHtml::_('bootstrap.addTab', 'myTab', 'general', JText::_('Description', true)); ?>
		<div class="row-fluid form-horizontal-desktop">
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
				<?php echo $this->form->getInput('talenttext'); ?>
			</fieldset>
		</div>
		<?php echo JHtml::_('bootstrap.endTab'); ?>
	
		<?php echo JHtml::_('bootstrap.addTab', 'myTab', 'publishing', JText::_('Publishing', true)); ?>
		<div class="row-fluid form-horizontal-desktop">
			<div class="span6">
				<?php echo JLayoutHelper::render('joomla.edit.metadata', $this); ?>
			</div>
		</div>
		<?php echo JHtml::_('bootstrap.endTab'); ?>
		
		<?php echo JHtml::_('bootstrap.addTab', 'myTab', 'images', JText::_('Images', true)); ?>
		<div class="row-fluid form-horizontal-desktop">
			<?php echo $this->form->getInput('talentimages'); ?>
		</div>
		<?php echo JHtml::_('bootstrap.endTab'); ?>
			
	<?php echo JHtml::_('bootstrap.endTabSet'); ?>
	</div>
	<input type="hidden" name="task" value="" />
	<input type="hidden" name="id" value="<?php echo $this->item->id?>" />
	<?php echo JHtml::_('form.token'); ?>
</form>