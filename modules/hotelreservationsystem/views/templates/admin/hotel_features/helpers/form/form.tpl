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
		<i class="icon-plus"></i>&nbsp {l s='Add New Features' mod='hotelreservationsystem'}
	</div>
	<div class="panel-content">
		<form id="{$table|escape:'htmlall':'UTF-8'}_form" class="defaultForm {$name_controller|escape:'htmlall':'UTF-8'} form-horizontal" action="{$current|escape:'htmlall':'UTF-8'}&{if !empty($submit_action)}{$submit_action|escape:'htmlall':'UTF-8'}{/if}&token={$token|escape:'htmlall':'UTF-8'}" method="post" enctype="multipart/form-data">
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
				<div class="form-group row">
					<label class="col-sm-3 control-label" >
						{l s='Parent Feature Name' mod='hotelreservationsystem'}
						{include file="../../../_partials/mp-form-fields-flag.tpl"}
					</label>
					<div class="col-sm-4">
						{if isset($edit)}
							<input type="hidden" name="id" value="{$featureInfo.id}" />
						{/if}
						{foreach from=$languages item=language}
							{assign var="parent_ftr_name" value="parent_ftr_name_`$language.id_lang`"}
							<input type="text"
							id="parent_ftr_name_{$language.id_lang}"
							name="parent_ftr_name_{$language.id_lang}"
							value="{if isset($smarty.post.$parent_ftr_name)}{$smarty.post.$parent_ftr_name|escape:'htmlall':'UTF-8'}{elseif isset($edit)}{$featureInfo.name[{$language.id_lang}]|escape:'htmlall':'UTF-8'}{/if}"
							class="form-control wk_text_field_all wk_text_field_{$language.id_lang}"
							maxlength="128"
							{if $currentLang.id_lang != $language.id_lang}style="display:none;"{/if} />
						{/foreach}
					</div>
				</div>
				<div class="form-group row">
					<label class="col-sm-3 control-label">{l s='Position' mod='hotelreservationsystem'}</label>
					<div class="col-sm-4">
						<input type="text" name="position" class="position" placeholder="{l s='Feature position' mod='hotelreservationsystem'}" class="form-control" value="{if isset($smarty.post.position)}{$smarty.post.position|escape:'htmlall':'UTF-8'}{elseif isset($edit)}{$featureInfo.position|escape:'htmlall':'UTF-8'}{/if}"/>
						<p class="error_text" id="pos_err_p"></p>
					</div>
				</div>
				<div class="form-group row">
					<label class="col-sm-3 control-label">
						{l s='Child Features' mod='hotelreservationsystem'}
						{include file="../../../_partials/mp-form-fields-flag.tpl"}
					</label>
					<div class="col-sm-4">
						<input type="text" placeholder="Enter child feature name" class="child_ftr_name" name="child_ftr_name">
						<p class="error_text" id="chld_ftr_err_p"></p>
					</div>
					<div class="col-sm-4">
						<button type="button" class='col-sm-2 btn btn-primary add_feature_to_list'>{l s='Add' mod='hotelreservationsystem'}</button>
					</div>
				</div>
				<div class="added_child_features_container">
					{if isset($edit) && $edit && isset($featureInfo.child_features) && $featureInfo.child_features}
						{foreach from=$featureInfo.child_features item=child_feature}
							<div class="child_feature_row row">
								<label class="col-sm-3 control-label text-right">
								</label>
								<div class="col-sm-4">
									<input type="hidden" name="child_feature_id[]" value="{$child_feature.id}" />
									{foreach from=$languages item=language}
										<input type="text"
										value="{$child_feature.name[{$language.id_lang}]|escape:'htmlall':'UTF-8'}"
										name="child_features_{$language.id_lang}[]"
										class="form-control wk_text_field_all wk_text_field_{$language.id_lang}"
										maxlength="128"
										{if $currentLang.id_lang != $language.id_lang}style="display:none;"{/if} />
									{/foreach}
								</div>
								<div class="col-sm-4">
									<a href="#" class="remove-chld-ftr btn btn-default">
										<i class="icon-trash"></i>
									</a>
								</div>
							</div>
						{/foreach}
					{/if}
				</div>
			</div>
			<div class="panel-footer">
				<a href="{$link->getAdminLink('AdminHotelFeatures')|escape:'html':'UTF-8'}" class="btn btn-default">
					<i class="process-icon-cancel"></i>{l s='Cancel' mod='hotelreservationsystem'}
				</a>
				<button type="submit" name="submitHtlFeatures" class="btn btn-default pull-right submit_feature">
					<i class="process-icon-save"></i> {l s='Save' mod='hotelreservationsystem'}
				</button>
				<button type="submit" name="submitHtlFeaturesAndStay" class="btn btn-default pull-right submit_feature">
					<i class="process-icon-save"></i> {l s='Save and stay' mod='hotelreservationsystem'}
				</button>
			</div>
		</form>
	</div>
</div>

{strip}
	{addJsDefL name=prnt_ftr_err}{l s='Enter Parent feature name first.' js=1 mod='hotelreservationsystem'}{/addJsDefL}
	{addJsDefL name=chld_ftr_err}{l s='Enter at least one child feature.' js=1 mod='hotelreservationsystem'}{/addJsDefL}
	{addJsDefL name=chld_ftr_text_err}{l s='Enter child feature name.' js=1 mod='hotelreservationsystem'}{/addJsDefL}
	{addJsDefL name=pos_numeric_err}{l s='Position should be numeric.' js=1 mod='hotelreservationsystem'}{/addJsDefL}
{/strip}
