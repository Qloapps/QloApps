/*
* NOTICE OF LICENSE
*
* This source file is subject to the Open Software License version 3.0
* that is bundled with this package in the file LICENSE.md
* It is also available through the world-wide-web at this URL:
* https://opensource.org/license/osl-3-0-php
* If you did not receive a copy of the license and are unable to
* obtain it through the world-wide-web, please send an email
* to support@qloapps.com so we can send you a copy immediately.
*
* DISCLAIMER
*
* Do not edit or add to this file if you wish to upgrade this module to a newer
* versions in the future. If you wish to customize this module for your needs
* please refer to https://store.webkul.com/customisation-guidelines for more information.
*
* @author Webkul IN
* @copyright Since 2010 Webkul
* @license https://opensource.org/license/osl-3-0-php Open Software License version 3.0
*/
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
		var $resetBtn = $('.mail-template-reset-btn');
		if (rte_name_match) {
			var name_parts = rte_name_match[1].split('|');
			var has_module = name_parts.length > 1;
			$('#reset_mail_name').val(has_module ? name_parts[1] : name_parts[0]);
			$('#reset_template_type').val(has_module ? EMAIL_TEMPLATE_TYPE_MODULE : EMAIL_TEMPLATE_TYPE_CORE);
			$('#reset_module_name').val(has_module ? name_parts[0] : '');
			$resetBtn.prop('disabled', false);

			var reset_tooltip = has_module
				? mailResetTooltipModule.replace('{module}', name_parts[0]).replace('{iso}', $('input[name="lang"]').val())
				: mailResetTooltipCore;
			$resetBtn.attr('title', reset_tooltip).attr('data-original-title', reset_tooltip).tooltip('fixTitle');
			rte_textarea
		} else {
			$('#reset_mail_name').val('');
			$('#reset_template_type').val('');
			$('#reset_module_name').val('');
			$resetBtn.prop('disabled', true);
			$resetBtn.attr('title', '').attr('data-original-title', '').tooltip('fixTitle');
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

		var templateType = $('#reset_template_type').val();
		var moduleName = $('#reset_module_name').val();

		var $panel = $btn.closest('.email-collapse.in');
		var $htmlTextarea = $panel.find('textarea.rte-mail');
		var $txtTextarea = $panel.find('textarea.rte.noEditor');
		var $titleInput = $panel.find('input[name^="title_"]');
		var $frame = $panel.find('.email-html-frame');
		var $icon = $btn.find('.process-icon-refresh');

		$btn.prop('disabled', true);
		$icon.addClass('icon-spin');

		$.ajax({
			url: admin_translations_link,
			type: 'POST',
			dataType: 'json',
			data: {
				ajax: true,
				action: 'ResetMailTemplate',
				iso_code: $('input[name="lang"]').val(),
				theme: $('input[name="theme"]').val(),
				mail_name: mailName,
				template_type: templateType,
				module_name: moduleName,
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
