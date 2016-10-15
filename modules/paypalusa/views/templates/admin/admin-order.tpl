{*
** @author PrestaShop SA <contact@prestashop.com>
** @copyright  2007-2014 PrestaShop SA
** @version  Release: $Revision: 1.2.0 $
**
** International Registered Trademark & Property of PrestaShop SA
**
** Description: PayPal addon's Back-office template - Order details page
**
** This template is displayed in the Back-office, when you are looking the details of an order placed with PayPal
** It allows you no only to review the transaction details but also to perform a full or partial refund if the order was placed less than 60 days ago
**
*}
<br />
<fieldset>
    <legend><img src="{$module_dir}logo.gif" alt="" /> {l s='PayPal transaction details' mod='paypalusa'}</legend>
	<table cellpadding="0" cellspacing="0" class="table">
		<tr>
			<td>{l s='Method' mod='paypalusa'}</td>
			<td>{$paypal_usa_transaction_details.source|escape:'htmlall':'UTF-8'}</td>
		</tr>
		<tr>
			<td>{l s='Transaction ID' mod='paypalusa'}</td>
			<td>{$paypal_usa_transaction_details.id_transaction|escape:'htmlall':'UTF-8'}</td>
		</tr>
		<tr>
			<td>{l s='Amount charged' mod='paypalusa'}</td>
			<td>{$paypal_usa_transaction_details.amount|escape:'htmlall':'UTF-8'} {$paypal_usa_transaction_details.currency|escape:'htmlall':'UTF-8'}</td>
		</tr>
		<tr>
			<td>{l s='Mode' mod='paypalusa'}</td>
			<td>{if $paypal_usa_transaction_details.mode == 'test'}<span style="color: #CC0000;">{l s='Sandbox (Test)' mod='paypalusa'}</span>{else}{l s='Live' mod='paypalusa'}{/if}</td>
		</tr>
		<tr>
			<td>{l s='Date' mod='paypalusa'}</td>
			<td>{$paypal_usa_transaction_details.date_add|escape:'htmlall':'UTF-8'}</td>
		</tr>
		{if isset($paypal_usa_transaction_details.cc_type) && $paypal_usa_transaction_details.cc_type != ''}
		<tr>
			<td>{l s='Credit card type' mod='paypalusa'}</td>
			<td>{$paypal_usa_transaction_details.cc_type|escape:'htmlall':'UTF-8'}</td>
		</tr>
		{/if}
		{if isset($paypal_usa_transaction_details.cc_exp) && $paypal_usa_transaction_details.cc_exp != ''}
		<tr>
			<td>{l s='Credit expiration date' mod='paypalusa'}</td>
			<td>{$paypal_usa_transaction_details.cc_exp|escape:'htmlall':'UTF-8'}</td>
		</tr>
		{/if}
		{if isset($paypal_usa_transaction_details.cc_last_digits) && $paypal_usa_transaction_details.cc_last_digits != ''}
		<tr>
			<td>{l s='Credit card last 4 digits' mod='paypalusa'}</td>
			<td>{$paypal_usa_transaction_details.cc_last_digits|escape:'htmlall':'UTF-8'}</td>
		</tr>
		{/if}
		{if isset($paypal_usa_transaction_details.cvc_check) && $paypal_usa_transaction_details.cvc_check != ''}
		<tr>
			<td>{l s='CVC Check' mod='paypalusa'}</td>
			<td>{$paypal_usa_transaction_details.cvc_check|escape:'htmlall':'UTF-8'}</td>
		</tr>
		{/if}
		{if isset($paypal_usa_transaction_details.fee) && $paypal_usa_transaction_details.fee != '0.00'}
		<tr>
			<td>{l s='PayPal fees' mod='paypalusa'}</td>
			<td>{$paypal_usa_transaction_details.fee|escape:'htmlall':'UTF-8'} {$paypal_usa_transaction_details.currency|escape:'htmlall':'UTF-8'}</td>
		</tr>
		{/if}
	</table>
</fieldset>

<br />
<fieldset>
	<legend><img src="{$module_dir}logo.gif" alt="" /> {l s='Proceed to a full or partial refund via PayPal' mod='paypalusa'}</legend>
	{if isset($paypal_usa_refund) && $paypal_usa_refund}
		<div class="conf">{l s='Refund successfully performed' mod='paypalusa'}</div><br />
	{else}
		{if isset($paypal_usa_refund) && !$paypal_usa_refund}
		<div class="error">{l s='An error occured during this refund' mod='paypalusa'}{if isset($paypal_usa_refund_error) && $paypal_usa_refund_error} - {$paypal_usa_refund_error|escape:'htmlall':'UTF-8'}{/if}</div><br />
		{/if}
	{/if}
	{if $paypal_usa_more60d}
		<div class="info">{l s='This order has been placed more than 60 days ago or no transaction details are available. Therefore, it cannot be refunded anymore.' mod='paypalusa'}</div>
	{/if}
	<table class="table" cellpadding="0" cellspacing="0">
		<tr>
			<th>{l s='Date' mod='paypalusa'}</th>
			<th>{l s='Amount refunded' mod='paypalusa'}</th>
		</tr>
		{assign var=total_refund value=0}
		{foreach from=$paypal_usa_refund_details item=refund_transaction}
		<tr>
			<td>{$refund_transaction.date_add|escape:'htmlall':'UTF-8'} </td>
			<td>{$refund_transaction.amount|escape:'htmlall':'UTF-8'} {$refund_transaction.currency|escape:'htmlall':'UTF-8'} </td>
		</tr>
		{assign var=total_refund value = $total_refund + $refund_transaction.amount}
		{/foreach}
		<tr>
			<td>{l s='Total refunded:' mod='paypalusa'}</td>
			<td>{$total_refund|escape:'htmlall':'UTF-8'} {$refund_transaction.currency|escape:'htmlall':'UTF-8'} </td>
		</tr>
	</table>
	<br />
	{if $paypal_usa_transaction_details.amount == $total_refund && $total_refund}
		{l s='This order has been fully refunded.' mod='paypalusa'}
	{else}
		<form method="post" action="" name="refund">
			{l s='Refund:' mod='paypalusa'} <input type="text" name="refund_amount" value="{($paypal_usa_transaction_details.amount-$total_refund)|floatval}" />
			<input type="hidden" name="id_transaction" value="{$paypal_usa_transaction_details.id_transaction|escape:'htmlall':'UTF-8'}" />
			<input type="submit" name="process_refund" value ="{l s='Process Refund' mod='paypalusa'}" class="button" />
		</form>
	{/if}
</fieldset>