{*
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
*}
<div class="panel">
	{if isset($header)}{$header}{/if}
	{if isset($nodes)}
	<ul id="{$id|escape:'html':'UTF-8'}" class="cattree tree">
		{$nodes}
	</ul>
	{/if}
</div>
<script type="text/javascript">
	var currentToken="{$token|@addslashes}";
	var treeClickFunc = function() {
		var newURL = window.location.protocol + "//" + window.location.host + window.location.pathname;
		var queryString = window.location.search.replace(/&id_category=[0-9]*/, "") + "&id_category=" + $(this).val();
		location.href = newURL+queryString; // hash part is dropped: window.location.hash
	};
	function addDefaultCategory(elem)
	{
		$('select#id_category_default').append('<option value="' + elem.val()+'">' + (elem.val() !=1 ? elem.parent().find('label').html() : home) + '</option>');
		if ($('select#id_category_default option').length > 0)
		{
			$('select#id_category_default').closest('.form-group').show();
			$('#no_default_category').hide();
		}
	}

	{if isset($use_checkbox) && $use_checkbox == true}
		function checkAllAssociatedHotels($tree)
		{
			$tree.find(':input[type=checkbox]').each(function(){
				$(this).prop('checked', true);
				$(this).parent().addClass('tree-selected');
			});
		}

		function uncheckAllAssociatedHotels($tree)
		{
			$tree.find(':input[type=checkbox]').each(function(){
				$(this).prop('checked', false);
				$(this).parent().removeClass('tree-selected');
			});
		}

		function checkAllRoomTypeOfHotel(node)
		{
			$(node).find(':input[type=checkbox]').each(function(){
				$(this).prop('checked', true);
				$(this).parent().addClass('tree-selected');
			});
		}

		function unCheckAllRoomTypeOfHotel(node)
		{
			$(node).find(':input[type=checkbox]').each(function(){
				$(this).prop('checked', false);
				$(this).parent().removeClass('tree-selected');
			});
		}
	{/if}
	$('#{$id|escape:'html':'UTF-8'}').find('.select_hotel').on('click', function(){
		if ($(this).is(":checked")) {
			checkAllRoomTypeOfHotel($(this).closest('li.tree-folder').find('ul.tree'));
		} else {
			unCheckAllRoomTypeOfHotel($(this).closest('li.tree-folder').find('ul.tree'));
		}
	});

	$('#{$id|escape:'html':'UTF-8'}').find(':input[type=checkbox].select_room_type').on('change', function(){
		var totalNodes = $(this).closest('ul.tree').find('li.tree-item');
		var checkedNodes = $(this).closest('ul.tree').find('li.tree-item :input[type=checkbox].select_room_type:checked');
		if (totalNodes.length == checkedNodes.length) {
			$(this).closest('li.tree-folder').find(':input[type=checkbox].select_hotel').prop('checked', true).parent().addClass('tree-selected');
		} else {
			$(this).closest('li.tree-folder').find(':input[type=checkbox].select_hotel').prop('checked', false).parent().removeClass('tree-selected');
		}
	});
	{if isset($use_search) && $use_search == true}
		$('#{$id|escape:'html':'UTF-8'}-categories-search').bind('typeahead:selected', function(obj, datum){
			var match = $('#{$id|escape:'html':'UTF-8'}').find(':input[value="'+datum.id_category+'"]').first();
			if (match.length)
			{
				match.each(function(){
						$(this).prop("checked", true);
						$(this).parent().addClass("tree-selected");
						$(this).parents('ul.tree').each(function(){
							$(this).show();
							$(this).prev().find('.icon-folder-close').removeClass('icon-folder-close').addClass('icon-folder-open');
						});
						addDefaultCategory($(this));
					}
				);
			}
			else
			{
				var selected = [];
				that = this;
				$('#{$id|escape:'html':'UTF-8'}').find('.tree-selected input').each(
					function()
					{
						selected.push($(this).val());
					}
				);
				{literal}
				$.get(
					'ajax-tab.php',
					{controller:'AdminNormalProducts',token:currentToken,action:'getCategoryTree', fullTree:1, selected:selected},
					function(content) {
				{/literal}
						$('#{$id|escape:'html':'UTF-8'}').html(content);
						$('#{$id|escape:'html':'UTF-8'}').tree('init');
						$('#{$id|escape:'html':'UTF-8'}').find(':input[value="'+datum.id_category+'"]').each(function(){
								$(this).prop("checked", true);
								$(this).parent().addClass("tree-selected");
								$(this).parents('ul.tree').each(function(){
									$(this).show();
									$(this).prev().find('.icon-folder-close').removeClass('icon-folder-close').addClass('icon-folder-open');
								});
								full_loaded = true;
							}
						);
					}
				);
			}
		});
	{/if}
	function startTree() {
		if (typeof $.fn.tree === 'undefined') {
			setTimeout(startTree, 100);
			return;
		}

		$('#{$id|escape:'html':'UTF-8'}').tree('expandAll');
		$('#expand-all-{$id|escape:'html':'UTF-8'}').hide();
		$('#{$id|escape:'html':'UTF-8'}').find(':input[type=radio]').click(treeClickFunc);

		{if isset($selected_hotels)}
			$('#no_default_category').hide();
			{assign var=imploded_selected_hotels value='","'|implode:$selected_hotels}
			var selected_hotels = new Array("{$imploded_selected_hotels}");



			$('#{$id|escape:'html':'UTF-8'}').find(':input.select_hotel').each(function(){
				if ($.inArray($(this).val(), selected_hotels) != -1)
				{
					$(this).prop("checked", true);
					$(this).parent().addClass("tree-selected");
					$(this).parents('ul.tree').each(function(){
						$(this).show();
						$(this).prev().find('.icon-folder-close').removeClass('icon-folder-close').addClass('icon-folder-open');
					});
				}
			});
		{/if}

		{if isset($selected_roomtypes)}
			$('#no_default_category').hide();
			{assign var=imploded_selected_hotels value='","'|implode:$selected_roomtypes}
			var selected_roomtypes = new Array("{$imploded_selected_hotels}");



			$('#{$id|escape:'html':'UTF-8'}').find(':input.select_room_type').each(function(){
				if ($.inArray($(this).val(), selected_roomtypes) != -1)
				{
					$(this).prop("checked", true);
					$(this).parent().addClass("tree-selected");
					$(this).parents('ul.tree').each(function(){
						$(this).show();
						$(this).prev().find('.icon-folder-close').removeClass('icon-folder-close').addClass('icon-folder-open');
					});
				}
			});
		{/if}
	}
	startTree();
</script>
