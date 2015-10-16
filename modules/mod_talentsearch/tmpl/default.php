<?php
defined ( '_JEXEC' ) or die ();
// Including fallback code for the placeholder attribute in the search field.
?>
<div class="talentsearch <?php echo $moduleclass_sfx ?>">
	<form
		action="<?php echo 'index.php?option=com_talent&view=talents&itemId='.$itemId;?>"
		method="post" class="form-inline">

		<?php echo JLayoutHelper::render('default', $displayData, JPATH_SITE.'/modules/mod_talentsearch/layouts')?>
		
	<?php echo JHtml::_('form.token')?>
	</form>
</div>
