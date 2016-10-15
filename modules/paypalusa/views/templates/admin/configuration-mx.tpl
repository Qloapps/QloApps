{*
** @author PrestaShop SA <contact@prestashop.com>
** @copyright  2007-2014 PrestaShop SA
** @version  Release: $Revision: 1.2.0 $
**
** International Registered Trademark & Property of PrestaShop SA
**
** Description: PayPal addon's configuration page
**
** This template is displayed in the Back-office section of your store when you are configuring the PayPal's addon
** It allows you to enable PayPal on your store and to configure your credentials and preferences
**
*}
<img src="{$paypal_usa_tracking|escape:'htmlall':'UTF-8'}" alt="" style="display: none;" />
<div class="paypal_usa-module-wrapper">
	<div class="paypal_usa-module-header">
		<a rel="external" href="https://www.paypal.com/us/cgi-bin/webscr?cmd=_registration-run&from=prestashop" target="_blank"><img class="paypal_usa-logo" alt="" src="{$module_dir}/img/logo.png" /></a>
		<span class="paypal_usa-module-intro">{l s='PayPal is the #1 solution to start accepting payments on the web today' mod='paypalusa'}.<br />
		<span class="paypal_usa-module-singup-text">{l s='If you don\'t have a PayPal account' mod='paypalusa'}.</span>
		<a class="paypal_usa-module-create-btn" rel="external" href="https://www.paypal.com/mx/webapps/mpp/referral/paypal-business-account2?partner_id=XYAYGKRUJMJTG" target="_blank"><span>{l s='Sign up for a PayPal account here' mod='paypalusa'}</span></a></span>
	</div>
	<div class="paypal_usa-module-wrap">
		<div class="paypal_usa-module-col1-mx L">
			<h3>{l s='Credit and Debit Cards' mod='paypalusa'}</h3>
			<p>{l s='With PayPal you can accept payments with all major credit and debit cards in 25 currencies from 190 countries.' mod='paypalusa'}</p>
			
		</div>
		<div class="paypal_usa-module-col1-mx R">
			<h3>{l s='Monthly Payments Feature' mod='paypalusa'}</h3>
			<p>{l s='Offer to your clients the possibility to make monthly payments using the following credit cards: Bancomer, Banamex, HSBC, Santander y Banorte.' mod='paypalusa'}</p>
			<img class="paypal_usa-cc" alt="" src="{$module_dir}/img/accpmark_tarjdeb_mx.png" style="margin-top: 15px;" />
		</div>
	</div>
	{if $paypal_usa_validation}
		<div class="conf">
			{foreach from=$paypal_usa_validation item=validation}
				{$validation|escape:'htmlall':'UTF-8'}<br />
			{/foreach}
		</div>
	{/if}
	{if $paypal_usa_error}
		<div class="error">
			{foreach from=$paypal_usa_error item=error}
				{$error|escape:'htmlall':'UTF-8'}<br />
			{/foreach}
		</div>
	{/if}
	<form action="{$paypal_usa_form_link|escape:'htmlall':'UTF-8'}" method="post">
		<fieldset>
			<legend><img src="{$module_dir}img/settings.gif" alt="" /><span>{l s='PayPal PayPal Express Checkout API Settings' mod='paypalusa'}</span></legend>
			<label for="paypal_usa_sandbox_on">{l s='Mode' mod='paypalusa'}</label>
			<div class="margin-form PT4">
				<input type="radio" name="paypal_usa_sandbox" id="paypal_usa_sandbox_on" value="0"{if $paypal_usa_configuration.PAYPAL_USA_SANDBOX == 0} checked="checked"{/if} /> <label for="paypal_usa_sandbox_on" class="resetLabel">{l s='Live' mod='paypalusa'}</label>
				<input type="radio" name="paypal_usa_sandbox" id="paypal_usa_sandbox_off" value="1"{if $paypal_usa_configuration.PAYPAL_USA_SANDBOX == 1} checked="checked"{/if} /> <label for="paypal_usa_sandbox_off" class="resetLabel">{l s='Test (Sandbox)' mod='paypalusa'}</label>
				<p>{l s='Use the links below to retreive your PayPal API credentials:' mod='paypalusa'}<br />
				<a onclick="window.open(this.href, '1369346829804', 'width=415,height=530,toolbar=0,menubar=0,location=0,status=0,scrollbars=0,resizable=0,left=0,top=0');
					return false;" href="https://www.paypal.com/us/cgibin/webscr?cmd=_get-api-signature&generic-flow=true" class="paypal_usa-module-btn">{l s='Live Mode API' mod='paypalusa'}</a>&nbsp;&nbsp;&nbsp;//&nbsp;&nbsp;&nbsp;<a onclick="window.open(this.href, '1369346829804', 'width=415,height=530,toolbar=0,menubar=0,location=0,status=0,scrollbars=0,resizable=0,left=0,top=0');
					return false;" href="https://www.sandbox.paypal.com/us/cgi-bin/webscr?cmd=_get-api-signature&generic-flow=true" class="paypal_usa-module-btn">{l s='Sandbox Mode API' mod='paypalusa'}</a></p>
			</div>
			<label for="paypal_usa_account">{l s='PayPal Business Account:' mod='paypalusa'}</label></td>
			<div class="margin-form">
				<input type="text" name="paypal_usa_account" style="width: 250px;" value="{if $paypal_usa_configuration.PAYPAL_USA_ACCOUNT}{$paypal_usa_configuration.PAYPAL_USA_ACCOUNT|escape:'htmlall':'UTF-8'}{/if}" /> <sup>*</sup>
			</div>
			<label for="paypal_usa_api_username">{l s='PayPal API Username:' mod='paypalusa'}</label></td>
			<div class="margin-form">
				<input type="text" name="paypal_usa_api_username" style="width: 250px;" value="{if $paypal_usa_configuration.PAYPAL_USA_API_USERNAME}{$paypal_usa_configuration.PAYPAL_USA_API_USERNAME|escape:'htmlall':'UTF-8'}{/if}" /> <sup>*</sup>
			</div>
			<label for="paypal_usa_api_password">{l s='PayPal API Password:' mod='paypalusa'}</label></td>
			<div class="margin-form">
				<input type="password" name="paypal_usa_api_password" style="width: 250px;" value="{if $paypal_usa_configuration.PAYPAL_USA_API_PASSWORD}{$paypal_usa_configuration.PAYPAL_USA_API_PASSWORD|escape:'htmlall':'UTF-8'}{/if}" /> <sup>*</sup>
			</div>
			<label for="paypal_usa_api_signature">{l s='PayPal API Signature:' mod='paypalusa'}</label></td>
			<div class="margin-form">
				<input type="password" name="paypal_usa_api_signature" style="width: 250px;" value="{if $paypal_usa_configuration.PAYPAL_USA_API_SIGNATURE}{$paypal_usa_configuration.PAYPAL_USA_API_SIGNATURE|escape:'htmlall':'UTF-8'}{/if}" /> <sup>*</sup>
			</div>
			<input type="hidden" name="paypal_usa_express_checkout" value="1" />
			<h4 class="sep-title">{l s='PayPal Express Checkout button settings:' mod='paypalusa'}</h4>
			<label for="paypal_usa_checkbox_product">{l s='Display button on' mod='paypalusa'}</label>
			<div class="margin-form PT2">
				<input type="checkbox" name="paypal_usa_checkbox_product"{if $paypal_usa_configuration.PAYPAL_USA_EXP_CHK_PRODUCT} checked="checked"{/if} /> <label for="paypal_usa_checkbox_product" class="resetLabel">{l s='Product page' mod='paypalusa'}</label> 
				<input type="checkbox" name="paypal_usa_checkbox_shopping_cart"{if $paypal_usa_configuration.PAYPAL_USA_EXP_CHK_SHOPPING_CART} checked="checked"{/if} /> <label for="paypal_usa_checkbox_shopping_cart}" class="resetLabel">{l s='Shopping Cart' mod='paypalusa'}</label>
			</div>
			<label for="paypal_usa_checkbox_border_color">{l s='Button border color' mod='paypalusa'}</label></td>
			<div class="margin-form">
				<input class="colorSelector" type="text" id="paypal_usa_checkbox_border_color" name="paypal_usa_checkbox_border_color" value="{$paypal_usa_configuration.PAYPAL_USA_EXP_CHK_BORDER_COLOR|escape:'htmlall':'UTF-8'}" />
			</div>
			<div class="margin-form">
				<input type="submit" name="SubmitBasicSettings" class="button" value="{l s='Save settings' mod='paypalusa'}" />
			</div>
			<span class="small"><sup style="color: red;">*</sup> {l s='Required fields' mod='paypalusa'}</span>
		</fieldset>
	</form>
</div>
<script type="text/javascript">
	{literal}
		$(document).ready(function() {
			$('#content table.table tbody tr th span').html('paypalmx');
		});
	{/literal}
</script>