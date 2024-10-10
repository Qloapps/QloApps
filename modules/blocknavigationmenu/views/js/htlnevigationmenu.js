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

$(document).ready(function()
{
	$(document).on("click", ".navigation-link", function(e) {
		if (typeof $(this).context.hash !== 'undefined') {
			var block = $(this).context.hash;
		}
		if (block !== 'undefined' && block) {
			$('html, body').animate(
				{scrollTop:$(block).offset().top},
				1000
			);
			if (currentPage == 'index') {
				return false;
			}
		}
	});

	$(".nav_toggle").on("click", function()
	{
		var menu_cont = $("#menu_cont");
		if (menu_cont.hasClass("menu_cont_right"))
			menu_cont.removeClass("menu_cont_right").addClass("menu_cont_left");
	});

	$(".close_navbar").on("click", function()
	{
		var menu_cont = $("#menu_cont");
		if (menu_cont.hasClass("menu_cont_left"))
			menu_cont.removeClass("menu_cont_left").addClass("menu_cont_right");
	});

	$(document).on('click', function(e) {
		const navigationMenu = $('#menu_cont');
		if (navigationMenu.length) {
			if (!($(e.target).closest('#menu_cont').length
				|| $(e.target).closest('.header-top .header-top-menu .nav_toggle').length
			)) {
				if (navigationMenu.hasClass('menu_cont_left')) {
					navigationMenu.removeClass('menu_cont_left').addClass('menu_cont_right');
				}
			}
		}
	});
});
