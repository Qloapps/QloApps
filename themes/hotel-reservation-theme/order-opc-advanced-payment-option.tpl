{if isset($advance_payment_active)}
	<div class="opc-main-block">
		<div class="row margin-lr-0">
			<div class="col-sm-12 col-xs-12 box">
				<h3 class="page-subheading">{l s='Payment Types'}</h3>
				<div class="row">
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

							<div class="row" id="partial_data">
								<div class="row margin-lr-0">
									<div class="col-xs-offset-2 col-xs-6 col-sm-offset-1 col-sm-5 partial_subcont">
										<span class="partial_txt">{l s='Currently Payment Amount'} - </span>
										<span class="partial_mim_cost">{displayPrice price=$adv_amount}</span>
									</div>
								</div>
								
								{if isset($customer_adv_dtl)}
									<div class="row margin-lr-0">
										<div class="col-xs-offset-2 col-xs-6 col-sm-offset-1 col-sm-5 partial_subcont">
											<span class="partial_txt">{l s='Due Amount'} - </span>
											<span class="partial_mim_cost">{displayPrice price=$customer_adv_dtl['due_amount']}</span>
										</div>
									</div>
								{/if}
							</div>
						</div>
						<div class="col-sm-12 col-xs-12 margin-top-10">
							<button class="btn btn-default" name="submitAdvPayment" type="submit">
								<span>{l s='OK'}</span>
							</button>
						</div>
					</form>
				</div>
			</div>
		</div>
	</div>
{/if}