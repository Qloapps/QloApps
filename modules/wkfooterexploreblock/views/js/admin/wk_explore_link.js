/**
* 2010-2018 Webkul.
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
*  @copyright 2010-2018 Webkul IN
*  @license   https://store.webkul.com/license.html
*/

$(document).ready(function() {
	// Show/Hide Catalog Product Reward Advertise Reward fields
	$("input[name='is_cms_block_link").on('change', function () {
		if (parseInt($(this).val())) {
			$("#cms_block_content").removeClass('hidden');
			$("#non_cms_block_content").addClass('hidden');
		} else {
			$("#cms_block_content").addClass('hidden');
			$("#non_cms_block_content").removeClass('hidden');
		}
	});
});

function showExploreLinkLangField(lang_iso_code, id_lang)
{
	$('#explore_link_lang_btn').html(lang_iso_code + ' <span class="caret"></span>');

	$('.explore_link_name_all').hide();
	$('#explore_link_name_'+id_lang).show();
}