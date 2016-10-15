{*
** @author PrestaShop SA <contact@prestashop.com>
** @copyright  2007-2014 PrestaShop SA
**
** International Registered Trademark & Property of PrestaShop SA
**
** Description: "PayPal Standard" payment form template
**
** This template is displayed on the payment page and called by the Payment hook
**
** Step 1: The customer is validating this form by clicking on the PayPal payment button
** Step 2: All parameters are sent to PayPal including the billing address to pre-fill a maximum of values/fields for the customer
** Step 3: The transaction success or failure is sent to you by PayPal at the following URL: http://www.mystore.com/modules/paypalusa/controllers/front/validation.php?pps=1
** This step is also called IPN ("Instant Payment Notification")
** Step 4: The customer is redirected to his/her "Order history" page ("My account" section)
*}
<form action="{$paypal_usa_action|escape:'htmlall':'UTF-8'}" method="post">
	<p class="payment_module">
		<input type="hidden" name="cmd" value="_cart" />
		<input type="hidden" name="upload" value="1" />
		<input type="hidden" name="charset" value="utf8" />
		<input type="hidden" name="business" value="{$paypal_usa_business_account|escape:'htmlall':'UTF-8'}" />
		<input type="hidden" name="currency_code" value="{$currency->iso_code|escape:'htmlall':'UTF-8'}" />
		<input type="hidden" name="custom" value="{$cart->id|intval};{if isset($cart->id_shop)}{$cart->id_shop|intval}{else}0{/if}" />
		<input type="hidden" name="amount" value="{$cart->getOrderTotal(true)|floatval}" />
		<input type="hidden" name="first_name" value="{$paypal_usa_billing_address->firstname|escape:'htmlall':'UTF-8'}" />
		<input type="hidden" name="last_name" value="{$paypal_usa_billing_address->lastname|escape:'htmlall':'UTF-8'}" />
		<input type="hidden" name="address1" value="{$paypal_usa_billing_address->address1|escape:'htmlall':'UTF-8'}" />
		{if $paypal_usa_billing_address->address2}<input type="hidden" name="address2" value="{$paypal_usa_billing_address->address2|escape:'htmlall':'UTF-8'}" />{/if}
		<input type="hidden" name="city" value="{$paypal_usa_billing_address->city|escape:'htmlall':'UTF-8'}" />
		{if ($paypal_usa_billing_address->id_state != 0)}
			<input type="hidden" name="state" value="{$paypal_usa_billing_address->state->iso_code|escape:'htmlall':'UTF-8'}" />
		{/if}
		<input type="hidden" name="zip" value="{$paypal_usa_billing_address->postcode|escape:'htmlall':'UTF-8'}" />
		<input type="hidden" name="email" value="{$paypal_usa_customer->email|escape:'htmlall':'UTF-8'}" />
		{if (isset($paypal_usa_billing_address->phone_mobile) && !empty($paypal_usa_billing_address->phone_mobile)) || (isset($paypal_usa_billing_address->phone) && !empty($paypal_usa_billing_address->phone))}
		<input type="hidden" name="night_phone_b" value="{if isset($paypal_usa_billing_address->phone_mobile) && !empty($paypal_usa_billing_address->phone_mobile)}{$paypal_usa_billing_address->phone_mobile|escape:'htmlall':'UTF-8'}{else}{if isset($paypal_usa_billing_address->phone) && !empty($paypal_usa_billing_address->phone)}{$paypal_usa_billing_address->phone|escape:'htmlall':'UTF-8'}{/if}{/if}" />
		{/if}
		<input type="hidden" name="address_override" value="1" />
		
		{assign var="paypal_usa_total_discounts" value=$cart->getOrderTotal(true, Cart::ONLY_DISCOUNTS)}
		{if $paypal_usa_total_discounts == 0}
			{foreach from=$cart->getProducts() item=paypal_usa_product name="paypal_usa_products"}
				<input type="hidden" name="item_name_{$smarty.foreach.paypal_usa_products.index+1|escape:'htmlall':'UTF-8'}" value="{$paypal_usa_product.name|escape:'htmlall':'UTF-8'}" />
				<input type="hidden" name="amount_{$smarty.foreach.paypal_usa_products.index+1|escape:'htmlall':'UTF-8'}" value="{$paypal_usa_product.price|string_format:"%.2f"}" />
				<input type="hidden" name="quantity_{$smarty.foreach.paypal_usa_products.index+1|escape:'htmlall':'UTF-8'}" value="{$paypal_usa_product.quantity|intval}" />
			{/foreach}
			{assign var="paypal_usa_total_shipping" value=$cart->getOrderTotal(true, Cart::ONLY_SHIPPING)}
			{if $paypal_usa_total_shipping}
				<input type="hidden" name="item_name_{$smarty.foreach.paypal_usa_products.index+2|escape:'htmlall':'UTF-8'}" value="{l s='Shipping' mod='paypalusa'}" />
				<input type="hidden" name="amount_{$smarty.foreach.paypal_usa_products.index+2|escape:'htmlall':'UTF-8'}" value="{$paypal_usa_total_shipping|floatval}" />
				<input type="hidden" name="quantity_{$smarty.foreach.paypal_usa_products.index+2|escape:'htmlall':'UTF-8'}" value="1">
			{/if}
		{else}	
			<input type="hidden" name="item_name_1" value="{l s="Your order" mod="paypalusa"}" />
			<input type="hidden" name="amount_1" value="{$cart->getOrderTotal(!$show_taxes)|floatval}" />
		{/if}
		
		<input type="hidden" name="tax_cart" value="{$paypal_usa_total_tax|floatval}" />
		
		<input type="hidden" name="notify_url" value="{$paypal_usa_notify_url|escape:'htmlall':'UTF-8'}" />
		<input type="hidden" name="return" value="{$paypal_usa_return_url|escape:'htmlall':'UTF-8'}" />
		<input type="hidden" name="cancel_return" value="{$paypal_usa_cancel_url|escape:'htmlall':'UTF-8'}" />
		<input type="hidden" name="no_shipping" value="1" />
		<input type="hidden" name="bn" value="PrestashopUS_Cart" />
		<input id="paypal-standard-btn" type="image" name="submit" src="https://www.paypalobjects.com/{if $lang_iso == 'en'}en_US{else}{if $lang_iso == 'fr'}fr_CA{else}{if $lang_iso == 'es'}es_ES{else}en_US{/if}{/if}{/if}/i/bnr/horizontal_solution_PPeCheck.gif" alt="" style="vertical-align: middle; margin-right: 10px;" /> {l s='Pay with PayPal' mod='paypalusa'}
	</p>
</form>
