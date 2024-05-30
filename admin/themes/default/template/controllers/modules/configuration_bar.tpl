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

{$module_name = $module_name|escape:'html':'UTF-8'}
{capture}{'/&module_name='|cat:$module_name|cat:'/'}{/capture}
{if isset($display_multishop_checkbox) && $display_multishop_checkbox}
<div class="bootstrap panel">
	<h3><i class="icon-cogs"></i> {l s='Configuration'}</h3>
	<input type="checkbox" name="activateModule" value="1"{if $module->isEnabledForShopContext()} checked="checked"{/if} 
		onclick="location.href = '{$current_url|regex_replace:$smarty.capture.default:''}&amp;module_name={$module_name}&amp;enable=' + (($(this).attr('checked')) ? 1 : 0);" />
	{l s='Activate module for this shop context: %s.' sprintf=$shop_context}
</div>
{/if}
