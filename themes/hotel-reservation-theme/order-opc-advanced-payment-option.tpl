{if isset($advance_payment_active)}
	<div class="opc_advance_payment_block">
		<p class="block-small-header">{l s='PAYMENT TYPES'}</p>
		<div class="row adv_payment_type_form">
			<form method="POST" action="{$link->getPageLink('order-opc')|escape:'html':'UTF-8'}" id="advanced-payment">
				<div class="col-sm-12 col-xs-12">
					<label>
						<input type="radio" value="1" name="payment_type" class="payment_type" {if !isset($is_advance_payment)}checked="checked"{/if}>
						<span>{l s='Full Payment'}</span>
					</label>
				</div>
				<div class="col-sm-12 col-xs-12">
					<label>
						<input type="radio" value="2" name="payment_type" class="payment_type" {if isset($is_advance_payment)}checked="checked"{/if}>
						<span>{l s='Partial Payment'}</span>
					</label>

					<div class="row" id="partial_data">
						<div class="row margin-lr-0">
							<div class="col-xs-12 partial_subcont">
								<span class="partial_txt">{l s='Advance Payment Amount'} - </span>
								<span class="partial_min_cost">{displayPrice price=$advPaymentAmount}</span>
							</div>
						</div>

						{if isset($is_advance_payment)}
							<div class="row margin-lr-0">
								<div class="col-xs-12 partial_subcont">
									<span class="partial_txt">{l s='Due Amount'} - </span>
									<span class="partial_min_cost">{displayPrice price=$dueAmount}</span>
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