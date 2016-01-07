<div class="panel">
	<div class="panel-heading">
		{if isset($edit)}
			<i class='icon-pencil'></i>&nbsp{l s='Edit Hotel' mod='hotelreservationsystem'}
		{else}
			<i class='icon-plus'></i>&nbsp{l s='Add New Hotel' mod='hotelreservationsystem'}
		{/if}
	</div>
	<form id="{$table|escape:'htmlall':'UTF-8'}_form" class="defaultForm {$name_controller|escape:'htmlall':'UTF-8'} form-horizontal" action="{$current|escape:'htmlall':'UTF-8'}&{if !empty($submit_action)}{$submit_action|escape:'htmlall':'UTF-8'}{/if}&token={$token|escape:'htmlall':'UTF-8'}" method="post" enctype="multipart/form-data" {if isset($style)}style="{$style|escape:'htmlall':'UTF-8'}"{/if}>
		{if isset($edit)}
			<input type="hidden" value="{$hotel_info.id|escape:'html':'UTF-8'}" name="id" />
		{/if}
		<div class="form-group">
			<label class="control-label col-lg-3">
				<span>
					{l s='Enable Hotel' mod='hotelreservationsystem'}
				</span>
			</label>
			<div class="col-lg-9 ">
				<span class="switch prestashop-switch fixed-width-lg">
					<input type="radio" {if isset($edit) && $hotel_info.active==1} checked="checked" {else}checked="checked"{/if} value="1" id="ENABLE_HOTEL_on" name="ENABLE_HOTEL">
					<label for="ENABLE_HOTEL_on">{l s='Yes'}</label>
					<input {if isset($edit) && $hotel_info.active==0} checked="checked" {/if} type="radio" value="0" id="ENABLE_HOTEL_off" name="ENABLE_HOTEL">
					<label for="ENABLE_HOTEL_off">{l s='No'}</label>
					<a class="slide-button btn"></a>
				</span>
			</div>
		</div>
		<div class="form-group">	
			<label class="col-sm-3 control-label required" for="hotel_name" >
				{l s='Hotel Name :' mod='hotelreservationsystem'}
			</label>
			<div class="col-sm-6">
				<input type="text" id="hotel_name" name="hotel_name" class="form-control" {if isset($edit)}value="{$hotel_info.hotel_name|escape:'html':'UTF-8'}"{/if}/> 
			</div>
		</div>
		<div class="form-group">
			<label class="col-sm-3 control-label">{l s='Short Description :' mod='hotelreservationsystem'}</label>
			<div class="col-sm-6">
				<textarea name="short_description" class="short_description wk_tinymce" >{if isset($edit)}{$hotel_info.short_description|escape:'htmlall':'UTF-8'}{/if}</textarea>
			</div>
		</div>
		<div class="form-group">
			<label class="col-sm-3 control-label">{l s='Description :' mod='hotelreservationsystem'}</label>
			<div class="col-sm-6">
				<textarea name="description" class="description wk_tinymce" rows="4" cols="35" >{if isset($edit)}{$hotel_info.description|escape:'htmlall':'UTF-8'}{/if}</textarea>
			</div>
		</div>
		<div class="form-group">
			<label class="col-sm-3 control-label required">{l s='Phone :' mod='hotelreservationsystem'}</label>
			<div class="col-sm-6">
				<input type="text" name="phone" id="phone" maxlength="{$max_phone_digit|escape:'htmlall':'UTF-8'}" {if isset($edit)}value="{$hotel_info.phone|escape:'htmlall':'UTF-8'}"{/if}/>
			</div>
		</div>
		<div class="form-group">	
			<label class="col-lg-3 control-label required">{l s='Email :' mod='hotelreservationsystem'}</label>
			<div class="col-sm-6">
				<div class="input-group">
					<span class="input-group-addon">
						<i class="icon-envelope-o"></i>
					</span>
					<input class="reg_sel_input form-control-static" type="text" name="email" id="hotel_email"  {if isset($edit)}value="{$hotel_info.email}"{/if}/>
				</div>
			</div>
		</div>
		<div class="form-group">
			<label class="col-sm-3 control-label required">{l s='Address :' mod='hotelreservationsystem'}</label>
			<div class="col-sm-6">
				<textarea name="address" rows="4" cols="35" >{if isset($edit)}{$hotel_info.address|escape:'htmlall':'UTF-8'}{/if}</textarea>
			</div>
		</div>
		<div class="form-group">
			<label class="control-label col-sm-3 required" for="hotel_country">{l s='Rating :'}</label>
			<div class="col-sm-6">
				<div style="width: 195px;">
					<select class="form-control" name="hotel_rating" id="hotel_rating" value="">
						<option value="" selected="selected">No Star</option>
						<option value="1" {if isset($edit)} {if $hotel_info['rating'] == 1}selected{/if}{/if}>*</option>
						<option value="2" {if isset($edit)} {if $hotel_info['rating'] == 2}selected{/if}{/if}>**</option>
						<option value="3" {if isset($edit)} {if $hotel_info['rating'] == 3}selected{/if}{/if}>***</option>
						<option value="4" {if isset($edit)} {if $hotel_info['rating'] == 4}selected{/if}{/if}>****</option>
						<option value="5" {if isset($edit)} {if $hotel_info['rating'] == 5}selected{/if}{/if}>*****</option>
					</select>
				</div>
			</div>
		</div>
		<div class="form-group check_in_div" style="position:relative">
			<label class="col-sm-3 control-label required" for="check_in_time">
				{l s='Check In :' mod='hotelreservationsystem'}
			</label>
			<div class="col-sm-2">
				<input autocomplete="off" type="text" class="form-control" id="check_in_time" name="check_in" {if isset($edit)}value="{$hotel_info.check_in|escape:'htmlall':'UTF-8'}"{/if} />
			</div>
		</div>
		<div class="form-group check_out_div" style="position:relative">
			<label class="col-sm-3 control-label required" for="check_out_time">
				{l s='Check Out :' mod='hotelreservationsystem'}
			</label>
			<div class="col-sm-2">
				<input autocomplete="off" type="text" class="form-control" id="check_out_time" name="check_out" {if isset($edit)}value="{$hotel_info.check_out|escape:'htmlall':'UTF-8'}"{/if} />
			</div>
		</div>
		<div class="form-group">
			<label class="control-label col-sm-3 required" for="hotel_country">{l s='Country :' mod='hotelreservationsystem'}</label>
			<div class="col-sm-6">
				<div style="width: 195px;">
					<select class="form-control" name="hotel_country" id="hotel_country" value="">
						<option value="0" selected="selected">{l s='Choose your Country' mod='hotelreservationsystem'} </option>
						{if $country_var}
							{foreach $country_var as $countr}
								<option value="{$countr['id_country']}" {if isset($edit)} {if $hotel_info['country_id'] == "{$countr['id_country']}"}selected{/if}{/if}> {$countr['name']}</option>
							{/foreach}
						{/if}
					</select>
				</div>
			</div>
		</div>
		<div class="form-group">
			<label class="control-label col-sm-3 required hotel_state_lbl" for="hotel_state" {if isset($edit) && !$state_var}style="display:none;"{/if}>{l s='State :' mod='hotelreservationsystem'}</label>
			<div class="col-sm-6 hotel_state_dv"  {if isset($edit) && !$state_var}style="display:none;"{/if}>
				<div style="width: 195px;">
					<select class="form-control" name="hotel_state" id="hotel_state">
					{if isset($edit)}
						{if $state_var}
							{foreach $state_var as $state}
								<option value="{$state['id']}" {if isset($edit)} {if $hotel_info['state_id'] == "{$state['id']}"}selected{/if}{/if}> {$state['name']}</option>
							{/foreach}
						{/if}
					{else}
						<option value="0" selected="selected">{l s='Choose Country First' mod='hotelreservationsystem'}</option>
					{/if}
					</select>
				</div>
			</div>
			<span class="country_import_note col-sm-10 text-right" style='font-style:italic;'>{l s='* If selected country is not imported already, Please import selected country from localization in you prestashop To get its states.' js=1 mod='hotelreservationsystem'}</span>
		</div>
		<div class="form-group">
			<label class="control-label col-sm-3 required" for="hotel_city">{l s='City :' mod='hotelreservationsystem'}</label>
			<div class="col-sm-6">
				<input class="form-control" type="" data-validate="" id="hotel_city" name="hotel_city" {if isset($edit)}value="{$hotel_info.city|escape:'htmlall':'UTF-8'}"{/if} />
			</div>	
		</div>
		<div class="form-group">
			<label class="control-label col-sm-3 required" for="hotel_postal_code">{l s='Zip Code :' mod='hotelreservationsystem'}</label>
			<div class="col-sm-6">
				<input class="form-control" type="" data-validate="" id="hotel_postal_code" name="hotel_postal_code" {if isset($edit)}value="{$hotel_info.zipcode|escape:'htmlall':'UTF-8'}"{/if} />
			</div>	
		</div>
		<div class="form-group">
			<label class="col-sm-3 control-label">{l s='Hotel Policies :' mod='hotelreservationsystem'}</label>
			<div class="col-sm-6">
				<textarea name="hotel_policies" class="hotel_policies wk_tinymce" rows="4" cols="35" >{if isset($edit)}{$hotel_info.policies|escape:'htmlall':'UTF-8'}{/if}</textarea>
			</div>
		</div>
		<div class="form-group">  
			<div id="upload_hotel_images" class="sell_row">
				<label class="col-sm-3 control-label">{l s='Hotel Image :' mod='hotelreservationsystem'}</label>
				<div class="col-sm-6">
					<input type="file" name="hotel_image"/>
				</div>
			</div>
		</div>
		<div class="form-group">
			<label class="col-sm-3 control-label" for="other_image">
			</label>
			<div class="col-sm-6">
				<a class="btn btn-default hotel-other-img">
					<i class="icon-image"></i>
					<span>{l s='Add More Images :' mod='hotelreservationsystem'}</span>
				</a>
				<div id="htl_other_images"></div>
			</div>
		</div>
		<div class="panel-footer">
			<a href="{$link->getAdminLink('AdminAddHotel')|escape:'html':'UTF-8'}" class="btn btn-default">
				<i class="process-icon-cancel"></i>{l s='Cancel' mod='hotelreservationsystem'}
			</a>
			<button type="submit" name="submitAddhotel_branch_info" class="btn btn-default pull-right">
				<i class="process-icon-save"></i> {l s='Save' mod='hotelreservationsystem'}
			</button>
			<button type="submit" name="submitAdd{$table|escape:'html':'UTF-8'}AndStay" class="btn btn-default pull-right">
				<i class="process-icon-save"></i> {l s='Save and stay' mod='hotelreservationsystem'}
			</button>
		</div>
	</form>
</div>
{strip}
	{addJsDef statebycountryurl=$link->getAdminLink('AdminAddHotel') mod='hotelreservationsystem'}
	{addJsDefL name=image_remove}{l s='Remove' js=1 mod='hotelreservationsystem'}{/addJsDefL}
{/strip}

{block name=script}
<script type="text/javascript">
	// for tiny mce setup
	var iso = "{$iso|escape:'htmlall':'UTF-8'}";
	var pathCSS = "{$smarty.const._THEME_CSS_DIR_|escape:'htmlall':'UTF-8'}";
	var ad = "{$ad|escape:'htmlall':'UTF-8'}";
	$(document).ready(function(){
		{block name="autoload_tinyMCE"}
			tinySetup({
				editor_selector :"wk_tinymce",
				width : 700
			});
		{/block}
	});
</script>
{/block}