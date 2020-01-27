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
});

function showNavigationLinkLangField(lang_iso_code, id_lang)
{
	$('#navigation_link_lang_btn').html(lang_iso_code + ' <span class="caret"></span>');

	$('.navigation_link_name_all').hide();
	$('#navigation_link_name_'+id_lang).show();
}