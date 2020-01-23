{*
* 2010-2019 Webkul.
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
*  @copyright 2010-2019 Webkul IN
*  @license   https://store.webkul.com/license.html
*}

<div class="panel form-horizontal" id="customer_cart_details">
	<div class="panel-heading">
		<i class="icon-shopping-cart"></i>
		{l s='Cart Details'}
	</div>
	<div class="row">
		<div class="col-lg-12">
			<table class="table" id="customer_cart_details_table">
				<thead>
					<tr>
						<th><span class="title_box">{l s='Room No.'}</span></th>
						<th><span class="title_box">{l s='Room Image'}</th>
						<th><span class="title_box">{l s='Room Type'}</span></th>
						<th><span class="title_box">{l s='Duration'}</span></th>
						<th><span class="title_box">{l s='Unit Price (tax excl)'}</span></th>
						<th><span class="title_box">{l s='Additinal Facilities (tax excl)'}</span></th>
						<th><span class="title_box">{l s='Total Rooms Price (tax excl)'}</span></th>
						<th><span class="title_box">{l s='Total Price (tax excl)'}</span></th>
						<th></th>
					</tr>
				</thead>
				<tbody>
				{if isset($cart_detail_data) && $cart_detail_data}
					{assign var=curr_id value=$cart->id_currency|intval}
					{foreach from=$cart_detail_data item=data}
						<tr>
							<td>{$data.room_num|escape:'html':'UTF-8'}</td>
							<td><img src="{$data.image_link|escape:'html':'UTF-8'}" title="Room image" /></td>
							<td>
								<p>{$data.room_type|escape:'html':'UTF-8'}</p>
								{if isset($data.selected_demands) && $data.selected_demands}
									<ul class="extra-demand-list">
									{foreach $data.selected_demands as $selDemand}
										<li>
											{$selDemand.name|escape:'html':'UTF-8'}
										</li>
									{/foreach}
									</ul>
								{/if}
							</td>
							<td>{$data.date_from|escape:'html':'UTF-8'|date_format:"%d-%m-%Y"}&nbsp;-&nbsp; {$data.date_to|escape:'html':'UTF-8'|date_format:"%d-%m-%Y"}</td>
							<td id="cart_detail_data_unit_price_{$data.id|escape:'html':'UTF-8'}">
								<span class="product_original_price {if $data.feature_price_diff > 0}room_type_old_price{/if}" {if $data.feature_price_diff < 0} style="display:none;"{/if}>
									{displayPrice price=$data.product_price_tax_excl|escape:'html':'UTF-8'}
								</span>&nbsp;
								<span class="room_type_current_price" {if !$data.feature_price_diff}style="display:none;"{/if}>
									{displayPrice price=$data.feature_price_tax_excl|escape:'html':'UTF-8'}
								</span>
							</td>
							<td>
								{if isset($data.extra_demands) && $data.extra_demands}
									{displayPrice price=$data.demand_price|escape:'html':'UTF-8'}
								{else}
									{displayPrice price=0}
								{/if}
							</td>
							<td id="cart_detail_data_price_{$data.id|escape:'html':'UTF-8'}">{displayPrice price=$data.amt_with_qty}</td>
							<td>
								{if isset($data.extra_demands) && $data.extra_demands}
									{displayPrice price=$data.amt_with_qty|escape:'html':'UTF-8'+$data.demand_price|escape:'html':'UTF-8'}
								{else}
									{displayPrice price=$data.amt_with_qty|escape:'html':'UTF-8'}
								{/if}
							</td>
							<td>
								<button class="delete_hotel_cart_data btn btn-danger" data-id_room={$data.id_room|escape:'html':'UTF-8'} data-id_product={$data.id_product|escape:'html':'UTF-8'} data-id = {$data.id|escape:'html':'UTF-8'} data-id_cart = {$data.id_cart|escape:'html':'UTF-8'} data-date_to = {$data.date_to|escape:'html':'UTF-8'} data-date_from = {$data.date_from|escape:'html':'UTF-8'}>
									<i class="icon-trash"></i>&nbsp;{l s='Delete'}
								</button>
								{if isset($data.extra_demands) && $data.extra_demands}
									<br />
									<a href="#" id_room={$data.id_room|escape:'html':'UTF-8'} date_from="{$data.date_from|escape:'html':'UTF-8'}" date_to="{$data.date_to|escape:'html':'UTF-8'}" id_product="{$data.id_product|escape:'html':'UTF-8'}" id_cart="{$data.id_cart|escape:'html':'UTF-8'}" class="open_rooms_extra_demands btn btn-success" title="{l s='Click here to add or remove the additinal facilities of this room type.'}">
										<i class="icon-plus"></i>&nbsp;{l s='Facilities'}
									</a>
								{/if}
							</td>
						</tr>
					{/foreach}
				{else}
					<tr>
						<td>{l s='No Room Found in the cart.'}</td>
					</tr>
				{/if}
				</tbody>
			</table>
		</div>
	</div>
</div>

{* Modal for extra demands *}
<div class="modal" tabindex="-1" role="dialog" id="rooms_type_extra_demands">
	<div class="modal-dialog" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
				<span aria-hidden="true">&times;</span>
				</button>
				<h3 class="modal-title">{l s='Additional Facilities'}</h3>
			</div>
			<div class="modal-body" id="rooms_extra_demands">
				<div class="rooms_extra_demands_head">
					<p class="rooms_extra_demands_text">{l s='Add below additional facilities to the room for better hotel experience'}</p>
				</div>
				<div id="room_type_demands_desc"></div>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-secondary" data-dismiss="modal">{l s='Close'}</button>
			</div>
		</div>
	</div>
</div>

{strip}
	{addJsDefL name=txtExtraDemandSucc}{l s='Updated Successfully' js=1}{/addJsDefL}
	{addJsDefL name=txtExtraDemandErr}{l s='Some error occurred while updating demands' js=1}{/addJsDefL}
{/strip}

{* Css for handling extra demands changes *}
<style type="text/css">
	#customer_cart_details .extra-demand-list {
		padding-left:15px;}
	#customer_cart_details .delete_hotel_cart_data {
		margin-bottom:2px !important;}
	#customer_cart_details .room_type_old_price {
		text-decoration: line-through;
		color:#979797;
		font-size:12px;}
	/*Extra demands CSS*/
	#rooms_extra_demands {
		font-size: 16px;}
	#rooms_extra_demands .room_demands_container {
		border: 1px solid #ddd;}
	#rooms_extra_demands .demand_header {
		padding: 10px;
		color: #333;
    	border-bottom: 1px solid #ddd;}
	#rooms_extra_demands .rooms_extra_demands_head {
		margin-bottom: 18px;}
	#rooms_extra_demands .room_demand_block {
		margin-bottom: 15px;
		color: #333;
		font-size: 14px;}
	#rooms_extra_demands .room_demand_detail {
		padding: 15px 15px 0px 15px;}
</style>

{* JS for handling extra demands changes *}
<script type="text/javascript">
	$(document).ready(function() {
		// modalbox for extra bed requirement
		$('body').on('click', '.open_rooms_extra_demands', function() {
			var idProduct = $(this).attr('id_product');
			var idCart = $(this).attr('id_cart');
			var idRoom = $(this).attr('id_room');
			var dateFrom = $(this).attr('date_from');
			var dateTo = $(this).attr('date_to');
			$.ajax({
				type: 'POST',
				headers: {
					"cache-control": "no-cache"
				},
				url: "{$link->getAdminLink('AdminOrders')|addslashes}",
				cache: false,
				data: {
					date_from: dateFrom,
					date_to: dateTo,
					id_room: idRoom,
					id_cart: idCart,
					id_product: idProduct,
					action: 'getRoomTypeCartDemands',
					ajax: true
				},
				success: function(result) {
					$('#rooms_type_extra_demands').find('#room_type_demands_desc').html('');
					$('#rooms_type_extra_demands').find('#room_type_demands_desc').append(result);
					$('#rooms_type_extra_demands').modal('show');
				},
			});
		});
		$('#rooms_type_extra_demands').on('hidden.bs.modal', function (e) {
			// reload so that changes prices will reflect everywhere
			location.reload();
		});

		// select/unselect extra demand
		$(document).on('click', '.id_room_type_demand', function() {
			var roomDemands = [];
			// get the selected extra demands by customer
			$(this).closest('.room_demand_detail').find('input:checkbox.id_room_type_demand:checked').each(function () {
				roomDemands.push({
					'id_global_demand':$(this).val(),
					'id_option': $(this).closest('.room_demand_block').find('.id_option').val()
				});
			});
			var idBookingCart = $(this).attr('id_cart_booking');
			$.ajax({
				type: 'POST',
				headers: {
					"cache-control": "no-cache"
				},
				url: "{$link->getAdminLink('AdminOrders')|addslashes}",
				dataType: 'JSON',
				cache: false,
				data: {
					id_cart_booking: idBookingCart,
					room_demands: JSON.stringify(roomDemands),
					action: 'changeRoomDemands',
					ajax: true
				},
				success: function(result) {
					if (result == 1) {
						showSuccessMessage(txtExtraDemandSucc);
					} else {
						showErrorMessage(txtExtraDemandErr);
					}
				}
			});
		});

		// change advance option of extra demand
		$(document).on('change', '.demand_adv_option_block .id_option', function(e) {
			var option_selected = $(this).find('option:selected');
			var extra_demand_price = option_selected.attr("optionPrice")
			extra_demand_price = parseFloat(extra_demand_price);
			extra_demand_price = formatCurrency(extra_demand_price, currency_format, currency_sign, currency_blank);
			$(this).closest('.room_demand_block').find('.extra_demand_option_price').text(extra_demand_price);
			var roomDemands = [];
			// get the selected extra demands by customer
			$(this).closest('.room_demand_detail').find('input:checkbox.id_room_type_demand:checked').each(function () {
				roomDemands.push({
					'id_global_demand':$(this).val(),
					'id_option': $(this).closest('.room_demand_block').find('.id_option').val()
				});
			});
			var idBookingCart = $(this).closest('.room_demand_block').find('.id_room_type_demand').attr('id_cart_booking');
			$.ajax({
				type: 'POST',
				headers: {
					"cache-control": "no-cache"
				},
				url: "{$link->getAdminLink('AdminOrders')|addslashes}",
				dataType: 'JSON',
				cache: false,
				data: {
					id_cart_booking: idBookingCart,
					room_demands: JSON.stringify(roomDemands),
					action: 'changeRoomDemands',
					ajax: true
				},
				success: function(result) {
					if (result == 1) {
						showSuccessMessage(txtExtraDemandSucc);
					} else {
						showErrorMessage(txtExtraDemandErr);
					}
				}
			});
		});
	});
</script>