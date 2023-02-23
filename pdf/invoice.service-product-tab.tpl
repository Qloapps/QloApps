{*
* 2010-2020 Webkul.
*
* NOTICE OF LICENSE
*
* All right is reserved,
* Please go through this link for complete license : https://store.webkul.com/license.html
*
* DISCLAIMER
*
* Do not edit or add to this file if you wish to upgrade this module to newer
* versions in the future. If you wish to customize this module for your
* needs please refer to https://store.webkul.com/customisation-guidelines/ for more information.
*
*  @author    Webkul IN <support@webkul.com>
*  @copyright 2010-2020 Webkul IN
*  @license   https://store.webkul.com/license.html
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

