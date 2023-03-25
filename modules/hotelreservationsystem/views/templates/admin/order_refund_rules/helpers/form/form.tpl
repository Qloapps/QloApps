<div class="panel">
	<div class="panel-heading">
		{if isset($edit)}
			<i class='icon-pencil'></i>&nbsp{l s='Edit Refund Rule' mod='hotelreservationsystem'}
		{else}
			<i class='icon-plus'></i>&nbsp{l s='Add New Refund Rule' mod='hotelreservationsystem'}
		{/if}
	</div>
	<form id="{$table|escape:'htmlall':'UTF-8'}_form" class="defaultForm {$name_controller|escape:'htmlall':'UTF-8'} form-horizontal" action="{$current|escape:'htmlall':'UTF-8'}&{if !empty($submit_action)}{$submit_action|escape:'htmlall':'UTF-8'}{/if}&token={$token|escape:'htmlall':'UTF-8'}" method="post" {if isset($style)}style="{$style|escape:'htmlall':'UTF-8'}"{/if}>

		{if count($languages) > 1}
			<div class="col-lg-12">
				<label class="control-label">{l s='Choose Language' mod='hotelreservationsystem'}</label>
				<input type="hidden" name="choosedLangId" id="choosedLangId" value="{$currentLang.id_lang|escape:'htmlall':'UTF-8'}">
				<button type="button" id="multi_lang_btn" class="btn btn-default dropdown-toggle wk_language_toggle" data-toggle="dropdown">
					{$currentLang.name|escape:'htmlall':'UTF-8'}
					<span class="caret"></span>
				</button>
				<ul class="dropdown-menu wk_language_menu" style="left:14%;top:32px;">
					{foreach from=$languages item=language}
						<li>
							<a href="javascript:void(0)" onclick="showLangField('{$language.name|escape:'htmlall':'UTF-8'}', {$language.id_lang|escape:'htmlall':'UTF-8'});">
								{$language.name|escape:'htmlall':'UTF-8'}
							</a>
						</li>
					{/foreach}
				</ul>
				<p class="help-block">{l s='Change language for updating information in multiple language.' mod='hotelreservationsystem'}</p>
				<hr>
			</div>
		{/if}

		{if isset($edit)}
			<input type="hidden" value="{$refund_rules_info.id|escape:'html':'UTF-8'}" name="id_refund_rule" />
		{/if}

		<div class="form-group">
			<label class="col-sm-3 control-label required" for="name" >
				{l s='Name' mod='hotelreservationsystem'}
				{include file="../../../_partials/htl-form-fields-flag.tpl"}
			</label>
			<div class="col-lg-6">
				{foreach from=$languages item=language}
					{assign var="name" value="name_`$language.id_lang`"}
					<input type="text"
					id="name_{$language.id_lang|escape:'htmlall':'UTF-8'}"
					name="name_{$language.id_lang|escape:'htmlall':'UTF-8'}"
					value="{if isset($smarty.post.$name)}{$smarty.post.$name|escape:'htmlall':'UTF-8'}{elseif isset($edit)}{$refund_rules_info.name[$language.id_lang]|escape:'htmlall':'UTF-8'}{/if}"
					class="form-control wk_text_field_all wk_text_field_{$language.id_lang|escape:'htmlall':'UTF-8'}"
					maxlength="128"
					{if $currentLang.id_lang != $language.id_lang}style="display:none;"{/if} />
				{/foreach}
			</div>
		</div>
		<div class="form-group">
			<label class="col-sm-3 control-label required">
				{l s='Description :' mod='hotelreservationsystem'}
				{include file="../../../_partials/htl-form-fields-flag.tpl"}
			</label>
			<div class="col-lg-9">
				{foreach from=$languages item=language}
					{assign var="description" value="description_`$language.id_lang`"}
					<div class="wk_text_field_all wk_text_field_{$language.id_lang|escape:'htmlall':'UTF-8'}" {if $currentLang.id_lang != $language.id_lang}style="display:none;"{/if}>
						<textarea
						name="description_{$language.id_lang|escape:'htmlall':'UTF-8'}"
						id="description_{$language.id_lang|escape:'htmlall':'UTF-8'}"
						cols="2" rows="3"
						class="form-control">{if isset($smarty.post.$description)}{$smarty.post.$description}{elseif isset($edit)}{$refund_rules_info.description[$language.id_lang]|escape:'htmlall':'UTF-8'}{/if}</textarea>
					</div>
				{/foreach}
			</div>
		</div>

		<div class="form-group">
			<label for="refund_payment_type" class="required control-label col-lg-3">
				<span title="" data-toggle="tooltip" class="label-tooltip" data-original-title='{l s="Select type of deduction (percentage or fixed amount)." mod="hotelreservationsyatem"}'>{l s="Select deduction type" mod="hotelreservationsystem"}</span>
			</label>
			<div class="col-lg-8">
				<div class="row">
					<div class="col-lg-3">
						<select id="refund_payment_type" name="refund_payment_type">
							<option {if isset($edit)} {if $refund_rules_info['payment_type'] == $WK_REFUND_RULE_PAYMENT_TYPE_PERCENTAGE}selected{/if}{/if} value="{$WK_REFUND_RULE_PAYMENT_TYPE_PERCENTAGE|escape:'htmlall':'UTF-8'}">{l s="Percentage" mod="hotelreservationsystem"}</option>
							<option value="{$WK_REFUND_RULE_PAYMENT_TYPE_FIXED|escape:'htmlall':'UTF-8'}" {if isset($edit)} {if $refund_rules_info['payment_type'] == $WK_REFUND_RULE_PAYMENT_TYPE_FIXED}selected{/if}{/if}>{l s="Fixed amount" mod="hotelreservationsystem"}</option>
						</select>
					</div>
				</div>
			</div>
		</div>

		<div class="form-group">
			<label for="deduction_value_adv_pay" class="required control-label col-lg-3">
				<span title="" data-toggle="tooltip" class="label-tooltip" data-original-title='{l s="Enter deduction value (percentage or fixed amount) from the total amount, which will be deducted as cancellation charges for advance payment." mod="hotelreservationsyatem"}'>{l s='Deduction value for advance payment' mod="hotelreservationsyatem"}</span>
			</label>
			<div class="col-lg-2">
				<div class="input-group">
					<input type="text" id="deduction_value_adv_pay" name="deduction_value_adv_pay" {if isset($edit) && isset($refund_rules_info['deduction_value_adv_pay'])} value="{$refund_rules_info['deduction_value_adv_pay']|escape:'html':'UTF-8'}" {/if}>
					<span class="input-group-addon payment_type_icon">{if isset($edit)} {if $refund_rules_info['payment_type'] == $WK_REFUND_RULE_PAYMENT_TYPE_FIXED}{$objCurrency->sign|escape:'html':'UTF-8'}</span>{else}%{/if}{else}%{/if}
				</div>
			</div>
		</div>

		<div class="form-group">
			<label for="deduction_value_full_pay" class="required control-label col-lg-3">
				<span title="" data-toggle="tooltip" class="label-tooltip" data-original-title='{l s="Enter deduction value (percentage or fixed amount) from the total amount, which will be deducted as cancellation charges for full payment." mod="hotelreservationsyatem"}'>{l s='Deduction value for full payment' mod="hotelreservationsyatem"}</span>
			</label>
			<div class="col-lg-2">
				<div class="input-group">
					<input type="text" id="deduction_value_full_pay" name="deduction_value_full_pay" {if isset($edit) && isset($refund_rules_info['deduction_value_full_pay'])} value="{$refund_rules_info['deduction_value_full_pay']|escape:'html':'UTF-8'}" {/if}>
					<span class="input-group-addon payment_type_icon">{if isset($edit)} {if $refund_rules_info['payment_type'] == $WK_REFUND_RULE_PAYMENT_TYPE_FIXED}{$objCurrency->sign|escape:'html':'UTF-8'}</span>{else}%{/if}{else}%{/if}
				</div>
			</div>
		</div>

		<div class="form-group">
			<label for="cancelation_days" class="required control-label col-lg-3">
				<span title="" data-toggle="tooltip" class="label-tooltip" data-original-title='{l s="Enter number of days before check-in date for this rule to be applicable." mod="hotelreservationsyatem"}'>{l s='Days before check-in' mod="hotelreservationsyatem"}</span>
			</label>
			<div class="col-lg-2">
				<input class="form-control" type="text" id="cancelation_days" name="cancelation_days" {if isset($edit)} {if isset($refund_rules_info['days'])}style = "display:block;" value="{$refund_rules_info['days']}" {/if}{/if}>
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
