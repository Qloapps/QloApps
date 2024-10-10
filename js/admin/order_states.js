/**
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

$(document).ready(function() {
	$('#refunded_on, #denied_on').click(function() {
		if ($(this).is(':checked')) {
			$('#refunded_on, #denied_on').prop('checked', false);
			$(this).prop('checked', true);
		}
	});

	// for refunded and denied options we have to load page
	// Because both options are changing and default js showing only state change in click action
	$(".ajax_table_link").click(function () {
		var statusLink = $(this);
		if (statusLink.closest('td').hasClass('state-refunded') || statusLink.closest('td').hasClass('state-denied')) {
			$(document).off().on('ajaxComplete', function( e, xhr, settings ) {
				if (typeof xhr.responseJSON.success !== 'undefined' && xhr.responseJSON.success == 1) {
					if (statusLink.hasClass('action-enabled')){
						if (statusLink.closest('td').hasClass('state-refunded')) {
							var toChangeStatus = statusLink.closest('tr').find('.state-denied .ajax_table_link');
						} else if (statusLink.closest('td').hasClass('state-denied')) {
							var toChangeStatus = statusLink.closest('tr').find('.state-refunded .ajax_table_link');
						}
						if (toChangeStatus.hasClass('action-enabled')) {
							toChangeStatus.removeClass('action-enabled').addClass('action-disabled');
							toChangeStatus.children().each(function () {
								if ($(this).hasClass('hidden')) {
									$(this).removeClass('hidden');
								} else {
									$(this).addClass('hidden');
								}
							});
						}
					}
				}
			});
		}
	});
});
