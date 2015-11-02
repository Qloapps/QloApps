{*
* 2007-2014 PrestaShop
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
*  @copyright  2007-2014 PrestaShop SA
*  @license    http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*}

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
	<head>
		<meta http-equiv="content-type" content="text/html; charset=utf-8" />
		<title>PrestaShop&trade; - {l s='Affiliation' mod='trackingfront'}</title>
	</head>
	<body>
		{include file="$tpl_dir./errors.tpl"}
		<div style="width: 400px; height: 300px; background-color: #8AB50E; margin: 0 auto; color: white; font-family: arial; ">
			<div style="width: 400px; height: 65px; background-color: #567500; margin: 0 auto; color: white; font-family: arial; text-align: center">
				<div style="font-size: 36px;  margin: 5px auto; font-weight: bold; height: 65x; line-height: 65px; vertical-align: middle;">{l s='Affiliation space' mod='trackingfront'}</div>
			</div>
			<form action="{$request_uri|escape:'html':'UTF-8'}" method="post" style="margin: 20px 50px; text-align: left; font-weight: bold;">
				<label>{l s='Sign in' mod='trackingfront'}</label><br />
				<input type="text" id="email" name="login" value="{if isset($smarty.post.login)}{$smarty.post.login|escape:'html'|stripslashes}{/if}" class="input" />
				<div style="margin: 2.0em 0 0 0;">
					<label>{l s='Password' mod='trackingfront'}</label><br />
					<input type="password" name="passwd" class="input" />
				</div>
				<div style="margin: 2.0em 0 0 0; text-align :right">
					<div id="submit"><input type="submit" name="submitLoginTracking" value="{l s='Sign in' mod='trackingfront'}" class="button" style="border: none; background-color: #567500; color: white; font-weight: bold; width: 90px; height: 30px;" /></div>
				</div>
			</form>
		</div>
		<script type="text/javascript">
			if (document.getElementById('email'))
				document.getElementById('email').focus();
		</script>
	</body>
</html>