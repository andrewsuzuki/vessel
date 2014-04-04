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
			name = $(this).attr('name');
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

$(document).ready(function() {

	$(document).on('change', '.vessel-page-edit-form .vessel-select-formatter', function() {
		refreshFormatter($('.vessel-page-edit-form .vessel-carry-field'));
	});

	$(document).on('change', '.vessel-block-edit-form .vessel-select-formatter', function() {
		refreshFormatter($('.vessel-block-edit-form .vessel-carry-field'));
	});

	// autoslugs
	
	refreshAutoslugs();
});