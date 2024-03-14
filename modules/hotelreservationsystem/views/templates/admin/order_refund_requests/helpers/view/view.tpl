<div id="row">
	<div class="col-lg-12">
		<div class="panel">
			<div class="panel-heading">
				<i class="icon-info-circle"></i> &nbsp;{l s='Order Cancellation Request Information' mod='hotelreservationsystem'}
			</div>
			<br>
			<form id="{$table|escape:'htmlall':'UTF-8'}_form" class="defaultForm {$name_controller|escape:'htmlall':'UTF-8'} form-horizontal" action="{$current|escape:'htmlall':'UTF-8'}&{if !empty($submit_action)}{$submit_action|escape:'htmlall':'UTF-8'}{/if}&token={$token|escape:'htmlall':'UTF-8'}" method="post" enctype="multipart/form-data" {if isset($style)}style="{$style|escape:'htmlall':'UTF-8'}"{/if}>
				<div class="panel">
					{if isset($customer_name)}
						<div class="row">
							<div class="col-sm-12">
								<h3><i class="icon-info-circle"></i> &nbsp;{l s="Customer Details" mod="hotelreservationsystem"}</h3>
							</div>
							<div class="form-group">
								<div class="col-sm-3">
									<strong>{l s='Customer name' mod='hotelreservationsystem'} :</strong>
								</div>
								<div class="col-sm-9">
									{$customer_name|escape:'html':'UTF-8'}
								</div>
							</div>
							<div class="form-group">
								<div class="col-sm-3">
									<strong>{l s='Customer email' mod='hotelreservationsystem'} :</strong>
								</div>
								<div class="col-sm-9">
									<a target="_blank" href="{$link->getAdminLink('AdminCustomers')|escape:'htmlall':'UTF-8'}&amp;id_customer={$orderReturnInfo['id_customer']|escape:'html':'UTF-8'}&amp;viewcustomer">{$customer_email|escape:'html':'UTF-8'}</a>
								</div>
							</div>
						</div>
					{else}
						<p><strong>{l s='Customer' mod='hotelreservationsystem'} :  </strong>{l s='As a guest' mod='hotelreservationsystem'}</p>
					{/if}

					<div class="row">
						<div class="form-group">
							<div class="col-sm-3">
								<strong>{l s='Cancelation reason' mod='hotelreservationsystem'} :</strong>
							</div>
							<div class="col-sm-9">
								{if $orderReturnInfo['question']}
									{$orderReturnInfo['question']|escape:'html':'UTF-8'}
								{else}
									--
								{/if}
							</div>
						</div>
					</div>
				</div>

				<div class="panel">
					<div class="col-sm-12">
						<h3><i class="icon-info-circle"></i> &nbsp;{l s="Booking Details" mod="hotelreservationsystem"}</h3>
					</div>

					<input type="hidden" name="id_order_return" value="{$orderReturnInfo['id']|escape:'html':'UTF-8'}">

					<div class="form-wrapper">
						<div class="form-group row">
							<div class="col-sm-3">
								<strong>{l s='Total order amount' mod='hotelreservationsystem'} :</strong>
							</div>
							<div class="col-sm-9">
								{displayPrice price=$orderInfo['total_paid_tax_incl'] currency=$orderInfo['id_currency']}
							</div>
						</div>
						<div class="form-group row">
							<div class="col-sm-3">
								<strong>{l s='Total paid amount' mod='hotelreservationsystem'} :</strong>
							</div>
							<div class="col-sm-9">
								{displayPrice price=$orderTotalPaid currency=$orderInfo['id_currency']}
							</div>
						</div>
						<div class="form-group row">
							<div class="col-sm-3">
								<strong>{l s='Way of payment' mod='hotelreservationsystem'} :</strong>
							</div>
							<div class="col-sm-9">
								{if $orderInfo['is_advance_payment']}{l s='Advance Payment' mod='hotelreservationsystem'}{else}{l s='Full Payment' mod='hotelreservationsystem'}{/if}
							</div>
						</div>
						<div class="form-group row">
							<div class="col-sm-3">
								<strong>{l s='Current order state' mod='hotelreservationsystem'} :</strong>
							</div>
							<div class="col-sm-9">
								<span class="badge" style="background-color:{$currentOrderStateInfo['color']|escape:'html':'UTF-8'}">{$currentOrderStateInfo['name']|escape:'html':'UTF-8'}</span>
							</div>
						</div>
						<div class="form-group row">
							<div class="col-sm-3">
								<strong>{l s='Current refund state' mod='hotelreservationsystem'} :</strong>
							</div>
							<div class="col-sm-9">
								<span class="badge" style="background-color:{$currentStateInfo['color']|escape:'html':'UTF-8'}">{$currentStateInfo['name']|escape:'html':'UTF-8'}</span>
							</div>
						</div>

						{* list of booking requested for refund by the customer *}
						{if isset($refundReqBookings) && $refundReqBookings}
							<br>
							<div class="form-group">
								<div class="col-sm-12">
									<label for="id_refund_state" class="control-label">
										<p><span title="" data-toggle="tooltip" class="label-tooltip" data-original-title='{l s="List of booking requested for refund by the customer." mod="hotelreservationsystem"}'><strong>{l s="Bookings requested for refund" mod="hotelreservationsystem"}</strong></span> :
										</p>
									</label>
								</div>
								<div class="col-sm-12">
									<table class="table" id="rooms_refund_info">
										<tr>
											{if !$isRefundCompleted}
												<th></th>
											{/if}
											<th>{l s='Room num' mod='hotelreservationsystem'}</th>
											<th>{l s='Room type' mod='hotelreservationsystem'}</th>
											<th>{l s='Hotel' mod='hotelreservationsystem'}</th>
											<th>{l s='Duration' mod='hotelreservationsystem'}</th>
											<th>{l s='Total cost (tax incl.)'}</th>
											<th>{l s='Total paid amount (tax incl.)'}</th>
											{if !$isRefundCompleted}
												<th>{l s='Rooms cancelation charges' mod='hotelreservationsystem'}</th>
											{/if}
											{if $orderTotalPaid|floatval}
												<th>{l s='Refund amount' mod='hotelreservationsystem'}</th>
											{/if}
										</tr>
										{foreach $refundReqBookings as $booking}
											<tr>
												{if !$isRefundCompleted}
													<td><input type="checkbox" name="id_order_return_detail[]" value="{$booking['id_order_return_detail']|escape:'html':'UTF-8'}" checked/></td>
												{/if}
												<td>{$booking['room_num']|escape:'htmlall':'UTF-8'}</td>
												<td>{$booking['room_type_name']|escape:'htmlall':'UTF-8'}</td>
												<td>{$booking['hotel_name']|escape:'htmlall':'UTF-8'}</td>
												<td>{$booking['date_from']|date_format:"%d-%m-%Y"} {l s='To' mod='hotelreservationsystem'} {$booking['date_to']|date_format:"%d-%m-%Y"}</td>
												<td>
													{displayPrice price=($booking['total_price_tax_incl'] + $booking['extra_service_total_price_tax_incl']) currency=$orderCurrency['id']}
													<span class="price_info">
														&nbsp;<img src="{$info_icon_path|escape:'htmlall':'UTF-8'}" />
													</span>
													<div class="price_info_container" style="display: none;">
														<div>
															<label>{l s='Room cost:'}</label>
															<span class="pull-right">{displayPrice price=$booking['total_price_tax_incl'] currency=$orderCurrency['id']}</span>
														</div>
														<div>
															<label>{l s='Services cost:'}</label>
															<span class="pull-right">{displayPrice price=$booking['extra_service_total_price_tax_incl'] currency=$orderCurrency['id']}</span>
														</div>
													</div>
												</td>
												<td>
													{displayPrice price=($booking['room_paid_amount'] + $booking['extra_service_total_paid_amount']) currency=$orderCurrency['id']}
													<span class="price_info">
														&nbsp;<img src="{$info_icon_path|escape:'htmlall':'UTF-8'}" />
													</span>
													<div class="price_info_container" style="display: none;">
														<div>
															<label>{l s='Room paid amount:'}</label>
															<span class="pull-right">{displayPrice price=$booking['room_paid_amount'] currency=$orderCurrency['id']}</span>
														</div>
														<div>
															<label>{l s='Services paid amount:'}</label>
															<span class="pull-right">{displayPrice price=$booking['extra_service_total_paid_amount'] currency=$orderCurrency['id']}</span>
														</div>
													</div>
												</td>
												{if !$isRefundCompleted}
													<td>
														{displayPrice price=$booking['cancelation_charge'] currency=$orderCurrency['id']}
														{if $booking['reduction_type'] == HotelOrderRefundRules::WK_REFUND_RULE_PAYMENT_TYPE_PERCENTAGE}
															<p class="help-block">{Tools::ps_round($booking['reduction_value'], 2)}{l s='% of'} {displayPrice price=($booking['total_price_tax_incl'] + $booking['extra_service_total_price_tax_incl']) currency=$orderCurrency['id']}</p>
														{/if}
													</td>
												{/if}
												{if $orderTotalPaid|floatval}
													<td>
														<div class="input-group">
															{if $isRefundCompleted}
																{displayPrice price=$booking['refunded_amount'] currency=$orderCurrency['id']}
															{else}
																<span class="input-group-addon">{$orderCurrency['sign']|escape:'html':'UTF-8'}</span>
																<input placeholder="" type="text" name="refund_amounts[{$booking['id_order_return_detail']|escape:'html':'UTF-8'}]" value="{if ($booking['room_paid_amount'] + $booking['extra_service_total_paid_amount'] - $booking['cancelation_charge']) > 0}{Tools::ps_round(($booking['room_paid_amount'] + $booking['extra_service_total_paid_amount'] - $booking['cancelation_charge']), 2)}{else}0{/if}">
																<span class="input-group-addon">{l s='tax incl.' mod='hotelreservationsystem'}</span>
															{/if}
														</div>
													</td>
												{/if}
											</tr>
										{/foreach}
									</table>
								</div>
							</div>
						{/if}

						<br>

						{if $isRefundCompleted}
							{if $currentStateInfo['refunded']}
								<div class="form-group row">
									<div class="col-sm-3">
										<strong>{l s='Refunded amount:' mod='hotelreservationsystem'}</strong>
									</div>
									<div class="col-sm-9">
										{displayPrice price=$orderReturnInfo['refunded_amount'] currency=$orderInfo['id_currency']}
									</div>
								</div>

								{if $orderReturnInfo['payment_mode'] != '' && $orderReturnInfo['id_transaction'] != ''}
									<div class="form-group row">
										<div class="col-sm-3">
											<strong>{l s='Payment mode:' mod='hotelreservationsystem'}</strong>
										</div>
										<div class="col-sm-9">
											{$orderReturnInfo['payment_mode']|escape:'html':'UTF-8'}
										</div>
									</div>
									<div class="form-group row">
										<div class="col-sm-3">
											<strong>{l s='Transaction ID:' mod='hotelreservationsystem'}</strong>
										</div>
										<div class="col-sm-9">
											{$orderReturnInfo['id_transaction']|escape:'html':'UTF-8'}
										</div>
									</div>
								{/if}

								{if isset($orderReturnInfo['return_type'])}
									{if $orderReturnInfo['return_type'] == OrderReturn::RETURN_TYPE_CART_RULE}
										<div class="form-group row">
											<div class="col-sm-3">
												<strong>{l s='Voucher ID:' mod='hotelreservationsystem'}</strong>
											</div>
											<div class="col-sm-9">
												<a href="{$link->getAdminLink('AdminCartRules')}&updatecart_rule&id_cart_rule={$orderReturnInfo['id_return_type']}" target="_blank">#{$orderReturnInfo['id_return_type']}</a>
											</div>
										</div>
									{elseif $orderReturnInfo['return_type'] == OrderReturn::RETURN_TYPE_ORDER_SLIP}
										<div class="form-group row">
											<div class="col-sm-3">
												<strong>{l s='Credit Slip ID:' mod='hotelreservationsystem'}</strong>
											</div>
											<div class="col-sm-9">
												#{$orderReturnInfo['id_return_type']}
												<a class="btn btn-default" href="{$link->getAdminLink('AdminPdf')}&submitAction=generateOrderSlipPDF&id_order_slip={$orderReturnInfo['id_return_type']}" title="#{Configuration::get('PS_CREDIT_SLIP_PREFIX', $current_id_lang)}{$orderReturnInfo['id_return_type']|string_format:'%06d'}">
													<i class="icon-file-text"></i>
													{l s='Download' mod='hotelreservationsystem'}
												</a>
											</div>
										</div>
									{/if}
								{/if}
							{/if}
						{else}
							<div class="form-group">
								<div class="col-sm-3">
									<label for="id_refund_state" class="control-label">
										<span title="" data-toggle="tooltip" class="label-tooltip" data-original-title="{l s='Select new state for refund request.' mod='hotelreservationsystem'}"> {l s='Room refund state' mod='hotelreservationsystem'}</span> :
									</label>
								</div>
								<div class="col-sm-3">
									<select id="id_refund_state" name="id_refund_state">
										{foreach from=$refundStatuses item=state}
											<option refunded="{$state['refunded']|escape:'html':'UTF-8'}" denied="{$state['denied']|escape:'html':'UTF-8'}" value="{$state['id_order_return_state']|intval}"{if isset($currentStateInfo) && $state['id_order_return_state'] == $currentStateInfo['id']} selected="selected"{/if}>{$state['name']|escape:'html':'UTF-8'}</option>
										{/foreach}
									</select>
								</div>
							</div>

							{* Fields to submit refund information *}
							{if $orderTotalPaid|floatval}
								<div class="refunded_state_fields" style="display:none;">
									<div class="form-group">
										<div class="col-sm-3">
										</div>
										<div class="col-sm-3">
											<div class="checkbox">
												<label>
													<input value="1" type="checkbox" name="generateCreditSlip" id="generateCreditSlip"/> &nbsp;{l s='Create Credit Slip' mod='hotelreservationsystem'}
												</label>
											</div>
										</div>
									</div>
									<div class="form-group">
										<div class="col-sm-3">
										</div>
										<div class="col-sm-3">
											<div class="checkbox">
												<label>
													<input value="1" type="checkbox" name="refundTransactionAmount" id="refundTransactionAmount"/> &nbsp;{l s='Refund Transaction Amount' mod='hotelreservationsystem'}
												</label>
											</div>
										</div>
									</div>
									<div class="refund_transaction_fields" style="display:none;">
										{if isset($paymentMethods) && $paymentMethods|count}
											<div class="form-group">
												<div class="col-sm-3">
													<label for="payment_method" class="control-label">
														<span title="" data-toggle="tooltip" class="label-tooltip" data-original-title="{l s='Select the method of payment through which you have refunded to the customer.' mod='hotelreservationsystem'}">{l s='Payment method' mod="hotelreservationsystem"}</span> :
													</label>
												</div>
												<div class="col-sm-3">
													<select name="payment_method" id="payment_methods">
														{foreach $paymentMethods as $paymentMod}
															<option value="{$paymentMod|escape:'html':'UTF-8'}">{$paymentMod|escape:'html':'UTF-8'}</option>
														{/foreach}
														<option value="0">{l s='Others' mod="hotelreservationsystem"}</option>
													<select>
												</div>
											</div>
										{/if}
										<div class="form-group other_payment_mode" {if isset($paymentMethods) && $paymentMethods|count}style="display:none;"{/if}>
											<div class="col-sm-3">
												<label for="other_payment_mode" class="control-label required">
													<span title="" data-toggle="tooltip" class="label-tooltip" data-original-title="{l s='Enter the mode of payment through which you have refunded to the customer.' mod='hotelreservationsystem'}">{l s='Payment mode name' mod="hotelreservationsystem"}</span> :
												</label>
											</div>
											<div class="col-sm-3">
												<input type="text" name="other_payment_mode">
											</div>
										</div>
										<div class="form-group">
											<div class="col-sm-3">
												<label for="id_transaction" class="control-label required">
													<span title="" data-toggle="tooltip" class="label-tooltip" data-original-title="{l s='Enter the Transaction Id of the payment through which you have refunded to the customer.' mod='hotelreservationsystem'}">{l s='Transaction ID' mod='hotelreservationsystem'}</span> :
												</label>
											</div>
											<div class="col-sm-3">
												<input type="text" name="id_transaction">
											</div>
										</div>
									</div>
									<div class="form-group">
										<div class="col-sm-3">
										</div>
										<div class="col-sm-3">
											<div class="checkbox">
												<label>
													<input value="1" type="checkbox" name="generateDiscount" id="generateDiscount"/> &nbsp;{l s='Create Voucher' mod='hotelreservationsystem'}
												</label>
											</div>
										</div>
									</div>
								</div>
							{/if}
						{/if}
					</div>
				</div>

				{* footer panel only if refund is not at its final state *}
				{if !$isRefundCompleted}
					<div class="panel-footer">
						<a href="{$link->getAdminLink('AdminAddHotel')|escape:'html':'UTF-8'}" class="btn btn-default">
							<i class="process-icon-cancel"></i>{l s='Cancel' mod='hotelreservationsystem'}
						</a>
						<button type="submit" name="submitRefundReqBookings" class="btn btn-default pull-right">
							<i class="process-icon-save"></i> {l s='Save' mod='hotelreservationsystem'}
						</button>
						<button type="submit" name="submitRefundReqBookingsAndStay" class="btn btn-default pull-right">
							<i class="process-icon-save"></i> {l s='Save and stay' mod='hotelreservationsystem'}
						</button>
					</div>
				{/if}
			</form>
		</div>
	</div>
</div>