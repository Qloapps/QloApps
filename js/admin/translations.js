$(document).ready(function () {
	$('a.useSpecialSyntax').click(function () {
		var syntax = $(this).find('img').attr('alt');
		$('#BoxUseSpecialSyntax .syntax span').html(syntax + ".");
	});

	$('.mails_field').on('shown.bs.collapse', function () {
		// get active email
		var active_email = $(this).find('.email-collapse.in');
		// get iframe container for active email
		var frame = active_email.find('.email-html-frame');
		// get source url for active email
		var src = frame.data('email-src');
		// get rte container for active email
		var rte_textarea = active_email.find('textarea.rte-mail');
		var rte_mail_selector = rte_textarea.data('rte');
		var rte_name_match = (rte_textarea.attr('name') || '').match(/\[html\]\[(.+)\]$/);
		if (!rte_name_match) {
			// this template has no .html file (txt-only) - fall back to the txt textarea's name
			var txt_textarea = active_email.find('textarea.rte.noEditor');
			rte_name_match = (txt_textarea.attr('name') || '').match(/\[txt\]\[(.+)\]$/);
		}
		if (rte_name_match) {
			$('#reset_mail_name').val(rte_name_match[1]);
			$('.mail-template-reset-btn').prop('disabled', false);
			rte_textarea
		} else {
			$('#reset_mail_name').val('');
			$('.mail-template-reset-btn').prop('disabled', true);
		}
		var rte_mail_config = {};
		rte_mail_config['editor_selector'] = 'rte-mail-' + rte_mail_selector;
		rte_mail_config['plugins'] = "colorpicker link image paste pagebreak table contextmenu filemanager table code media autoresize textcolor anchor fullpage";
		$('#translation_mails-control-actions').appendTo($(this).find('.panel-collapse.in'));
		if (frame.find('iframe.email-frame').length == 0) {
			// load iframe
			frame.append('<iframe class="email-frame" />');
			$.ajax({
				url: 'ajax.php',
				type: 'POST',
				dataType: 'html',
				data: {
					getEmailHTML: true,
					email: src
				},
				success: function (result) {
					var doc = frame.find('iframe')[0].contentWindow.document;
					doc.open();
					doc.write(result);
					doc.close();
					tinySetup(rte_mail_config);
				}
			});

		}
	});

	$('.mail-variable-tag').on('click', function () {
		var textarea = $(this).closest('.email-collapse').find('> .tab-content > .tab-pane.active textarea').get(0);
		if (textarea) {
			insertVariable(textarea, $(this).data('variable'));
		}
	});

	$('.mail-template-reset-btn').on('click', function () {
		var $btn = $(this);
		if ($btn.prop('disabled')) {
			return;
		}

		var mailName = $('#reset_mail_name').val();
		if (!mailName) {
			return;
		}

		var $panel = $btn.closest('.email-collapse.in');
		var $htmlTextarea = $panel.find('textarea.rte-mail');
		var $txtTextarea = $panel.find('textarea.rte.noEditor');
		var $titleInput = $panel.find('input[name^="title_"]');
		var $frame = $panel.find('.email-html-frame');
		var $icon = $btn.find('.process-icon-refresh');

		$btn.prop('disabled', true);
		$icon.addClass('icon-spin');

		$.ajax({
			url: 'ajax.php',
			type: 'POST',
			dataType: 'json',
			data: {
				resetTranslationMail: true,
				iso_code: $('input[name="lang"]').val(),
				theme: $('input[name="theme"]').val(),
				mail_name: mailName,
				token: $('#translation_mails_token').val()
			},
			success: function (result) {
				$btn.prop('disabled', false);
				$icon.removeClass('icon-spin');

				if (!result || result.hasError) {
					showErrorMessage((result && result.error) || mailResetErrorMsg);
					return;
				}

				if (result.html !== null && $htmlTextarea.length) {
					var editor = (typeof tinymce !== 'undefined' && $htmlTextarea.attr('id')) ? tinymce.get($htmlTextarea.attr('id')) : null;
					if (editor) {
						editor.setContent(result.html);
					} else {
						$htmlTextarea.val(result.html);
					}

					var titleMatch = result.html.match(/<title>([^<]*)<\/title>/i);
					if (titleMatch) {
						$titleInput.val(titleMatch[1]);
					}
				}

				if (result.txt !== null && $txtTextarea.length) {
					$txtTextarea.val(result.txt);
				}

				if ($frame.length) {
					$frame.empty().append('<iframe class="email-frame" />');
					$.ajax({
						url: 'ajax.php',
						type: 'POST',
						dataType: 'html',
						data: {
							getEmailHTML: true,
							email: $frame.data('email-src')
						},
						success: function (htmlResult) {
							var doc = $frame.find('iframe')[0].contentWindow.document;
							doc.open();
							doc.write(htmlResult);
							doc.close();
						}
					});
				}

				showSuccessMessage(mailResetSuccessMsg);
			},
			error: function () {
				$btn.prop('disabled', false);
				$icon.removeClass('icon-spin');
				showErrorMessage(mailResetErrorMsg);
			}
		});
	});
});

function insertVariable(field, text) {
	var editor = (typeof tinymce !== 'undefined' && field.id) ? tinymce.get(field.id) : null;

	if (editor) {
		editor.execCommand('mceInsertContent', false, text);
		editor.focus();
		return;
	}

	field.focus();
	var startPos = field.selectionStart;
	var endPos = field.selectionEnd;
	field.value = field.value.substring(0, startPos) + text + field.value.substring(endPos, field.value.length);
	field.selectionStart = field.selectionEnd = startPos + text.length;
}
