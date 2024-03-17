<div class="modal-dialog" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close margin-right-10" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
				</button>
				<h4 class="modal-title" id="myModalLabel"><i class="icon-shopping-cart"></i>&nbsp; {l s='Cart Options' mod='hotelreservationsystem'}</h4>
			</div>
			<div class="modal-body">
				<div class="row margin-lr-0">
					<div class="cart_table_container">
                        {if isset($cart_bdata)}
							<table class="table table-responsive addtocart-table">
								<thead class="cart-table-thead">
									<tr>
										<th class="text-center">{l s='Room No.' mod='hotelreservationsystem'}</th>
										<th class="text-center">{l s='Room Type' mod='hotelreservationsystem'}</th>
										<th class="text-center">{l s='Duration' mod='hotelreservationsystem'}</th>
										<th class="text-center">{l s='Amount (Tax excl.)' mod='hotelreservationsystem'}</th>
										<th></th>
									</tr>
								</thead>
								<tbody class="cart_tbody">
								{if isset($cart_bdata)}
									{foreach $cart_bdata as $cart_data}
										<tr>
											<td class="text-center">{$cart_data['room_num']|escape:'htmlall':'UTF-8'}</td>
											<td class="text-center">{$cart_data['room_type']|escape:'htmlall':'UTF-8'}</td>
											<td class="text-center">{dateFormat date=$cart_data['date_from']} - {dateFormat date=$cart_data['date_to']}</td>
											<td class="text-center">{convertPrice price=($cart_data['amt_with_qty'] + $cart_data['additional_services_auto_add_with_room_price'] + $cart_data['additional_service_price'] + $cart_data['demand_price'])}</td>
											<td class="text-center"><button class="btn btn-default ajax_cart_delete_data" data-id-product="{$cart_data['id_product']|escape:'htmlall':'UTF-8'}" data-id-hotel="{$cart_data['id_hotel']|escape:'htmlall':'UTF-8'}" data-id-cart="{$cart_data['id_cart']|escape:'htmlall':'UTF-8'}" data-id-cart-book-data="{$cart_data['id_cart_book_data']|escape:'htmlall':'UTF-8'}" data-date-from="{$cart_data['date_from']|escape:'htmlall':'UTF-8'}" data-date-to="{$cart_data['date_to']|escape:'htmlall':'UTF-8'}"><i class='icon-trash'></i></button></td>
										</tr>
									{/foreach}
								{/if}
								</tbody>
							</table>
						{/if}
						{if isset($cart_normal_data)}
                            <table class="table table-responsive addtocart-table">
                                <thead class="cart-table-thead">
                                    <tr>
                                        <th>{l s='Id' mod='hotelreservationsystem'}</th>
                                        <th>{l s='Name' mod='hotelreservationsystem'}</th>
                                        <th>{l s='Hotel' mod='hotelreservationsystem'}</th>
                                        <th>{l s='Quantity' mod='hotelreservationsystem'}</th>
                                        <th>{l s='Amount (Tax excl.)' mod='hotelreservationsystem'}</th>
                                        <th></th>
                                    </tr>
                                    <tbody class="cart_tbody">
                                        {if isset($cart_normal_data)}
                                            {foreach $cart_normal_data as $cart_data}
                                                <tr>
                                                    <td>{$cart_data['id_product']|escape:'htmlall':'UTF-8'}</td>
                                                    <td>{$cart_data['name']|escape:'htmlall':'UTF-8'}</td>
                                                    <td>{$cart_data['hotel_name']|escape:'htmlall':'UTF-8'}</td>
                                                    <td>{$cart_data['quantity']|escape:'htmlall':'UTF-8'}</td>
                                                    <td>{convertPrice price=$cart_data['total_price_tax_excl']}</td>
                                                    <td><button class="btn btn-default service_product_delete" data-id-hotel="{$cart_data['id_hotel']|escape:'htmlall':'UTF-8'}" data-id-product="{$cart_data['id_product']|escape:'htmlall':'UTF-8'}" data-id-cart="{$id_cart|escape:'htmlall':'UTF-8'}"><i class='icon-trash'></i></button></td>
                                                </tr>
                                            {/foreach}
                                        {/if}
                                    </tbody>

                                </thead>
                            </table>
                        {/if}

					</div>
					<div class="row cart_amt_div">
						<table class="table table-responsive">
							<tr>
								<th colspan="2">{l s='Total Amount (Tax excl.):' mod='hotelreservationsystem'}</th>
								<th colspan="2" class="text-right" id="cart_total_amt">
									{if isset($cart_tamount)}{convertPrice price=$cart_tamount}{else}{convertPrice price=0}{/if}
								</th>
								<th colspan="1"></th>
							</tr>
						</table>
					</div>
				</div>
			</div>
			<div class="modal-footer">
				<a href="{$link->getAdminLink('AdminOrders')}&amp;addorder&amp;cart_id={$id_cart|escape:'htmlall':'UTF-8'|intval}" class="btn btn-primary cart_booking_btn" {if !$total_products_in_cart}disabled="disabled"{/if}>
					{l s='Book Now' mod='hotelreservationsystem'}
				</a>
				<button type="button" class="btn btn-default" data-dismiss="modal">{l s='Close' mod='hotelreservationsystem'}</button>
			</div>
		</div>
	</div>