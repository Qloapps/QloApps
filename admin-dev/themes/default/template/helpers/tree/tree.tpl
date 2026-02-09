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
{block name="tree_panel"}

	<div class="panel">
		{block name="tree_header"}
			{if isset($header)}{$header}{/if}
		{/block}
		{block name="tree"}
			{if isset($nodes)}
				<ul id="{$id|escape:'html':'UTF-8'}" class="tree top cattree" style="max-height: {$max_height}px;">
				{$nodes}
				</ul>
			{/if}
		{/block}
	</div>
{/block}
<script type="text/javascript">
	{block name="script"}
		{if isset($use_checkbox) && $use_checkbox == true}
			function checkAll($tree)
			{
				$tree.find(":input[type=checkbox]:not([hidden])").each(
					function()
					{
						$(this).prop("checked", true);
						$(this).parent().addClass("tree-selected");
					}
				);
			}

			function uncheckAll($tree)
			{
				$tree.find(":input[type=checkbox]:not([hidden])").each(
					function()
					{
						$(this).prop("checked", false);
						$(this).parent().removeClass("tree-selected");
					}
				);
			}
		{/if}
		{if isset($use_search) && $use_search == true}
			$("#{$id|escape:'html':'UTF-8'}-search").bind("typeahead:selected", function(obj, datum) {
				var suffix = '{if isset($use_checkbox) && $use_checkbox == true}[]{/if}';
				$("#{$id|escape:'html':'UTF-8'}").find('[name="'+datum.input_name + suffix + '"]:input').each(
					function()
					{
						if ($(this).val() == datum.value)
						{
							{if (!(isset($use_checkbox) && $use_checkbox == true))}
								$("#{$id|escape:'html':'UTF-8'} label").removeClass("tree-selected");
							{/if}
							$(this).prop("checked", true);
							$(this).parent().addClass("tree-selected");
							$(this).parents('ul.tree').each(function(){
								$(this).show();
								$(this).prev().find('.icon-folder-close').removeClass('icon-folder-close').addClass('icon-folder-open');
							});

							{if isset($auto_select_children) && $auto_select_children == true}
								if ($(this).closest('.tree-item').length == 0) {
									$(this).closest('.tree-folder').find(':input[type=checkbox]').each(function(){
										$(this).prop('checked', true);
										$(this).parent().addClass('tree-selected');
									});
								}
							{/if}
						}
					}
				);
			});
		{/if}

		{if isset($auto_select_children) && $auto_select_children == true}
			$('#{$id|escape:'html':'UTF-8'}').find(':input[type=checkbox]').on('click', function(){
				if ($(this).closest('.tree-item').length == 0) {
					if ($(this).is(":checked")) {
						$(this).closest('.tree-folder').find(':input[type=checkbox]').each(function(){
							$(this).prop('checked', true);
							$(this).parent().addClass('tree-selected');
						});
					} else {
						$(this).closest('.tree-folder').find(':input[type=checkbox]').each(function(){
							$(this).prop('checked', false);
							$(this).parent().removeClass('tree-selected');
						});
					}
				}

				if ($(this).closest('.tree-item').length) {
					if (!$(this).is(":checked")) {
						$(this).parents('.tree-folder').find(':input[type=checkbox]:first').each(function(){
							$(this).prop('checked', false);
							$(this).parent().removeClass('tree-selected');
						});
					}
				}
			});
		{/if}

		function startTree(idElem) {
			if (typeof $.fn.tree === 'undefined') {
				setTimeout(startTree, 100);
				return;
			}

			let tree = $("#"+idElem).tree('collapseAll');
			if ($("#"+idElem).find(":input:checked").length > 1)
					$('#expand-all-'+idElem).hide();
				else
					$('#collapse-all-'+idElem).hide();

			$("#"+idElem).find(":input:checked").each(function(){
				$(this).parent().addClass("tree-selected");
				$(this).parents('ul.tree').each(function(){
					$(this).show();
					$(this).prev().find('.icon-folder-close').removeClass('icon-folder-close').addClass('icon-folder-open');
				});
			});
		}

		$(document).ready(function () {
			startTree("{$id|escape:'html':'UTF-8'}");
		});
	{/block}
</script>