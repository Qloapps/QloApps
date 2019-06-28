{if isset($advance_payment_active)}
	<div class="opc_advance_payment_block">
		<p class="block-small-header">{l s='PAYMENT TYPES'}</p>
		<div class="row adv_payment_type_form">
			<form method="POST" action="{$link->getPageLink('order-opc')|escape:'html':'UTF-8'}" id="advanced-payment">
				<div class="col-sm-12 col-xs-12">
					<label>
						<input type="radio" value="1" name="payment_type" class="payment_type" {if !isset($customer_adv_dtl)}checked="checked"{/if}>
						<span>{l s='Full Payment'}</span>
					</label>
				</div>
				<div class="col-sm-12 col-xs-12">
					<label>
						<input type="radio" value="2" name="payment_type" class="payment_type" {if isset($customer_adv_dtl)}checked="checked"{/if}>
						<span>{l s='Partial Payment'}</span>
					</label>

					{if isset($customer_adv_dtl)}
						<input type="hidden" value="{$customer_adv_dtl['id']}" name="id_customer_adv">
					{/if}
					{if isset($customer_adv_product_dtl)}
						<input type="hidden" value="{$customer_product_adv_dtl['id']}" name="id_customer_product_adv">
					{/if}

					<div class="row" id="partial_data">
						<div class="row margin-lr-0">
							<div class="col-xs-12 partial_subcont">
								<span class="partial_txt">{l s='Advance Payment Amount'} - </span>
								<span class="partial_min_cost">{displayPrice price=$adv_amount}</span>
							</div>
						</div>

						{if isset($customer_adv_dtl)}
							<div class="row margin-lr-0">
								<div class="col-xs-12 partial_subcont">
									<span class="partial_txt">{l s='Due Amount'} - </span>
									<span class="partial_min_cost">{displayPrice price=$customer_adv_dtl['due_amount']}</span>
								</div>
							</div>
						{/if}
					</div>
				</div>
				<div class="col-sm-12 col-xs-12 margin-top-10">
					<button class="opc-button-small opc-btn-primary" name="submitAdvPayment" type="submit">
						<span>{l s='OK'}</span>
					</button>
				</div>
			</form>
		</div>
	</div>
{/if}