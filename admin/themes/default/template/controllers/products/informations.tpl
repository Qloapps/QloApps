{*
* 2007-2017 PrestaShop
*
* NOTICE OF LICENSE
*
* This source file is subject to the Academic Free License (AFL 3.0)
* that is bundled with this package in the file LICENSE.txt.
* It is also available through the world-wide-web at this URL:
* http://opensource.org/licenses/afl-3.0.php
* If you did not receive a copy of the license and are unable to
* obtain it through the world-wide-web, please send an email
* to license@prestashop.com so we can send you a copy immediately.
*
* DISCLAIMER
*
* Do not edit or add to this file if you wish to upgrade PrestaShop to newer
* versions in the future. If you wish to customize PrestaShop for your
* needs please refer to http://www.prestashop.com for more information.
*
*  @author PrestaShop SA <contact@prestashop.com>
*  @copyright  2007-2017 PrestaShop SA
*  @license    http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*}
{if $check_product_association_ajax}
	{assign var=class_input_ajax value='check_product_name '}
{else}
	{assign var=class_input_ajax value=''}
{/if}

<div id="product-informations" class="panel product-tab">
	<input type="hidden" name="submitted_tabs[]" value="Informations" />
	<h3 class="tab"> <i class="icon-info"></i> {l s='Information'}</h3>
	<script type="text/javascript">

		var msg_select_one = "{l s='Please select at least one room type.' js=1}";
		var msg_set_quantity = "{l s='Please set a quantity to add a room type.' js=1}";

		{if isset($ps_force_friendly_product) && $ps_force_friendly_product}
			var ps_force_friendly_product = 1;
		{else}
			var ps_force_friendly_product = 0;
		{/if}
		{if isset($PS_ALLOW_ACCENTED_CHARS_URL) && $PS_ALLOW_ACCENTED_CHARS_URL}
			var PS_ALLOW_ACCENTED_CHARS_URL = 1;
		{else}
			var PS_ALLOW_ACCENTED_CHARS_URL = 0;
		{/if}
		{$combinationImagesJs}
		{if $check_product_association_ajax}
				var search_term = '';
				$('document').ready( function() {
					$(".check_product_name")
						.autocomplete(
							'{$link->getAdminLink('AdminProducts', true)|addslashes}', {
								minChars: 3,
								max: 10,
								width: $(".check_product_name").width(),
								selectFirst: false,
								scroll: false,
								dataType: "json",
								formatItem: function(data, i, max, value, term) {
									search_term = term;
									// adding the little
									if ($('.ac_results').find('.separation').length == 0)
										$('.ac_results').css('background-color', '#EFEFEF')
											.prepend('<div style="color:#585A69; padding:2px 5px">{l s='Use a product from the list'}<div class="separation"></div></div>');
									return value;
								},
								parse: function(data) {
									var mytab = new Array();
									for (var i = 0; i < data.length; i++)
										mytab[mytab.length] = { data: data[i], value: data[i].name };
									return mytab;
								},
								extraParams: {
									ajax: 1,
									action: 'checkProductName',
									id_lang: {$id_lang}
								}
							}
						)
						.result(function(event, data, formatted) {
							// keep the searched term in the input
							$('#name_{$id_lang}').val(search_term);
							jConfirm('{l s='Do you want to use this product?'}&nbsp;<strong>'+data.name+'</strong>', '{l s='Confirmation'}', function(confirm){
								if (confirm == true)
									document.location.href = '{$link->getAdminLink('AdminProducts', true)}&updateproduct&id_product='+data.id_product;
								else
									return false;
							});
						});
				});
		{/if}
	</script>

	{if isset($display_common_field) && $display_common_field}
	<div class="alert alert-warning" style="display: block">{l s='Warning, if you change the value of fields with an orange bullet %s, the value will be changed for all other shops for this room type' sprintf=$bullet_common_field}</div>
	{/if}

	{include file="controllers/products/multishop/check_fields.tpl" product_tab="Informations"}

	<div class="form-group hidden">
		<label class="control-label col-lg-3" for="simple_product">
			{$bullet_common_field} {l s='Type'}
		</label>
		<div class="col-lg-9">
			<div class="radio">
				<label for="simple_product">
					<input type="radio" name="type_product" id="simple_product" value="{Product::PTYPE_SIMPLE}" {if $product_type == Product::PTYPE_SIMPLE}checked="checked"{/if}>
					{l s='Standard product'}</label>
			</div>
			<div class="radio">
				<label for="pack_product">
					<input type="radio" name="type_product" {if $is_in_pack}disabled="disabled"{/if} id="pack_product" value="{Product::PTYPE_PACK}" {if $product_type == Product::PTYPE_PACK}checked="checked"{/if}> {l s='Pack of existing products'}</label>
			</div>
			<div class="radio">
				<label for="virtual_product">
					<input type="radio" name="type_product" id="virtual_product" {if $is_in_pack}disabled="disabled"{/if} value="{Product::PTYPE_VIRTUAL}" {if $product_type == Product::PTYPE_VIRTUAL}checked="checked"{/if}>
					{l s='Virtual product (services, booking, downloadable products, etc.)'}</label>
			</div>
			<div class="row row-padding-top">
				<div id="warn_virtual_combinations" class="alert alert-warning" style="display:none">{l s='You cannot use combinations with a virtual product.'}</div>
				<div id="warn_pack_combinations" class="alert alert-warning" style="display:none">{l s='You cannot use combinations with a pack.'}</div>
			</div>
		</div>
	</div>

	<div id="product-pack-container" {if $product_type != Product::PTYPE_PACK}style="display:none"{/if}></div>

	{*<hr />*}
	<div class="form-group">
		<div class="col-lg-1"><span class="pull-right">{include file="controllers/products/multishop/checkbox.tpl" field="name" type="default" multilang="true"}</span></div>
		<label class="control-label col-lg-2 required" for="name_{$id_lang}">
			<span class="label-tooltip" data-toggle="tooltip" title="{l s='Write the name of the Room Type for ex. Delux, Executive etc.'} {l s='Invalid characters:'} &lt;&gt;;=#{}">
				{l s='Room Type'}
			</span>
		</label>
		<div class="col-lg-5">
			{include file="controllers/products/input_text_lang.tpl"
				languages=$languages
				input_class="{$class_input_ajax}{if !$product->id || Configuration::get('PS_FORCE_FRIENDLY_PRODUCT')}copy2friendlyUrl{/if} updateCurrentText"
				input_value=$product->name
				input_name="name"
				required=true
			}
		</div>
	</div>

	<div class="form-group hidden">
		<label class="control-label col-lg-3" for="reference">
			<span class="label-tooltip" data-toggle="tooltip"
			title="{l s='Your internal reference code for this room type.'} {l s='Allowed special characters:'} .-_#\">
				{$bullet_common_field} {l s='Reference code'}
			</span>
		</label>
		<div class="col-lg-5">
			<input type="text" id="reference" name="reference" value="{$product->reference|htmlentitiesUTF8}" />
		</div>
	</div>

	<div class="form-group hidden">
		<label class="control-label col-lg-3" for="ean13">
			<span class="label-tooltip" data-toggle="tooltip"
				title="{l s='This type of room type code is specific to Europe and Japan, but is widely used internationally. It is a superset of the UPC code: all products marked with an EAN will be accepted in North America.'}">
				{$bullet_common_field} {l s='EAN-13 or JAN barcode'}
			</span>
		</label>
		<div class="col-lg-3">
			<input maxlength="13" type="text" id="ean13" name="ean13" value="{$product->ean13|htmlentitiesUTF8}" />
		</div>
	</div>

	<div class="form-group hidden">
		<label class="control-label col-lg-3" for="upc">
			<span class="label-tooltip" data-toggle="tooltip"
				title="{l s='This type of room type code is widely used in the United States, Canada, the United Kingdom, Australia, New Zealand and in other countries.'}">
				{$bullet_common_field} {l s='UPC barcode'}
			</span>
		</label>
		<div class="col-lg-3">
			<input maxlength="12" type="text" id="upc" name="upc" value="{$product->upc|escape:'html':'UTF-8'}" />
		</div>
	</div>

	<!-- <hr/> -->

	{* status informations *}
	<div class="form-group">
		<div class="col-lg-1"><span class="pull-right">{include file="controllers/products/multishop/checkbox.tpl" field="active" type="radio" onclick=""}</span></div>
		<label class="control-label col-lg-2">
			{l s='Enabled'}
		</label>
		<div class="col-lg-9">
			<span class="switch prestashop-switch fixed-width-lg">
				<input onclick="toggleDraftWarning(false);showOptions(true);showRedirectProductOptions(false);" type="radio" name="active" id="active_on" value="1" {if $product->active || !$product->isAssociatedToShop()}checked="checked" {/if} />
				<label for="active_on" class="radioCheck">
					{l s='Yes'}
				</label>
				<input onclick="toggleDraftWarning(true);showOptions(false);showRedirectProductOptions(true);"  type="radio" name="active" id="active_off" value="0" {if !$product->active && $product->isAssociatedToShop()}checked="checked"{/if} />
				<label for="active_off" class="radioCheck">
					{l s='No'}
				</label>
				<a class="slide-button btn"></a>
			</span>
		</div>
	</div>

	<div class="form-group redirect_product_options" style="display:none">
		<div class="col-lg-1"><span class="pull-right">{include file="controllers/products/multishop/checkbox.tpl" field="redirect_type" type="radio" onclick=""}</span></div>
		<label class="control-label col-lg-2" for="redirect_type">
			{l s='Redirect when disabled'}
		</label>
		<div class="col-lg-5">
			<select name="redirect_type" id="redirect_type">
				<option value="404" {if $product->redirect_type == '404'} selected="selected" {/if}>{l s='No redirect (404)'}</option>
				<option value="301" {if $product->redirect_type == '301'} selected="selected" {/if}>{l s='Redirected permanently (301)'}</option>
				<option value="302" {if $product->redirect_type == '302'} selected="selected" {/if}>{l s='Redirected temporarily (302)'}</option>
			</select>
		</div>
	</div>
	<div class="form-group redirect_product_options" style="display:none">
		<div class="col-lg-9 col-lg-offset-3">
			<div class="alert alert-info">
				{l s='404 Not Found = Do not redirect and display a 404 page.'}<br/>
				{l s='301 Moved Permanently = Permanently display another room type instead.'}<br/>
				{l s='302 Moved Temporarily = Temporarily display another room type instead.'}
			</div>
		</div>
	</div>

	<div class="form-group redirect_product_options redirect_product_options_product_choise" style="display:none">
		<div class="col-lg-1"><span class="pull-right">{include file="controllers/products/multishop/checkbox.tpl" field="id_product_redirected" type="radio" onclick=""}</span></div>
		<label class="control-label col-lg-2" for="related_product_autocomplete_input">
			{l s='Related product:'}
		</label>
		<div class="col-lg-7">
			<input type="hidden" value="" name="id_product_redirected" />

			<div class="input-group">
				<input type="text" id="related_product_autocomplete_input" name="related_product_autocomplete_input" autocomplete="off" class="ac_input" />
				<span class="input-group-addon"><i class="icon-search"></i></span>
			</div>

			<div class="form-control-static">
				<span id="related_product_name"><i class="icon-warning-sign"></i>&nbsp;{l s='No related product.'}</span>
				<span id="related_product_remove" style="display:none">
					<a class="btn btn-default" href="#" onclick="removeRelatedProduct(); return false" id="related_product_remove_link">
						<i class="icon-remove text-danger"></i>
					</a>
				</span>
			</div>

		</div>
		<script>
			var no_related_product = '{l s='No related product'}';
			var id_product_redirected = {$product->id_product_redirected|intval};
			var product_name_redirected = '{$product_name_redirected|escape:'html':'UTF-8'}';
		</script>
	</div>
	<div class="form-group hidden">
		<div class="col-lg-1"><span class="pull-right">{include file="controllers/products/multishop/checkbox.tpl" field="visibility" type="default"}</span></div>
		<label class="control-label col-lg-2" for="visibility">
			{l s='Visibility'}
		</label>
		<div class="col-lg-3">
			<select name="visibility" id="visibility">
				<option value="both" {if $product->visibility == 'both'}selected="selected"{/if} >{l s='Everywhere'}</option>
				<option value="catalog" {if $product->visibility == 'catalog'}selected="selected"{/if} >{l s='Catalog only'}</option>
				<option value="search" {if $product->visibility == 'search'}selected="selected"{/if} >{l s='Search only'}</option>
				<option value="none" {if $product->visibility == 'none'}selected="selected"{/if}>{l s='Nowhere'}</option>
			</select>
		</div>
	</div>

	<div id="product_options" class="form-group hidden">
		<div class="col-lg-12">
			<div class="form-group">
				<div class="col-lg-1">
					<span class="pull-right">
						{if isset($display_multishop_checkboxes) && $display_multishop_checkboxes}
							{include file="controllers/products/multishop/checkbox.tpl" only_checkbox="true" field="available_for_order" type="default"}
							{include file="controllers/products/multishop/checkbox.tpl" only_checkbox="true" field="show_price" type="show_price"}
							{include file="controllers/products/multishop/checkbox.tpl" only_checkbox="true" field="online_only" type="default"}
						{/if}
					</span>
				</div>
				<label class="control-label col-lg-2" for="available_for_order">
					{l s='Options'}
				</label>
				<div class="col-lg-9">
					<div class="checkbox">
						<label for="available_for_order">
							<input type="checkbox" name="available_for_order" id="available_for_order" value="1" {if $product->available_for_order}checked="checked"{/if} >
							{l s='Available for order'}</label>
					</div>
					<div class="checkbox">
						<label for="show_price">
							<input type="checkbox" name="show_price" id="show_price" value="1" {if $product->show_price}checked="checked"{/if} {if $product->available_for_order}disabled="disabled"{/if} >
							{l s='Show price'}</label>
					</div>
					<div class="checkbox">
						<label for="online_only">
							<input type="checkbox" name="online_only" id="online_only" value="1" {if $product->online_only}checked="checked"{/if} >
							{l s='Online only (not sold in your retail store)'}</label>
					</div>
				</div>
			</div>
			<div class="form-group">
				<div class="col-lg-1"><span class="pull-right">{include file="controllers/products/multishop/checkbox.tpl" field="condition" type="default"}</span></div>
				<label class="control-label col-lg-2" for="condition">
					{l s='Condition'}
				</label>
				<div class="col-lg-3">
					<select name="condition" id="condition">
						<option value="new" {if $product->condition == 'new'}selected="selected"{/if} >{l s='New'}</option>
						<option value="used" {if $product->condition == 'used'}selected="selected"{/if} >{l s='Used'}</option>
						<option value="refurbished" {if $product->condition == 'refurbished'}selected="selected"{/if}>{l s='Refurbished'}</option>
					</select>
				</div>
			</div>
		</div>
	</div>
	<hr/>

	<div class="form-group">
		<div class="col-lg-1"><span class="pull-right">{include file="controllers/products/multishop/checkbox.tpl" field="description_short" type="tinymce" multilang="true"}</span></div>
		<label class="control-label col-lg-2" for="description_short_{$id_lang}">
			<span class="label-tooltip" data-toggle="tooltip" title="{l s='Appears in the room type list(s), and at the top of the room type page.'}">
				{l s='Short description'}
			</span>
		</label>
		<div class="col-lg-9">
			{include
				file="controllers/products/textarea_lang.tpl"
				languages=$languages
				input_name='description_short'
				class="autoload_rte"
				input_value=$product->description_short
				max=$PS_PRODUCT_SHORT_DESC_LIMIT}
		</div>
	</div>
	<div class="form-group">
		<div class="col-lg-1"><span class="pull-right">{include file="controllers/products/multishop/checkbox.tpl" field="description" type="tinymce" multilang="true"}</span></div>
		<label class="control-label col-lg-2" for="description_{$id_lang}">
			<span class="label-tooltip" data-toggle="tooltip"
				title="{l s='Appears in the body of the room type.'}">
				{l s='Description'}
			</span>
		</label>
		<div class="col-lg-9">
			{include
				file="controllers/products/textarea_lang.tpl"
				languages=$languages input_name='description'
				class="autoload_rte"
				input_value=$product->description}
		</div>
	</div>
	{if $images}
	<div class="form-group">
		<div class="col-lg-9 col-lg-offset-3">
			<div class="alert alert-info">
				{capture}<a class="addImageDescription" href="javascript:void(0);">{l s='Click here'}</a>{/capture}
				{l s='Would you like to add an image in your description? %s and paste the given tag in the description.' sprintf=$smarty.capture.default}
			</div>
		</div>
	</div>
	<div id="createImageDescription" class="panel" style="display:none">
		<div class="form-group">
			<label class="control-label col-lg-3" for="smallImage_0">{l s='Select your image'}</label>
			<div class="col-lg-9">
				<ul class="list-inline">
					{foreach from=$images item=image key=key}
					<li>
						<input type="radio" name="smallImage" id="smallImage_{$key}" value="{$image.id_image}" {if $key == 0}checked="checked"{/if} >
						<label for="smallImage_{$key}" >
							<img src="{$image.src}" alt="{$image.legend}" />
						</label>
					</li>
					{/foreach}
				</ul>
			</div>
		</div>
		<div class="form-group">
			<label class="control-label col-lg-3" for="leftRight_1">{l s='Position'}</label>
			<div class="col-lg-5">
				<p class="checkbox">
					<input type="radio" name="leftRight" id="leftRight_1" value="left" checked>
					<label for="leftRight_1" >{l s='left'}</label>
				</p>
				<p class="checkbox">
					<input type="radio" name="leftRight" id="leftRight_2" value="right">
					<label for="leftRight_2" >{l s='right'}</label>
				</p>
			</div>
		</div>
		<div class="form-group">
			<label class="control-label col-lg-3" for="imageTypes_0">{l s='Select the type of picture'}</label>
			<div class="col-lg-5">
				{foreach from=$imagesTypes key=key item=type}
				<p class="checkbox">
					<input type="radio" name="imageTypes" id="imageTypes_{$key}" value="{$type.name}" {if $key == 0}checked="checked"{/if}>
					<label for="imageTypes_{$key}" >
						{$type.name} <span>{l s='%dpx by %dpx' sprintf=[$type.width, $type.height]}</span>
					</label>
				</p>
				{/foreach}
			</div>
		</div>
		<div class="form-group">
			<label class="control-label col-lg-3" for="resultImage">
				<span class="label-tooltip" data-toggle="tooltip"
				title="{l s='The tag to copy/paste into the description.'}">
					{l s='Image tag to insert'}
				</span>
			</label>
			<div class="col-lg-4">
				<input type="text" id="resultImage" name="resultImage" />
			</div>
			<p class="help-block"></p>
		</div>
	</div>
	{/if}

	<div class="form-group">
		<label class="control-label col-lg-3" for="tags_{$id_lang}">
			<span class="label-tooltip" data-toggle="tooltip"
				title="{l s='Will be displayed in the tags block when enabled. Tags help customers easily find your room types.'}">
				{l s='Tags:'}
			</span>
		</label>
		<div class="col-lg-9">
			{if $languages|count > 1}
			<div class="row">
			{/if}
				{foreach from=$languages item=language}
					{literal}
					<script type="text/javascript">
						$().ready(function () {
							var input_id = '{/literal}tags_{$language.id_lang}{literal}';
							$('#'+input_id).tagify({delimiters: [13,44], addTagPrompt: '{/literal}{l s='Add tag' js=1}{literal}'});
							$({/literal}'#{$table}{literal}_form').submit( function() {
								$(this).find('#'+input_id).val($('#'+input_id).tagify('serialize'));
							});
						});
					</script>
					{/literal}
				{if $languages|count > 1}
				<div class="translatable-field lang-{$language.id_lang}">
					<div class="col-lg-9">
				{/if}
						<input type="text" id="tags_{$language.id_lang}" class="tagify updateCurrentText" name="tags_{$language.id_lang}" value="{$product->getTags($language.id_lang, true)|htmlentitiesUTF8}" />
				{if $languages|count > 1}
					</div>
					<div class="col-lg-2">
						<button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown">
							{$language.iso_code}
							<span class="caret"></span>
						</button>
						<ul class="dropdown-menu">
							{foreach from=$languages item=language}
							<li>
								<a href="javascript:tabs_manager.allow_hide_other_languages = false;hideOtherLanguage({$language.id_lang});">{$language.name}</a>
							</li>
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
		<div class="col-lg-9 col-lg-offset-3">
			<div class="help-block">{l s='Each tag has to be followed by a comma. The following characters are forbidden: %s' sprintf='!&lt;;&gt;;?=+#&quot;&deg;{}_$%.'}
			</div>
		</div>
	</div>
	<div class="panel-footer">
		<a href="{$link->getAdminLink('AdminProducts')|escape:'html':'UTF-8'}{if isset($smarty.request.page) && $smarty.request.page > 1}&amp;submitFilterproduct={$smarty.request.page|intval}{/if}" class="btn btn-default"><i class="process-icon-cancel"></i> {l s='Cancel'}</a>
		<button type="submit" name="submitAddproduct" class="btn btn-default pull-right" disabled="disabled"><i class="process-icon-loading"></i> {l s='Save'}</button>
		<button type="submit" name="submitAddproductAndStay" class="btn btn-default pull-right" disabled="disabled"><i class="process-icon-loading"></i> {l s='Save and stay'}</button>
	</div>
</div>
<script type="text/javascript">
	hideOtherLanguage({$default_form_language});
	var missing_product_name = '{l s='Please fill room type name input field' js=1}';
</script>
