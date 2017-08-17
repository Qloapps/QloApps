<div class="panel">
	<div class="panel-heading">
		{if isset($edit)}
			<i class='icon-pencil'></i>&nbsp{l s='Edit Feature Price' mod='hotelreservationsystem'}
		{else}
			<i class='icon-plus'></i>&nbsp{l s='Add New Feature Price' mod='hotelreservationsystem'}
		{/if}
	</div>
	<form id="{$table|escape:'htmlall':'UTF-8'}_form" class="defaultForm {$name_controller|escape:'htmlall':'UTF-8'} form-horizontal" action="{$current|escape:'htmlall':'UTF-8'}&{if !empty($submit_action)}{$submit_action|escape:'htmlall':'UTF-8'}{/if}&token={$token|escape:'htmlall':'UTF-8'}" method="post" enctype="multipart/form-data" {if isset($style)}style="{$style|escape:'htmlall':'UTF-8'}"{/if}>
		{if isset($edit)}
			<input type="hidden" value="{$featurePriceInfo->id|escape:'html':'UTF-8'}" name="id" />
		{/if}
		<div class="form-group">	
			<label class="col-sm-3 control-label required" for="feature_price_name" >
				{l s='Room Type :' mod='hotelreservationsystem'}
			</label>
			<div class="col-sm-3">
				<input type="text" id="room_type_name" name="room_type_name" class="form-control" placeholder= "{l s='Enter Room Type Name' mod='hotelreservationsystem'}" value="{if isset($productName)}{$productName}{/if}"/>
				<input type="hidden" id="room_type_id" name="room_type_id" class="form-control" value="{if isset($featurePriceInfo->id_product)}{$featurePriceInfo->id_product}{else}0{/if}"/>
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
				{l s='Feature Price Name :' mod='hotelreservationsystem'}
			</label>
			<div class="col-sm-3">
				<input type="text" id="feature_price_name" name="feature_price_name" class="form-control" value="{if isset($featurePriceInfo->feature_price_name)}{$featurePriceInfo->feature_price_name}{/if}"/> 
			</div>
		</div>

		<div class="form-group">
            <label for="date_selection_type" class="control-label col-lg-3 required">
              {l s='Date Selection type :' mod='hotelreservationsystem'}
            </label>
            <div class="col-lg-3">
				<select class="form-control" name="date_selection_type" id="date_selection_type">
					<option value="1" {if isset($featurePriceInfo->date_selection_type) && $featurePriceInfo->date_selection_type == 1}selected = "selected"{/if}>
					  {l s='Date Range' mod='hotelreservationsystem'}
					</option>
					<option value="2" {if isset($featurePriceInfo->date_selection_type) && $featurePriceInfo->date_selection_type == 2}selected = "selected"{/if}>
					  {l s='Specific Date' mod='hotelreservationsystem'}
					</option>
				</select>
			</div>
		</div>

		<div class="form-group specific_date_type" {if isset($edit) && $edit}{if isset($featurePriceInfo->date_selection_type) && $featurePriceInfo->date_selection_type != 2}style="display:none;"{/if}{else}style="display:none;"{/if}>
			<label class="col-sm-3 control-label required" for="specific_date" >
				{l s='Specific Date' mod='hotelreservationsystem'}
			</label>
			<div class="col-sm-3">
				<input type="text" id="specific_date" name="specific_date" class="form-control" value="{if isset($featurePriceInfo->date_from)}{$featurePriceInfo->date_from}{else}{$date_from}{/if}" readonly/>
			</div>
		</div>

		<div class="form-group date_range_type" {if isset($featurePriceInfo->date_selection_type) && $featurePriceInfo->date_selection_type == 2}style="display:none;"{/if}>
			<label class="col-sm-3 control-label required" for="date_form">
				{l s='Date From' mod='hotelreservationsystem'}
			</label>
			<div class="col-sm-3">
				<input type="text" id="feature_plan_date_from" name="date_from" class="form-control" value="{if isset($featurePriceInfo->date_from)}{$featurePriceInfo->date_from|date_format:"%d-%m-%Y"}{else}{$date_from|date_format:"%d-%m-%Y"}{/if}" readonly/>
			</div>
		</div>
		<div class="form-group date_range_type" {if isset($featurePriceInfo->date_selection_type) && $featurePriceInfo->date_selection_type == 2}style="display:none;"{/if}>
			<label class="col-sm-3 control-label required" for="date_to" >
				{l s='Date To' mod='hotelreservationsystem'}
			</label>
			<div class="col-sm-3">
				<input type="text" id="feature_plan_date_to" name="date_to" class="form-control" value="{if isset($featurePriceInfo->date_to)}{$featurePriceInfo->date_to|date_format:"%d-%m-%Y"}{else}{$date_to|date_format:"%d-%m-%Y"}{/if}" readonly/> 
			</div>
		</div>
		<div class="form-group special_days_content" {if isset($featurePriceInfo->date_selection_type) && $featurePriceInfo->date_selection_type == 2}style="display:none;"{/if}>
			<label class="col-sm-3 control-label required" for="date_to">
				{l s='For Special Days' mod='hotelreservationsystem'}
			</label>
			<div class="col-sm-2">
				<p class="checkbox">
					<label>
						<input class="is_special_days_exists pull-left" type="checkbox" name="is_special_days_exists" {if isset($featurePriceInfo->is_special_days_exists) && $featurePriceInfo->is_special_days_exists}checked="checked"{/if}/>
						{l s='Check to select special days' mod='hotelreservationsystem'}
					</label>
				</p>
			</div>
			<div class="col-sm-7 week_days" {if isset($featurePriceInfo->is_special_days_exists) && $featurePriceInfo->is_special_days_exists}style="display:block;"{/if}>
				<div class="col-sm-1">
					<input type="checkbox" name="special_days[]" value="mon" {if isset($special_days) && $special_days && in_array('mon', $special_days)}checked="checked"{/if}/>
					<p>{l s='Mon' mod='hotelreservationsystem'}</p>
				</div>
				<div class="col-sm-1">
					<input type="checkbox" name="special_days[]" value="tue" {if isset($special_days) && $special_days && in_array('tue', $special_days)}checked="checked"{/if}/>
					<p>{l s='Tue' mod='hotelreservationsystem'}</p>
				</div>
				<div class="col-sm-1">
					<input type="checkbox" name="special_days[]" value="wed" {if isset($special_days) && $special_days && in_array('wed', $special_days)}checked="checked"{/if}/>
					<p>{l s='Wed' mod='hotelreservationsystem'}</p>
				</div>
				<div class="col-sm-1">
					<input type="checkbox" name="special_days[]" value="thu" {if isset($special_days) && $special_days && in_array('thu', $special_days)}checked="checked"{/if}/>
					<p>{l s='Thu' mod='hotelreservationsystem'}</p>
				</div>
				<div class="col-sm-1">
					<input type="checkbox" name="special_days[]" value="fri" {if isset($special_days) && $special_days && in_array('fri', $special_days)}checked="checked"{/if}/>
					<p>{l s='Fri' mod='hotelreservationsystem'}</p>
				</div>
				<div class="col-sm-1">
					<input type="checkbox" name="special_days[]" value="sat" {if isset($special_days) && $special_days && in_array('sat', $special_days)}checked="checked"{/if}/>
					<p>{l s='Sat' mod='hotelreservationsystem'}</p>
				</div>
				<div class="col-sm-1">
					<input type="checkbox" name="special_days[]" value="sun" {if isset($special_days) && $special_days && in_array('sun', $special_days)}checked="checked"{/if}/>
					<p>{l s='Sun' mod='hotelreservationsystem'}</p>
				</div>
			</div>
		</div>

		<div class="form-group">
            <label for="Price Impact Way" class="control-label col-lg-3">
              {l s='Impact Way :' mod='hotelreservationsystem'}
            </label>
            <div class="col-lg-3">
				<select class="form-control" name="price_impact_way" id="price_impact_way">
					<option value="1" {if isset($featurePriceInfo->impact_way) && $featurePriceInfo->impact_way == 1}selected = "selected"{/if}>
					  {l s='Decrease Price' mod='hotelreservationsystem'}
					</option>
					<option value="2" {if isset($featurePriceInfo->impact_way) && $featurePriceInfo->impact_way == 2}selected = "selected"{/if}>
					  {l s='Increase Price' mod='hotelreservationsystem'}
					</option>
				</select>
			</div>
		</div>

		<div class="form-group">
            <label for="Price Impact Type" class="control-label col-lg-3 required">
              {l s='Impact Type :' mod='hotelreservationsystem'}
            </label>
            <div class="col-lg-3">
				<select class="form-control" name="price_impact_type" id="price_impact_type">
					<option value="1" {if isset($featurePriceInfo->impact_type) && $featurePriceInfo->impact_type == 1}selected = "selected"{/if}>
					  {l s='Percentage' mod='hotelreservationsystem'}
					</option>
					<option value="2" {if isset($featurePriceInfo->impact_type) && $featurePriceInfo->impact_type == 2}selected = "selected"{/if}>
					  {l s='Fixed Price' mod='hotelreservationsystem'}
					</option>
				</select>
			</div>
		</div>

		<div class="form-group">	
			<label class="col-sm-3 control-label required" for="feature_price_name" >
				{l s='Impact Value' mod='hotelreservationsystem'}
			</label>
			<div class="col-lg-3">
				<div class="input-group">
					<span class="input-group-addon payment_type_icon">{if isset($edit)} {if $featurePriceInfo->impact_type==2}{$defaultcurrency_sign}{else}%{/if}{else}%{/if}</span>
					<input type="text" id="impact_value" name="impact_value" value="{if isset($featurePriceInfo->impact_value)}{$featurePriceInfo->impact_value}{/if}">
					<span class="input-group-addon">{l s='tax excl.' mod='hotelreservationsystem'}</span>
				</div>
			</div>
		</div>

		<div class="form-group">
			<label class="control-label col-lg-3 required">
				<span>
					{l s='Enable Feature Price' mod='hotelreservationsystem'}
				</span>
			</label>
			<div class="col-lg-9 ">
				<span class="switch prestashop-switch fixed-width-lg">
					<input type="radio" {if isset($edit) && $featurePriceInfo->active==1} checked="checked" {else}checked="checked"{/if} value="1" id="enable_feature_price_on" name="enable_feature_price">
					<label for="enable_feature_price_on">{l s='Yes'}</label>
					<input {if isset($edit) && $featurePriceInfo->active==0} checked="checked" {/if} type="radio" value="0" id="enable_feature_price_off" name="enable_feature_price">
					<label for="enable_feature_price_off">{l s='No'}</label>
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

