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
		"id":            {$product.id_product},
		"link":          "{$link->getProductLink($product.id_product, $product.link_rewrite, $product.category, null, null, $product.id_shop, $product.id_product_attribute)|addslashes|replace:'\\\'':'\''}",
		"quantity":      {$product.cart_quantity|intval},
		"priceByLine":   "{if $priceDisplay == $smarty.const.PS_TAX_EXC}{displayWtPrice|html_entity_decode:2:'UTF-8' p=$product.total}{else}{displayWtPrice|html_entity_decode:2:'UTF-8' p=$product.total_wt}{/if}",
		"image":         "{$link->getImageLink($product.link_rewrite, $product.id_image, 'home_default')|addslashes|replace:'\\\'':'\''}",		
		"name":          "{$product.name|html_entity_decode:2:'UTF-8'|truncate:15:'...':true|escape:'html'}",
		"price":         "{if $priceDisplay == $smarty.const.PS_TAX_EXC}{displayWtPrice|html_entity_decode:2:'UTF-8' p=$product.total}{else}{displayWtPrice|html_entity_decode:2:'UTF-8' p=$product.total_wt}{/if}",
		"price_float":   "{$product.total}",
		"idCombination": {if isset($product.attributes_small)}{$productAttributeId}{else}0{/if},
		"idAddressDelivery": {if isset($product.id_address_delivery)}{$product.id_address_delivery}{else}0{/if},
		"is_gift" : {if isset($product.is_gift) && $product.is_gift}1{else}0{/if},
{if isset($product.attributes_small)}
		"hasAttributes": true,
		"attributes":    "{$product.attributes_small|addslashes|replace:'\\\'':'\''}",
{else}
		"hasAttributes": false,
{/if}
		"hasCustomizedDatas": {if isset($customizedDatas.$productId.$productAttributeId)}true{else}false{/if},
		"customizedDatas":[
		{if isset($customizedDatas.$productId.$productAttributeId[$product.id_address_delivery])}
		{foreach from=$customizedDatas.$productId.$productAttributeId[$product.id_address_delivery] key='id_customization' item='customization' name='customizedDatas'}{ldelim}
{* This empty line was made in purpose (product addition debug), please leave it here *}
			"customizationId":	{$id_customization},
			"quantity":			"{$customization.quantity}",
			"datas": [
				{foreach from=$customization.datas key='type' item='datas' name='customization'}
				{ldelim}
					"type":	"{$type}",
					"datas":
					[
					{foreach from=$datas key='index' item='data' name='datas'}
						{ldelim}
						"index":			{$index},
						"value":			"{Tools::nl2br($data.value|addslashes|replace: '\\\'':'\'')}",
						"truncatedValue":	"{Tools::nl2br($data.value|truncate:28:'...'|addslashes|replace: '\\\'':'\'')}"
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
		"id":              "{$discount.id_discount}",
		"name":            "{$discount.name|truncate:18:'...'|addslashes|replace:'\\\'':'\''}",
		"description":     "{$discount.description|addslashes|replace:'\\\'':'\''}",
		"nameDescription": "{$discount.name|cat:' : '|cat:$discount.description|truncate:18:'...'|addslashes|replace:'\\\'':'\''}",
		"code":            "{$discount.code}",
		"link":            "{$link->getPageLink("$order_process", true, NULL, "deleteDiscount={$discount.id_discount}")|escape:'html'}",
		"price":           "{if $priceDisplay == 1}{convertPrice|html_entity_decode:2:'UTF-8' price=$discount.value_tax_exc}{else}{convertPrice|html_entity_decode:2:'UTF-8' price=$discount.value_real}{/if}",
		"price_float":     "{if $priceDisplay == 1}{$discount.value_tax_exc}{else}{$discount.value_real}{/if}"
	{rdelim}
	{if !$smarty.foreach.discounts.last},{/if}
{/foreach}{/if}
],
"shippingCost": "{$shipping_cost|html_entity_decode:2:'UTF-8'}",
"shippingCostFloat": "{$shipping_cost_float|html_entity_decode:2:'UTF-8'}",
{if isset($tax_cost)}
"taxCost": "{$tax_cost|html_entity_decode:2:'UTF-8'}",
{/if}
"wrappingCost": "{$wrapping_cost|html_entity_decode:2:'UTF-8'}",
"nbTotalProducts": "{$nb_total_products}",
"total": "{$total|html_entity_decode:2:'UTF-8'}",
"productTotal": "{$product_total|html_entity_decode:2:'UTF-8'}",
"freeShipping": "{displayWtPrice|html_entity_decode:2:'UTF-8' p=$free_shipping}",
"freeShippingFloat": "{$free_shipping|html_entity_decode:2:'UTF-8'}",
{if isset($errors) && $errors}
"hasError" : true,
"errors" : [
{foreach from=$errors key=k item=error name='errors'}
	"{$error|addslashes|html_entity_decode:2:'UTF-8'}"
	{if !$smarty.foreach.errors.last},{/if}
{/foreach}
]
{else}
"hasError" : false
{/if}
{rdelim}