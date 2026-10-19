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
	<ul id="{$id|escape:'html':'UTF-8'}" class="tree">
		{$nodes}
	</ul>
	{/if}
</div>
<script type="text/javascript">
	function checkAllAssociatedShops($tree)
	{
		$tree.find(":input[type=checkbox]").each(
			function()
			{
				$(this).prop("checked", true);
				$(this).parent().addClass("tree-selected");
			}
		);
	}

	function uncheckAllAssociatedShops($tree)
	{
		$tree.find(":input[type=checkbox]").each(
			function()
			{
				$(this).prop("checked", false);
				$(this).parent().removeClass("tree-selected");
			}
		);
	}

	$(document).ready(function () {
		$("#{$id|escape:'html':'UTF-8'}").tree("expandAll");
		$("#{$id|escape:'html':'UTF-8'}").find(":input[type=checkbox]").click(
			function()
			{
				if($(this).is(':checked'))
				{
					$(this).parent().addClass("tree-selected");
					$(this).parent().parent().find("ul").find(":input[type=checkbox]").each(
						function()
						{
							$(this).prop("checked", true);
							$(this).parent().addClass("tree-selected");
						}
					);
				}
				else
				{
					$(this).parent().removeClass("tree-selected");
					$(this).parent().parent().find("ul").find(":input[type=checkbox]").each(
						function()
						{
							$(this).prop("checked", false);
							$(this).parent().removeClass("tree-selected");
						}
					);
				}
			}
		);

		{if isset($selected_shops)}
			{assign var=imploded_selected_shops value='","'|implode:$selected_shops}
			var selected_shops = new Array("{$imploded_selected_shops}");

			$("#{$id|escape:'html':'UTF-8'}").find(".tree-item :input").each(
				function()
				{
					if ($.inArray($(this).val(), selected_shops) != -1)
					{
						$(this).prop("checked", true);
						$(this).parent().addClass("tree-selected");
						$(this).parents("ul.tree").each(
							function()
							{
								$(this).children().children().children(".icon-folder-close")
									.removeClass("icon-folder-close")
									.addClass("icon-folder-open");
								$(this).show();
							}
						);
					}
				}
			);
		{/if}
	});
</script>