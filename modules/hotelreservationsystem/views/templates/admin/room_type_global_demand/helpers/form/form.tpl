{*
* 2010-2020 Webkul.
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
*  @copyright 2010-2020 Webkul IN
*  @license   https://store.webkul.com/license.html
*}

<div class="panel">
	<div class="panel-heading">
		{if isset($edit)}
			<i class='icon-pencil'></i>&nbsp;{l s='Edit Facility' mod='hotelreservationsystem'}
		{else}
			<i class='icon-plus'></i>&nbsp;{l s='Add Facility' mod='hotelreservationsystem'}
		{/if}
	</div>
	<form id="{$table}_form" class="defaultForm form-horizontal" action="{$current}&{if !empty($submit_action)}{$submit_action}{/if}&token={$token}" method="post" enctype="multipart/form-data" {if isset($style)}style="{$style}"{/if}>
		{if isset($edit)}
			<input type="hidden" value="{$globalDemands['id']|escape:'html':'UTF-8'}" name="id_global_demand" />
		{/if}
		{if count($languages) > 1}
			<div class="col-sm-12">
				<label class="control-label">{l s='Choose Language' mod='hotelreservationsystem'}</label>
				<input type="hidden" name="choosedLangId" id="choosedLangId" value="{$currentLang.id_lang}">
				<button type="button" id="multi_lang_btn" class="btn btn-default dropdown-toggle wk_language_toggle" data-toggle="dropdown">
					{$currentLang.name}
					<span class="caret"></span>
				</button>
				<ul class="dropdown-menu wk_language_menu" style="left:14%;top:32px;">
					{foreach from=$languages item=language}
						<li>
							<a href="javascript:void(0)" onclick="showLangField('{$language.name}', {$language.id_lang});">
								{$language.name}
							</a>
						</li>
					{/foreach}
				</ul>
				<p class="help-block">{l s='Change language for updating information in multiple language.' mod='hotelreservationsystem'}</p>
				<hr>
			</div>
		{/if}
		<div class="form-group">
			<label class="col-sm-3 control-label required" for="hotel_name" >
				{l s='Facility Name :' mod='hotelreservationsystem'}
				{include file="../../../_partials/htl-form-fields-flag.tpl"}
			</label>
			<div class="col-sm-6">
				{foreach from=$languages item=language}
					{assign var="demand_name" value="demand_name_`$language.id_lang`"}
					<input type="text"
					id="demand_name_{$language.id_lang}"
					name="demand_name_{$language.id_lang}"
					value="{if isset($smarty.post.$demand_name)}{$smarty.post.$demand_name|escape:'htmlall':'UTF-8'}{elseif isset($edit)}{$globalDemands.name[{$language.id_lang}]|escape:'htmlall':'UTF-8'}{/if}"
					class="form-control wk_text_field_all wk_text_field_{$language.id_lang}"
					maxlength="128"
					{if $currentLang.id_lang != $language.id_lang}style="display:none;"{/if} />
				{/foreach}
			</div>
		</div>
		<div class="form-group">
			<label class="col-sm-3 control-label">
				<span class="label-tooltip" data-toggle="tooltip" data-html="true" title="" data-original-title="{l s='Price of the facility will be calculated according to the price of the advance options.' mod='hotelreservationsystem'}">{l s='Create advance options' mod='hotelreservationsystem'}</span>
			</label>
			<div class="col-sm-6">
				<span class="switch prestashop-switch fixed-width-lg">
					<input type="radio" value="1" id="active_adv_option_on" name="active_adv_option"
					{if isset($smarty.post.active_adv_option)}
						{if $smarty.post.active_adv_option}
							checked="checked"
						{/if}
					{elseif isset($globalDemands) && $globalDemands['adv_option']|@count}
						checked="checked"
					{/if}>
					<label for="active_adv_option_on">{l s='Yes' mod='hotelreservationsystem'}</label>
					<input type="radio" value="0" id="active_adv_option_off" name="active_adv_option"
					{if isset($smarty.post.active_adv_option)}
						{if !$smarty.post.active_adv_option}
							checked="checked"
						{/if}
					{elseif !isset($globalDemands)}
						checked="checked"
					{elseif isset($globalDemands) && !$globalDemands['adv_option']|@count}
						checked="checked"
					{/if}>
					<label for="active_adv_option_off">{l s='No' mod='hotelreservationsystem'}</label>
					<a class="slide-button btn"></a>
				</span>
			</div>
		</div>
		<div class="adv_options_dtl form-group" {if isset($smarty.post.active_adv_option)}{if !$smarty.post.active_adv_option}style="display:none;"{/if}{elseif !isset($globalDemands['adv_option']) || !$globalDemands['adv_option']|@count}style="display:none;"{/if}>
			<label class="col-sm-3 control-label">
				{* {l s='Advance options' mod='hotelreservationsystem'} *}
			</label>
			<div class="col-sm-9">
				<div class="table-responsive-row clearfix">
					<table class="table table-bordered adv_option_table">
						<tr class="nodrag nodrop">
							<th class="left">
								<span>{l s='Option Name' mod='hotelreservationsystem'}</span>
							</th>
							<th class="left">
								<span>{l s='Price' mod='hotelreservationsystem'}</span>
							</th>
							<th class="center">
								<span>{l s='action' mod='hotelreservationsystem'}</span>
							</th>
						</tr>
						{if isset($globalDemands['adv_option']) && $globalDemands['adv_option']}
							{foreach from=$globalDemands['adv_option'] key=key item=info}
								<tr>
									<td class="center">
										{if count($languages) > 1}
											<div class="input-group">
												<span class="input-group-addon">{include file="../../../_partials/htl-form-fields-flag.tpl"}</span>
										{/if}
											{foreach from=$languages item=language}
												{assign var="option_name" value="option_name_`$language.id_lang`"}
												<input type="text"
												name="option_name_{$language.id_lang}[]"
												value="{if isset($smarty.post.$option_name[$key]) && $smarty.post.$option_name[$key]}{$smarty.post.$option_name[$key]|escape:'htmlall':'UTF-8'}{else}{$info['name'][$language.id_lang]}{/if}"
												class="form-control wk_text_field_all wk_text_field_{$language.id_lang}"
												maxlength="128"
												{if $currentLang.id_lang != $language.id_lang}style="display:none;"{/if} />
											{/foreach}
										{if count($languages) > 1}
											</div>
										{/if}
									</td>
									<td class="center">
										<div class="input-group">
											<span class="input-group-addon">{$defaultcurrencySign}</span>
											<input type="text" name="option_price[]" value="{if isset($smarty.post.option_price[$key]) && $smarty.post.option_price[$key]}{$smarty.post.option_price[$key]|escape:'htmlall':'UTF-8'}{else}{$info['price']}{/if}"/>
										</div>
										<input type="hidden" name="id_option[]" value="{$info['id']}" />
									</td>
									<td class="center">
										<a href="#" class="remove_adv_option btn btn-default"><i class="icon-trash"></i></a>
									</td>
								</tr>
							{/foreach}
						{else}
							{for $k=0 to 1}
								<tr>
									<td class="center">
											{if count($languages) > 1}
												<div class="input-group">
													<span class="input-group-addon">{include file="../../../_partials/htl-form-fields-flag.tpl"}</span>
											{/if}
											{foreach from=$languages item=language}
												{assign var="option_name" value="option_name_`$language.id_lang`"}
												<input type="text"
												name="option_name_{$language.id_lang}[]"
												value="{if isset($smarty.post.$option_name[$k]) && $smarty.post.$option_name[$k]}{$smarty.post.$option_name[$k]|escape:'htmlall':'UTF-8'}{/if}"
												class="form-control wk_text_field_all wk_text_field_{$language.id_lang}"
												maxlength="128"
												{if $currentLang.id_lang != $language.id_lang}style="display:none;"{/if} />
											{/foreach}
											{if count($languages) > 1}
												</div>
											{/if}
									</td>
									<td class="center">
										<div class="input-group">
											<span class="input-group-addon">{$defaultcurrencySign}</span>
											<input type="text" name="option_price[]" value="{if isset($smarty.post.option_price[$k]) && $smarty.post.option_price[$k]}{$smarty.post.option_price[$k]|escape:'htmlall':'UTF-8'}{/if}"/>
										</div>
										<input type="hidden" name="id_option[]"/>
									</td>
									<td class="center">
										<a href="#" class="remove_adv_option btn btn-default"><i class="icon-trash"></i></a>
									</td>
								</tr>
							{/for}
						{/if}
					</table>
					<div class="form-group">
						<div class="col-sm-12">
							<button id="add_more_options_button" class="btn btn-default" type="button">
								<i class="icon-plus-circle"></i>
								{l s='Add More Options' mod='hotelreservationsystem'}
							</button>
						</div>
					</div>
				</div>
			</div>
		</div>
		<div class="form-group" {if isset($smarty.post.active_adv_option)}{if $smarty.post.active_adv_option}style="display:none;"{/if}{elseif isset($globalDemands['adv_option']) && $globalDemands['adv_option']|@count}style="display:none;"{/if}>
			<label class="col-sm-3 control-label required" >
				{l s='Price' mod='hotelreservationsystem'}({l s='tax excl.' mod='hotelreservationsystem'})
			</label>
			<div class="col-sm-3">
				<div class="input-group">
					<span class="input-group-addon">{$defaultcurrencySign}</span>
					<input type="text" id="price" name="price"
					value="{if isset($smarty.post.price)}{$smarty.post.price}{elseif isset($globalDemands['price'])}{$globalDemands['price']}{/if}"/>
				</div>
			</div>
		</div>
		{if isset($taxRuleGroups)}
			<div class="form-group">
				<label class="col-sm-3 control-label required" >
					{l s='Tax Rule' mod='hotelreservationsystem'}
				</label>
				<div class="col-sm-3">
					<select name="id_tax_rules_group" id="id_tax_rules_group" class="form-control form-control-select" data-action="input_excl">
						<option value="0">{l s='No tax' mod='hotelreservationsystem'}</option>
						{foreach $taxRuleGroups as $tax_rule}
							<option value="{$tax_rule.id_tax_rules_group|escape:'html':'UTF-8'}" {if isset($smarty.post.id_tax_rules_group) && ($smarty.post.id_tax_rules_group == $tax_rule.id_tax_rules_group)}selected{elseif isset($globalDemands['id_tax_rules_group']) && ($globalDemands['id_tax_rules_group'] == $tax_rule.id_tax_rules_group)}selected{/if}>
								{$tax_rule.name|escape:'html':'UTF-8'}
							</option>
						{/foreach}
					</select>
				</div>
			</div>
		{/if}
		<div class="form-group">
			<label class="col-sm-3 control-label">
				<span class="label-tooltip" data-toggle="tooltip" data-html="true" data-original-title="{l s='Enable, if you want to add the price of this facility for each day in the booking. Disable, If you want to add price of the facility for entire date range of the booking.' mod='hotelreservationsystem'}">{l s='Per day price calculation' mod='hotelreservationsystem'}</span>
			</label>
			<div class="col-sm-6">
				<span class="switch prestashop-switch fixed-width-lg">
					<input type="radio" value="1" id="price_calc_method_on" name="price_calc_method"
					{if isset($smarty.post.price_calc_method)}
						{if $smarty.post.price_calc_method}
							checked="checked"
						{/if}
					{elseif isset($globalDemands['price_calc_method']) && $globalDemands['price_calc_method']}
						checked="checked"
					{/if}>
					<label for="price_calc_method_on">{l s='Yes' mod='hotelreservationsystem'}</label>
					<input type="radio" value="0" id="price_calc_method_off" name="price_calc_method"
					{if isset($smarty.post.price_calc_method)}
						{if !$smarty.post.price_calc_method}
							checked="checked"
						{/if}
					{elseif !isset($globalDemands['price_calc_method'])}
						checked="checked"
					{elseif isset($globalDemands['price_calc_method']) && !$globalDemands['price_calc_method']}
						checked="checked"
					{/if}>
					<label for="price_calc_method_off">{l s='No' mod='hotelreservationsystem'}</label>
					<a class="slide-button btn"></a>
				</span>
			</div>
		</div>
		<div class="form-group">
			<div class="col-lg-9 col-lg-offset-3">
				<div class="alert alert-info">
					{l s='Enable ("Per day price calculation"), if you want to add the price of this facility for each day in the booking. Disable, If you want to add price of the facility for entire date range of the booking.' mod='hotelreservationsystem'}
				</div>
			</div>
		</div>

		<div class="panel-footer">
			<a href="{$link->getAdminLink('AdminRoomTypeGlobalDemand')|escape:'html':'UTF-8'}" class="btn btn-default">
				<i class="process-icon-cancel"></i>{l s='Cancel' mod='hotelreservationsystem'}
			</a>
			<button type="submit" name="submitAdd{$table|escape:'html':'UTF-8'}" class="btn btn-default pull-right">
				<i class="process-icon-save"></i> {l s='Save' mod='hotelreservationsystem'}
			</button>
			<button type="submit" name="submitAdd{$table|escape:'html':'UTF-8'}AndStay" class="btn btn-default pull-right">
				<i class="process-icon-save"></i> {l s='Save and stay' mod='hotelreservationsystem'}
			</button>
		</div>
	</form>
</div>