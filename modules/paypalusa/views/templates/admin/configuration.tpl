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

{if $paypal_usa_ps_14}
<script type="text/javascript">
		{literal}
		$(document).ready(function() {
			var scripts = [{/literal}{$paypal_usa_js_files}{literal}];
			for(var i = 0; i < scripts.length; i++) {
				$.getScript(scripts[i], function() {paypal_usa_init()});
			}
		});
		{/literal}
</script>
{/if}
<div class="paypal_usa-module-wrapper">
	<div class="paypal_usa-module-header">
		<img src="{$paypal_usa_tracking|escape:'htmlall':'UTF-8'}" alt="" style="display: none;" />
		<a rel="external" href="https://www.paypal.com/us/cgi-bin/webscr?cmd=_registration-run&from=prestashop" target="_blank"><img class="paypal_usa-logo" alt="" src="{$module_dir}/img/logo.png" /></a>
		<span class="paypal_usa-module-intro">{l s='PayPal is the #1 solution to start accepting payments on the web today' mod='paypalusa'}.<br />
		<a class="paypal_usa-module-create-btn L" rel="external" href="https://www.paypal.com/us/cgi-bin/webscr?cmd=_registration-run&partner_id=XYAYGKRUJMJTG" target="_blank"><span>{l s='Sign Up' mod='paypalusa'}</span></a></span>
	</div>
	<div class="paypal_usa-module-wrap">
		<div class="paypal_usa-module-col2">
			<div class="paypal_usa-module-col1inner" style="width: {$paypal_usa_b1width|escape:'htmlall':'UTF-8'}px;">
				<h3>{l s='Benefits of using PayPal' mod='paypalusa'}</h3>
				<ul>
					<li><b>{l s='It\'s Fast and Easy:' mod='paypalusa'}</b> {l s='PayPal is pre-integrated with Prestashop, so you can configure it with a few clicks.' mod='paypalusa'}</li>
					<li><b>{l s='It\'s Global:' mod='paypalusa'}</b> {l s='Accept payments in 21 currencies from 190 markets around the globe.' mod='paypalusa'}</li>
					<li><b>{l s='It\'s Trusted:' mod='paypalusa'}</b> {l s='Industry-leading fraud and buyer protections keep you and your customers safe.' mod='paypalusa'}</li>
					<li><b>{l s='It\'s Cost-Effective:' mod='paypalusa'}</b> {l s='There are no setup fees or long-term contracts. You only pay a low transaction fee.' mod='paypalusa'}</li>
				</ul>
			</div>
			<div class="paypal_usa-module-col1inner" style="width: 220px; margin-left: 40px;">
				<h3>{l s='PayPal Pricing' mod='paypalusa'}</h3>
				<p><b>{l s='PayPal Payments Standard' mod='paypalusa'}</b><br />
					{l s='No monthly fee' mod='paypalusa'}</p>
				<br />
				<p><b>{l s='PayPal Express Checkout' mod='paypalusa'}</b><br />
					{l s='No monthly fee' mod='paypalusa'}</p>
				<br />
				<p><b>{l s='PayPal Payments Advanced' mod='paypalusa'}</b><br />
					{l s='$5 per month' mod='paypalusa'}<br /></p>
				<br />
				<p><a href="https://www.paypal.com/webapps/mpp/merchant-fees?partner_id=XYAYGKRUJMJTG" target="_blank">{l s='(Detailed pricing available at PayPal.com)' mod='paypalusa'}</a></p>
			</div>
			<div class="paypal_usa-module-col1inner" style="width: 307px; margin-left: 30px;">
				<h3>{l s='Unique Features' mod='paypalusa'}</h3>
				<ul>
					<li>{l s='Accept all major' mod='paypalusa'} <b>{l s='credit cards' mod='paypalusa'}</b>, <b>{l s='PayPal' mod='paypalusa'}</b>{l s=', and' mod='paypalusa'} <b>{l s='Bill Me Later®' mod='paypalusa'}</b></li>
					<li>{l s='Tap into millions of active' mod='paypalusa'} <b>{l s='PayPal buyers' mod='paypalusa'}</b> {l s='around the globe' mod='paypalusa'}</li>
					<li>{l s='Get paid within minutes of making a sale' mod='paypalusa'}</li>
					<li>{l s='Process' mod='paypalusa'} <b>{l s='full or partial refunds' mod='paypalusa'}</b></li>
					<li>{l s='Get easy-to-understand reporting' mod='paypalusa'}</li>
				</ul>
			</div>
			<div class="clear"></div>
			<div class="paypal_usa-module-col2inner">
				<h3>{l s='Accept Credit Card Payments Today!' mod='paypalusa'}</h3>
				<img class="paypal_usa-cc" alt="" src="{$module_dir}/img/credit_card.png" style="float: left;" />
				<div style="line-height: 9px; width: 255px; float: left;">
					<a class="paypal_usa-module-btn" style="margin-bottom: 2px; margin-top: 0" href="https://www.paypal.com/us/webapps/mpp/referral/paypal-payments-standard?partner_id=XYAYGKRUJMJTG" target="_blank">{l s='Sign Up for PayPal Payments Standard' mod='paypalusa'}</a><br />
					<a class="paypal_usa-module-btn" style="margin-bottom: 2px;" href="https://www.paypal.com/us/webapps/mpp/referral/paypal-payments-advanced?partner_id=XYAYGKRUJMJTG" target="_blank">{l s='Sign Up for PayPal Payments Advanced' mod='paypalusa'}</a><br />
					<a class="paypal_usa-module-btn" style="margin-bottom: 2px;" href="https://www.paypal.com/us/webapps/mpp/referral/paypal-payflow-link?partner_id=XYAYGKRUJMJTG" target="_blank">{l s='Sign Up for PayPal Payflow Link' mod='paypalusa'}</a><br />
					<a class="paypal_usa-module-btn" style="margin-bottom: 2px;" href="https://www.paypal.com/us/webapps/mpp/referral/paypal-express-checkout?partner_id=XYAYGKRUJMJTG" target="_blank">{l s='Sign Up for PayPal Express Checkout' mod='paypalusa'}</a>
				</div>
				{if $paypal_usa_merchant_country_is_usa}
					<div style="line-height: 9px; width: 194px; float: left;">
						<img src="{$module_dir}/img/logo-slogan.gif" />
					</div>
					<div style="float: left; line-height: 16px; margin: -2px 0; padding: 0; width: 204px;">
						{l s='Boost your online sales by promoting 6 months financing on your website.  Add free PayPal hosted banner ads in minutes.' mod='paypalusa'}  <a href="https://financing.paypal.com/ppfinportal" target="_blank"><b>{l s='Learn more' mod='paypalusa'}</b></a>.
					</div>
				{/if}
			</div>
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
	{if $paypal_usa_warning}
		<div class="info">
			{foreach from=$paypal_usa_warning item=warning}
				{$warning|escape:'htmlall':'UTF-8'}<br />
			{/foreach}
		</div>
	{/if}
	{if isset($paypal_usa_advanced_only_us) && $paypal_usa_advanced_only_us}
		<div class="warn">{l s='You enabled PayPal Payments Advanced however this product only works in the USA' mod='paypalusa'}</div>
	{/if}
	<form action="{$paypal_usa_form_link|escape:'htmlall':'UTF-8'}" method="post">
		<fieldset>
			<legend><img src="{$module_dir}logo.gif" alt="" />{l s='PayPal Products' mod='paypalusa'}</legend>
			<a href="https://www.paypal.com/webapps/mpp/compare-business-products" class="paypal_usa-module-btn right resetMargin" target="_blank">{l s='Compare all PayPal products' mod='paypalusa'}</a>
			<h4>{l s='Which PayPal Product(s) would you like to enable?' mod='paypalusa'}</h4>
			<div class="paypal-usa-threecol">
				<div class="paypal-usa-product first fixCol{if $paypal_usa_configuration.PAYPAL_USA_PAYMENT_STANDARD} paypal-usa-product-active{/if}">
					<h4>{l s='PayPal Payments Standard' mod='paypalusa'}</h4>
					<a class="paypal_usa-module-btn" href="https://www.paypal.com/us/webapps/mpp/referral/paypal-payments-standard?partner_id=XYAYGKRUJMJTG" target="_blank">{l s='Sign Up' mod='paypalusa'}</a><br />
					<p>{l s='Accept credit cards wherever you do business. Your customers don\'t even need a PayPal account. Easy setup, no programming skills required. No setup or monthly charges.' mod='paypalusa'}</p>
					<center><input type="radio" name="paypal_usa_products" id="paypal_usa_payment_standard" value="1" {if $paypal_usa_configuration.PAYPAL_USA_PAYMENT_STANDARD} checked="checked"{/if} /> <label for="paypal_usa_payment_standard"> {l s='Enabled' mod='paypalusa'}</label></center>
					<span class="paypal-usa-or">{l s='OR' mod='paypalusa'}</span>
				</div>
				<div class="paypal-usa-product fixCol{if $paypal_usa_configuration.PAYPAL_USA_PAYMENT_ADVANCED} paypal-usa-product-active{/if}">
					<h4>{l s='PayPal Payments Advanced' mod='paypalusa'}</h4>
					<a class="paypal_usa-module-btn" href="https://www.paypal.com/us/webapps/mpp/referral/paypal-payments-advanced?partner_id=XYAYGKRUJMJTG" target="_blank">{l s='Sign Up' mod='paypalusa'}</a><br />
					<p>{l s='Offer a seamless checkout experience. Get the extra advantage of allowing your customers to check out directly on your site. Simplify PCI compliance. Only $5 per month (See Pricing)' mod='paypalusa'}</p>
					<center><input type="radio" name="paypal_usa_products" id="paypal_usa_payment_advanced" value="2" {if $paypal_usa_configuration.PAYPAL_USA_PAYMENT_ADVANCED} checked="checked"{/if} /> <label for="paypal_usa_payment_advanced"> {l s='Enabled' mod='paypalusa'}</label></center>
					<span class="paypal-usa-or">{l s='OR' mod='paypalusa'}</span>
				</div>
				<div class="paypal-usa-product last fixCol{if $paypal_usa_configuration.PAYPAL_USA_PAYFLOW_LINK} paypal-usa-product-active{/if}">
					<h4>{l s='PayPal Payflow Link' mod='paypalusa'}</h4>
					<a class="paypal_usa-module-btn" href="https://www.paypal.com/us/webapps/mpp/referral/paypal-payflow-link?partner_id=XYAYGKRUJMJTG" target="_blank">{l s='Sign Up' mod='paypalusa'}</a><br />
					<p>{l s='Offer a seamless checkout experience. Get the extra advantage of allowing your customers to check out directly on your site. Simplify PCI compliance. Only $5 per month (See Pricing)' mod='paypalusa'}</p>
					<center><input type="radio" name="paypal_usa_products" id="paypal_usa_payflow_link" value="3"{if $paypal_usa_configuration.PAYPAL_USA_PAYFLOW_LINK} checked="checked"{/if} /> <label for="paypal_usa_payflow_link"> {l s='Enabled' mod='paypalusa'}</label></center>
				</div>
			</div>
			<div class="paypal-usa-onecol">
				<div class="paypal-usa-product_eco fixCol{if $paypal_usa_configuration.PAYPAL_USA_EXPRESS_CHECKOUT} paypal-usa-product-active{/if}">
					<h4>{l s='PayPal Express Checkout' mod='paypalusa'}</h4>
					<a class="paypal_usa-module-btn" href="https://www.paypal.com/us/webapps/mpp/referral/paypal-express-checkout?partner_id=XYAYGKRUJMJTG" target="_blank">{l s='Sign Up' mod='paypalusa'}</a><br />
					<p>{l s='If you accept credit cards online, you can also accept PayPal payments by adding an Express Checkout button. It\'s a proven way to grow your business. No setup or monthly charges.' mod='paypalusa'}</p>
					<center><input type="checkbox" id="paypal_usa_express_checkout" name="paypal_usa_express_checkout" {if $paypal_usa_configuration.PAYPAL_USA_EXPRESS_CHECKOUT} checked="checked"{/if} /> <label for="paypal_usa_express_checkout"> {l s='Enabled' mod='paypalusa'}</label></center>
				</div>
			</div>
			<div class="clear centerText">
				<input type="submit" name="SubmitPayPalProducts" class="button MB15" value="{l s='Enable selected product(s)' mod='paypalusa'}" />
			</div>
			<h4 class="sep-title">{l s='PayPal Express Checkout is optional and can be added to any other PayPal product or alone' mod='paypalusa'} <input type="button" value="{l s='Enable PayPal Express Checkout only' mod='paypalusa'}" class="button right" /></h4>
		</fieldset>
	</form>
	<br />
	<form action="{$paypal_usa_form_link|escape:'htmlall':'UTF-8'}" method="post" id="paypal_usa_paypal_api_settings" class="half-form L">
		<fieldset>
			<legend><img src="{$module_dir}img/settings.gif" alt="" /><span>{l s='PayPal API Settings' mod='paypalusa'}</span></legend>
			<div id="paypal-usa-basic-settings-table">
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
					<input type="text" name="paypal_usa_account" class="input-text" value="{if $paypal_usa_configuration.PAYPAL_USA_ACCOUNT}{$paypal_usa_configuration.PAYPAL_USA_ACCOUNT|escape:'htmlall':'UTF-8'}{/if}" /> <sup>*</sup>
				</div>
				<label for="paypal_usa_api_username">{l s='PayPal API Username:' mod='paypalusa'}</label></td>
				<div class="margin-form">
					<input type="text" name="paypal_usa_api_username" class="input-text" value="{if $paypal_usa_configuration.PAYPAL_USA_API_USERNAME}{$paypal_usa_configuration.PAYPAL_USA_API_USERNAME|escape:'htmlall':'UTF-8'}{/if}" /> <sup>*</sup>
				</div>
				<label for="paypal_usa_api_password">{l s='PayPal API Password:' mod='paypalusa'}</label></td>
				<div class="margin-form">
					<input type="password" name="paypal_usa_api_password" class="input-text" value="{if $paypal_usa_configuration.PAYPAL_USA_API_PASSWORD}{$paypal_usa_configuration.PAYPAL_USA_API_PASSWORD|escape:'htmlall':'UTF-8'}{/if}" /> <sup>*</sup>
				</div>
				<label for="paypal_usa_api_signature">{l s='PayPal API Signature:' mod='paypalusa'}</label></td>
				<div class="margin-form">
					<input type="password" name="paypal_usa_api_signature" class="input-text" value="{if $paypal_usa_configuration.PAYPAL_USA_API_SIGNATURE}{$paypal_usa_configuration.PAYPAL_USA_API_SIGNATURE|escape:'htmlall':'UTF-8'}{/if}" /> <sup>*</sup>
				</div>
			</div>
			<div id="paypal_usa_express_checkout_config" {if !$paypal_usa_configuration.PAYPAL_USA_EXPRESS_CHECKOUT}style="display:none;"{/if}>
				<h4 class="sep-title">{l s='PayPal Express Checkout settings:' mod='paypalusa'}</h4>
				<label for="paypal_usa_checkbox_product">{l s='Show Express Checkout on' mod='paypalusa'}</label>
				<div class="margin-form PT3">
					<input type="checkbox" name="paypal_usa_checkbox_product"{if $paypal_usa_configuration.PAYPAL_USA_EXP_CHK_PRODUCT} checked="checked"{/if} /> <label for="paypal_usa_checkbox_product" class="resetLabel">{l s='Product page' mod='paypalusa'}</label> 
					<input type="checkbox" name="paypal_usa_checkbox_shopping_cart"{if $paypal_usa_configuration.PAYPAL_USA_EXP_CHK_SHOPPING_CART} checked="checked"{/if} /> <label for="paypal_usa_checkbox_shopping_cart}" class="resetLabel">{l s='Shopping Cart' mod='paypalusa'}</label>
				</div>
				<label for="paypal_usa_checkbox_border_color">{l s='Express Checkout border color' mod='paypalusa'}</label></td>
				<div class="margin-form">
					<input class="colorSelector" type="text" id="paypal_usa_checkbox_border_color" name="paypal_usa_checkbox_border_color" value="{$paypal_usa_configuration.PAYPAL_USA_EXP_CHK_BORDER_COLOR|escape:'htmlall':'UTF-8'}" />
				</div>
			</div>
			<div class="margin-form">
				<input type="submit" name="SubmitBasicSettings" class="button" value="{l s='Save settings' mod='paypalusa'}" />
			</div>
			<span class="small"><sup style="color: red;">*</sup> {l s='Required fields' mod='paypalusa'}</span>
		</fieldset>
	</form>
	<form action="{$paypal_usa_form_link|escape:'htmlall':'UTF-8'}" method="post" {if !$paypal_usa_configuration.PAYPAL_USA_PAYMENT_ADVANCED && !$paypal_usa_configuration.PAYPAL_USA_PAYFLOW_LINK} style="display: none;"{else} class="half-form R"{/if}>
		<fieldset id="paypal-usa-advanced-settings">
			<legend><img src="{$module_dir}img/settings.gif" alt="" />{l s='Advanced Settings' mod='paypalusa'}</legend>
			<h4>{l s='These settings are required to use PayPal Advanced' mod='paypalusa'}</h4>
			<a onclick="$('#paypal_payments_advanced_help').lightbox_me({literal}{centered: true}{/literal});" href="javascript:void(0);" class="paypal_usa-module-btn learn_more">{l s='Learn how to configure PayPal Payments Advanced' mod='paypalusa'}</a><br />
			<label for="paypal_usa_sandbox_advanced_on">{l s='Mode' mod='paypalusa'}</label>
			<div class="margin-form PT4">
				<input type="radio" name="paypal_usa_sandbox_advanced" id="paypal_usa_sandbox_advanced_on" value="0"{if $paypal_usa_configuration.PAYPAL_USA_SANDBOX_ADVANCED == 0} checked="checked"{/if} /> <label for="paypal_usa_sandbox_advanced_on" class="resetLabel">{l s='Live' mod='paypalusa'}</label>
				<input type="radio" name="paypal_usa_sandbox_advanced" id="paypal_usa_sandbox_advanced_off" value="1"{if $paypal_usa_configuration.PAYPAL_USA_SANDBOX_ADVANCED == 1} checked="checked"{/if} /> <label for="paypal_usa_sandbox_advanced_off" class="resetLabel">{l s='Test (Sandbox)' mod='paypalusa'}</label>
			</div>
			<label for="paypal_usa_manager_partner">{l s='PayPal Manager Partner' mod='paypalusa'}</label>
			<div class="margin-form">
				<input type="text" name="paypal_usa_manager_partner" class="input-text" value="{if $paypal_usa_configuration.PAYPAL_USA_MANAGER_PARTNER}{$paypal_usa_configuration.PAYPAL_USA_MANAGER_PARTNER|escape:'htmlall':'UTF-8'}{else}paypal{/if}" /> <sup>*</sup>
			</div>
			<label for="paypal_usa_manager_login">{l s='PayPal Manager Merchant Login' mod='paypalusa'}</label>
			<div class="margin-form">
				<input type="text" name="paypal_usa_manager_login" class="input-text" value="{if $paypal_usa_configuration.PAYPAL_USA_MANAGER_LOGIN}{$paypal_usa_configuration.PAYPAL_USA_MANAGER_LOGIN|escape:'htmlall':'UTF-8'}{/if}" /> <sup>*</sup>
			</div>
			<label for="paypal_usa_manager_partner">{l s='PayPal Manager User' mod='paypalusa'}</label></td>
			<div class="margin-form">
				<input type="text" name="paypal_usa_manager_user" class="input-text" value="{if $paypal_usa_configuration.PAYPAL_USA_MANAGER_USER}{$paypal_usa_configuration.PAYPAL_USA_MANAGER_USER|escape:'htmlall':'UTF-8'}{/if}" /> <sup>*</sup>
			</div>
			<label for="paypal_usa_manager_password">{l s='PayPal Manager Password' mod='paypalusa'}</label>
			<div class="margin-form">
				<input type="password" name="paypal_usa_manager_password" class="input-text" value="{if $paypal_usa_configuration.PAYPAL_USA_MANAGER_PASSWORD}{$paypal_usa_configuration.PAYPAL_USA_MANAGER_PASSWORD|escape:'htmlall':'UTF-8'}{/if}" /> <sup>*</sup>
			</div>
			<div class="margin-form">
				<input type="submit" name="SubmitAdvancedSettings" class="button" value="{l s='Save Settings' mod='paypalusa'}" />
			</div>
			<span class="small"><sup style="color: red;">*</sup> {l s='Required fields' mod='paypalusa'}</span>
		</fieldset>
	</form>
	<div id="paypal_payments_advanced_help" class="paypal_help_box paypal_usa-module-wrap" style="display: none; width: 400px; height: 330px;">
		<p>{l s='For PayPal Payments Advanced to work properly on your store, please adjust your PayPal settings by following these steps:' mod='paypalusa'}</p>
		<ul style="list-style: decimal; margin: 5px 0 0 25px">
			<li>{l s='Log-in to' mod='paypalusa'} <a href="https://manager.paypal.com?partner_id=XYAYGKRUJMJTG" target="_blank">{l s='Paypal Manager' mod='paypalusa'}</a></li>
			<li>{l s='Select Hosted Checkout Pages, then select "Setup"' mod='paypalusa'}</li>
			<li>{l s='Under Security Options, set the "Enable Secure Token" to "Yes". This change is required in order for your checkout to work, and it is required to be set by you personally for security reasons. Please do not change any other values on this page or on the Customize page, as PrestaShop will pass these values on your behalf for ease of configuration.' mod='paypalusa'}</li>
		</ul>
		<input type="button" value="{l s='close' mod='paypalusa'}" class="close" />
	</div>
	<div id="paypal_link_help" class="paypal_help_box paypal_usa-module-wrap" style="display: none;">
		{l s='Help about PayPal Express Checkout will be here.' mod='paypalusa'}<br />
		<input type="button" value="{l s='Close' mod='paypalusa'}" class="button close" />
	</div>
</div>
{if $paypal_usa_merchant_country_is_mx}
	<script type="text/javascript">
		{literal}
		$(document).ready(function() {
			$('#content table.table tbody tr th span').html('paypalmx');
		});
		{/literal}
	</script>
{/if}