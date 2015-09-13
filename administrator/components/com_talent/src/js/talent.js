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
});