{*
* 2007-2015 PrestaShop
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
*  @copyright  2007-2015 PrestaShop SA
*  @license    http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*}

{if $status == 'ok'}
<p class="alert alert-success">{l s='Your order on %s is complete.' sprintf=$shop_name mod='cheque'}</p><br /><br />
		{l s='Please send us a bank wire with' mod='bankwire'}
		<br />- {l s='Amount' mod='bankwire'} <span class="price"><strong>{$total_to_pay}</strong></span>
		<br /><br />- {l s='Name of account owner' mod='bankwire'}  <strong>{if $bankwireOwner}{$bankwireOwner}{else}___________{/if}</strong>
		<br /><br />- {l s='Include these details' mod='bankwire'}  <strong>{if $bankwireDetails}{$bankwireDetails}{else}___________{/if}</strong>
		<br /><br />- {l s='Bank name' mod='bankwire'}  <strong>{if $bankwireAddress}{$bankwireAddress}{else}___________{/if}</strong>
		{if !isset($reference)}
			<br />- {l s='Do not forget to insert your order number #%d in the subject of your bank wire.' sprintf=$id_order mod='bankwire'}
		{else}
			<br />- {l s='Do not forget to insert your order reference %s in the subject of your bank wire.' sprintf=$reference mod='bankwire'}
		{/if}
	</p>
{else}
	<p class="warning">
		{l s='We noticed a problem with your order. If you think this is an error, feel free to contact our' mod='bankwire'} 
		<a href="{$link->getPageLink('contact', true)|escape:'html'}">{l s='expert customer support team' mod='bankwire'}</a>.
	</p>
{/if}
