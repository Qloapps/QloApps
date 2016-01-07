<div class="panel">
	<div class="panel-heading">
		{if isset($edit)}
			<i class='icon-pencil'></i>&nbsp{l s='Edit Refund Rule' mod='hotelreservationsystem'}
		{else}
			<i class='icon-plus'></i>&nbsp{l s='Add New Refund Rule' mod='hotelreservationsystem'}
		{/if}
	</div>
	<form id="{$table|escape:'htmlall':'UTF-8'}_form" class="defaultForm {$name_controller|escape:'htmlall':'UTF-8'} form-horizontal" action="{$current|escape:'htmlall':'UTF-8'}&{if !empty($submit_action)}{$submit_action|escape:'htmlall':'UTF-8'}{/if}&token={$token|escape:'htmlall':'UTF-8'}" method="post" {if isset($style)}style="{$style|escape:'htmlall':'UTF-8'}"{/if}>
		{if isset($edit)}
			<input type="hidden" value="{$refund_rules_info.id|escape:'html':'UTF-8'}" name="id" />
		{/if}
		<div class="form-group">
			<label for="refund_payment_type" class="required control-label col-lg-3">
				<span title="" data-toggle="tooltip" class="label-tooltip" data-original-title='{l s="Select type of payment you want." mod="hotelreservationsyatem"}'>{l s="Select Payment Type" mod="hotelreservationsystem"}</span>
			</label>
			<div class="col-lg-8">
				<div class="row">
					<div class="col-lg-3">
						<select id="refund_payment_type" name="refund_payment_type">
							<option {if isset($edit)} {if $refund_rules_info['payment_type'] == 1}selected{/if}{/if} value="1">{l s="Percentage" mod="hotelreservationsystem"}</option>
							<option value="2" {if isset($edit)} {if $refund_rules_info['payment_type'] == 2}selected{/if}{else}selected="true"{/if}>{l s="Fixed Amount" mod="hotelreservationsystem"}</option>
						</select>
					</div>
				</div>
			</div>
		</div>

		<div class="form-group">
			<label for="deduction_value_adv_pay" class="required control-label col-lg-3">
				<span title="" data-toggle="tooltip" class="label-tooltip" data-original-title='{l s="Enter How much percent of total amount will be deducted as cancellation charges." mod="hotelreservationsyatem"}'>{l s='Deduction Value For Advance Payment' mod="hotelreservationsyatem"}</span>
			</label>
			<div class="col-lg-2">
				<div class="input-group">
					<span class="input-group-addon payment_type_icon">{if isset($edit)} {if $refund_rules_info['payment_type'] == 2}{$defaultcurrency_sign}{else}%{/if}{else}{$defaultcurrency_sign}{/if}</span>
					<input type="text" id="deduction_value_adv_pay" name="deduction_value_adv_pay" {if isset($edit) && $refund_rules_info['deduction_value_adv_pay']} value="{$refund_rules_info['deduction_value_adv_pay']}" {/if}>
				</div>
			</div>
		</div>

		<div class="form-group">
			<label for="deduction_value_full_pay" class="required control-label col-lg-3">
				<span title="" data-toggle="tooltip" class="label-tooltip" data-original-title='{l s="Enter How much percent of total amount will be deducted as cancellation charges." mod="hotelreservationsyatem"}'>{l s='Deduction Value For Full Payment' mod="hotelreservationsyatem"}</span>
			</label>
			<div class="col-lg-2">
				<div class="input-group">
					<span class="input-group-addon payment_type_icon">{if isset($edit)} {if $refund_rules_info['payment_type'] == 2}{$defaultcurrency_sign}{else}%{/if}{else}{$defaultcurrency_sign}{/if}</span>
					<input type="text" id="deduction_value_full_pay" name="deduction_value_full_pay" {if isset($edit) && $refund_rules_info['deduction_value_full_pay']} value="{$refund_rules_info['deduction_value_full_pay']}" {/if}>
				</div>
			</div>
		</div>

		<div class="form-group">
			<label for="cancellation_days" class="required control-label col-lg-3">
				<span title="" data-toggle="tooltip" class="label-tooltip" data-original-title='{l s="Enter the days How much days before this rule will be applied." mod="hotelreservationsyatem"}'>{l s='Days Before Cancellation' mod="hotelreservationsyatem"}</span>
			</label>
			<div class="col-lg-2">
				<div class="input-group">
					<input type="text" id="cancellation_days" name="cancellation_days" {if isset($edit)} {if isset($refund_rules_info['days'])}style = "display:block;" value="{$refund_rules_info['days']}" {/if}{/if}>
				</div>
			</div>
		</div>

		<div class="panel-footer">
			<a href="{$link->getAdminLink('AdminOrderRefundRules')|escape:'html':'UTF-8'}" class="btn btn-default">
				<i class="process-icon-cancel"></i>{l s='Cancel' mod='hotelreservationsystem'}
			</a>
			<button type="submit" name="submitAddorder_refund_rules" class="btn btn-default pull-right">
				<i class="process-icon-save"></i> {l s='Save' mod='hotelreservationsystem'}
			</button>
			<button type="submit" name="submitAdd{$table|escape:'html':'UTF-8'}AndStay" class="btn btn-default pull-right">
				<i class="process-icon-save"></i> {l s='Save and stay' mod='hotelreservationsystem'}
			</button>
		</div>
	</form>
</div>

{strip}
	{addJsDef defaultcurrency_sign=$defaultcurrency_sign mod='hotelreservationsystem'}
{/strip}