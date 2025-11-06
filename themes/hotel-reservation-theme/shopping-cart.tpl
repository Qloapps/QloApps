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

{capture name=path}{l s='Your shopping cart'}{/capture}

{* <h1 id="cart_title" class="page-heading">{l s='Shopping-cart summary'}
	{if !isset($empty) && !$PS_CATALOG_MODE}
		<span class="heading-counter">{l s='Your shopping cart contains:'}
			<span id="summary_products_quantity">{$productNumber} {if $productNumber == 1}{l s='product'}{else}{l s='products'}{/if}</span>
		</span>
	{/if}
</h1> *}

{if isset($account_created)}
	<p class="alert alert-success">
		{l s='Your account has been created.'}
	</p>
{/if}

{assign var='current_step' value='summary'}
{block name='errors'}
	{include file="$tpl_dir./order-steps.tpl"}
{/block}

{if isset($empty)}
	<p class="alert alert-warning">{l s='Your shopping cart is empty.'}</p>
{elseif $PS_CATALOG_MODE}
	<p class="alert alert-warning">{l s='This store has not accepted your new order.'}</p>
{else}
	<p id="emptyCartWarning" class="alert alert-warning unvisible">{l s='Your shopping cart is empty.'}</p>
	{* eu-legal *}
	{block name='displayBeforeShoppingCartBlock'}
		{hook h="displayBeforeShoppingCartBlock"}
	{/block}

	{block name='shopping_cart_detail'}
		{include file="$tpl_dir./shopping-cart-detail.tpl"}
	{/block}

	{if $show_option_allow_separate_package}
	<p>
		<label for="allow_seperated_package" class="checkbox inline">
			<input type="checkbox" name="allow_seperated_package" id="allow_seperated_package" {if $cart->allow_seperated_package}checked="checked"{/if} autocomplete="off"/>
			{l s='Send available products first'}
		</label>
	</p>
	{/if}

	{block name='displayShoppingCartFooter'}
		<div id="HOOK_SHOPPING_CART">{$HOOK_SHOPPING_CART}</div>
	{/block}

	<div class="clear"></div>
	<div class="cart_navigation_extra">
		{block name='displayShoppingCart'}
			<div id="HOOK_SHOPPING_CART_EXTRA">{if isset($HOOK_SHOPPING_CART_EXTRA)}{$HOOK_SHOPPING_CART_EXTRA}{/if}</div>
		{/block}
	</div>
	{block name='shopping_cart_js_vars'}
		{strip}
			{addJsDef deliveryAddress=$cart->id_address_delivery|intval}
			{addJsDefL name=txtProduct}{l s='product' js=1}{/addJsDefL}
			{addJsDefL name=txtProducts}{l s='products' js=1}{/addJsDefL}
		{/strip}
	{/block}
{/if}

{* Fancybox for extra demands*}
{block name='shopping_cart_extra_services'}
	<div style="display:none;" id="rooms_extra_services">
	</div>
{/block}
