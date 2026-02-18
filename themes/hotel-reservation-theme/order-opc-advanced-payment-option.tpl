
{if isset($advance_payment_active)}
	<div class="opc_advance_payment_block">
		{block name='order_opc_advanced_payment_option_form'}
			<form method="POST" action="{$link->getPageLink('order-opc')|escape:'html':'UTF-8'}" id="advanced-payment">
				<div class="row adv_payment_type_form">
					{* FULL PAYMENT *}
					<div class="col-md-6 col-12">
						<label class="qlo-card payment-option {if !isset($is_advance_payment)}border-active{/if}">
							<div class="option-content-primary">
								<input type="radio" name="payment_type" value="1" {if !isset($is_advance_payment)}checked{/if}>
								<span class="option-title ml-2">{l s='Full Payment'}</span>
							</div>
							<div class="option-content-secondary">
								<div class="option-price">{displayPrice price=$total_price}</div>
								<div class="option-sub h10">{l s='Nothing to pay at property'}</div>
							</div>
						</label>
					</div>
					{* PARTIAL PAYMENT *}
					<div class="col-md-6 col-12">
						<label class="qlo-card payment-option {if isset($is_advance_payment)}border-active{/if}">
							<div class="option-content-primary">
								<input type="radio" name="payment_type" value="2" {if isset($is_advance_payment)}checked{/if}>
								<span class="option-title ml-2">{l s='Pay only '}<span class="payment-badge">{displayPrice price=$advPaymentAmount}</span>{l s=' for Booking'}</span>
							</div>
							<div class="option-content-secondary">
								<div class="option-price">{displayPrice price=$advPaymentAmount}</div>
								<div class="option-sub h10">{l s='Rest after Accommodation'}</div>
							</div>
						</label>
					</div>
				</div>
				<div class="row">
					{block name='order_opc_advanced_payment_option_submit'}
						<div class="col-12 mb-3">
							<button class="btn btn-primary pull-right" name="submitAdvPayment" type="submit"><span>{l s='OK'}</span></button>
						</div>
					{/block}
				</div>
			</form>
		{/block}
	</div>
{/if}
