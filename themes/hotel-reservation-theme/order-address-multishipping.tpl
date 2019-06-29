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
*  @version  Release: $Revision$
*  @license    http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*}
{if !$opc}
	{assign var='current_step' value='address'}
	{capture name=path}{l s='Addresses'}{/capture}
	{assign var="back_order_page" value="order.php"}
	<h1 class="page-heading">{l s='Addresses'}</h1>
	{include file="$tpl_dir./order-steps.tpl"}
	{include file="$tpl_dir./errors.tpl"}
	{include file="$tpl_dir./order-address-multishipping-products.tpl"}
		<form action="{$link->getPageLink('order', true, NULL, 'multi-shipping=1')|escape:'html':'UTF-8'}" method="post">
{else}
	{assign var="back_order_page" value="order-opc.php"}
	<h1 class="page-heading step-num"><span>1</span> {l s='Addresses'}</h1>
	<div id="opc_account" class="opc-main-block">
		<div id="opc_account-overlay" class="opc-overlay" style="display: none;"></div>
{/if}
<div class="addresses clearfix">
	<input type="hidden" name="id_address_delivery" id="id_address_delivery" value="{$cart->id_address_delivery}"/>
	<p id="address_invoice_form" class="select" {if $cart->id_address_invoice == $cart->id_address_delivery}style="display: none;"{/if}>
	
	{if $addresses|@count >= 1}
    <div class="form-group selector1">
		<label for="id_address_invoice" class="strong">{l s='Choose a billing address:'}</label>
		<select name="id_address_invoice" id="id_address_invoice" class="address_select form-control">
		{section loop=$addresses step=-1 name=address}
			<option value="{$addresses[address].id_address|intval}" {if $addresses[address].id_address == $cart->id_address_invoice && $cart->id_address_delivery != $cart->id_address_invoice}selected="selected"{/if}>{$addresses[address].alias|escape:'html':'UTF-8'}</option>
		{/section}
		</select>
    </div>
	{else}
		<a href="{$link->getPageLink('address', true, NULL, "back={$back_order_page}?step=1{'&multi-shipping=1'|urlencode}{if $back}&mod={$back}{/if}")|escape:'html':'UTF-8'}" title="{l s='Add'}" class="button button-small btn btn-default"><span>{l s='Add a new address'}<i class="icon-chevron-right right"></i></span></a>
	{/if}
	</p>
	<div class="row">
    	<div class="col-sm-12 col-md-6">
            <ul class="address alternate_item {if $cart->isVirtualCart()}full_width{/if} box" id="address_invoice">
            </ul>
        </div>
	</div>
	<p class="address_add submit">
		<a href="{$link->getPageLink('address', true, NULL, "back={$back_order_page}?step=1{'&multi-shipping=1'|urlencode}{if $back}&mod={$back|urlencode}{/if}")|escape:'html':'UTF-8'}" title="{l s='Add'}" class="button button-small btn btn-default"><span>{l s='Add a new address'}<i class="icon-chevron-right right"></i></span></a>
	</p>
	{if !$opc}
	<div id="ordermsg" class="form-group">
		<label>{l s='If you would like to add a comment about your order, please write it in the field below.'}</label>
		<textarea class="form-control" cols="60" rows="6" name="message">{if isset($oldMessage)}{$oldMessage}{/if}</textarea>
	</div>
	{/if}
</div>
{if !$opc}
			<p class="cart_navigation clearfix">
				<input type="hidden" class="hidden" name="step" value="2" />
				<input type="hidden" name="back" value="{$back}" />
				{if $back}
					<a href="{$link->getPageLink('order', true, NULL, "back={$back}")|escape:'html':'UTF-8'}" title="{l s='Previous'}" class="button-exclusive btn btn-default"><i class="icon-chevron-left"></i>{l s='Continue Shopping'}</a>
				{else}
					<a href="{$link->getPageLink('order', true, NULL)|escape:'html':'UTF-8'}" title="{l s='Previous'}" class="button-exclusive btn btn-default"><i class="icon-chevron-left"></i>{l s='Continue Shopping'}</a>
				{/if}
		        <button type="submit" name="processAddress" class="button btn btn-default button-medium"><span>{l s='Proceed to checkout'}<i class="icon-chevron-right right"></i></span></button>
			</p>
		</form>
{else}
	</div>
{/if}
{strip}
{if !$opc}
	{addJsDef orderProcess='order'}
	{addJsDefL name=txtProduct}{l s='product' js=1}{/addJsDefL}
	{addJsDefL name=txtProducts}{l s='products' js=1}{/addJsDefL}
	{addJsDefL name=CloseTxt}{l s='Submit' js=1}{/addJsDefL}
	{addJsDefL name=txtSelectAnAddressFirst}{l s='Please start by selecting an address' js=1}{/addJsDefL}
{/if}
{capture}{if $back}&mod={$back|urlencode}{/if}{/capture}
{capture name=addressUrl}{$link->getPageLink('address', true, NULL, 'back='|cat:$back_order_page|cat:'?step=1'|cat:$smarty.capture.default)|escape:'quotes':'UTF-8'}{/capture}
{addJsDef addressUrl=$smarty.capture.addressUrl}
{capture}{'&multi-shipping=1'|urlencode}{/capture}
{addJsDef addressMultishippingUrl=$smarty.capture.addressUrl|cat:$smarty.capture.default}
{capture name=addressUrlAdd}{$smarty.capture.addressUrl|cat:'&id_address='}{/capture}
{addJsDef addressUrlAdd=$smarty.capture.addressUrlAdd}
{addJsDef formatedAddressFieldsValuesList=$formatedAddressFieldsValuesList}
{addJsDef opc=$opc|boolval}
{capture}<h3 class="page-subheading">{l s='Your billing address' js=1}</h3>{/capture}
{addJsDefL name=titleInvoice}{$smarty.capture.default|@addcslashes:'\''}{/addJsDefL}
{capture}<h3 class="page-subheading">{l s='Your delivery address' js=1}</h3>{/capture}
{addJsDefL name=titleDelivery}{$smarty.capture.default|@addcslashes:'\''}{/addJsDefL}
{capture}<a class="button button-small btn btn-default" href="{$smarty.capture.addressUrlAdd}" title="{l s='Update' js=1}"><span>{l s='Update' js=1}<i class="icon-chevron-right right"></i></span></a>{/capture}
{addJsDefL name=liUpdate}{$smarty.capture.default|@addcslashes:'\''}{/addJsDefL}
{/strip}
