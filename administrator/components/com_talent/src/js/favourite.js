var FavouriteTalents = new function() {
	var className = 'FavouriteTalents';
	this.elements = [];
	this.run = function() {
		this.elements = jQuery('.'+className);
		for (var i=0;i<this.elements.length;i++) {
			this.buildHtml(this.elements[i]);
		}
	};
	this.getData = function(el) {
		return window[el.id]
	}
	this.addItems = function(elId,items) {
		SqueezeBox.close();
		if	(items) {
			for(var i=0;i<items.length;i++) {
				this.addItem(jQuery('#'+elId)[0], items[i], i);
			}
		}
	};
	this.addItem = function(el, item, i) {
		var data = this.getData(el);
		var exists = false;
		for (var j=0;j<data.length;j++) {
			if (data[j].id == item.id) {
				if(data[j].added === undefined)
					data[j].added = false;
				exists = data[j].added;
				break;
			}
		}
		if (!exists) {
			item.added = true;
			data.push(item);
			jQuery('#'+el.id+' tbody').append(this.buildItemHtml(el, item, i));
			this.bindItemEvents(el,item,i);
		} 
	};
	this.bindItemEvents = function(el,item,i) {
	}
	this.removeItems = function(el) {
		var checkedBoxes = jQuery('#'+el.id+' input.'+el.id+'checkbox:checked');
		var data = this.getData(el);
		if (checkedBoxes) {
			for (var i=0;i<checkedBoxes.length;i++) {
				var checkbox = jQuery(checkedBoxes[i]);
				for (var j=0;j<data.length;j++) {
					if (data[j].id = checkbox.val()) {
						this.removeItem(checkbox.closest('tr')[0],j);
						break;
					}
				}
			}
		}
	};
	this.removeItem = function(itemEl,i) {
		var data = this.getData(jQuery(itemEl).closest('.'+className)[0]);
		data[i].added = false;
		data.splice(i,1);
		jQuery(itemEl).remove();
	};
	this.getListTalentsUrl = function(el) {
		var list_talent_url = window.talentListURL + '&elId=' + el.id;
		var checkBoxes = jQuery('#'+el.id+' input.'+el.id+'checkbox');
		var addedTalents = [];
		if (checkBoxes) {
			for (var i=0;i<checkBoxes.length;i++) {
				list_talent_url +='&id[]='+jQuery(checkBoxes[i]).val();
			}
		}
		return list_talent_url;
	};
	this.buildHtml = function(el) {
		var html = [];
		html.push('<div class="btn-wrapper">');
		html.push('<button type="button" class="btn btn-small btn-success"><a href="#" id="'+el.id+'_add" onclick="this.href=FavouriteTalents.getListTalentsUrl(jQuery(\'#'+el.id+'\')[0]);return false;" rel="{handler: \'iframe\', size: {x: 1000, y: 600}}"><span class="icon-new icon-white"></span>Add Talent</a></button>');
		html.push('</div>');
		html.push('<div class="btn-wrapper">');
		html.push('<button type="button" onclick="FavouriteTalents.removeItems(jQuery(\'#'+el.id+'\')[0])" class="btn btn-small"><span class="icon-trash"></span>Delete</button>');
		html.push('</div>');

		html.push('<table class="table table-striped table-hover">');
		html.push('<thead>');
		html.push('<tr>');
		html.push('<th width="1%">#</th>');
		html.push('<th width="2%"><input type="checkbox" name="checkall-toggle" value="" class="hasTooltip" title="" onclick="Joomla.checkAll(this)" data-original-title="Check All"></th>');
		html.push('<th width="95%"><a>Name</a></th>');
		html.push('<th width="2%"><a>Id</a></th>');
		html.push('</tr>');
		html.push('</thead>');
		html.push('<tbody>');
		html.push('</tbody>');
		html.push('</table>');
		
		el.innerHTML = html.join('');
		SqueezeBox.assign(jQuery('a#'+el.id+'_add').get(), {
			parse: 'rel'
		});

		var data = this.getData(el);
		for (var i=0;i<data.length;i++) {
			this.addItem(el, data[i], i);
		}
	};
	this.buildItemHtml = function(el, item, i) {
		var html = [];

		html.push('<tr>');
		html.push('<td>'+(i+1)+'</td>');
		html.push('<td><input type="checkbox" id="cb'+i+'" value="'+item.id+'" class="'+el.id+'checkbox"/>');
		html.push('<input type="hidden" name="jform['+el.id+'][]" value="'+item.id+'"></td>');
		html.push('<td><a href="/administrator/index.php?option=com_talent&task=talent.edit&id='+item.id+'" target="_blank">'+item.title+'</a></td>');
		html.push('<td align="center">'+item.id+'</td>');
		html.push('</tr>');

		return html.join('');
	};
}

function addTalents(elId, items) {
	FavouriteTalents.addItems(elId, items);
}

jQuery(function() {
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
				alert(Joomla.JText._('COM_TALENT_FAVOURITE_ERROR_UNACCEPTABLE',
						'Some values are unacceptable'));
				return false;
			}
		}
	}
	document.formvalidator.setHandler('title', function(value) {
		regex = /^[\w\W]+$/;
		return regex.test(value);
	});
	FavouriteTalents.run();
});