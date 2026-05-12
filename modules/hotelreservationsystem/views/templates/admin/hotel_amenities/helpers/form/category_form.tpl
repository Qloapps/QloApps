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
		{if isset($category.id) && $category.id}
			<i class="icon-edit"></i> {l s='Edit Amenity Category' mod='hotelreservationsystem'}
		{else}
			<i class="icon-plus"></i> {l s='Add New Amenity Category' mod='hotelreservationsystem'}
		{/if}
	</div>
	<div class="panel-body">
		<form id="htl_category_form" class="defaultForm form-horizontal"
			  action="{$current_index|escape:'htmlall':'UTF-8'}&token={$token|escape:'htmlall':'UTF-8'}"
			  method="post">

			{if isset($category.id) && $category.id}
				<input type="hidden" name="id" value="{$category.id|intval}" />
				<input type="hidden" name="updatehtl_amenity" value="1" />
			{else}
				<input type="hidden" name="addhtl_amenity" value="1" />
			{/if}

			<div class="form-group">
				<label class="col-lg-3 control-label required">
					{l s='Category Name' mod='hotelreservationsystem'}
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
							{assign var="cat_name_key" value="cat_name_`$language.id_lang`"}
					<input type="text"
								   id="cat_name_{$language.id_lang}"
								   name="cat_name_{$language.id_lang}"
								   value="{if isset($smarty.post.$cat_name_key)}{$smarty.post.$cat_name_key|escape:'htmlall':'UTF-8'}{elseif isset($category.name[$language.id_lang])}{$category.name[$language.id_lang]|escape:'htmlall':'UTF-8'}{/if}"
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
				<label class="col-lg-3 control-label required">
					{l s='Position' mod='hotelreservationsystem'}
				</label>
				<div class="col-lg-3">
					<input type="number" min="1" name="position" class="form-control"
						   value="{if isset($smarty.post.position)}{$smarty.post.position|intval}{elseif isset($category.position)}{$category.position|intval}{else}1{/if}" />
				</div>
			</div>

			<div class="panel-footer">
				<a href="{$admin_link|escape:'html':'UTF-8'}" class="btn btn-default">
					<i class="process-icon-cancel"></i> {l s='Cancel' mod='hotelreservationsystem'}
				</a>
				<button type="submit" name="submitHtlCategory" class="btn btn-default pull-right">
					<i class="process-icon-save"></i> {l s='Save' mod='hotelreservationsystem'}
				</button>
				<button type="submit" name="submitHtlCategoryAndStay" class="btn btn-default pull-right">
					<i class="process-icon-save"></i> {l s='Save and Stay' mod='hotelreservationsystem'}
				</button>
			</div>
		</form>
	</div>
</div>
