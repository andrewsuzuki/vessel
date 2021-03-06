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

var nextnew = 0;

function showMenuitemBox(menuitemIf) {
	data = {};

	if (menuitemIf) {
		data.id = menuitemIf.data('id');
	}
	else
	{
		data.id = 'new' + nextnew;
		nextnew = nextnew + 1;
	}

	$('#menuitem-alert').remove();

	template = Handlebars.compile($('#menuitem-alert-template').html());

	$('body').append(template(data));
	$('#menuitem-alert').css({borderTopLeftRadius: '6px', borderTopRightRadius : '6px'});
	$('#menuitem-alert').modal();

	if (menuitemIf) {
		var type = menuitemIf.data('type');

		if (type == 'page') {
			$('#menuitem-edit-page-title').val(menuitemIf.data('title'));
			$('#menuitem-edit-page-input').val(menuitemIf.data('page'));
		} else if (type == 'link') {
			$('#menuitem-edit-link-title').val(menuitemIf.data('title'));
			$('#menuitem-edit-link-input').val(menuitemIf.data('link'));
		}

		$('#menuitem-alert a[href="#menuitem-edit-' + type + '"]').tab('show'); // show correct tab
	}

	$('#menuitem-alert').on('hidden.bs.modal', function() {
		$(this).remove();
	});
}

String.prototype.addSlashes = function() {
   return this.replace(/[\\"']/g, '\\$&').replace(/\u0000/g, '\\0');
};

String.prototype.quoteAsAttr = function(preserveCR) {
    preserveCR = preserveCR ? '&#13;' : '\n';
    return ('' + this)
        .replace(/&/g, '&amp;')
        .replace(/'/g, '&apos;')
        .replace(/"/g, '&quot;')
        .replace(/</g, '&lt;')
        .replace(/>/g, '&gt;')
        .replace(/\r\n/g, preserveCR)
        .replace(/[\r\n]/g, preserveCR);
};

function loadHashTab() {
	var hash = document.location.hash;
	if (hash) {
		$('.nav-tabs.tabs-from-url a[href='+hash+']').tab('show');
	}
}

function toggleSettingsDefaultRoleSelect() {
	sel = $('input#registration').first();
	if (sel.length) {
		if (sel.is(':checked')) {
			$('select#default_role').prop('disabled', false);
			$('input[name=registration_confirm]').prop('disabled', false);
		} else {
			$('select#default_role').prop('disabled', true);
			$('input[name=registration_confirm]').prop('disabled', true);
		}
	}
}

$(document).ready(function() {

	loadHashTab();

	$(document).on('shown.bs.tab', '.nav-tabs.tabs-from-url a', function (e) {
		window.location.hash = e.target.hash;
	});

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

	toggleSettingsDefaultRoleSelect();

	$(document).on('change', 'input#registration', function() {
		toggleSettingsDefaultRoleSelect();
	});

	if ($('.dd').length) {

		$('.dd').nestable();

		serializeNestable();

		$('.dd').on('change', function() {
			serializeNestable();
		});

		$(document).on('click', '.menu-add-item', function(e) {
			e.preventDefault();
			showMenuitemBox();
		});

		$(document).on('click', '.menuitem-edit', function(e) {
			e.preventDefault();
			showMenuitemBox($(this).closest('.dd-item'));
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
				item_title = tab.find('#menuitem-edit-page-title').first().val();
				item_page  = tab.find('#menuitem-edit-page-input').first().val();
				item_paget = tab.find('#menuitem-edit-page-input option:selected').first().text();
				title      = item_title + ' (Page: ' + item_paget + ')';
				dataattrs  = 'data-title="'+item_title.quoteAsAttr()+'" data-page="'+item_page+'"';
			} else if (tab.length && tab.attr('id') == 'menuitem-edit-link') {
				type       = 'link';
				item_title = tab.find('#menuitem-edit-link-title').first().val();
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

			if ($('.dd > .dd-list .dd-item[data-id="'+id+'"').length) {
				$('.dd > .dd-list .dd-item[data-id="'+id+'"').replaceWith(html);
			} else {
				$('.dd > .dd-list').prepend(html);
			}

			$('.dd').trigger('change');
			$('#menuitem-alert').modal('hide');
		});
		
		$(document).on('click', '.menuitem-copy-page-title', function(e) {
			e.preventDefault();
			$('#menuitem-edit-page-title').val($('#menuitem-edit-page-input option:selected').first().text());
		});
	}
});