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
	setCategoryWiseCmsPages();
	$("input[name='is_cms_block_link").on('change', function () {
		toggleElements(this, "#cms_block_content", "#non_cms_block_content");
	});

	$("input[name='is_custom_redirect_link").on('change', function () {
		toggleElements(this, ".custom_redirect_link_div", ".custom_redirect_page_div");
	});

	$(document).on('change', '#id_cms_category', function(){
		setCategoryWiseCmsPages();
	});
});

function showNavigationLinkLangField(lang_iso_code, id_lang)
{
	$('#navigation_link_lang_btn').html(lang_iso_code + ' <span class="caret"></span>');

	$('.navigation_link_name_all').hide();
	$('#navigation_link_name_'+id_lang).show();
}

function toggleElements(element, selectorFirst, selectorSecond)
{
	if (parseInt($(element).val())) {
		$(selectorFirst).removeClass('hidden');
		$(selectorSecond).addClass('hidden');
	} else {
		$(selectorFirst).addClass('hidden');
		$(selectorSecond).removeClass('hidden');
	}
}

function setCategoryWiseCmsPages()
{
	var selectedPage = parseInt($('input[name="id_cms"]:checked').val());
	var selectedCategory = parseInt($('#id_cms_category').val());
	var formattedData = JSON.parse(catFormatCmsPages);
	if (!isNaN(selectedCategory)) {
		formattedData = formattedData[selectedCategory];
		var html = '';
		$.each(formattedData, function(i, v) {
			html += '<tr>';
			html += '<td><input type="radio" value="'+v['id_cms']+'" name="id_cms"></td>';
			html += '<td>'+v['id_cms']+'</td>';
			html += '<td><label for="groupBox_'+v['id_cms']+'">'+v['meta_title']+'</label></td>'
			html += '</tr>';
		});
		$('table.cms_pages tbody').html(html);
	}

	if (!isNaN(selectedPage)) {
		$('input[name="id_cms"][value="'+selectedPage+'"]').attr('checked', 'checked');
	}

}