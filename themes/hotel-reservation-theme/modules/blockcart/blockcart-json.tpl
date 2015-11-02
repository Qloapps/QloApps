{*
* 2007-2015 PrestaShop
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
*  @copyright  2007-2015 PrestaShop SA
*  @license    http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*}
{ldelim}
"products": [
{if $products}
{foreach from=$products item=product name='products'}
{assign var='productId' value=$product.id_product}
{assign var='productAttributeId' value=$product.id_product_attribute}
	{ldelim}
		"id": {$product.id_product|intval},
		"link": {$link->getProductLink($product.id_product, $product.link_rewrite, $product.category, null, null, $product.id_shop, $product.id_product_attribute)|json_encode},
		"quantity": {$product.cart_quantity|intval},
		"image": {$link->getImageLink($product.link_rewrite, $product.id_image, 'home_default')|json_encode},
		"image_cart": {$link->getImageLink($product.link_rewrite, $product.id_image, 'cart_default')|json_encode},
		"priceByLine": {if $priceDisplay == $smarty.const.PS_TAX_EXC}{displayWtPrice|json_encode p=$product.total}{else}{displayWtPrice|json_encode p=$product.total_wt}{/if},
		"name": {$product.name|trim|html_entity_decode:2:'UTF-8'|json_encode},
		"price": {if $priceDisplay == $smarty.const.PS_TAX_EXC}{displayWtPrice|json_encode p=$product.total}{else}{displayWtPrice|json_encode p=$product.total_wt}{/if},
		"price_float": {$product.total|floatval|json_encode},
		"idCombination": {if isset($product.attributes_small)}{$productAttributeId|intval}{else}0{/if},
		"idAddressDelivery": {if isset($product.id_address_delivery)}{$product.id_address_delivery|intval}{else}0{/if},
		"is_gift": {if isset($product.is_gift) && $product.is_gift}true{else}false{/if},
{if isset($product.attributes_small)}
		"hasAttributes": true,
		"attributes": {$product.attributes_small|json_encode},
{else}
		"hasAttributes": false,
{/if}
		"hasCustomizedDatas": {if isset($customizedDatas.$productId.$productAttributeId)}true{else}false{/if},
		"customizedDatas": [
		{if isset($customizedDatas.$productId.$productAttributeId[$product.id_address_delivery])}
		{foreach from=$customizedDatas.$productId.$productAttributeId[$product.id_address_delivery] key='id_customization' item='customization' name='customizedDatas'}{ldelim}
{* This empty line was made in purpose (product addition debug), please leave it here *}
			"customizationId": {$id_customization|intval},
			"quantity": {$customization.quantity|intval},
			"datas": [
				{foreach from=$customization.datas key='type' item='datas' name='customization'}
				{ldelim}
					"type":	{$type|json_encode},
					"datas": [
					{foreach from=$datas key='index' item='data' name='datas'}
						{ldelim}
						"index": {$index|intval},
						"value": {Tools::nl2br($data.value)|json_encode},
						"truncatedValue": {Tools::nl2br($data.value|truncate:28:'...')|json_encode}
						{rdelim}{if !$smarty.foreach.datas.last},{/if}
					{/foreach}]
				{rdelim}{if !$smarty.foreach.customization.last},{/if}
				{/foreach}
			]
		{rdelim}{if !$smarty.foreach.customizedDatas.last},{/if}
		{/foreach}
		{/if}
		]
	{rdelim}{if !$smarty.foreach.products.last},{/if}
{/foreach}{/if}
],
"discounts": [
{if $discounts}{foreach from=$discounts item=discount name='discounts'}
	{ldelim}
		"id": {$discount.id_discount|intval},
		"name": {$discount.name|trim|truncate:18:'...'|json_encode},
		"description": {$discount.description|json_encode},
		"nameDescription": {$discount.name|cat:' : '|cat:$discount.description|trim|truncate:18:'...'|json_encode},
		"code": {$discount.code|json_encode},
		"link": {$link->getPageLink("$order_process", true, NULL, "deleteDiscount={$discount.id_discount}")|json_encode},
		"price": {if $priceDisplay == 1}{convertPrice|json_encode price=$discount.value_tax_exc}{else}{convertPrice|json_encode price=$discount.value_real}{/if},
		"price_float": {if $priceDisplay == 1}{$discount.value_tax_exc|json_encode}{else}{$discount.value_real|json_encode}{/if}
	{rdelim}
	{if !$smarty.foreach.discounts.last},{/if}
{/foreach}{/if}
],
"shippingCost": {$shipping_cost|json_encode},
"shippingCostFloat": {$shipping_cost_float|json_encode},
{if isset($tax_cost)}
"taxCost": {$tax_cost|json_encode},
{/if}
"wrappingCost": {$wrapping_cost|json_encode},
"nbTotalProducts": {$nb_total_products|intval},
"total": {$total|json_encode},
"productTotal": {$product_total|json_encode},
"freeShipping": {displayWtPrice|json_encode p=$free_shipping},
"freeShippingFloat": {$free_shipping|json_encode},
{if isset($errors) && $errors}
"hasError" : true,
"errors" : [
{foreach from=$errors key=k item=error name='errors'}
	{$error|json_encode}
	{if !$smarty.foreach.errors.last},{/if}
{/foreach}
]
{else}
"hasError" : false
{/if}
{rdelim}