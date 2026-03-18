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
	function updateNavOverflow() {
		var menuCont = $('#menu_cont');
		var nav = menuCont.find('.main-nav');
		var moreItem = nav.find('.nav-more');
		if (!nav.length || !moreItem.length) {
			return;
		}

		var maxItems = topNavCount;
		var items = nav.children('li').not('.nav-more');
		items.removeClass('d-none nav-hidden');
		moreItem.addClass('d-none');
		moreItem.find('.nav-more-menu').empty();

		if (!menuCont.hasClass('nav_cont_right')) {
			return;
		}

		if (items.length > maxItems) {
			items.slice(maxItems).each(function() {
				var link = $(this).find('a').first();
				if (!link.length) {
					return;
				}
				var dropdownItem = $('<a class="dropdown-item navigation-link"></a>');
				dropdownItem.attr('href', link.attr('href'));
				dropdownItem.text($.trim(link.text()));
				moreItem.find('.nav-more-menu').append(dropdownItem);
			});
			items.slice(maxItems).addClass('d-none nav-hidden');
			moreItem.removeClass('d-none');
		}
	}

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

	$(document).on("click", '.nav_toggle', function(e) {
		var menu_cont = $("#menu_cont");
		if (menu_cont.hasClass("nav_cont_right")) {
			menu_cont.removeClass("nav_cont_right").addClass("nav_cont_left");
			$('#nav_overlay').addClass('is-active');
		}
		updateNavOverflow();
		e.stopPropagation();
	});

	$(".close_navbar").on("click", function() {
		var menu_cont = $("#menu_cont");
		if (menu_cont.hasClass("nav_cont_left")) {
			menu_cont.removeClass("nav_cont_left").addClass("nav_cont_right");
			$('#nav_overlay').removeClass('is-active');
		}
		updateNavOverflow();
	});

	$(document).on('click', function(e) {
		const navigationMenu = $('#menu_cont');
		if (navigationMenu.length) {
			if (!($(e.target).closest('#menu_cont').length
				|| $(e.target).closest('.header-top .header-top-menu .nav_toggle').length
			)) {
				if (navigationMenu.hasClass('nav_cont_left')) {
					navigationMenu.removeClass('nav_cont_left').addClass('nav_cont_right');
					$('#nav_overlay').removeClass('is-active');
					updateNavOverflow();
				}
			}
		}
	});

	$(window).on('resize', function() {
		updateNavOverflow();
	});

	updateNavOverflow();
});
