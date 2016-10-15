{**
* 2010-2016 Webkul.
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
*  @copyright 2010-2016 Webkul IN
*  @license   https://store.webkul.com/license.html
*}

{if $valid == 1}
	<div class="conf confirmation">
		{l s='Your order on %s is complete with' sprintf=$shop_name mod='mppaypaladaptive'}
		{if isset($reference)}
			{l s='reference' mod='mppaypaladaptive'} <b>{$reference|escape:'html':'UTF-8'}</b>
		{else}
			{l s='Order ID' mod='mppaypaladaptive'} <b>{$id_order|escape:'html':'UTF-8'}</b>
		{/if}.
	</div>
	<p>
		{l s='For any questions or for further information, please contact our' mod='mppaypaladaptive'} <a href="{$link->getPageLink('contact', true)|escape:'html':'UTF-8'}">{l s='customer service department.' mod='mppaypaladaptive'}</a>.
	</p>
{else}
	<div class="error">
		{l s='Thanks for your purchase.' mod='mppaypaladaptive'}<br /><br />
		{l s='Your order is awaiting by paypal. You will get the notification after any order update' mod='mppaypaladaptive'}<br /><br />
		{l s='For any query feel free to contact' mod='mppaypaladaptive'}
		<a href="{$link->getPageLink('contact', true)|escape:'html':'UTF-8'}">{l s='customer service department' mod='mppaypaladaptive'}</a>
		{l s='anytime.' mod='mppaypaladaptive'}<br /><br />

		{if isset($reference)}
			({l s='Your Order\'s Reference:' mod='mppaypaladaptive'} <b>{$reference|escape:'html':'UTF-8'}</b>)
		{else}
			({l s='Your Order\'s ID:' mod='mppaypaladaptive'} <b>{$id_order|escape:'html':'UTF-8'}</b>)
		{/if}
	</div>
{/if}
