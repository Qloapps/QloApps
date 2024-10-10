{**
* NOTICE OF LICENSE
*
* This source file is subject to the Open Software License version 3.0
* that is bundled with this package in the file LICENSE.md
* It is also available through the world-wide-web at this URL:
* https://opensource.org/license/osl-3-0-php
* If you did not receive a copy of the license and are unable to
* obtain it through the world-wide-web, please send an email
* to support@qloapps.com so we can send you a copy immediately.
*
* DISCLAIMER
*
* Do not edit or add to this file if you wish to upgrade this module to a newer
* versions in the future. If you wish to customize this module for your needs
* please refer to https://store.webkul.com/customisation-guidelines for more information.
*
* @author Webkul IN
* @copyright Since 2010 Webkul
* @license https://opensource.org/license/osl-3-0-php Open Software License version 3.0
*}

{if isset($service_product_data) && $service_product_data}
	<table class="product" class="bordered-table" width="100%" cellpadding="4" cellspacing="0">
		<thead>
			<tr>
				<th colspan="{if $display_product_images}6{else}5{/if}" class="header">{l s='Service Products Details' pdf='true'}</th>
			</tr>
			<tr>
				{if $display_product_images}
					<th class="product header small">{l s='Image' pdf='true'}</th>
				{/if}
				<th class="product header small">{l s='Name' pdf='true'}</th>
				<th class="product header small">{l s='Tax Rate(s)' pdf='true'}</th>
				<th class="product header small">{l s='Unit Price' pdf='true'} <br /> {l s='(Tax excl.)' pdf='true'}</th>
				<th class="product header small">{l s='Qty' pdf='true'}</th>
				<th class="product header small">{l s='Total' pdf='true'} <br /> {l s='(Tax excl.)' pdf='true'}</th>
			</tr>
		</thead>
		<tbody>
			{foreach $service_product_data as $product}
				{cycle values=["color_line_even", "color_line_odd"] assign=bgcolor_class}
				<tr class="product {$bgcolor_class}">
					{if $display_product_images}
						<td class="product center">
							{if isset($product.image) && $product.image->id}
								{$product.image_tag}
							{/if}
						</td>
					{/if}
					<td class="product center">
						{$product.product_name}
					</td>
					<td class="product center">
						{$product.order_detail_tax_label}
					</td>
					<td class="product right">
						{displayPrice currency=$order->id_currency price=$product.unit_price_tax_excl_including_ecotax}
					</td>
					<td class="product center">
						{$product.product_quantity}
					</td>
					<td  class="product right">
						{displayPrice currency=$order->id_currency price=$product.total_price_tax_excl_including_ecotax}
					</td>
				</tr>
		{/foreach}
		</tbody>
	</table>
{/if}

