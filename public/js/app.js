function trip(options) {

	settings = {
		endpoint: null,
		data: {},
		updateEntities: true,
		doEval: true,
		makeAsync: true,
		success: function(response) {

		},
		fail: function(response) {
			handleError(response.message);
		},
		completed: function(response) {
			
		}
	};

	settings = $.extend(settings, options);

	if (!settings.endpoint) {
		return false;
	}

	$.ajax({
		type: 'POST',

		url: settings.endpoint,

		data: JSON.stringify(settings.data),

		async: settings.makeAsync,

		contentType: 'application/json; charset=utf-8',

		complete: function(jqXHR, textStatus) {

			return_data = {
				success: false,
				message: null,
				data: {},
				entities: {},
				eval: ''
			};

			if (textStatus != 'success') {

				handleError('unknown');

			} else {

				if (jqXHR.responseText) {
					
					try {
						tb_return_data = $.parseJSON(jqXHR.responseText);

						if (tb_return_data) {
							// handle as json

							return_data = tb_return_data;

							if (return_data.success) {
								// successful server response with successful action

								// update page entities specified in response
								if (settings.updateEntities && $.isArray(return_data.entities) && return_data.entities.length) {
									return_data.entities.forEach(function(tbu) {
										if (typeof tbu === 'object') {
											// find entity with id
											sel = findEntity(tbu.entity, tbu.eid);
											// replace html
											sel.html(tbu.html);
										}
									});
								}

								if (settings.doEval && return_data.eval) {
									eval(return_data.eval);
								}

								settings.success(return_data);

							} else {
								// successful server response with failed action

								if (settings.fail !== false) {
									// failed action callback
									settings.fail(return_data);
								}
							}
						}
					} catch (e) {
						handleError('unknown');
					}
				}

			}
			
			settings.completed(return_data);
		}
	});
}

function handleError(message) {
	if (!message || !message.length || (message && message == 'unknown')) {
		message = 'An unknown error occurred. Please refresh and try again.';
	}

	showAlert(message, 'danger', 'Error');
}

function showAlert(message, type, title) {
	$('#vessel-alert').remove();

	template = Handlebars.compile($('#vessel-alert-template').html());

	data = {body: message};

	if (title) {
		data.title = title;
	} else if (type == 'danger') {
		data.title = 'Error';
	} else if (type) {
		data.title = type.charAt(0).toUpperCase() + type.substr(1);
	}

	$('body').append(template(data));

	if (type) {
		$('#vessel-alert').find('.modal-header').addClass('bg-'+type).css({borderTopLeftRadius: '6px', borderTopRightRadius : '6px'});
	}

	$('#vessel-alert').modal();
}

function refreshAutoslugs() {
	$('[data-slugto]').each(function() {
		$($(this).data('slugto')).slugify(this);
	});
}

function refreshFormatter(fields) {
	var inputs = {};

	fields.each(function() {
		if (this.nodeName == 'INPUT' || this.nodeName == 'TEXTAREA' || this.nodeName == 'SELECT') {
			var name = $(this).attr('name');
			if (name) {
				if (!inputs.hasOwnProperty(name)) {
					inputs[name] = $(this).val();
				}
			}
		}
	});

	showAlert('Reloading...', 'info');

	trip({
		endpoint: '/vessel/api/flashinput',
		data: inputs,
		success: function(response) {
			location.reload();
		}
	});
}

function loadSetTheme() {
	var field = $('input[type=hidden][name=theme]').first();
	if (field.length) {
		var name = field.val();
		if (name) {
			setThemeChoice(name);
		}
	}
}

function setThemeChoice(name) {
	var field = $('input[type=hidden][name=theme]').first();
	if (name && field.length) {
		$('.vessel-theme-choice').removeClass('panel-default panel-success vessel-chosen-theme');
		panel = $('.vessel-choose-theme[data-themename='+name+']').first().closest('.vessel-theme-choice');
		panel.addClass('panel-success vessel-chosen-theme');
		field.val(name);
	}
}

function serializeNestable() {
	sel = $('input.hidden-menu-serialized');
	if (sel.length) {
		return sel.val(JSON.stringify($('.dd').nestable('serialize')));
	}
}

function showMenuitemBox(menuitemIf) {
	data = {};

	if (menuitemIf) {
		// get existing data from menuitem
	}
	else
	{
		data.id = 'new';
	}

	$('#menuitem-alert').remove();

	template = Handlebars.compile($('#menuitem-alert-template').html());

	$('body').append(template(data));
	$('#menuitem-alert').css({borderTopLeftRadius: '6px', borderTopRightRadius : '6px'});
	$('#menuitem-alert').modal();
}

String.prototype.addSlashes = function() {
   return this.replace(/[\\"']/g, '\\$&').replace(/\u0000/g, '\\0');
};

String.prototype.quoteAsAttr = function(preserveCR) {
    preserveCR = preserveCR ? '&#13;' : '\n';
    return ('' + this) /* Forces the conversion to string. */
        .replace(/&/g, '&amp;') /* This MUST be the 1st replacement. */
        .replace(/'/g, '&apos;') /* The 4 other predefined entities, required. */
        .replace(/"/g, '&quot;')
        .replace(/</g, '&lt;')
        .replace(/>/g, '&gt;')
        /*
        You may add other replacements here for HTML only 
        (but it's not necessary).
        Or for XML, only if the named entities are defined in its DTD.
        */
        .replace(/\r\n/g, preserveCR) /* Must be before the next replacement. */
        .replace(/[\r\n]/g, preserveCR);
};

$(document).ready(function() {

	$(document).on('change', '.vessel-page-edit-form .vessel-select-formatter', function() {
		refreshFormatter($('.vessel-page-edit-form .vessel-carry-field'));
	});

	$(document).on('change', '.vessel-block-edit-form .vessel-select-formatter', function() {
		refreshFormatter($('.vessel-block-edit-form .vessel-carry-field'));
	});

	$(document).on('click', '.vessel-choose-theme', function(e) {
		e.preventDefault();
		var name = $(this).data('themename');
		setThemeChoice(name);
	});

	// autoslugs
	refreshAutoslugs();

	// grab set theme value and choose it
	loadSetTheme();

	$('.dd').nestable();

	serializeNestable();

	$('.dd').on('change', function() {
		serializeNestable();
	});

	$(document).on('click', '.menu-add-item', function(e) {
		e.preventDefault();
		showMenuitemBox();
	});

	$(document).on('click', '.menuitem-delete', function(e) {
		e.preventDefault();
		var item = $(this).closest('.dd-item');
		if (item.length && !item.siblings().length) {
			var parent = item.parent().closest('.dd-item');
			parent.find('.dd-list').remove();
			parent.find('button').remove();
		}
		item.remove();
		$('.dd').trigger('change');
	});

	$(document).on('click', '.menuitem-alert-save', function(e) {
		e.preventDefault();

		id = $(this).data('id');

		tab = $('#menuitem-alert .tab-content .tab-pane.active').first();

		if (tab.length && tab.attr('id') == 'menuitem-edit-page') {
			type       = 'page';
			item_title = tab.find('#menuitem-edit-title').first().val();
			item_page  = tab.find('#menuitem-edit-page-input').first().val();
			title      = item_title + ' (Page: ' + item_page + ')';
			dataattrs  = 'data-title="'+item_title.quoteAsAttr()+'" data-page="'+item_page+'"';
		} else if (tab.length && tab.attr('id') == 'menuitem-edit-link') {
			type       = 'link';
			item_title = tab.find('#menuitem-edit-title').first().val();
			item_link  = tab.find('#menuitem-edit-link-input').first().val();
			title      = item_title + ' (Link: <a href="'+item_link.quoteAsAttr()+'">'+item_link+'</a>)';
			dataattrs  = 'data-title="'+item_title.quoteAsAttr()+'" data-link="'+item_link.quoteAsAttr()+'"';
		} else if (tab.length && tab.attr('id') == 'menuitem-edit-sep') {
			type      = 'sep';
			title     = 'Separator';
			dataattrs = '';
		} else {
			$('#menuitem-alert').modal('hide');
			return false;
		}
		
		template = Handlebars.compile($('#vessel-menuitem-template').html());
		html = template({id: id, title: title, type: type, dataattrs: dataattrs});
		$('.dd > .dd-list').prepend(html);
		$('.dd').trigger('change');
		$('#menuitem-alert').modal('hide');
	});
});