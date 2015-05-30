<?php
defined ( '_JEXEC' ) or die ();

JHtml::_ ( 'behavior.tabstate' );
JHtml::_ ( 'behavior.keepalive' );
JHtml::_ ( 'behavior.calendar' );
JHtml::_ ( 'behavior.formvalidator' );
// JHtml::_ ( 'formbehavior.chosen', 'select' );
// JHtml::_ ( 'behavior.modal', 'a.modal_jform_activityhistory' );

// Create shortcut to parameters.
$params = $this->state->get ( 'params' );
?>
<div class="edit item-page<?php echo $this->pageclass_sfx; ?>">
	<?php if ($params->get('show_page_heading', 1)) : ?>
	<div class="page-header">
		<h1>
			<?php echo $this->escape($params->get('page_heading')); ?>
		</h1>
	</div>
	<?php endif; ?>

	<form
		action="<?php echo JRoute::_('index.php?option=com_activity&a_id=' . (int) $this->item->id); ?>"
		method="post" name="adminForm" id="adminForm"
		class="form-validate form-vertical">
		<div class="btn-toolbar">
			<div class="btn-group">
				<button type="button" class="btn btn-primary"
					onclick="Joomla.submitbutton('type.save')">
					<span class="icon-ok"></span><?php echo JText::_('JSAVE')?>
				</button>
			</div>
			<div class="btn-group">
				<button type="button" class="btn"
					onclick="Joomla.submitbutton('type.cancel')">
					<span class="icon-cancel"></span><?php echo JText::_('JCANCEL')?>
				</button>
			</div>
		</div>
	<?php echo JLayoutHelper::render('joomla.edit.title_alias', $this); ?>
	<?php echo JHtml::_('bootstrap.startTabSet', 'myTab', array('active' => 'general')); ?>
		<?php echo JHtml::_('bootstrap.addTab', 'myTab', 'general', JText::_('Content', true)); ?>
		<div class="row-fluid form-horizontal-desktop">
			<div class="span9">
				<fieldset class="adminform">
					<?php echo $this->form->getInput('typetext'); ?>
				</fieldset>
			</div>
			<div class="span3">
				<?php echo JLayoutHelper::render('joomla.edit.global', $this); ?>
			</div>
		</div>
		<?php echo JHtml::_('bootstrap.endTab'); ?>
	
		<?php echo JHtml::_('bootstrap.addTab', 'myTab', 'publishing', JText::_('Publishing', true)); ?>
		<div class="row-fluid form-horizontal-desktop">
			<div class="span6">
				<?php echo JLayoutHelper::render('joomla.edit.publishingdata', $this); ?>
			</div>
			<div class="span6">
				<?php echo JLayoutHelper::render('joomla.edit.metadata', $this); ?>
			</div>
		</div>
		<?php echo JHtml::_('bootstrap.endTab'); ?>
		
		<?php echo JHtml::_('bootstrap.addTab', 'myTab', 'images', JText::_('Images', true)); ?>
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
		</div>
		<?php echo JHtml::_('bootstrap.endTab'); ?>
		
	<?php echo JHtml::_('bootstrap.endTabSet'); ?>	
	<input type="hidden" name="task" value="" /> <input type="hidden"
			name="return" value="<?php echo $this->return_page; ?>" />
			<?php echo JHtml::_('form.token'); ?>
	</form>
</div>
