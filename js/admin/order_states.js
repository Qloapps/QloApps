/**
* 2010-2020 Webkul.
*
* NOTICE OF LICENSE
*
* All right is reserved,
* Please go through this link for complete license : https://store.webkul.com/license.html
*
* DISCLAIMER
*
* Do not edit or add to this file if you wish to upgrade this module to newer
* versions in the future. If you wish to customize this module for your
* needs please refer to https://store.webkul.com/customisation-guidelines/ for more information.
*
*  @author    Webkul IN <support@webkul.com>
*  @copyright 2010-2020 Webkul IN
*  @license   https://store.webkul.com/license.html
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
