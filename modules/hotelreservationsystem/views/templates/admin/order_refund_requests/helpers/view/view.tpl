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
									<strong>{l s='Customer Name' mod='hotelreservationsystem'} :</strong>
								</div>
								<div class="col-sm-9">
									{$customer_name|escape:'html':'UTF-8'}
								</div>
							</div>
							<div class="form-group">
								<div class="col-sm-3">
									<strong>{l s='Customer Email' mod='hotelreservationsystem'} :</strong>
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

				<div class="row panel">
					<div class="col-sm-12">
						<h3><i class="icon-info-circle"></i> &nbsp;{l s="Booking Details" mod="hotelreservationsystem"}</h3>
					</div>

					<input type="hidden" name="id_order_return" value="{$orderReturnInfo['id']|escape:'html':'UTF-8'}">

					<div class="col-sm-12">
						<div class="form-group row">
							<div class="col-sm-3">
								<strong>{l s='Total Order Amount' mod='hotelreservationsystem'} :</strong>
							</div>
							<div class="col-sm-9">
								{displayPrice price=$orderInfo['total_paid_tax_incl'] currency=$orderInfo['id_currency']}
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
									<table class="table">
										<tr>
											{if !$isRefundCompleted}
												<th></th>
											{/if}
											<th>{l s='Room num' mod='hotelreservationsystem'}</th>
											<th>{l s='Room type' mod='hotelreservationsystem'}</th>
											<th>{l s='Hotel' mod='hotelreservationsystem'}</th>
											<th>{l s='Duration' mod='hotelreservationsystem'}</th>
											<th>{l s='Total rooms paid (tax incl.)'}</th>
											<th>{l s='Additional facilities price (tax incl.)'}</th>
											{if !$isRefundCompleted}
												<th>{l s='Rooms cancelation charges' mod='hotelreservationsystem'}</th>
											{/if}
											{if $hasOrderPaid}
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
												<td>{displayPrice price=$booking['total_paid_amount'] currency=$orderCurrency['id']}</td>
												<td>{displayPrice price=$booking['extra_demands_price_tax_incl'] currency=$orderCurrency['id']}</td>
												{if !$isRefundCompleted}
													<td>{displayPrice price=$booking['cancelation_charge'] currency=$orderCurrency['id']}</td>
												{/if}
												{if $hasOrderPaid}
													<td>
														<div class="input-group">
															{if $isRefundCompleted}
																{displayPrice price=$booking['refunded_amount'] currency=$orderCurrency['id']}
															{else}
																<span class="input-group-addon">{$orderCurrency['sign']|escape:'html':'UTF-8'}</span>
																<input placeholder="" type="text" name="refund_amounts[{$booking['id_order_return_detail']|escape:'html':'UTF-8'}]">
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
										<strong>{l s='Payment Mode' mod='hotelreservationsystem'} :</strong>
									</div>
									<div class="col-sm-9">
										{$orderReturnInfo['payment_mode']|escape:'html':'UTF-8'}
									</div>
								</div>
								<div class="form-group row">
									<div class="col-sm-3">
										<strong>{l s='Transaction Id' mod='hotelreservationsystem'} :</strong>
									</div>
									<div class="col-sm-9">
										{$orderReturnInfo['id_transaction']|escape:'html':'UTF-8'}
									</div>
								</div>
								<div class="form-group row">
									<div class="col-sm-3">
										<strong>{l s='Refunded Amount' mod='hotelreservationsystem'} :</strong>
									</div>
									<div class="col-sm-9">
										{displayPrice price=$orderReturnInfo['refunded_amount'] currency=$orderInfo['id_currency']}
									</div>
								</div>
							{/if}
						{else}
							<div class="form-group">
								<div class="col-sm-3">
									<label for="id_refund_state" class="control-label">
										<span title="" data-toggle="tooltip" class="label-tooltip" data-original-title="{l s='Select new state for refund request.' mod='hotelreservationsystem'}"> {l s='Room Refund State' mod='hotelreservationsystem'}</span> :
									</label>
								</div>
								<div class="col-sm-3">
									<select id="id_refund_state" name="id_refund_state">
										{foreach from=$refundStatuses item=state}
											<option refunded="{$state['refunded']|escape:'html':'UTF-8'}" denied="{$state['denied']|escape:'html':'UTF-8'}" value="{$state['id_order_return_state']|intval}"{if isset($currentStateInfo) && $state['id_order_return_state'] == $currentStateInfo['id']} selected="selected" disabled="disabled"{/if}>{$state['name']|escape:'html':'UTF-8'}</option>
										{/foreach}
									</select>
								</div>
							</div>

							{* Fields to submit refund information *}
							{if $hasOrderPaid}
								<div class="refunded_state_fields" style="display:none;">
									<div class="form-group">
										<div class="col-sm-3">
										</div>
										<div class="col-sm-3">
											<div class="checkbox">
												<label>
													<input value="1" type="checkbox" name="generateCreditSlip" id="generateCreditSlip"/> &nbsp;{l s='Create Refund Slip' mod='hotelreservationsystem'}
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
														<span title="" data-toggle="tooltip" class="label-tooltip" data-original-title="{l s='Select the method of payment through which you have refunded to the customer.' mod='hotelreservationsystem'}">{l s='Payment Method' mod="hotelreservationsystem"}</span> :
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
												<label for="other_payment_mode" class="control-label">
													<span title="" data-toggle="tooltip" class="label-tooltip" data-original-title="{l s='Enter the mode of payment through which you have refunded to the customer.' mod='hotelreservationsystem'}">{l s='Payment Mode Name' mod="hotelreservationsystem"}</span> :
												</label>
											</div>
											<div class="col-sm-3">
												<input type="text" name="other_payment_mode">
											</div>
										</div>
										<div class="form-group">
											<div class="col-sm-3">
												<label for="id_transaction" class="control-label">
													<span title="" data-toggle="tooltip" class="label-tooltip" data-original-title="{l s='Enter the Transaction Id of the payment through which you have refunded to the customer.' mod='hotelreservationsystem'}">{l s='Transaction Id' mod='hotelreservationsystem'}</span> :
												</label>
											</div>
											<div class="col-sm-3">
												<input type="text" name="id_transaction">
											</div>
										</div>
									</div>
									{if $orderReturnInfo['by_admin']}
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
										{* <div class="form-group">
											<div class="col-sm-3">
											</div>
											<div class="col-sm-3">
												<div class="checkbox">
													<label>
														<input value="1" type="checkbox" name="disable_date_range"/> &nbsp;{l s='Disable For Date Range' mod='hotelreservationsystem'}
													</label>
												</div>
											</div>
										</div> *}
										{* <div class="form-group">
											<div class="col-sm-3">
											</div>
											<div class="col-sm-3">
												<div class="checkbox">
													<label>
														<input value="1" type="checkbox" name="unavailable_for_order"/> &nbsp;{l s='Unavailable For Order' mod='hotelreservationsystem'}
													</label>
												</div>
											</div> *}
										</div>
									{/if}
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
							<i class="process-icon-save"></i> {l s='Save and stay' mod='hotelreservationsystem'}
						</button>
					</div>
				{/if}
			</form>
		</div>
	</div>
</div>
