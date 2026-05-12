{**
* NOTICE OF LICENSE
*
* This source file is subject to the Open Software License version 3.0
* that is bundled with this package in the file LICENSE.md
* It is also available through the world-wide-web at this URL:
* https://opensource.org/license/osl-3-0-php
* If you did not receive a copy of the license and are unable to
* obtain it through the world-wide-web, please send an email
* to support@qloapps.com so we can send you a copy immediately.
*
* DISCLAIMER
*
* Do not edit or add to this file if you wish to upgrade this module to a newer
* versions in the future. If you wish to customize this module for your needs
* please refer to https://store.webkul.com/customisation-guidelines for more information.
*
* @author Webkul IN
* @copyright Since 2010 Webkul
* @license https://opensource.org/license/osl-3-0-php Open Software License version 3.0
*}

<div class="panel">
	<div class="panel-heading">
		{if isset($amenity.id) && $amenity.id}
			<i class="icon-edit"></i> {l s='Edit Amenity' mod='hotelreservationsystem'}
		{else}
			<i class="icon-plus"></i> {l s='Add New Amenity' mod='hotelreservationsystem'}
		{/if}
	</div>
	<div class="panel-body">
		<form id="htl_amenity_form" class="defaultForm form-horizontal"
			  action="{$current_index|escape:'htmlall':'UTF-8'}&token={$token|escape:'htmlall':'UTF-8'}"
			  method="post" enctype="multipart/form-data">

			<input type="hidden" name="id_category" value="{$id_category|intval}" />
			{if isset($amenity.id) && $amenity.id}
				<input type="hidden" name="id" value="{$amenity.id|intval}" />
				<input type="hidden" name="updatehtl_amenity_item" value="1" />
			{else}
				<input type="hidden" name="addhtl_amenity_item" value="1" />
			{/if}

			<div class="form-group">
				<label class="col-lg-3 control-label required">
					{l s='Amenity Name' mod='hotelreservationsystem'}
				</label>
				<div class="col-lg-9">
					{if $languages|count > 1}
					<div class="form-group">
					{/if}
					{foreach from=$languages item=language}
						{if $languages|count > 1}
						<div class="translatable-field lang-{$language.id_lang}" {if $language.id_lang != $default_lang_id}style="display:none;"{/if}>
							<div class="col-lg-9">
						{/if}
						{assign var="amenity_name_key" value="amenity_name_`$language.id_lang`"}
							<input type="text"
								   id="amenity_name_{$language.id_lang}"
								   name="amenity_name_{$language.id_lang}"
								   value="{if isset($smarty.post.$amenity_name_key)}{$smarty.post.$amenity_name_key|escape:'htmlall':'UTF-8'}{elseif isset($amenity.name[$language.id_lang])}{$amenity.name[$language.id_lang]|escape:'htmlall':'UTF-8'}{/if}"
								   class="form-control"
								   maxlength="128" />
						{if $languages|count > 1}
							</div>
							<div class="col-lg-2">
								<button type="button" class="btn btn-default dropdown-toggle" tabindex="-1" data-toggle="dropdown">
									{$language.iso_code}
									<i class="icon-caret-down"></i>
								</button>
								<ul class="dropdown-menu">
									{foreach from=$languages item=lang}
									<li><a href="javascript:hideOtherLanguage({$lang.id_lang});" tabindex="-1">{$lang.name}</a></li>
									{/foreach}
								</ul>
							</div>
						</div>
						{/if}
					{/foreach}
					{if $languages|count > 1}
					</div>
					{/if}
				</div>
			</div>

			<div class="form-group">
				<label class="col-lg-3 control-label">
					{l s='Enabled' mod='hotelreservationsystem'}
				</label>
				<div class="col-lg-9">
					<span class="switch prestashop-switch fixed-width-lg">
						<input type="radio" name="active" id="active_on" value="1"
							{if (isset($smarty.post.active) && $smarty.post.active == '1') || (!isset($smarty.post.active) && (!isset($amenity.active) || $amenity.active))}checked="checked"{/if} />
						<label for="active_on">{l s='Yes'}</label>
						<input type="radio" name="active" id="active_off" value="0"
							{if (isset($smarty.post.active) && $smarty.post.active == '0') || (!isset($smarty.post.active) && isset($amenity.active) && !$amenity.active)}checked="checked"{/if} />
						<label for="active_off">{l s='No'}</label>
						<a class="slide-button btn"></a>
					</span>
				</div>
			</div>

			<div class="form-group">
				<label class="col-lg-3 control-label">
					{l s='Feature this Amenity' mod='hotelreservationsystem'}
				</label>
				<div class="col-lg-9">
					<span class="switch prestashop-switch fixed-width-lg">
						<input type="radio" name="is_featured" id="is_featured_on" value="1"
							{if (isset($smarty.post.is_featured) && $smarty.post.is_featured == '1') || (!isset($smarty.post.is_featured) && isset($amenity.is_featured) && $amenity.is_featured)}checked="checked"{/if} />
						<label for="is_featured_on">{l s='Yes'}</label>
						<input type="radio" name="is_featured" id="is_featured_off" value="0"
							{if (isset($smarty.post.is_featured) && $smarty.post.is_featured == '0') || (!isset($smarty.post.is_featured) && (!isset($amenity.is_featured) || !$amenity.is_featured))}checked="checked"{/if} />
						<label for="is_featured_off">{l s='No'}</label>
						<a class="slide-button btn"></a>
					</span>
				</div>
			</div>

			<div class="form-group">
				<label class="col-lg-3 control-label required">{l s='Logo Type' mod='hotelreservationsystem'}</label>
				<div class="col-lg-4">
					<select name="logo_type" id="htl_logo_type_select" class="form-control">
						<option value="image"
							{if (isset($smarty.post.logo_type) && $smarty.post.logo_type != 'icon') || (!isset($smarty.post.logo_type) && (!isset($amenity.logo_type) || $amenity.logo_type != 'icon'))}selected="selected"{/if}>
							{l s='Image (Upload)' mod='hotelreservationsystem'}
						</option>
						<option value="icon"
							{if (isset($smarty.post.logo_type) && $smarty.post.logo_type == 'icon') || (!isset($smarty.post.logo_type) && isset($amenity.logo_type) && $amenity.logo_type == 'icon')}selected="selected"{/if}>
							{l s='Icon (Font Awesome)' mod='hotelreservationsystem'}
						</option>
					</select>
				</div>
			</div>

			<div class="form-group" id="htl_logo_image_row"
				 {if (isset($smarty.post.logo_type) && $smarty.post.logo_type == 'icon') || (!isset($smarty.post.logo_type) && isset($amenity.logo_type) && $amenity.logo_type == 'icon')}style="display:none;"{/if}>
				<label class="col-lg-3 control-label">{l s='Logo Image' mod='hotelreservationsystem'}</label>
				<div class="col-lg-5">
					{if $existing_img}
						<div style="margin-bottom:8px;">
							<img src="{$existing_img|escape:'htmlall':'UTF-8'}" alt=""
								 style="max-width:80px;max-height:80px;border:1px solid #ddd;padding:4px;" />
						</div>
					{/if}
					<input id="logo_image" type="file" name="logo_image" accept="image/*" class="hide" />
					<div class="dummyfile input-group">
						<span class="input-group-addon"><i class="icon-file"></i></span>
						<input id="logo_image-name" type="text" name="filename" class="form-control" readonly />
						<span class="input-group-btn">
							<button id="logo_image-selectbutton" type="button" class="btn btn-default">
								<i class="icon-folder-open"></i> {l s='Add file...' mod='hotelreservationsystem'}
							</button>
						</span>
					</div>
					<p class="help-block">
						{l s='Please upload a square image. Accepted formats: JPG, PNG, GIF.' mod='hotelreservationsystem'}
					</p>
				</div>
			</div>

			<div class="form-group" id="htl_logo_icon_row"
				 {if (isset($smarty.post.logo_type) && $smarty.post.logo_type != 'icon') || (!isset($smarty.post.logo_type) && (!isset($amenity.logo_type) || $amenity.logo_type != 'icon'))}style="display:none;"{/if}>
				<label class="col-lg-3 control-label">{l s='Icon Class' mod='hotelreservationsystem'}</label>
				<div class="col-lg-5">
					<input type="text" name="logo_icon" class="form-control"
						   value="{if isset($smarty.post.logo_icon)}{$smarty.post.logo_icon|escape:'htmlall':'UTF-8'}{elseif isset($amenity.logo) && isset($amenity.logo_type) && $amenity.logo_type == 'icon'}{$amenity.logo|escape:'htmlall':'UTF-8'}{/if}"
						   placeholder="icon-cogs" />
					<p class="help-block">
						{l s='Refer to the following link for available icons:' mod='hotelreservationsystem'}
						<a href="https://fontawesome.com/v4/cheatsheet" target="_blank">https://fontawesome.com/v4/cheatsheet</a><br />
						{l s='Note: Replace fa- with icon- in icon name. Example: fa-wifi becomes icon-wifi.' mod='hotelreservationsystem'}
					</p>
				</div>
			</div>

			<div class="panel-footer">
				<a href="{$admin_link|escape:'html':'UTF-8'}" class="btn btn-default">
					<i class="process-icon-cancel"></i> {l s='Cancel' mod='hotelreservationsystem'}
				</a>
				<button type="submit" name="submitHtlAmenityItem" class="btn btn-default pull-right">
					<i class="process-icon-save"></i> {l s='Save' mod='hotelreservationsystem'}
				</button>
				<button type="submit" name="submitHtlAmenityItemAndStay" class="btn btn-default pull-right">
					<i class="process-icon-save"></i> {l s='Save and Stay' mod='hotelreservationsystem'}
				</button>
			</div>
		</form>
	</div>
</div>

<script type="text/javascript">
$(document).ready(function () {
	$('#htl_logo_type_select').on('change', function () {
		if ($(this).val() === 'icon') {
			$('#htl_logo_image_row').hide();
			$('#htl_logo_icon_row').show();
		} else {
			$('#htl_logo_icon_row').hide();
			$('#htl_logo_image_row').show();
		}
	});

	$('#logo_image-selectbutton').on('click', function () {
		$('#logo_image').trigger('click');
	});

	$('#logo_image-name').on('click', function () {
		$('#logo_image').trigger('click');
	});

	$('#logo_image').on('change', function () {
		var files = this.files;
		if (files && files.length > 0) {
			$('#logo_image-name').val(files[0].name);
		} else {
			var name = $(this).val().split(/[\\/]/);
			$('#logo_image-name').val(name[name.length - 1]);
		}
	});
});
</script>
