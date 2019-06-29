<div class="panel">
	<div class="panel-heading">
		{if isset($edit)}
			<i class='icon-pencil'></i>&nbsp{l s='Edit Hotel' mod='hotelreservationsystem'}
		{else}
			<i class='icon-plus'></i>&nbsp{l s='Add New Hotel' mod='hotelreservationsystem'}
		{/if}
	</div>
	<form id="{$table|escape:'htmlall':'UTF-8'}_form" class="defaultForm {$name_controller|escape:'htmlall':'UTF-8'} form-horizontal" action="{$current|escape:'htmlall':'UTF-8'}&{if !empty($submit_action)}{$submit_action|escape:'htmlall':'UTF-8'}{/if}&token={$token|escape:'htmlall':'UTF-8'}" method="post" enctype="multipart/form-data" {if isset($style)}style="{$style|escape:'htmlall':'UTF-8'}"{/if}>
		{if count($languages) > 1}
			<div class="col-lg-12">
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

		<div class="tabs wk-tabs-panel">
			<ul class="nav nav-tabs">
				<li class="active">
					<a href="#hotel-information" data-toggle="tab">
						<i class="icon-info-sign"></i>
						{l s='Information' mod='hotelreservationsystem'}
					</a>
				</li>
				<li>
					<a href="#hotel-images" data-toggle="tab">
						<i class="icon-image"></i>
						{l s='Images' mod='hotelreservationsystem'}
					</a>
				</li>
			</ul>
			<div class="tab-content panel collapse in">
				<div class="tab-pane active" id="hotel-information">
					{if isset($edit)}
						<input id="id-hotel" type="hidden" value="{$hotel_info.id|escape:'html':'UTF-8'}" name="id" />
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
							{include file="../../../_partials/htl-form-fields-flag.tpl"}
						</label>
						<div class="col-lg-6">
							{foreach from=$languages item=language}
								{assign var="hotel_name" value="hotel_name_`$language.id_lang`"}
								<input type="text"
								id="hotel_name_{$language.id_lang}"
								name="hotel_name_{$language.id_lang}"
								value="{if isset($smarty.post.$hotel_name)}{$smarty.post.$hotel_name|escape:'htmlall':'UTF-8'}{elseif isset($edit)}{$hotel_info.hotel_name[{$language.id_lang}]|escape:'htmlall':'UTF-8'}{/if}"
								class="form-control wk_text_field_all wk_text_field_{$language.id_lang}"
								maxlength="128"
								{if $currentLang.id_lang != $language.id_lang}style="display:none;"{/if} />
							{/foreach}
						</div>
					</div>
					<div class="form-group">
						<label class="col-sm-3 control-label">
							{l s='Short Description :' mod='hotelreservationsystem'}
							{include file="../../../_partials/htl-form-fields-flag.tpl"}
						</label>
						<div class="col-lg-6">
							{foreach from=$languages item=language}
								{assign var="short_desc_name" value="short_description_`$language.id_lang`"}
								<div id="short_desc_div_{$language.id_lang}" class="wk_text_field_all wk_text_field_{$language.id_lang}" {if $currentLang.id_lang != $language.id_lang}style="display:none;"{/if}>
									<textarea
									name="short_description_{$language.id_lang}"
									id="short_description_{$language.id_lang}"
									cols="2" rows="3"
									class="wk_tinymce form-control">{if isset($smarty.post.$short_desc_name)}{$smarty.post.$short_desc_name}{elseif isset($edit)}{$hotel_info.short_description[{$language.id_lang}]}{/if}</textarea>
								</div>
							{/foreach}
						</div>
					</div>
					<div class="form-group">
						<label class="col-sm-3 control-label">
							{l s='Description :' mod='hotelreservationsystem'}
							{include file="../../../_partials/htl-form-fields-flag.tpl"}
						</label>
						<div class="col-lg-6">
							{foreach from=$languages item=language}
								{assign var="description" value="description_`$language.id_lang`"}
								<div id="description_div_{$language.id_lang}" class="wk_text_field_all wk_text_field_{$language.id_lang}" {if $currentLang.id_lang != $language.id_lang}style="display:none;"{/if}>
									<textarea
									name="description_{$language.id_lang}"
									id="description_{$language.id_lang}"
									cols="2" rows="3"
									class="wk_tinymce form-control">{if isset($smarty.post.$description)}{$smarty.post.$description}{elseif isset($edit)}{$hotel_info.description[{$language.id_lang}]}{/if}</textarea>
								</div>
							{/foreach}
						</div>
					</div>
					<div class="form-group">
						<label class="col-sm-3 control-label required">{l s='Phone :' mod='hotelreservationsystem'}</label>
						<div class="col-sm-6">
							<input type="text" name="phone" id="phone" {if isset($edit)}value="{$hotel_info.phone|escape:'htmlall':'UTF-8'}"{/if}/>
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
						<span class="country_import_note col-sm-offset-3 col-sm-9">
							<em>
								{l s='* If selected country is not imported, then please import selected country from' mod='hotelreservationsystem'}<a href="{$link->getAdminLink('AdminLocalization')|escape:'html':'UTF-8'}"> {l s='localization tab' mod='hotelreservationsystem'} </a>{l s='in you qloapps to get its states.' mod='hotelreservationsystem'}
							</em>
						</span>


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
						<label class="col-sm-3 control-label">
							{l s='Hotel Policies :' mod='hotelreservationsystem'}
							{include file="../../../_partials/htl-form-fields-flag.tpl"}
						</label>
						<div class="col-lg-6">
							{foreach from=$languages item=language}
								{assign var="policies" value="policies_`$language.id_lang`"}
								<div id="policies_div_{$language.id_lang}" class="wk_text_field_all wk_text_field_{$language.id_lang}" {if $currentLang.id_lang != $language.id_lang}style="display:none;"{/if}>
									<textarea
									name="policies_{$language.id_lang}"
									id="policies_{$language.id_lang}"
									cols="2" rows="3"
									class="wk_tinymce form-control">{if isset($smarty.post.$policies)}{$smarty.post.$policies}{elseif isset($edit)}{$hotel_info.policies[{$language.id_lang}]}{/if}</textarea>
								</div>
							{/foreach}
						</div>
					</div>
					{if isset($enabledDisplayMap) && $enabledDisplayMap}
						<div class="form-group">
							<label class="col-sm-3 control-label">{l s='Map :' mod='hotelreservationsystem'}</label>
							<div class="col-sm-6" id="googleMapContainer">
								<input type="hidden" id="loclatitude" name="loclatitude" value="{if isset($edit)}{$hotel_info.latitude|escape:'htmlall':'UTF-8'}{/if}" />
								<input type="hidden" id="loclongitude" name="loclongitude" value="{if isset($edit)}{$hotel_info.longitude|escape:'htmlall':'UTF-8'}{/if}" />
								<input type="hidden" id="locformatedAddr" name="locformatedAddr" value="{if isset($edit)}{$hotel_info.map_formated_address}{/if}" />
								<input type="hidden" id="googleInputField" name="googleInputField" value="{if isset($edit)}{$hotel_info.map_input_text}{/if}" />
								<input id="pac-input" class="controls" type="text" placeholder="Enter a location">
								<div id="map"></div>
							</div>
						</div>
					{/if}
				</div>
				<div class="tab-pane" id="hotel-images">
					{if isset($hotel_info.id) && $hotel_info.id}
						<div class="form-group row">
							<label for="hotel_images" class="col-sm-3 control-label padding-top-0">
								{l s='Upload images' mod='hotelreservationsystem'}&nbsp;:&nbsp;&nbsp;
							</label>
							<div class="col-sm-5">
								<input class="form-control-static" type="file" id="hotel_images" name="hotel_images[]" multiple>
							</div>
						</div>
						<hr>
						{* Image table *}
						<h4><i class="icon-image"></i> <span>{l s='Hotel Images' mod='hotelreservationsystem'}</span></h4>
						<div class="row">
							<div class="col-sm-12">
								<div class="table-responsive">
									<table class="table" id="hotel-image-table">
										<thead>
											<tr>
												<th class="text-center">{l s='Image Id' mod='hotelreservationsystem'}</th>
												<th class="text-center">{l s='Image' mod='hotelreservationsystem'}</th>
												<th class="text-center">{l s='Cover' mod='hotelreservationsystem'}</th>
												<th class="text-center">{l s='Action' mod='hotelreservationsystem'}</th>
											</tr>
										</thead>
										<tbody>
											{if isset($hotelImages) && $hotelImages}
												{foreach from=$hotelImages item=image name=hotelImage}
													<tr class="{if $image.cover == 1}cover-image-tr{/if}">
														<td class="text-center">{$image.id|escape:'html':'UTF-8'}</td>
														<td class="text-center">
															<a class="htl-img-preview" href="{$image.image_link|escape:'html':'UTF-8'}">
																<img class="img-thumbnail" width="100" src="{$image.image_link|escape:'html':'UTF-8'}"/>
															</a>
														</td>
														<td class="text-center {if $image.cover == 1}cover-image-td{/if}">
															<a href="#" class="{if $image.cover == 1}text-success{else}text-danger{/if} changer-cover-image" data-id-hotel="{$hotel_info.id|escape:'html':'UTF-8'}" data-is-cover="{if $image.cover == 1}1{else}0{/if}" data-id-image="{$image.id|escape:'html':'UTF-8'}">
																{if $image.cover == 1}
																	<i class="icon-check"></i>
																{else}
																	<i class="icon-times"></i>
																{/if}
															</a>
														</td>
														<td class="text-center">
															<button type="button" class="btn btn-default delete-hotel-image" data-id-hotel="{$hotel_info.id|escape:'html':'UTF-8'}" data-is-cover="{if $image.cover == 1}1{else}0{/if}" data-id-image="{$image.id|escape:'html':'UTF-8'}"><i class="icon-trash"></i></button>
														</td>
													</tr>
												{/foreach}
											{else}
												<tr class="list-empty-tr">
													<td class="list-empty" colspan="5">
														<div class="list-empty-msg">
															<i class="icon-warning-sign list-empty-icon"></i>
															{l s='No Image Found' mod='hotelreservationsystem'}
														</div>
													</td>
												</tr>
											{/if}
										</tbody>
									</table>
								</div>
							</div>
						</div>
					{else}
						<div class="alert alert-warning">
							{l s='Please save the hotel information before saving the hotel images.' mod='hotelreservationsystem'}
						</div>
					{/if}
				</div>
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
	{addJsDef adminHotelCtrlUrl = $link->getAdminlink('AdminAddHotel')}
		{addJsDefL name=imgUploadSuccessMsg}{l s='Image Successfully Uploaded' js=1 mod='hotelreservationsystem'}{/addJsDefL}
	{addJsDefL name=imgUploadErrorMsg}{l s='Something went wrong while uploading images. Please try again later !!' js=1 mod='hotelreservationsystem'}{/addJsDefL}

	{addJsDefL name=coverImgSuccessMsg}{l s='Cover image changed successfully' js=1 mod='hotelreservationsystem'}{/addJsDefL}
	{addJsDefL name=coverImgErrorMsg}{l s='Error while changing cover image' js=1 mod='hotelreservationsystem'}{/addJsDefL}

	{addJsDefL name=deleteImgSuccessMsg}{l s='Image deleted successfully' js=1 mod='hotelreservationsystem'}{/addJsDefL}
	{addJsDefL name=deleteImgErrorMsg}{l s='Something went wrong while deleteing image. Please try again later !!' js=1 mod='hotelreservationsystem'}{/addJsDefL}

	{addJsDef enabledDisplayMap = $enabledDisplayMap}
	{addJsDef defaultCountry = $defaultCountry}
	{addJsDef statebycountryurl = $link->getAdminLink('AdminAddHotel')}
	{addJsDefL name=htlImgDeleteSuccessMsg}{l s='Image removed successfully.' js=1 mod='hotelreservationsystem'}{/addJsDefL}
	{addJsDefL name=htlImgDeleteErrMsg}{l s='Some error occurred while deleting hotel image.' js=1 mod='hotelreservationsystem'}{/addJsDefL}
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