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

<div id="container_express_checkout" style="float:right; margin: 10px 40px 0 0">
	{if isset($use_mobile) && $use_mobile}
		<div style="margin-left:30px">
			<img id="payment_paypal_express_checkout" src="{$base_dir_ssl|escape:'htmlall':'UTF-8'}modules/paypal/views/img/logos/express_checkout_mobile/CO_{$PayPal_lang_code|escape:'htmlall':'UTF-8'}_orange_295x43.png" alt="" />
		</div>
	{else}
		{if $paypal_express_checkout_shortcut_logo != false}
		<img id="payment_paypal_express_checkout" src="{$paypal_express_checkout_shortcut_logo|escape:'htmlall':'UTF-8'}" alt="" />
		{else}
		<img id="payment_paypal_express_checkout" src="https://www.paypal.com/{$PayPal_lang_code|escape:'htmlall':'UTF-8'}/i/btn/btn_xpressCheckout.gif" alt="" />
		{/if}
	{/if}
	{if isset($include_form) && $include_form}
		{include file="$template_dir./express_checkout_shortcut_form.tpl"}
	{/if}
</div>
<div class="clearfix"></div>
