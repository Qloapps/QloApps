{*
* 2007-2018 PrestaShop
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
*  @author    PrestaShop SA <contact@prestashop.com>
*  @copyright 2007-2018 PrestaShop SA
*  @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*}

<form style="display: none" id="paypal_form" name="paypal_form" method="post" action="{$action_url|escape:'htmlall':'UTF-8'}">
	@hiddenSubmit
	<input type="hidden" name="cmd" value="_hosted-payment" />

	<input type="hidden" name="billing_first_name" value="{$billing_address->firstname|escape:'htmlall':'UTF-8'}" />
	<input type="hidden" name="billing_last_name" value="{$billing_address->lastname|escape:'htmlall':'UTF-8'}" />
	<input type="hidden" name="billing_address1" value="{$billing_address->address1|escape:'htmlall':'UTF-8'}" />
	<input type="hidden" name="billing_address2" value="{$billing_address->address2|escape:'htmlall':'UTF-8'}" />
	<input type="hidden" name="billing_city" value="{$billing_address->city|escape:'htmlall':'UTF-8'}" />
	<input type="hidden" name="billing_zip" value="{$billing_address->postcode|escape:'htmlall':'UTF-8'}" />
	<input type="hidden" name="billing_country" value="{$billing_address->country->iso_code|escape:'htmlall':'UTF-8'}" />
	{if ($billing_address->id_state != 0)}
		<input type="hidden" name="billing_state" value="{$billing_address->state->name|escape:'htmlall':'UTF-8'}" />
	{/if}
	<input type="hidden" name="first_name" value="{$delivery_address->firstname|escape:'htmlall':'UTF-8'}" />
	<input type="hidden" name="last_name" value="{$delivery_address->lastname|escape:'htmlall':'UTF-8'}" />
	<input type="hidden" name="buyer_email" value="{$customer->email|escape:'htmlall':'UTF-8'}" />
	<input type="hidden" name="address1" value="{$delivery_address->address1|escape:'htmlall':'UTF-8'}" />
	<input type="hidden" name="address2" value="{$delivery_address->address2|escape:'htmlall':'UTF-8'}" />
	<input type="hidden" name="city" value="{$delivery_address->city|escape:'htmlall':'UTF-8'}" />
	<input type="hidden" name="zip" value="{$delivery_address->postcode|escape:'htmlall':'UTF-8'}" />
	<input type="hidden" name="country" value="{$delivery_address->country->iso_code|escape:'htmlall':'UTF-8'}" />
	{if ($delivery_address->id_state != 0)}
		<input type="hidden" name="billing_state" value="{$delivery_address->state->name|escape:'htmlall':'UTF-8'}" />
	{/if}

	<input type="hidden" name="address_override" value="true" />
	<input type="hidden" name="showShippingAddress" value="true" />

	<input type="hidden" name="currency_code" value="{$currency->iso_code|escape:'htmlall':'UTF-8'}" />
	<input type="hidden" name="invoice" value="{$customer->id|intval}_{$time|escape:'htmlall':'UTF-8'}" />
	<input type="hidden" name="shipping" value="{$shipping|escape:'htmlall':'UTF-8'}" />
	<input type="hidden" name="tax" value="{$cart_details.total_tax|escape:'htmlall':'UTF-8'}" />
	<input type="hidden" name="subtotal" value="{$subtotal|escape:'htmlall':'UTF-8'}" />

	<input type="hidden" name="custom" value="{$custom|escape:'htmlall':'UTF-8'}" />
	<input type="hidden" name="notify_url" value="{$notify_url|escape:'htmlall':'UTF-8'}" />
	<input type="hidden" name="paymentaction" value="sale" />
	<input type="hidden" name="business" value="{$business_account|escape:'htmlall':'UTF-8'}" />
	<input type="hidden" name="template" value="template{$payment_hss_template|escape:'htmlall':'UTF-8'}" />
	<input type="hidden" name="cbt" value="{l s='Return back to the merchant\'s website' mod='paypal'}" />
	<input type="hidden" name="cancel_return" value="{$cancel_return|escape:'htmlall':'UTF-8'}" />
	<input type="hidden" name="return" value="{$return_url|escape:'htmlall':'UTF-8'}" />
    <input type="hidden" name="bn" value="{$tracking_code|escape:'htmlall':'UTF-8'}" />
    <input type="hidden" name="lc" value="{$iso_code|escape:'htmlall':'UTF-8'}" />
</form>
