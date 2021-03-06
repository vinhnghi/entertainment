var TalentImages = new function() {
	var className = 'TalentImages';
	this.elements = [];
	this.run = function() {
		this.elements = jQuery('.' + className);
		for (var i = 0; i < this.elements.length; i++) {
			this.buildHtml(this.elements[i]);
		}
	};
	this.getData = function(el) {
		return window[el.id]
	}
	this.addItem = function(el, item, i) {
		if (!item) {
			item = {
				id : 0,
				talent_id : 0,
				src : '',
				alt : '',
				caption : '',
				media_type : '',
				ordering : 0
			};
			var data = this.getData(el);
			data.push(item);
			i = data.length - 1;
		}
		jQuery(el).append(this.buildItemHtml(el, item, i));
		this.bindItemEvents(el, item, i);
	};
	this.bindItemEvents = function(el, item, i) {
		SqueezeBox.assign(jQuery('a#' + this.getItemId(el, i) + '_select')
				.get(), {
			parse : 'rel'
		});
		var previewId = this.getItemId(el, i) + '_showimagepreview';
		var xOffset = 20;
		var yOffset = 20;
		jQuery('#' + previewId)
				.hover(
						function(e) {
							var src = jQuery(
									'#' + TalentImages.getItemId(el, i)
											+ '_src').val();
							jQuery('body')
									.append(
											'<div class="imagepreview_container" id="'
													+ previewId
													+ '_imagepreview"><img src="/'
													+ src
													+ '"/><div class="imagepreview_text">'
													+ src + '</div></div>');
							jQuery('#' + previewId + '_imagepreview').css(
									'top', (e.pageY + yOffset) + 'px').css(
									'left', (e.pageX + xOffset) + 'px').fadeIn(
									'fast');
						}, function() {
							jQuery('#' + previewId + '_imagepreview').remove();
						});
		jQuery('#' + previewId).mousemove(
				function(e) {
					jQuery('#' + previewId + '_imagepreview').css('top',
							(e.pageY + yOffset) + 'px').css('left',
							(e.pageX + xOffset) + 'px');
				});
	}
	this.removeItem = function(itemEl, i) {
		var data = this.getData(jQuery(itemEl).closest('.' + className)[0]);
		data.splice(i, 1);
		jQuery(itemEl).remove();
	};
	this.buildHtml = function(el) {
		var data = this.getData(el);
		var html = [];
		html.push('<div class="btn-wrapper">');
		html
				.push('<button type="button" onclick="TalentImages.addItem(jQuery(\'#'
						+ el.id
						+ '\')[0])" class="btn btn-small btn-success"><span class="icon-new icon-white"></span>Add Image</button>');
		html.push('</div>');
		el.innerHTML = html.join('');
		for (var i = 0; i < data.length; i++) {
			this.addItem(el, data[i], i);
		}
	};
	this.getItemId = function(el, i) {
		return 'jform_' + el.id + '_' + i;
	};
	this.buildItemHtml = function(el, item, i) {
		var id = this.getItemId(el, i);
		var html = [];
		var media_manager_url = 'index.php?option=com_media&view=images&tmpl=component&asset=com_talent&author=&fieldid='
				+ id + '_src&folder=com_talent';
		html.push('<div class="' + el.getAttribute('blockclass') + '" id="'
				+ id + '">');
		html.push('<div class="control-group">');
		// html.push('<div class="control-label"><label id="'+id+'-lbl"
		// for="'+id+'_src" class="">Image</label></div>');
		html.push('<div class="controls">');
		html.push('<div class="input-prepend input-append">');
		html
				.push('<div class="media-preview add-on" id="'
						+ id
						+ '_showimagepreview" ><span class="hasTipPreview" title=""><i class="icon-eye"></i></span></div>');
		html.push('<input type="text" placeholder="image/video path" id="' + id
				+ '_src" name="jform[' + el.id + '][' + i
				+ '][src]" class="input-small hasTipImgpath" value="'
				+ item.src + '" readonly="readonly"/>');
		html
				.push('<a href="'
						+ media_manager_url
						+ '" onclick="return false;" id="'
						+ id
						+ '_select" class="modal btn" rel="{handler: \'iframe\', size: {x: 1000, y: 600}}" title="Select">Select</a>');
		html
				.push('<a href="#" onclick="jInsertFieldValue(\'\', \''
						+ id
						+ '_src\');return false;" data-original-title="Clear" class="btn hasTooltip" title="Clear"> <i class="icon-remove"></i></a>');
		html
				.push('<a href="#" onclick="TalentImages.removeItem(jQuery(\'#'
						+ id
						+ '\')[0],'
						+ i
						+ ')" data-original-title="Clear" class="btn hasTooltip" title="Delete"> <i class="icon-trash"></i></a>');
		html.push('</div>');
		html.push('</div>');
		html.push('</div>');
		html.push('<div class="control-group">');
		// html.push('<div class="control-label"><label id="'+id+'_alt-lbl"
		// for="'+id+'_alt" class="">Alt Text</label></div>');
		html.push('<div class="controls">');
		html.push('<input type="text" placeholder="Alt text" name="jform['
				+ el.id + '][' + i + '][alt]" id="' + id + '_alt" value="'
				+ item.alt + '" size="20">');
		html.push('</div>');
		html.push('</div>');
		html.push('<div class="control-group">');
		// html.push('<div class="control-label"><label id="'+id+'_caption-lbl"
		// for="'+id+'_caption" class="">Caption</label></div>');
		html.push('<div class="controls">');
		html.push('<input type="text" placeholder="Caption" name="jform['
				+ el.id + '][' + i + '][caption]" id="' + id
				+ '_caption" value="' + item.caption + '" size="20">');
		html.push('</div>');
		html.push('</div>');
		html.push('</div>');

		return html.join('');
	};
}
/**
 * TalentActivities
 */
var TalentActivities = new function() {
	var className = 'TalentActivities';
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
		var exists = false, initiating = false;
		for (var j=0;j<data.length;j++) {
			if (data[j].added === undefined) {
				data[j].added = true;
				jQuery('#'+el.id+' tbody').append(this.buildItemHtml(el, data[j], i));
				this.bindItemEvents(el,item,i);
			}
			if (data[j].id == item.id) {
				exists = true;
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
	this.getListActivitiesUrl = function(el) {
		var list_activity_url = window.activityListURL + '&elId=' + el.id;
		var checkBoxes = jQuery('#'+el.id+' input.'+el.id+'checkbox');
		var addedActivities = [];
		if (checkBoxes) {
			for (var i=0;i<checkBoxes.length;i++) {
				list_activity_url +='&id[]='+jQuery(checkBoxes[i]).val();
			}
		}
		return list_activity_url;
	};
	this.buildHtml = function(el) {
		var html = [];
		html.push('<div class="btn-wrapper">');
		html.push('<button type="button" class="btn btn-small btn-success"><a href="#" id="'+el.id+'_add" onclick="this.href=TalentActivities.getListActivitiesUrl(jQuery(\'#'+el.id+'\')[0]);return false;" rel="{handler: \'iframe\', size: {x: 1000, y: 600}}"><span class="icon-new icon-white"></span>Add Activity</a></button>');
		html.push('</div>');
		html.push('<div class="btn-wrapper">');
		html.push('<button type="button" onclick="TalentActivities.removeItems(jQuery(\'#'+el.id+'\')[0])" class="btn btn-small"><span class="icon-trash"></span>Delete</button>');
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
		html.push('<td><a href="/administrator/index.php?option=com_activity&task=activity.edit&id='+item.id+'" target="_blank">'+item.title+'</a></td>');
		html.push('<td align="center">'+item.id+'</td>');
		html.push('</tr>');

		return html.join('');
	};
}

function addActivities(elId, items) {
	TalentActivities.addItems(elId, items);
}
/**
 * 
 */
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
				alert(Joomla.JText._('COM_TALENT_TALENT_ERROR_UNACCEPTABLE',
						'Some values are unacceptable'));
				return false;
			}
		}
	}
	document.formvalidator.setHandler('name', function(value) {
		regex = /^[\w\W]+$/;
		return regex.test(value);
	});
	document.formvalidator.setHandler('dob', function(value) {
		regex = /^\d{4}-\d{2}-\d{2}$/;
		return regex.test(value);
	});
	TalentImages.run();
	TalentActivities.run();
});