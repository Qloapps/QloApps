{*
* 2007-2017 PrestaShop
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
*  @copyright  2007-2017 PrestaShop SA
*  @license    http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*}

{if !$opc}
	{block name='order_payment_js_vars'}
		{addJsDefL name=txtProduct}{l s='product' js=1}{/addJsDefL}
		{addJsDefL name=txtProducts}{l s='products' js=1}{/addJsDefL}
		{capture name=path}{l s='Your payment method'}{/capture}
		<h1 class="page-heading">{l s='Please choose your payment method'}
			{if !isset($empty) && !$PS_CATALOG_MODE}
				<span class="heading-counter">{l s='Your shopping cart contains:'}
					<span id="summary_products_quantity">{$productNumber} {if $productNumber == 1}{l s='product'}{else}{l s='products'}{/if}</span>
				</span>
			{/if}
		</h1>
	{/block}
{/if}

{if !$opc}
	{assign var='current_step' value='payment'}
	{block name='order_steps_container'}
		{include file="$tpl_dir./order-steps.tpl"}
	{/block}
	{block name='order_payment_errors_block'}
		{include file="$tpl_dir./errors.tpl"}
	{/block}
{else}
	<div id="opc_payment_methods" class="opc-main-block">
		<div id="opc_payment_methods-overlay" class="opc-overlay" style="display: none;"></div>
{/if}
{if $advanced_payment_api}
	{block name='order_payment_advanced'}
    	{include file="$tpl_dir./order-payment-advanced.tpl"}
	{/block}
{else}
	{block name='order_payment_classic'}
    	{include file = "$tpl_dir./order-payment-classic.tpl"}
	{/block}
{/if}
