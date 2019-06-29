{*
* 2010-2018 Webkul.
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
*  @copyright 2010-2018 Webkul IN
*  @license   https://store.webkul.com/license.html
*}

<div class="panel">
	<div class="panel-heading">
		{if isset($edit)}
			<i class='icon-pencil'></i>&nbsp{l s='Edit Feature Price Rule' mod='hotelreservationsystem'}
		{else}
			<i class='icon-plus'></i>&nbsp{l s='Add New Feature Price Rule' mod='hotelreservationsystem'}
		{/if}
	</div>
	<form id="{$table}_form" class="defaultForm form-horizontal" action="{$current}&{if !empty($submit_action)}{$submit_action}{/if}&token={$token}" method="post" enctype="multipart/form-data" {if isset($style)}style="{$style}"{/if}>
		{if isset($edit)}
			<input type="hidden" value="{$objFeaturePrice->id|escape:'html':'UTF-8'}" name="id_feature_price" />
		{/if}
		<div class="form-group">
			<label class="col-sm-3 control-label required" for="feature_price_name" >
				{l s='Room Type :' mod='hotelreservationsystem'}
			</label>
			<div class="col-sm-3">
				<input autocomplete="off" type="text" id="room_type_name" name="room_type_name" class="form-control" placeholder= "{l s='Enter Room Type Name' mod='hotelreservationsystem'}" value="{if isset($productName)}{$productName}{/if}"/>
				<input type="hidden" id="room_type_id" name="room_type_id" class="form-control" value="{if isset($objFeaturePrice->id_product)}{$objFeaturePrice->id_product}{else}0{/if}"/>
				<div class="dropdown">
	                <ul class="room_type_search_results_ul"></ul>
	            </div>
				<p class="error-block" style="display:none; color: #CD5D5D;">{l s='No match found for this search. Please try with an existing name.' mod='hotelreservationsystem'}</p>
			</div>
			<div class="help-block">
				**{l s='Enter room type name and select the room for which you are going to create this feature price plan.' mod='hotelreservationsystem'}
			</div>
		</div>
		<div class="form-group">
			<label class="col-sm-3 control-label required" for="feature_price_name" >
				{l s='Feature Price Rule Name :' mod='hotelreservationsystem'}
			</label>
			<div class="col-lg-3">
				{foreach from=$languages item=language}
					{assign var="feature_price_name" value="feature_price_name_`$language.id_lang`"}
					<input type="text" id="{$feature_price_name}" name="{$feature_price_name}" value="{if isset($objFeaturePrice->feature_price_name[$language.id_lang]) && $objFeaturePrice->feature_price_name[$language.id_lang]}{$objFeaturePrice->feature_price_name[$language.id_lang]}{else if isset($smarty.post.$feature_price_name)}{$smarty.post.$feature_price_name}{/if}" data-lang-name="{$language.name}" placeholder="{l s='Enter Feature Price Rule Name' mod='hotelreservationsystem'}" class="form-control feature_price_name_all" {if $currentLang.id_lang != $language.id_lang}style="display:none;"{/if}/>
				{/foreach}
			</div>
			{if $languages|@count > 1}
				<div class="col-lg-2">
					<button type="button" id="feature_price_rule_lang_btn" class="btn btn-default dropdown-toggle" data-toggle="dropdown">
						{$currentLang.iso_code}
						<span class="caret"></span>
					</button>
					<ul class="dropdown-menu">
						{foreach from=$languages item=language}
							<li>
								<a href="javascript:void(0)" onclick="showFeaturePriceRuleLangField('{$language.iso_code}', {$language.id_lang});">{$language.name}</a>
							</li>
						{/foreach}
					</ul>
				</div>
			{/if}
		</div>

		<div class="form-group">
            <label for="date_selection_type" class="control-label col-lg-3">
              {l s='Date Selection type :' mod='hotelreservationsystem'}
            </label>
            <div class="col-lg-3">
				<select class="form-control" name="date_selection_type" id="date_selection_type">
					<option value="1" {if isset($objFeaturePrice->date_selection_type) && $objFeaturePrice->date_selection_type == 1}selected = "selected"{/if}>
					  {l s='Date Range' mod='hotelreservationsystem'}
					</option>
					<option value="2" {if isset($objFeaturePrice->date_selection_type) && $objFeaturePrice->date_selection_type == 2}selected = "selected"{/if}>
					  {l s='Specific Date' mod='hotelreservationsystem'}
					</option>
				</select>
			</div>
		</div>

		<div class="form-group specific_date_type" {if isset($edit) && $edit}{if isset($objFeaturePrice->date_selection_type) && $objFeaturePrice->date_selection_type != 2}style="display:none;"{/if}{else}style="display:none;"{/if}>
			<label class="col-sm-3 control-label required" for="specific_date" >
				{l s='Specific Date' mod='hotelreservationsystem'}
			</label>
			<div class="col-sm-3">
				<input type="text" id="specific_date" name="specific_date" class="form-control datepicker-input" value="{if isset($objFeaturePrice->date_from)}{$objFeaturePrice->date_from}{else}{$date_from}{/if}" readonly/>
			</div>
		</div>

		<div class="form-group date_range_type" {if isset($objFeaturePrice->date_selection_type) && $objFeaturePrice->date_selection_type == 2}style="display:none;"{/if}>
			<label class="col-sm-3 control-label required" for="date_form" >
				{l s='Date From' mod='hotelreservationsystem'}
			</label>
			<div class="col-sm-3">
				<input type="text" id="feature_plan_date_from" name="date_from" class="form-control datepicker-input" value="{if isset($objFeaturePrice->date_from)}{$objFeaturePrice->date_from|date_format:'%d-%m-%Y'}{else}{$date_from|date_format:'%d-%m-%Y'}{/if}" readonly/>
			</div>
		</div>
		<div class="form-group date_range_type" {if isset($objFeaturePrice->date_selection_type) && $objFeaturePrice->date_selection_type == 2}style="display:none;"{/if}>
			<label class="col-sm-3 control-label required" for="date_to" >
				{l s='Date To' mod='hotelreservationsystem'}
			</label>
			<div class="col-sm-3">
				<input type="text" id="feature_plan_date_to" name="date_to" class="form-control datepicker-input" value="{if isset($objFeaturePrice->date_to)}{$objFeaturePrice->date_to|date_format:'%d-%m-%Y'}{else}{$date_to|date_format:'%d-%m-%Y'}{/if}" readonly/>
			</div>
		</div>
		<div class="form-group special_days_content" {if isset($objFeaturePrice->date_selection_type) && 	$objFeaturePrice->date_selection_type == 2}style="display:none;"{/if}>
			<label class="control-label col-lg-3">
				<span class="label-tooltip" data-toggle="tooltip" data-html="true" title="" data-original-title="{l s='If you want to create this Feature price rule only for some special days of the week of selected date range then you can select select days after checking this option. Otherwise rule will be created for whole selected date range.' mod='hotelreservationsystem'}">
					{l s='For Special Days' mod='hotelreservationsystem'}
				</span>
			</label>
			<div class="col-sm-2">
				<p class="checkbox">
					<label>
						<input class="is_special_days_exists pull-left" type="checkbox" name="is_special_days_exists"
						{if (isset($smarty.post.is_special_days_exists) && $smarty.post.is_special_days_exists)
							|| (isset($objFeaturePrice->is_special_days_exists) && $objFeaturePrice->is_special_days_exists)}
							checked="checked"
						{/if}/>
						{l s='Check to select special days' mod='hotelreservationsystem'}
					</label>
				</p>
			</div>
			<div class="col-sm-7 week_days"
			{if (isset($smarty.post.is_special_days_exists) && $smarty.post.is_special_days_exists) 	|| (isset($objFeaturePrice->is_special_days_exists) && $objFeaturePrice->is_special_days_exists)}
				style="display:block;"
			{/if}>
				<div class="col-sm-1">
					<input type="checkbox" name="special_days[]" value="mon"
					{if (isset($smarty.post.special_days) && in_array('mon', $smarty.post.special_days))
						|| (isset($special_days) && $special_days && in_array('mon', $special_days))}
						checked="checked"
					{/if}/>
					<p>{l s='mon' mod='hotelreservationsystem'}</p>
				</div>
				<div class="col-sm-1">
					<input type="checkbox" name="special_days[]" value="tue"
					{if (isset($smarty.post.special_days) && in_array('tue', $smarty.post.special_days))
						|| (isset($special_days) && $special_days && in_array('tue', $special_days))}
						checked="checked"
					{/if}/>
					<p>{l s='tue' mod='hotelreservationsystem'}</p>
				</div>
				<div class="col-sm-1">
					<input type="checkbox" name="special_days[]" value="wed"
					{if (isset($smarty.post.special_days) && in_array('wed', $smarty.post.special_days))
						|| (isset($special_days) && $special_days && in_array('wed', $special_days))}
						checked="checked"
					{/if}/>
					<p>{l s='wed' mod='hotelreservationsystem'}</p>
				</div>
				<div class="col-sm-1">
					<input type="checkbox" name="special_days[]" value="thu"
					{if (isset($smarty.post.special_days) && in_array('thu', $smarty.post.special_days))
						|| (isset($special_days) && $special_days && in_array('thu', $special_days))}
						checked="checked"
					{/if}/>
					<p>{l s='thu' mod='hotelreservationsystem'}</p>
				</div>
				<div class="col-sm-1">
					<input type="checkbox" name="special_days[]" value="fri"
					{if (isset($smarty.post.special_days) && in_array('fri', $smarty.post.special_days))
						|| (isset($special_days) && $special_days && in_array('fri', $special_days))}
						checked="checked"
					{/if}/>
					<p>{l s='fri' mod='hotelreservationsystem'}</p>
				</div>
				<div class="col-sm-1">
					<input type="checkbox" name="special_days[]" value="sat"
					{if (isset($smarty.post.special_days) && in_array('sat', $smarty.post.special_days))
						|| (isset($special_days) && $special_days && in_array('sat', $special_days))}
						checked="checked"
					{/if}/>
					<p>{l s='sat' mod='hotelreservationsystem'}</p>
				</div>
				<div class="col-sm-1">
					<input type="checkbox" name="special_days[]" value="sun"
					{if (isset($smarty.post.special_days) && in_array('sun', $smarty.post.special_days))
						|| (isset($special_days) && $special_days && in_array('sun', $special_days))}
						checked="checked"
					{/if}/>
					<p>{l s='sun' mod='hotelreservationsystem'}</p>
				</div>
			</div>
		</div>

		<div class="form-group">
            <label for="Price Impact Way" class="control-label col-lg-3">
              {l s='Impact Way :' mod='hotelreservationsystem'}
            </label>
            <div class="col-lg-3">
				<select class="form-control" name="price_impact_way" id="price_impact_way">
					<option value="1" {if isset($objFeaturePrice->impact_way) && $objFeaturePrice->impact_way == 1}selected = "selected"{/if}>
					  {l s='Decrease Price' mod='hotelreservationsystem'}
					</option>
					<option value="2" {if isset($objFeaturePrice->impact_way) && $objFeaturePrice->impact_way == 2}selected = "selected"{/if}>
					  {l s='Increase Price' mod='hotelreservationsystem'}
					</option>
				</select>
			</div>
		</div>

		<div class="form-group">
            <label for="Price Impact Type" class="control-label col-lg-3">
              {l s='Impact Type :' mod='hotelreservationsystem'}
            </label>
            <div class="col-lg-3">
				<select class="form-control" name="price_impact_type" id="price_impact_type">
					<option value="1" {if isset($objFeaturePrice->impact_type) && $objFeaturePrice->impact_type == 1}selected = "selected"{/if}>
					  {l s='Percentage' mod='hotelreservationsystem'}
					</option>
					<option value="2" {if isset($objFeaturePrice->impact_type) && $objFeaturePrice->impact_type == 2}selected = "selected"{/if}>
					  {l s='Fixed Price' mod='hotelreservationsystem'}
					</option>
				</select>
			</div>
		</div>

		<div class="form-group">
			<label class="col-sm-3 control-label required" for="feature_price_name" >
				{l s='Impact Value' mod='hotelreservationsystem'}({l s='tax excl.' mod='hotelreservationsystem'})
			</label>
			<div class="col-lg-3">
				<div class="input-group">
					<span class="input-group-addon payment_type_icon">{if isset($edit)} {if $objFeaturePrice->impact_type==2}{$defaultcurrency_sign}{else}%{/if}{else}%{/if}</span>
					<input type="text" id="impact_value" name="impact_value"
					value="{if isset($smarty.post.impact_value) && $smarty.post.impact_value}{$smarty.post.impact_value}{elseif isset($objFeaturePrice->impact_value)}{$objFeaturePrice->impact_value}{/if}"/>
				</div>
			</div>
		</div>

		<div class="form-group">
			<label class="control-label col-lg-3">
				<span>
					{l s='Enable Feature Price Rule' mod='hotelreservationsystem'}
				</span>
			</label>
			<div class="col-lg-9 ">
				<span class="switch prestashop-switch fixed-width-lg">
					<input type="radio" {if isset($edit) && $objFeaturePrice->active==1} checked="checked" {else}checked="checked"{/if} value="1" id="enable_feature_price_on" name="enable_feature_price">
					<label for="enable_feature_price_on">{l s='Yes' mod='hotelreservationsystem'}</label>
					<input {if isset($edit) && $objFeaturePrice->active==0} checked="checked" {/if} type="radio" value="0" id="enable_feature_price_off" name="enable_feature_price">
					<label for="enable_feature_price_off">{l s='No' mod='hotelreservationsystem'}</label>
					<a class="slide-button btn"></a>
				</span>
			</div>
		</div>

		<div class="panel-footer">
			<a href="{$link->getAdminLink('AdminHotelFeaturePricesSettings')|escape:'html':'UTF-8'}" class="btn btn-default">
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

{strip}
	{addJsDef autocomplete_room_search_url = $link->getAdminLink('AdminHotelFeaturePricesSettings')}
	{addJsDef defaultcurrency_sign = $defaultcurrency_sign mod='hotelreservationsystem'}
	{addJsDef booking_date_from = $date_from mod='hotelreservationsystem'}
{/strip}