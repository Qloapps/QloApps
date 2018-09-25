{**
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
*}

{if $valid == 1}
	<div class="conf confirmation">
		{l s='Your order on %s is complete with' sprintf=$shop_name mod='wkpaypaladaptive'}
		{if isset($reference)}
			{l s='reference' mod='wkpaypaladaptive'} <b>{$reference|escape:'html':'UTF-8'}</b>
		{else}
			{l s='Order ID' mod='wkpaypaladaptive'} <b>{$id_order|escape:'html':'UTF-8'}</b>
		{/if}.
	</div>
	<p>
		{l s='For any questions or for further information, please contact our' mod='wkpaypaladaptive'} <a href="{$link->getPageLink('contact', true)|escape:'html':'UTF-8'}">{l s='customer service department.' mod='wkpaypaladaptive'}</a>.
	</p>
{else}
	<div class="error">
		{l s='Thanks for your purchase.' mod='wkpaypaladaptive'}<br /><br />
		{l s='Your order is awaiting by paypal. You will get the notification after any order update' mod='wkpaypaladaptive'}<br /><br />
		{l s='For any query feel free to contact' mod='wkpaypaladaptive'}
		<a href="{$link->getPageLink('contact', true)|escape:'html':'UTF-8'}">{l s='customer service department' mod='wkpaypaladaptive'}</a>
		{l s='anytime.' mod='wkpaypaladaptive'}<br /><br />

		{if isset($reference)}
			({l s='Your Order\'s Reference:' mod='wkpaypaladaptive'} <b>{$reference|escape:'html':'UTF-8'}</b>)
		{else}
			({l s='Your Order\'s ID:' mod='wkpaypaladaptive'} <b>{$id_order|escape:'html':'UTF-8'}</b>)
		{/if}
	</div>
{/if}
