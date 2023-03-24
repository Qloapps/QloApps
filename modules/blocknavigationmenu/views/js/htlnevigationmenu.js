/**
* 2010-2019 Webkul.
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
*  @copyright 2010-2019 Webkul IN
*  @license   https://store.webkul.com/license.html
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
