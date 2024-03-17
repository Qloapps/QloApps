/*
* 2007-2017 PrestaShop
*
* NOTICE OF LICENSE
*
* This source file is subject to the Open Software License (OSL 3.0)
* that is bundled with this package in the file LICENSE.txt.
* It is also available through the world-wide-web at this URL:
* http://opensource.org/licenses/osl-3.0.php
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
*  @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*/

$(document).ready(function() {
	initTableDnD();
});

function objToString(obj) {
    var str = '';
    for (var p in obj) {
        if (obj.hasOwnProperty(p)) {
            str += p + '=' + obj[p] + '&';
        }
    }
    return str;
}

function initTableDnD(table)
{
	if (typeof(table) == 'undefined')
		table = 'table.tableDnD';

	$(table).tableDnD({
		onDragStart: function(table, row) {
			originalOrder = $.tableDnD.serialize();
			reOrder = ':even';
			if (table.tBodies[0].rows[1] && $('#' + table.tBodies[0].rows[1].id).hasClass('alt_row'))
				reOrder = ':odd';
			$(table).find('#' + row.id).parent('tr').addClass('myDragClass');
		},
		dragHandle: 'dragHandle',
		onDragClass: 'myDragClass',
		onDrop: function(table, row) {
			if (originalOrder != $.tableDnD.serialize()) {
				var way = (originalOrder.indexOf(row.id) < $.tableDnD.serialize().indexOf(row.id))? 1 : 0;
				var ids = row.id.split('_');
				var tableDrag = table;
				var params = '';
				var tableId = table.id.replace('table-', '');
				if (tableId == 'cms_block_0' || tableId == 'cms_block_1')
					params = {
						updatePositions: true,
						configure: 'blockcms'
					};
				else if (tableId == 'category')
				{
					params = {
						action: 'updatePositions',
						id_category_parent: ids[1],
						id_category_to_move: ids[2],
						way: way
					};
				}
				else if (tableId == 'cms_category')
					params = {
						action: 'updateCmsCategoriesPositions',
						id_cms_category_parent: ids[1],
						id_cms_category_to_move: ids[2],
						way: way
					};
				else if (tableId == 'cms')
					params = {
						action: 'updateCmsPositions',
						id_cms_category: ids[1],
						id_cms: ids[2],
						way: way
					};
				else if (come_from == 'AdminModulesPositions')
					params = {
						action: 'updatePositions',
						id_hook: ids[0],
						id_module: ids[1],
						way: way
					};
				else if (tableId.indexOf('attribute') != -1 && tableId!= 'attribute_group') {
					params = {
						action: 'updateAttributesPositions',
						id_attribute_group: ids[1],
						id_attribute: ids[2],
						way: way
					};
				}
				else if (tableId == 'attribute_group') {
					params = {
						action: 'updateGroupsPositions',
						id_attribute_group: ids[2],
						way: way
					}
				}
				else if (tableId == 'product') {
					params = {
						action: 'updatePositions',
						id_category_hotel: ids[1],
						id_product: ids[2],
						way: way
					};
				}
				else if (tableId.indexOf('module-') != -1) {
					module = tableId.replace('module-', '');

					params = {
						updatePositions: true,
						configure: module
					};
				}
				// default
				else
				{
					params = {
						action : 'updatePositions',
						id : ids[2],
						way: way
					}
				}

				params['ajax'] = 1;
				params['page'] = parseInt($('input[name=page]').val());
				params['selected_pagination'] = parseInt($('input[name=selected_pagination]').val());

				if (tableId == 'tab') {
					var data = $.tableDnD.serialize().replace(/table-tab/g, 'menu');
				} else {
					var data = $.tableDnD.serialize().replace(/table-/g, '');
				}
				if ((tableId == 'category') && (data.indexOf('_0&') != -1))
					data += '&found_first=1';
				$.ajax({
					type: 'POST',
					headers: { "cache-control": "no-cache" },
					async: false,
					url: currentIndex + '&token=' + token + '&' + 'rand=' + new Date().getTime(),
					data:  data + '&' + objToString(params) ,
					success: function(data) {
						var nodrag_lines = $(tableDrag).find('tr:not(".nodrag")');
						var new_pos;
						if (come_from == 'AdminModulesPositions')
						{
							nodrag_lines.each(function(i) {
								$(this).find('.positions').html(i+1);
							});
						}
						else
						{
							if (tableId == 'product' || tableId.indexOf('attribute') != -1 || tableId == 'attribute_group' || tableId == 'feature')
								var reg = /_[0-9][0-9]*$/g;
							else
								var reg = /_[0-9]$/g;

							var up_reg  = new RegExp('position=[-]?[0-9]+&');
							nodrag_lines.each(function(i) {

								if (params['page'] > 1)
									new_pos = i + ((params['page'] - 1) * params['selected_pagination']);
								else
									new_pos = i;

								$(this).attr('id', $(this).attr('id').replace(reg, '_' + new_pos));
								$(this).find('.positions').text(new_pos + 1);
							});
						}

						nodrag_lines.removeClass('odd');
						nodrag_lines.filter(':odd').addClass('odd');
						nodrag_lines.children('td.dragHandle').find('a').attr('disabled',false);

						if (typeof alternate !== 'undefined' && alternate) {
							nodrag_lines.children('td.dragHandle:first').find('a:odd').attr('disabled',true);
							nodrag_lines.children('td.dragHandle:last').find('a:even').attr('disabled',true);
						}
						else {
							nodrag_lines.children('td.dragHandle:first').find('a:even').attr('disabled',true);
							nodrag_lines.children('td.dragHandle:last').find('a:odd').attr('disabled',true);
						}
						showSuccessMessage(update_success_msg);
					}
				});
			}
		}
	});
}

