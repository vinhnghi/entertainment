function listItemTask(id, task) {
	var f = document.adminForm;
	alert(id);
	alert(task);
	alert(f);
	cb = eval('f.' + id);
	alert(cb);
	if (cb) {
		for (i = 0; true; i++) {
			cbx = eval('f.cb' + i);
			if (!cbx)
				break;
			cbx.checked = false;
		} // for
		cb.checked = true;
		f.boxchecked.value = 1;
		submitbutton(task);
	}
	return false;
}