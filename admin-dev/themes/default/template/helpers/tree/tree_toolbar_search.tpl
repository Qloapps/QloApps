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

<!-- <label for="node-search">{l s=$label}</label> -->
<div class="pull-right">
	<input type="text"
		{if isset($id)}id="{$id|escape:'html':'UTF-8'}"{/if}
		{if isset($name)}name="{$name|escape:'html':'UTF-8'}"{/if}
		class="search-field{if isset($class)} {$class|escape:'html':'UTF-8'}{/if}"
		placeholder="{l s='search...'}" />
</div>

{if isset($typeahead_source) && isset($id)}

<script type="text/javascript">
	$(function() {

		function startTypeahead() {
			if (typeof $.fn.typeahead === 'undefined') {
				setTimeout(startTypeahead, 100);
				return;
			}
			$("#{$id|escape:'html':'UTF-8'}").typeahead(
			{
				name: "{$name|escape:'html':'UTF-8'}",
				valueKey: 'name',
				local: [{$typeahead_source}]
			});

			$("#{$id|escape:'html':'UTF-8'}").keypress(function (event) {
				if (event.which == 13) {
					event.stopPropagation();
				}
			});
		}

		startTypeahead();
	});
</script>
{/if}