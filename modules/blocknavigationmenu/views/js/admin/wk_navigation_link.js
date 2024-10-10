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
	$("input[name='is_cms_block_link").on('change', function () {
		if (parseInt($(this).val())) {
			$("#cms_block_content").removeClass('hidden');
			$("#non_cms_block_content").addClass('hidden');
		} else {
			$("#cms_block_content").addClass('hidden');
			$("#non_cms_block_content").removeClass('hidden');
		}
	});

	$("input[name='is_custom_redirect_link").on('change', function () {
		if (parseInt($(this).val())) {
			$(".custom_redirect_link_div").removeClass('hidden');
			$(".custom_redirect_page_div").addClass('hidden');
		} else {
			$(".custom_redirect_link_div").addClass('hidden');
			$(".custom_redirect_page_div").removeClass('hidden');
		}
	});

	$(document).on('change', '#id_cms_category', function(){
		var id_cat = parseInt($('#id_cms_category').val());
		$('.cms_pages_table').hide();
		$('#cms_pages_table_'+id_cat).show();
	});
});

function showNavigationLinkLangField(lang_iso_code, id_lang)
{
	$('#navigation_link_lang_btn').html(lang_iso_code + ' <span class="caret"></span>');

	$('.navigation_link_name_all').hide();
	$('#navigation_link_name_'+id_lang).show();
}
