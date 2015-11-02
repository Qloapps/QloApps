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
        <script type="text/javascript">
            var product_ids = [{$js_tpl_var.product_ids}];
            var referrer_id = {$js_tpl_var.referrer_id};
            var token = "{$js_tpl_var.token}";
            var display_tab = ["{$js_tpl_var.display_tab}"];

        </script>
        {foreach $js as $js_item}
            <script type="text/javascript" src="{$js_item}"></script>
        {/foreach}
        {foreach $css as $key => $css_item}
            <link type="text/css" rel="stylesheet" href="{$key}" media="{$css_item}" />
        {/foreach}
	</head>
	<body>

