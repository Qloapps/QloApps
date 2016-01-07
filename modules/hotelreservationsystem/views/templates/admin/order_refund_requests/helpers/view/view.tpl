<div id="row">
	<div class="col-lg-12">
		<div class="panel">
			<div class="panel-heading">
				<i class="icon-info"></i> &nbsp;&nbsp;{l s='Order Cancellation Request Information' mod='hotelreservationsystem'}
			</div>
			<div class="panel-content">
				<div class="customer_details details-div">
					<h3>{l s="Customer Details" mod="hotelreservationsyatem"}</h3>
					{if isset($customer_name)}
						
						<p><strong>{l s='Customer Name' mod='hotelreservationsystem'} :  </strong>&nbsp;&nbsp;{$customer_name|escape:'html':'UTF-8'}</p>
						
						<p><strong>{l s='Customer Email' mod='hotelreservationsystem'} :  </strong>&nbsp;&nbsp;{$customer_email|escape:'html':'UTF-8'}</p>
					{else}
						<p><strong>{l s='Customer' mod='hotelreservationsystem'} :  </strong>{l s='As a guest' mod='hotelreservationsystem'}</p>
					{/if}
				</div>
				<hr>
				<div class="order_cancellation_details details-div row">
					<h3>{l s="Order Cancellation Details" mod="hotelreservationsyatem"}</h3>
					<div class="col-lg-6">
						
						<p><strong>{l s='Hotel Name' mod='hotelreservationsystem'} :  </strong>&nbsp;&nbsp;{$htl_name|escape:'html':'UTF-8'}</p>
						
						<p><strong>{l s='Room Type' mod='hotelreservationsystem'} :  </strong>&nbsp;&nbsp;{$product_name|escape:'html':'UTF-8'}</p>
						
						<p><strong>{l s='Room Numbers' mod='hotelreservationsystem'} :  </strong>&nbsp;&nbsp;						{foreach from=$room_numbers item=rm_name}	
							{$rm_name|escape:'html':'UTF-8'},&nbsp;
						{/foreach}	
						</p>
						
						<p><strong>{l s='Date From' mod='hotelreservationsystem'} :  </strong>&nbsp;&nbsp;{$date_from|date_format:"%d-%b-%G"}</p>

						<p><strong>{l s='Date To' mod='hotelreservationsystem'} :  </strong>&nbsp;&nbsp;{$date_to|date_format:"%d-%b-%G"}</p>

					</div>
					<div class="col-lg-6">
						<p><strong>{l s='Total Amount' mod='hotelreservationsystem'} :  </strong>&nbsp;&nbsp;{$total_amount}&nbsp;{$curr_code}</p>

						
						<p><strong>{l s='Order Cancellation Stage' mod='hotelreservationsystem'} :  </strong>&nbsp;&nbsp;{$currentStage->name|escape:'html':'UTF-8'}</p>

						{if isset($currentStage->name) && $currentStage->name == 'Refunded'}
							{if $way_of_payment == 'Advance Payment'}
								{assign var="cancel_charge" value=$adv_paid_amount-$refunded_amount}
							{else}
								{assign var="cancel_charge" value=$total_amount-$refunded_amount}
							{/if}
							<p><strong>{l s='Total Cancellation Charges' mod='hotelreservationsystem'} :  </strong>&nbsp;&nbsp;{$cancel_charge|round:"2"}&nbsp;{$curr_code}</p>
							<p><strong>{l s='Total Refund Amount' mod='hotelreservationsystem'} :  </strong>&nbsp;&nbsp;{$refunded_amount|round:"2"}&nbsp;{$curr_code}</p>
						{/if}

						<p><strong>{l s='Way Of Payment' mod='hotelreservationsystem'} :  </strong>&nbsp;&nbsp;{$way_of_payment|escape:'html':'UTF-8'}</p>
						{if $way_of_payment == 'Advance Payment'}
							<p><strong>{l s='Advance Paid Amount' mod='hotelreservationsystem'} :  </strong>&nbsp;&nbsp;{$adv_paid_amount}&nbsp;{$curr_code}</p>
						{/if}
					</div>
				</div>

				<hr>

				<!-- Change order cancellation stage form -->
				{if $currentStage->name != 'Refunded' && $currentStage->name != 'Rejected'}
					<div class="new_stage_change details-div">
						<h3>{l s="Change Order Cancellation Status" mod="hotelreservationsyatem"}</h3>
						<form action="" method="post" class="form-horizontal well hidden-print">
							<div class="row">

							<p><strong>{l s='Total Cancellation Charges' mod='hotelreservationsystem'} :  </strong>&nbsp;&nbsp;{$deduction_amount|round:"2"}&nbsp;{$curr_code}</p>

							{if $way_of_payment == 'Advance Payment'}
								{assign var="refund_amount" value=$adv_paid_amount-$deduction_amount}
							{else}
								{assign var="refund_amount" value=$total_amount-$deduction_amount}
							{/if}
							<p><strong>{l s='Total Refund Amount' mod='hotelreservationsystem'} :  </strong>&nbsp;&nbsp;{$refund_amount|round:"2"}&nbsp;{$curr_code}</p>
							
							<hr>
								<div class="form-group">
									<label for="id_order_cancellation_stage" class="required control-label col-lg-2">
										<span title="" data-toggle="tooltip" class="label-tooltip" data-original-title='{l s="Select New Stage of order cancellation." mod="hotelreservationsyatem"}'>{l s="Select New Stage" mod="hotelreservationsystem"}</span>
									</label>
									<div class="col-lg-8">
										<div class="row">
											<div class="col-lg-3">
												<select id="id_order_cancellation_stage" name="id_order_cancellation_stage">
													{foreach from=$all_ord_refund_stages item=stage}
														<option value="{$stage['id']|intval}"{if isset($currentStage) && $stage['id'] == $currentStage->id} selected="selected" disabled="disabled"{/if}>{$stage['name']|escape}</option>
													{/foreach}
												</select>
											</div>
										</div>
									</div>
								</div>
								<div class="form-group cancellation_charge_div">
									<label for="cancellation_charge" class="control-label col-lg-2">
										<span title="" data-toggle="tooltip" class="label-tooltip" data-original-title="Enter How much Amount you want that will be paid by this customer as cancellation charges">{l s='Cancellation Charges' mod="hotelreservationsyatem"}</span>
									</label>
									<div class="col-lg-2">
										<div class="input-group col-lg-12">
											<input type="text" id="cancellation_charge" name="cancellation_charge">
										</div>
									</div>
								</div>
								<!-- <div class="form-group">
									<label for="refund_amount" class="required control-label col-lg-2">
										<span title="" data-toggle="tooltip" class="label-tooltip" data-original-title="">{l s='Refund Amount' mod="hotelreservationsyatem"}</span>
									</label>
									<div class="col-lg-2">
										<div class="input-group">
											<input type="text" id="refund_amount" name="refund_amount">
										</div>
									</div>
								</div> -->
								<div class="col-lg-4">
									<button type="submit" name="submitOrderCancelStage" class="btn btn-primary pull-right">
										{l s='Update Stage' mod="hotelreservationsystem"}
									</button>
								</div>
							</div>
						</form>
					</div>
				{/if}
				<hr>
			</div>
		</div>
	</div>
</div>
