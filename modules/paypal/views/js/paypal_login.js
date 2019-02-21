/*
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
*  @author PrestaShop SA <contact@prestashop.com>
*  @copyright  2007-2018 PrestaShop SA
*  @license    http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*/

$(function(){ldelim}
	if($("#create-account_form").length > 0)
		{if $smarty.const._PS_VERSION_ >= 1.6}
			$("#create-account_form").parent().before('<div id="buttonPaypalLogin1"></div>');
		{else}
			$("#create-account_form").before('<div id="buttonPaypalLogin1"></div>');
		{/if}
	else
	{ldelim}
		{if $smarty.const._PS_VERSION_ >= 1.6}
			$("#login_form").parent().before('<div id="buttonPaypalLogin1"></div>');
		{else}
			$("#login_form").before('<div id="buttonPaypalLogin1"></div>');
		{/if}
		$("#buttonPaypalLogin1").css({ldelim}
			"clear"       : "both",	
			"margin-bottom" : "13px"
		{rdelim});
	{rdelim}

	$("#buttonPaypalLogin1").css({ldelim}
		"clear"       : "both",
		'margin-bottom' : '10px',
		{if $smarty.const._PS_VERSION_ >= 1.6}
		'margin-left' : '20px',
		'width' : '100%'
		{/if}	
	{rdelim});

	paypal.use( ["login"], function(login) {ldelim}
		login.render ({ldelim}
			"appid": "{$PAYPAL_LOGIN_CLIENT_ID}",
			{if $PAYPAL_SANDBOX == 1} "authend" : "sandbox",{/if}
			"scopes": "openid profile email address phone https://uri.paypal.com/services/paypalattributes https://uri.paypal.com/services/expresscheckout",
			"containerid": "buttonPaypalLogin1",
			{if $PAYPAL_LOGIN_TPL == 2} "theme" : "neutral", {/if}
			"returnurl": "{$PAYPAL_RETURN_LINK}?{$page_name}",
			'locale' : '{$paypal_locale}',
		{rdelim});
	{rdelim});
{rdelim});


