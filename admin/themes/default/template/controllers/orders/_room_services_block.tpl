{*
* 2010-2023 Webkul.
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
*  @copyright 2010-2023 Webkul IN
*  @license   https://store.webkul.com/license.html
*}

<div id="room_type_service_product_desc" class="tab-pane {if isset($show_active) && $show_active}active{/if}">
	{if isset($orderEdit) && $orderEdit}

		<p class="col-sm-12 facility_nav_btn">
			<button id="btn_new_room_service" class="btn btn-success"><i class="icon-plus"></i> {l s='Add new service'}</button>
			<button id="back_to_service_btn" class="btn btn-default"><i class="icon-arrow-left"></i> {l s='Back'}</button>
		</p>

		{* Already selected room services *}
		<div class="col-sm-12 room_ordered_services table-responsive">
			<table class="table">
				<thead>
					<tr>
						<th>{l s='Name'}</th>
						<th>{l s='Quantity'}</th>
						<th>{l s='Price'}</th>
						<th class="text-center">{l s='Options'}</th>
						<th class="text-right">{l s='Action'}</th>
					</tr>
				</thead>
				<tbody>
					{if isset($additionalServices) && $additionalServices}
						{foreach $additionalServices['additional_services'] as $service}
							<tr class="room_demand_block">
								<td>
									<div>{$service['name']|escape:'html':'UTF-8'}</div>
								</td>
								<td>
									{if $service['allow_multiple_quantity']}
										<div class="qty_container">
											<input type="number" class="form-control qty" min="1" data-id_room_type_service_product_order_detail="{$service['id_room_type_service_product_order_detail']}" data-id_product="{$service['id_product']|escape:'html':'UTF-8'}" value="{$service['quantity']|escape:'html':'UTF-8'}">
										</div>
									{/if}
								</td>
								<td>{displayPrice price=$service['total_price_tax_excl']|escape:'html':'UTF-8' currency=$orderCurrency}</td>
								<td class="text-center">
									{if $service['product_auto_add'] && $service['product_price_addition_type'] == Product::PRICE_ADDITION_TYPE_INDEPENDENT}
										<span class="badge badge-info label">{l s='Convenience fee'}</span>
									{/if}
									{if $service['product_auto_add'] && $service['product_price_addition_type'] == Product::PRICE_ADDITION_TYPE_WITH_ROOM}
										<span class="badge badge-info label">{l s='Auto added'}</span>
									{/if}
								</td>
								<td><a class="btn btn-danger pull-right del_room_additional_service" data-id_room_type_service_product_order_detail="{$service['id_room_type_service_product_order_detail']}" href="#"><i class="icon-trash"></i></a></td>
							</tr>
						{/foreach}
					{else}
						<tr>
							<td colspan="3">
								<i class="icon-warning"></i> {l s='No services added yet.'}
							</td>
						</tr>
					{/if}
				</tbody>
			</table>
		</div>
		<form id="add_room_services_form" class="col-sm-12 room_services_container">
			<div class="room_demand_detail">
				{if isset($roomTypeServiceProducts) && $roomTypeServiceProducts}
					<table class="table">
						<thead>
							<tr>
								<th></th>
								<th>{l s='Name'}</th>
								<th>{l s='Quantity'}</th>
								<th class="text-right">{l s='Price'}</th>
							</tr>
						</thead>
						<tbody>
							{foreach $roomTypeServiceProducts as $product}
								<tr class="room_demand_block">
									<td>
										<input data-id_booking_detail="{$id_booking_detail}" value="{$product['id_product']|escape:'html':'UTF-8'}" name="selected_service[]" type="checkbox" class="id_room_type_service"/>
									</td>
									<td>
										{$product['name']|escape:'html':'UTF-8'}
									</td>
									<td>
										{if $product.allow_multiple_quantity}
											<div class="qty_container">
												<input type="number" class="form-control qty" min="1" id="qty_{$product['id_product']|escape:'html':'UTF-8'}" name="service_qty[{$product['id_product']|escape:'html':'UTF-8'}]" data-id-product="{$product.id_product|escape:'html':'UTF-8'}" value="1">
											</div>
										{else}
											{l s='--'}
										{/if}
									</td>
									<td class="text-right">
										{convertPrice price=$product.price_tax_exc}
									</td>
								</tr>
							{/foreach}
						</tbody>
					</table>
				{else}
					<i class="icon-warning"></i> {l s='No services available to add to this room.'}
				{/if}
			</div>
			<input type="hidden" name="id_booking_detail" value="{$id_booking_detail}">
			<button type="submit" id="save_service_service" class="btn btn-success pull-right"><i class="icon-save"></i> {l s='Save'}</button>
		</form>

	{elseif isset($additionalServices) && $additionalServices}
		<table class="table room_demand_detail">
			<thead>
				<tr>
					<th>{l s='ID'}</th>
					<th>{l s='Name'}</th>
					<th class="text-center">{l s='Options'}</th>
					<th>{l s='Price'}</th>
				</tr>
			</thead>
			</tbody>
				{foreach $additionalServices['additional_services'] as $service}
					<tr class="room_demand_block">
						<td>
							{$service['id_product']|escape:'html':'UTF-8'} <a target="blank" href="{$link->getAdminLink('AdminNormalProducts')|escape:'html':'UTF-8'}&amp;id_product={$service['id_product']|escape:'html':'UTF-8'}&amp;updateproduct"><i class="icon-external-link-sign"></i></a>
						</td>
						<td>{$service['name']|escape:'html':'UTF-8'}</td>
						<td class="text-center">
							{if $service['product_auto_add'] && $service['product_price_addition_type'] == Product::PRICE_ADDITION_TYPE_INDEPENDENT}
								<span class="badge badge-info label">{l s='Convenience fee'}</span>
							{/if}
							{if $service['product_auto_add'] && $service['product_price_addition_type'] == Product::PRICE_ADDITION_TYPE_WITH_ROOM}
								<span class="badge badge-info label">{l s='Auto added'}</span>
							{/if}
						</td>
						<td>
							{displayPrice price=$service['total_price_tax_excl'] currency=$orderCurrency}
						</td>
					</tr>
				{/foreach}
			</tbody>
		</table>
	{else}
		{l s='No services selected!'}
	{/if}
</div>
