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
							<th><span class="title_box">{l s='Unit Price'}</span></th>
							<th><span class="title_box">{l s='Price'}</span></th>
							<th><span class="title_box">{l s='Action'}</span></th>
						</tr>
					</thead>
					<tbody>
					{if isset($cart_detail_data) && $cart_detail_data}
						{assign var=curr_id value=$cart->id_currency|intval}
						{foreach from=$cart_detail_data item=data}
							<tr>
								<td>{$data.room_num}</td>
								<td><img src="{$data.image_link}" title="Room image" /></td>
								<td>{$data.room_type}</td>
								<td>{$data.date_from|date_format:"%d-%m-%Y"}&nbsp-&nbsp {$data.date_to|date_format:"%d-%m-%Y"}</td>
								<td id="cart_detail_data_unit_price_{$data.id}">
									<span class="product_original_price {if $data.feature_price_diff > 0}room_type_old_price{/if}" {if $data.feature_price_diff < 0} style="display:none;"{/if}>
			                        	{displayPrice price=$data.product_price}
									</span>&nbsp;
			                        <span class="room_type_current_price" {if !$data.feature_price_diff}style="display:none;"{/if}>	
										{displayPrice price=$data.feature_price}
			                        </span>
								</td>
								<td id="cart_detail_data_price_{$data.id}">{displayPrice price=$data.amt_with_qty}</td>
								<td>
									<button class="btn btn-primary delete_hotel_cart_data" data-id_room={$data.id_room} data-id_product={$data.id_product} data-id = {$data.id} data-id_cart = {$data.id_cart} data-date_to = {$data.date_to} data-date_from = {$data.date_from}><i class="icon-trash"></i>&nbsp{l s='Delete'}</button>
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

	<style type="text/css">
		.room_type_old_price {
			  text-decoration: line-through;
			  color:#979797;
			  font-size:12px; 
			}
	</style>