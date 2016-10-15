{**
* 2010-2016 Webkul.
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
*  @copyright 2010-2016 Webkul IN
*  @license   https://store.webkul.com/license.html
*}

{if isset($paypal_transaction) && $paypal_transaction}
<div class="panel">
	<div class="panel-heading">
		<i class="icon-paypal"></i>
		{l s='Paypal Transaction details' mod='wkpaypaladaptive'}
	</div>
	<table class="table">
		<tr>
			<td>{l s='Pay Key' mod='wkpaypaladaptive'}</td>
			<td>{$paypal_transaction.pay_key|escape:'htmlall':'UTF-8'}</td>
		</tr>
		<tr>
			<td>{l s='Status' mod='wkpaypaladaptive'}</td>
			<td>{$paypal_transaction.status|escape:'htmlall':'UTF-8'}</td>
		</tr>
		<tr>
			<td>{l s='Sender Email' mod='wkpaypaladaptive'}</td>
			<td>{$paypal_transaction.sender_email|escape:'htmlall':'UTF-8'}</td>
		</tr>
		<tr>
			<td>{l s='Currency Code' mod='wkpaypaladaptive'}</td>
			<td>{$paypal_transaction.currency_code|escape:'htmlall':'UTF-8'}</td>
		</tr>
		<tr>
			<td>{l s='Payment Information' mod='wkpaypaladaptive'}</td>
			<td>
				{if $paypal_transaction.payment_info}
					{foreach from=$paypal_transaction.payment_info item=payment key=key}
						<p><strong>{$key+1} :</strong> {if isset($payment->transactionId) && $payment->transactionId}
							<strong>&nbsp;&nbsp;{l s='Transaction Id - ' mod='wkpaypaladaptive'}</strong>{$payment->transactionId},
						{/if}&nbsp;&nbsp;
						<strong>{l s='Reciever Email - ' mod='wkpaypaladaptive'}</strong>{$payment->receiver->email},&nbsp;&nbsp;
						<strong>{l s='Transaction Amount - ' mod='wkpaypaladaptive'}</strong>{$payment->receiver->amount},&nbsp;&nbsp;
						{if isset($payment->senderTransactionId) && $payment->senderTransactionId} <strong>{l s='Sender Transaction  Id - ' mod='wkpaypaladaptive'}</strong>{$payment->senderTransactionId},&nbsp;&nbsp;{/if}
						{if isset($payment->senderTransactionStatus) && $payment->senderTransactionStatus} <strong>{l s='Sender Transaction Status - ' mod='wkpaypaladaptive'}</strong>{$payment->senderTransactionStatus}{/if}
						</p>
					{/foreach}
				{else}
					{l s='No Transaction details available.' mod='wkpaypaladaptive'}
				{/if}
			</td>
		</tr>
		<tr>
			<td>{l s='Payment Method' mod='wkpaypaladaptive'}</td>
			<td>
				{l s='Parallel' mod='wkpaypaladaptive'}
				{if $paypal_transaction.action_type == 'PAY_PRIMARY'}
					{if $paypal_transaction.is_refunded == 1}
						<br />{l s='Transaction Refunded.' mod='wkpaypaladaptive'}
					{/if}
				{/if}
				{if $paypal_transaction.is_refunded == 0}
					<button id="paypalRefundBtn" type="button" class="btn btn-primary">
						<span>{l s='Refund' mod='wkpaypaladaptive'}</span>
					</button>
					<img src="{$module_dir}views/img/ajax-loader.gif" id="paypalRefundImg" style="display: none;">
				{/if}
			</td>
		</tr>
		{if $paypal_transaction.is_refunded == 1}
			<tr>
				<td>{l s='Refund Information' mod='wkpaypaladaptive'}</td>
				<td>
					{if isset($refundDetails)}
						{foreach from=$refundDetails item=refundPre key=key1}
							{foreach from=$refundPre.refund_details->refundInfo item=refund key=key}
								<p><strong>{$key+1} :</strong> {if isset($refund->encryptedRefundTransactionId) && $refund->encryptedRefundTransactionId}
									<strong>&nbsp;&nbsp;{l s='Transaction Id - ' mod='wkpaypaladaptive'}</strong>{$refund->encryptedRefundTransactionId},
								{/if}&nbsp;&nbsp;
								<strong>{l s='Reciever Email - ' mod='wkpaypaladaptive'}</strong>{$refund->receiver->email},&nbsp;&nbsp;
								<strong>{l s='Reciever Amount - ' mod='wkpaypaladaptive'}</strong>{$refund->receiver->amount},&nbsp;&nbsp;
								<strong>{l s='Transaction Amount - ' mod='wkpaypaladaptive'}</strong>{$refund->receiver->amount},&nbsp;&nbsp;{if isset($refund->refundNetAmount)}
								<strong>{l s='Refund Net Amount - ' mod='wkpaypaladaptive'}</strong>{$refund->refundNetAmount},&nbsp;&nbsp;{/if}{if isset($refund->refundFeeAmount)}
								<strong>{l s='Refund Fee Amount - ' mod='wkpaypaladaptive'}</strong>{$refund->refundFeeAmount},&nbsp;&nbsp;{/if}{if isset($refund->refundGrossAmount)}
								<strong>{l s='Refund Gross Amount - ' mod='wkpaypaladaptive'}</strong>{$refund->refundGrossAmount},&nbsp;&nbsp;{/if}{if isset($refund->totalOfAllRefunds)}
								<strong>{l s='Total of All Refund - ' mod='wkpaypaladaptive'}</strong>{$refund->totalOfAllRefunds},&nbsp;&nbsp;{/if}						
								{if isset($refund->refundStatus) && $refund->refundStatus} <strong>{l s='Refund Status - ' mod='wkpaypaladaptive'}</strong>{$refund->refundStatus}{/if}
								</p>
							{/foreach}
						{/foreach}
					{else}
						{l s='No refund details available.' mod='wkpaypaladaptive'}
					{/if}
				</td>
			</tr>
		{/if}
	</table>
	<input type="hidden" name="wkPaypalTransactionPayKey" id="wkPaypalTransactionPayKey" value="{$paypal_transaction.pay_key|escape:'htmlall':'UTF-8'}" />
	<input type="hidden" name="wkPaypalTransactionId" id="wkPaypalTransactionId" value="{$paypal_transaction.id|escape:'htmlall':'UTF-8'}" />
	<p id="responseMsg"></p>
</div>
<script type="text/javascript">
	$(document).ready(function(){
		$(document).on('click', '#paypalRefundBtn', function(){
			var payKey = $("#wkPaypalTransactionPayKey").val();
			var wkPaypalTransactionId = $('#wkPaypalTransactionId').val();
			$('#paypalRefundImg').show();
			$.ajax({
				type: "POST",
				url: "{$module_dir}refund.php",
				dataType: "json",
				data: {
					wkPaypalTransactionPayKey: payKey,
					wkPaypalTransactionId: wkPaypalTransactionId
				},
				success: function(result){
					$('#paypalRefundImg').hide();
					$('#paypalRefundBtn').remove();
					$('#delayedPaymentBtn').remove();
					$("#responseMsg").text(result.msg);
					if (result.status == 'success') {
						$("#responseMsg").css('color', 'green');
					} else {
						$("#responseMsg").css('color', 'red');
					}
					location.reload();
				}
			});
		});
	});
</script>
{/if}
