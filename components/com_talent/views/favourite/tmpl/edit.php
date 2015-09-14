<?php
// No direct access
defined ( '_JEXEC' ) or die ( 'Restricted access' );
JHtml::_ ( 'behavior.formvalidation' );
?>
<form
	action="<?php echo JRoute::_('index.php?option=com_talent&layout=edit&id=' . (int) $this->item->id); ?>"
	method="post" name="adminForm" id="adminForm" class="form-validate">
	
	<div class="page-header">
		<h1>
			<?php echo $this->heading; ?>
		</h1>
	</div>
	
	<div class="btn-toolbar">
		<div class="btn-group">
			<button type="button" class="btn btn-primary"
				onclick="Joomla.submitbutton('favourite.save')">
				<span class="icon-ok"></span><?php echo JText::_('JSAVE')?>
				</button>
		</div>
		<div class="btn-group">
			<button type="button" class="btn"
				onclick="Joomla.submitbutton('favourite.cancel')">
				<span class="icon-cancel"></span><?php echo JText::_('JCANCEL')?>
				</button>
		</div>
	</div>
	
	<?php echo JLayoutHelper::render('joomla.edit.title_alias', $this); ?>
	<div class="form-horizontal">
	<?php echo JHtml::_('bootstrap.startTabSet', 'myTab', array('active' => 'general')); ?>
		<?php echo JHtml::_('bootstrap.addTab', 'myTab', 'general', JText::_('Content', true)); ?>
		<div class="row-fluid form-horizontal-desktop">
			<div class="clearfix">
				<?php foreach ($this->form->getFieldSet('basic') as $field) : ?>
						<?php echo $field->getControlGroup(); ?>
				<?php endforeach; ?>
			</div>
			<div class="clearfix">
				<fieldset class="adminform">
					<?php echo $this->form->getInput('favouritetext'); ?>
				</fieldset>
			</div>
		</div>
		<?php echo JHtml::_('bootstrap.endTab'); ?>
	
		<?php echo JHtml::_('bootstrap.addTab', 'myTab', 'publishing', JText::_('Publishing', true)); ?>
		<div class="row-fluid form-horizontal-desktop">
			<div class="span6">
				<?php foreach ($this->form->getGroup('images') as $field) : ?>
						<?php echo $field->getControlGroup(); ?>
				<?php endforeach; ?>
			</div>
			<div class="span6">
				<?php echo JLayoutHelper::render('joomla.edit.publishingdata', $this); ?>
				<?php echo JLayoutHelper::render('joomla.edit.metadata', $this); ?>
			</div>
		</div>
		<?php echo JHtml::_('bootstrap.endTab'); ?>
		
		<?php echo JHtml::_('bootstrap.addTab', 'myTab', 'talents', JText::_('Talents', true)); ?>
		<div class="row-fluid form-horizontal-desktop">
			<?php echo $this->form->getInput('favouritetalents'); ?>
		</div>
		<?php echo JHtml::_('bootstrap.endTab'); ?>
		
	<?php echo JHtml::_('bootstrap.endTabSet'); ?>
	</div>
	<input type="hidden" name="task" value="favourite.edit" />
	
	<?php echo JHtml::_('form.token'); ?>
</form>