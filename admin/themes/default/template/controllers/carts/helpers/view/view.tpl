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

{extends file="helpers/view/view.tpl"}

{block name="override_tpl"}
<div class="panel">
	{$kpi}
</div>
<div class="row">
	<div class="col-lg-6">
		<div class="panel">
			<h3><i class="icon-user"></i> {l s='Customer information'}</h3>
			{if $customer->id}
				<a class="btn btn-default pull-right" href="mailto:{$customer->email}"><i class="icon-envelope"></i> {$customer->email}</a>
				<h2>
					{if $customer->id_gender == 1}
					<i class="icon-male"></i>
					{elseif $customer->id_gender == 2}
					<i class="icon-female"></i>
					{else}
					<i class="icon-question"></i>
					{/if}
					<a href="{$link->getAdminLink('AdminCustomers')|escape:'html':'UTF-8'}&amp;id_customer={$customer->id|intval}&amp;viewcustomer">{$customer->firstname} {$customer->lastname}</a></h2>
				<div class="form-horizontal">
					<div class="form-group">
						<label class="col-lg-3 control-label">{l s='Account registration date:'}</label>
						<div class="col-lg-3"><p class="form-control-static">{dateFormat date=$customer->date_add}</p></div>
					</div>
					<div class="form-group">
						<label class="col-lg-3 control-label">{l s='Valid orders placed:'}</label>
						<div class="col-lg-3"><p class="form-control-static">{$customer_stats.nb_orders}</p></div>
					</div>
					<div class="form-group">
						<label class="col-lg-3 control-label">{l s='Total spent since registration:'}</label>
						<div class="col-lg-3"><p class="form-control-static">{displayWtPriceWithCurrency price=$customer_stats.total_orders currency=$currency}</p></div>
					</div>
				</div>
			{else}
				<h2>{l s='Guest not registered.'}</h2>
			{/if}
		</div>
	</div>
	<div class="col-lg-6">
		<div class="panel">
			<h3><i class="icon-shopping-cart"></i> {l s='Order(s) Information'}</h3>
			{if is_array($cart_orders) && count($cart_orders)}
				{foreach from=$cart_orders item=$cart_order}
					<h2><a href="{$link->getAdminLink('AdminOrders')|escape:'html':'UTF-8'}&amp;id_order={$cart_order.id_order|intval}&amp;vieworder"> {l s='Order #%d' sprintf=$cart_order.id_order|string_format:"%06d"}</a></h2>
				{/foreach}
				{l s='Created on:'} {dateFormat date=$cart_order.date_add}
			{else}
				<h2>{l s='No order was created from this cart.'}</h2>
				{if $customer->id}
					<a class="btn btn-default" href="{$link->getAdminLink('AdminOrders')|escape:'html':'UTF-8'}&amp;id_cart={$cart->id|intval}&amp;addorder"><i class="icon-shopping-cart"></i> {l s='Create an order from this cart.'}</a>
				{/if}
			{/if}
		</div>
	</div>
</div>
<div class="panel">
	<h3><i class="icon-archive"></i> {l s='Cart summary'}</h3>
	<div class="row">
		<table class="table" id="orderProducts">
			<thead>
				<tr>
					<th class="fixed-width-xs"><span class="title_box">{l s='Image'}</span></th>
					<th class="text-center"><span class="title_box">{l s='Room Type'}</span></th>
					<th class="text-center"><span class="title_box">{l s='Unit Price'}</span></th>
					<th class="text-right"><span class="title_box">{l s='Total'}</span></th>
				</tr>
			</thead>
			<tbody>
				{foreach from=$products item='product'}
					<tr>
						<td>{$product.image}</td>
						<td class="text-center">
							<a href="{$link->getAdminLink('AdminProducts')|escape:'html':'UTF-8'}&amp;id_product={$product.id_product}&amp;updateproduct">
								<span class="productName">{$product.name}</span>{if isset($product.attributes)}<br />{$product.attributes}{/if}<br />
								{if $product.reference}{l s='Ref:'} {$product.reference}{/if}
								{if $product.reference && $product.supplier_reference} / {$product.supplier_reference}{/if}
							</a>
						</td>
						<td class="text-center">{displayWtPriceWithCurrency price=$product.product_price currency=$currency}</td>
						<td class="text-right">{displayWtPriceWithCurrency price=$product.product_total currency=$currency}</td>
					</tr>
				{/foreach}
			</tbody>
			<tfoot>
				<tr>
					<td colspan="3">{l s='Total cost of room types:'}</td>
					<td class="text-right">{displayWtPriceWithCurrency price=$total_products currency=$currency}</td>
				</tr>
				{if $total_discounts != 0}
				<tr>
					<td colspan="3">{l s='Total value of vouchers:'}</td>
					<td class="text-right">{displayWtPriceWithCurrency price=$total_discounts currency=$currency}</td>
				</tr>
				{/if}
				{if $total_wrapping > 0}
				<tr>
					<td colspan="3">{l s='Total cost of gift wrapping:'}</td>
					<td class="text-right">{displayWtPriceWithCurrency price=$total_wrapping currency=$currency}</td>
				</tr>
				{/if}
				{if $cart->getOrderTotal(true, Cart::ONLY_SHIPPING) > 0}
				<tr>
					<td colspan="3">{l s='Total cost of shipping:'}</td>
					<td class="text-right">{displayWtPriceWithCurrency price=$total_shipping currency=$currency}</td>
				</tr>
				{/if}
				<tr>
					<td colspan="3" class=" success"><strong>{l s='Total:'}</strong></td>
					<td class="text-right success"><strong>{displayWtPriceWithCurrency price=$total_price currency=$currency}</strong></td>
				</tr>
			</tfoot>
		</table>
	</div>
	{if $discounts}
	<div class="clear">&nbsp;</div>
	<div class="row">
		<table class="table">
			<thead>
				<tr>
					<th class="fixed-width-xs"><img src="../img/admin/coupon.gif" alt="{l s='Discounts'}" /></th>
					<th>{l s='Discount name'}</th>
					<th class="text-right fixed-width-md">{l s='Value'}</th>
				</tr>
			</thead>
			<tbody>
			{foreach from=$discounts item='discount'}
				<tr>
					<td class="fixed-width-xs">{$discount.id_discount}</td>
					<td><a href="{$link->getAdminLink('AdminCartRules')|escape:'html':'UTF-8'}&amp;id_cart_rule={$discount.id_discount}&amp;updatecart_rule">{$discount.name}</a></td>
					<td class="text-right fixed-width-md">{if (float)$discount.value_real == 0 && (int)$discount.free_shipping == 1}{l s='Free shipping'}{else}- {displayWtPriceWithCurrency price=$discount.value_real currency=$currency}{/if}</td>
				</tr>
			{/foreach}
			</tbody>
		</table>
	</div>
	{/if}
	<div class="clear">&nbsp;</div>
	<div class="row alert alert-warning">
		{l s='For this particular customer group, prices are displayed as:'} <b>{if $tax_calculation_method == $smarty.const.PS_TAX_EXC}{l s='Tax excluded'}{else}{l s='Tax included'}{/if}</b>
	</div>
{/block}
</div>
