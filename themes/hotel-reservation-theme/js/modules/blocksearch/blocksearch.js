/*
* 2007-2017 PrestaShop
*
* NOTICE OF LICENSE
*
* This source file is subject to the Academic Free License (AFL 3.0)
* that is bundled with this package in the file LICENSE.txt.
* It is also available through the world-wide-web at this URL:
* http://opensource.org/licenses/afl-3.0.php
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
*  @license    http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*/

var instantSearchQueries = [];
$(document).ready(function()
{
	if (typeof blocksearch_type == 'undefined')
		return;

	var $input = $("#search_query_" + blocksearch_type);

	var width_ac_results = 	$input.parent('form').outerWidth();
	if (typeof ajaxsearch != 'undefined' && ajaxsearch) {
		$input.autocomplete(
			search_url,
			{
				minChars: 3,
				max: 10,
				width: (width_ac_results > 0 ? width_ac_results : 500),
				selectFirst: false,
				scroll: false,
				dataType: "json",
				formatItem: function(data, i, max, value, term) {
					return value;
				},
				parse: function(data) {
					var mytab = [];
					for (var i = 0; i < data.length; i++)
						mytab[mytab.length] = { data: data[i], value: data[i].cname + ' > ' + data[i].pname };
					return mytab;
				},
				extraParams: {
					ajaxSearch: 1,
					id_lang: id_lang
				}
			}
		)
		.result(function(event, data, formatted) {
			$input.val(data.pname);
			document.location.href = data.product_link;
		});
	}

	if (typeof instantsearch != 'undefined' && instantsearch) {
		$input.on('keyup', function(){
			if($(this).val().length > 4)
			{
				stopInstantSearchQueries();
				instantSearchQuery = $.ajax({
					url: search_url + '?rand=' + new Date().getTime(),
					data: {
						instantSearch: 1,
						id_lang: id_lang,
						q: $(this).val()
					},
					dataType: 'html',
					type: 'POST',
					headers: { "cache-control": "no-cache" },
					async: true,
					cache: false,
					success: function(data){
						if ($input.val().length > 0) {
							tryToCloseInstantSearch();
							$('#center_column').attr('id', 'old_center_column');
							$('#old_center_column').after('<div id="center_column" class="' + $('#old_center_column').attr('class') + '">' + data + '</div>').hide();
							// Button override
							ajaxCart.overrideButtonsInThePage();
							$("#instant_search_results a.close").on('click', function() {
								$input.val('');
								return tryToCloseInstantSearch();
							});
							return false;
						}
						else
							tryToCloseInstantSearch();
					}
				});
				instantSearchQueries.push(instantSearchQuery);
			}
			else
				tryToCloseInstantSearch();
		});
	}
});

function tryToCloseInstantSearch()
{
	var $oldCenterColumn = $('#old_center_column');
	if ($oldCenterColumn.length > 0)
	{
		$('#center_column').remove();
		$oldCenterColumn.attr('id', 'center_column').show();
		return false;
	}
}

function stopInstantSearchQueries()
{
	for(var i=0; i<instantSearchQueries.length; i++)
		instantSearchQueries[i].abort();
	instantSearchQueries = [];
}
