{if $valid == 1}
	<div class="alert alert-success">
		{l s='Your order on %s is complete with' sprintf=$shop_name mod='wkpaypaladaptive'}
		{if isset($reference)}
			{l s='reference' mod='wkpaypaladaptive'} <b>{$reference|escape:'html':'UTF-8'}</b>
		{else}
			{l s='Order ID' mod='wkpaypaladaptive'} <b>{$id_order|escape:'html':'UTF-8'}</b>
		{/if}.
	</div>
{else}
	<div class="error">
		{l s='Unfortunately, an error occurred during the transaction.' mod='wkpaypaladaptive'}<br /><br />
		{l s='Please double-check your credit card details and try again. If you need further assistance, feel free to contact' mod='wkpaypaladaptive'}
		<a href="{$link->getPageLink('contact', true)|escape:'html':'UTF-8'}">{l s='customer service department.' mod='wkpaypaladaptive'}</a>
		{l s='anytime.' mod='wkpaypaladaptive'}<br /><br />
		<a href="{$link->getPageLink('contact', true)|escape:'html':'UTF-8'}">{l s='customer service department.' mod='wkpaypaladaptive'}</a>.
		{if isset($reference)}
			({l s='Your Order\'s Reference:' mod='wkpaypaladaptive'} <b>{$reference|escape:'html':'UTF-8'}</b>)
		{else}
			({l s='Your Order\'s ID:' mod='wkpaypaladaptive'} <b>{$id_order|escape:'html':'UTF-8'}</b>)
		{/if}
	</div>
{/if}
