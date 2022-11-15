/*
* 2007-2017 PrestaShop
*
* NOTICE OF LICENSE
*
* This source file is subject to the Academic Free License (AFL 3.0)
* that is bundled with this package in the file LICENSE.txt.
* It is also available through the world-wide-web at this URL:
* http://opensource.org/licenses/afl-3.0.php
* If you did not receive a copy of the license and are unable to
* obtain it through the world-wide-web, please send an email
* to license@prestashop.com so we can send you a copy immediately.
*
* DISCLAIMER
*
* Do not edit or add to this file if you wish to upgrade PrestaShop to newer
* versions in the future. If you wish to customize PrestaShop for your
* needs please refer to http://www.prestashop.com for more information.
*
*  @author PrestaShop SA <contact@prestashop.com>
*  @copyright  2007-2017 PrestaShop SA
*  @license    http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*/

$(document).ready(function() {
	$(document).on('click', '#SubmitCreate', function(e) {
		e.preventDefault();
		submitFunction('SubmitCreate');
	});

	$(document).on('click', '.btn-transform', function(e) {
		e.preventDefault();
		submitFunction('submitTransformAccount');
	});
	$('.is_customer_param').hide();
});

function submitFunction(submitAction) {
	$('#create_account_error').html('').hide();
	var data = {
		controller: 'authentication',
		ajax: true,
		email_create: $('#email_create').val(),
		back: $('input[name=back]').val(),
		token: token
	};
	data[submitAction] = 1;

	$.ajax({
		type: 'POST',
		url: baseUri + '?rand=' + new Date().getTime(),
		async: true,
		cache: false,
		dataType : "json",
		headers: { "cache-control": "no-cache" },
		data: data,
		success: function(jsonData) {
			// display confirmations
			if (jsonData.hasConfirmation) {
				if (in_array('TRANSFORM_OK', jsonData.confirmations)) {
					$('#guest_transform_success').show('slow');
				}
				return;
			}
			if (jsonData.hasInformation) {
				var informations = '';
				for(information in jsonData.informations) {
					//IE6 bug fix
					if(information != 'indexOf') {
						informations += '<li>' + jsonData.informations[information] + '</li>';
					}
				}
				$('#create_account_information').html('<ol>' + informations + '</ol>').show();
				return;
			}
			if (jsonData.hasError) {
				var errors = '';
				for(error in jsonData.errors) {
					//IE6 bug fix
					if(error != 'indexOf') {
						errors += '<li>' + jsonData.errors[error] + '</li>';
					}
				}
				$('#create_account_error').html('<ol>' + errors + '</ol>').show();
			} else {
				// adding a div to display a transition
				$('#center_column').html('<div id="noSlide">' + $('#center_column').html() + '</div>');
				$('#noSlide').fadeOut('slow', function() {
					$('#noSlide').html(jsonData.page);
					$(this).fadeIn('slow', function() {
						if (typeof bindUniform !=='undefined') {
							bindUniform();
						}
						if (typeof bindStateInputAndUpdate !=='undefined') {
							bindStateInputAndUpdate();
						}
						document.location = '#account-creation';
					});
				});
			}
		},
		error: function(XMLHttpRequest, textStatus, errorThrown) {
			error = "TECHNICAL ERROR: unable to load form.\n\nDetails:\nError thrown: " + XMLHttpRequest + "\n" + 'Text status: ' + textStatus;
			if (!!$.prototype.fancybox) {
				$.fancybox.open([{
					type: 'inline',
					autoScale: true,
					minHeight: 30,
					content: "<p class='fancybox-error'>" + error + '</p>'
				}], {
					padding: 0
				});
			} else {
				alert(error);
			}
		}
	});
}