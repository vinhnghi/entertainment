Joomla.submitbutton = function(task) {
	if (task == '') {
		return false;
	} else {
		var isValid = true;
		var action = task.split('.');
		if (action[1] != 'cancel' && action[1] != 'close') {
			var forms = $$('form.form-validate');
			for (var i = 0; i < forms.length; i++) {
				if (!document.formvalidator.isValid(forms[i])) {
					isValid = false;
					break;
				}
			}
		}

		if (isValid) {
			Joomla.submitform(task);
			return true;
		} else {
			alert(Joomla.JText._(
					'COM_ACTIVITY_ACTIVITY_ERROR_UNACCEPTABLE',
					'Some values are unacceptable'));
			return false;
		}
	}
}
jQuery(function() {
	document.formvalidator.setHandler('title', function(value) {
		regex = /^[\w\W]+$/;
		return regex.test(value);
	});
});