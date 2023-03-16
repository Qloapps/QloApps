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
							'{$link->getAdminLink('AdminNormalProducts', true)|addslashes}', {
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
									document.location.href = '{$link->getAdminLink('AdminNormalProducts', true)}&updateproduct&id_product='+data.id_product;
								else
									return false;
							});
						});
				});
		{/if}
	</script>

	{if isset($display_common_field) && $display_common_field}
	<div class="alert alert-warning" style="display: block">{l s='Warning, if you change the value of fields with an orange bullet %s, the value will be changed for all other shops for this product' sprintf=$bullet_common_field}</div>
	{/if}

	{include file="controllers/products/multishop/check_fields.tpl" product_tab="Informations"}

	<div id="product-pack-container" {if $product_type != Product::PTYPE_PACK}style="display:none"{/if}></div>

	{*<hr />*}
	<div class="form-group">
		<div class="col-lg-1"><span class="pull-right">{include file="controllers/products/multishop/checkbox.tpl" field="name" type="default" multilang="true"}</span></div>
		<label class="control-label col-lg-2 required" id="name" for="name_{$id_lang}">
			<span class="label-tooltip" data-toggle="tooltip" title="{l s='Write the name of the Product for ex. water bottle, etc.'} {l s='Invalid characters:'} &lt;&gt;;=#{}">
				{l s='Name'}
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
			title="{l s='Your internal reference code for this product.'} {l s='Allowed special characters:'} .-_#\">
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
				title="{l s='This type of product code is specific to Europe and Japan, but is widely used internationally. It is a superset of the UPC code: all products marked with an EAN will be accepted in North America.'}">
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
				title="{l s='This type of product code is widely used in the United States, Canada, the United Kingdom, Australia, New Zealand and in other countries.'}">
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
				{l s='301 Moved Permanently = Permanently display another product instead.'}<br/>
				{l s='302 Moved Temporarily = Temporarily display another product instead.'}
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
	<div class="form-group" id="associated_hotel_rooms_tree" {if $product->service_product_type == Product::SERVICE_PRODUCT_WITHOUT_ROOMTYPE}style="display:none;"{/if}>
		<div class="col-lg-1"><span class="pull-right">{include file="controllers/products/multishop/checkbox.tpl" field="category_box" type="category_box"}</span></div>
		<label class="control-label col-lg-2" for="category_block">
			<span class="label-tooltip" data-toggle="tooltip" title="{l s='Select room type and hotels for which this service will be available.'}">
				{l s='Associated Hotels and Room Types'}
			</span>
		</label>
		<div class="col-lg-9">
			<div id="category_block">
				{$hotel_tree}
			</div>
		</div>
	</div>
	<div class="form-group" id="auto_add_to_cart_container">
		<label class="control-label col-lg-3" for="">
			<span class="label-tooltip" data-toggle="tooltip" title="{l s='When enabled, this service will be added in cart for each associated Room type or Hotel when they are added in cart. Also auto added services will not be visible to customers.'}">
				{l s='Auto add to cart this product'}
			</span>
		</label>
		<div class="col-lg-3">
			<span class="switch prestashop-switch fixed-width-lg">
				<input type="radio" name="auto_add_to_cart" id="auto_add_to_cart_on" value="1" {if $product->auto_add_to_cart}checked="checked"{/if}/>
				<label for="auto_add_to_cart_on" class="radioCheck">
					{l s='Yes'}
				</label>
				<input type="radio" name="auto_add_to_cart" id="auto_add_to_cart_off" value="0" {if !$product->auto_add_to_cart || !$product->isAssociatedToShop()}checked="checked"{/if}/>
				<label for="auto_add_to_cart_off" class="radioCheck">
					{l s='No'}
				</label>
				<a class="slide-button btn"></a>
			</span>
		</div>
	</div>

	<div id="price_addition_type_container" {if !$product->auto_add_to_cart || !$product->isAssociatedToShop()}style="display:none;"{/if}>
		<div class="form-group">
			<label class="control-label col-lg-3" for="service_product_type">
				<span class="label-tooltip" data-toggle="tooltip" title="{l s='Select whether price will be added in the base room price or as Convenience Fee'}">
					{l s='Price display preference'}
				<span>
			</label>
			<div class="col-lg-4">
				<select name="price_addition_type" id="price_addition_type">
					<option value="{Product::PRICE_ADDITION_TYPE_WITH_ROOM}" {if $product->price_addition_type == Product::PRICE_ADDITION_TYPE_WITH_ROOM}selected="selected"{/if} >{l s='Add price in room price'}</option>
					<option value="{Product::PRICE_ADDITION_TYPE_INDEPENDENT}" {if $product->price_addition_type == Product::PRICE_ADDITION_TYPE_INDEPENDENT}selected="selected"{/if} >{l s='Add price as convenience Fee'}</option>
				</select>
			</div>
		</div>
		<div class="form-group">
			<div class="col-lg-8 col-lg-offset-3">
				<div class="alert alert-info">
					{l s='Select how this service price will be added in booking.'}
					<ul>
						<li>
							<b>{l s='Add price in room price'}</b> : {l s='Service price will be added in room base price.'}<br>{l s='(e.g., Room price : 500, service price: 50, final room price : 550)'}
						</li>
						<li><b>{l s='Add price as convenience fee'}</b> : {l s='Service price will be dispalyed in order summary as "Convenience Fees"'}</li>
					</ul>
				</div>
			</div>
		</div>
	</div>

	{* <div class="form-group" id="global_product_type_container">
		<label class="control-label col-lg-3" for="service_product_type">
			<span class="label-tooltip" data-toggle="tooltip" title="{l s='Select whether this product will be sold with room type or as an independent product'}">
				{l s='Product selling preference'}
			<span>
		</label>
		<div class="col-lg-4">
			<select name="service_product_type" id="service_product_type">
				<option value="{Product::SERVICE_PRODUCT_WITH_ROOMTYPE}" {if $product->service_product_type == Product::SERVICE_PRODUCT_WITH_ROOMTYPE}selected="selected"{/if} >{l s='sell with room type'}</option>
				<option value="{Product::SERVICE_PRODUCT_WITHOUT_ROOMTYPE}" {if $product->service_product_type == Product::SERVICE_PRODUCT_WITHOUT_ROOMTYPE}selected="selected"{/if} >{l s='Sell as independent product'}</option>
			</select>
		</div>
	</div> *}
	{* <div class="form-group" id="independent_product_info" {if $product->service_product_type != Product::SERVICE_PRODUCT_WITHOUT_ROOMTYPE}style="display:none"{/if}>
		<div class="col-lg-6 col-lg-offset-3">
			<div class="alert alert-info">
			{l s='Independent products can only be bought from backoffice.'}
			</div>
		</div>
	</div> *}
	<div class="form-group" id="show_at_front_container" {if $product->auto_add_to_cart}style="display:none;"{/if}>
		<label class="control-label col-lg-3" for="">
			<span class="label-tooltip" data-toggle="tooltip" title="{l s='Enable if you want this product to be visible at front office of your website.'}">
				{l s='Show at front office'}
			</span>
		</label>
		<div class="col-lg-3">
			<span class="switch prestashop-switch fixed-width-lg">
				<input type="radio" name="show_at_front" id="show_at_front_on" value="1" {if $product->show_at_front || !$product->isAssociatedToShop()}checked="checked"{/if}/>
				<label for="show_at_front_on" class="radioCheck">
					{l s='Yes'}
				</label>
				<input type="radio" name="show_at_front" id="show_at_front_off" value="0" {if !$product->show_at_front && $product->isAssociatedToShop()}checked="checked"{/if}/>
				<label for="show_at_front_off" class="radioCheck">
					{l s='No'}
				</label>
				<a class="slide-button btn"></a>
			</span>
		</div>
	</div>
	<div class="form-group" id="available_for_order_container" {if (!$product->show_at_front  && $product->isAssociatedToShop()) || $product->auto_add_to_cart}style="display:none;"{/if}>
		<label class="control-label col-lg-3" for="">
			<span class="label-tooltip" data-toggle="tooltip" title="{l s='Enable if you want this product to be sold from front office of your website.'}">
				{l s='Availabe for order'}
			</span>
		</label>
		<div class="col-lg-3">
			<span class="switch prestashop-switch fixed-width-lg">
				<input type="radio" name="available_for_order" id="available_for_order_on" value="1" {if $product->available_for_order || !$product->isAssociatedToShop()}checked="checked"{/if}/>
				<label for="available_for_order_on" class="radioCheck">
					{l s='Yes'}
				</label>
				<input type="radio" name="available_for_order" id="available_for_order_off" value="0" {if !$product->available_for_order && $product->isAssociatedToShop()}checked="checked"{/if}/>
				<label for="available_for_order_off" class="radioCheck">
					{l s='No'}
				</label>
				<a class="slide-button btn"></a>
			</span>
		</div>
	</div>
	<div class="form-group" id="show_price_container" {if $product->available_for_order || (!$product->show_at_front  && $product->isAssociatedToShop()) || !$product->isAssociatedToShop() || $product->auto_add_to_cart}style="display:none;"{/if}>
		<label class="control-label col-lg-3" for="">
			<span class="label-tooltip" data-toggle="tooltip" title="{l s='Enable if you want show product price even when product is not availabe to be sold at front office.'}">
				{l s='Show price'}
			</span>
		</label>
		<div class="col-lg-3">
			<span class="switch prestashop-switch fixed-width-lg">
				<input type="radio" name="show_price" id="show_price_on" value="1" {if $product->show_price || !$product->isAssociatedToShop()}checked="checked"{/if}/>
				<label for="show_price_on" class="radioCheck">
					{l s='Yes'}
				</label>
				<input type="radio" name="show_price" id="show_price_off" value="0" {if !$product->show_price && $product->isAssociatedToShop()}checked="checked"{/if}/>
				<label for="show_price_off" class="radioCheck">
					{l s='No'}
				</label>
				<a class="slide-button btn"></a>
			</span>
		</div>
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
				<option value="none" {if $product->visibility == 'none'}selected="selected"{/if} selected="selected">{l s='Nowhere'}</option>
			</select>
		</div>
	</div>

	<div id="allow_multiple_quantity_container" {if (!$product->available_for_order && $product->isAssociatedToShop()) || $product->auto_add_to_cart}style="display:none;"{/if}>
		<div class="form-group">
			<label class="control-label col-lg-3">
				<span class="label-tooltip" data-toggle="tooltip" title="{l s='When enabled, customer can order multiple quantity of product otherwise only one quantity can be purchased by customer per room/hotel'}">
					{l s='Allow ordering of multiple quantities'}
				</span>
			</label>
			<div class="col-lg-9">
				<span class="switch prestashop-switch fixed-width-lg">
					<input type="radio" name="allow_multiple_quantity" id="allow_multiple_quantity_on"{if $product->allow_multiple_quantity}checked="checked"{/if} value="1"/>
					<label for="allow_multiple_quantity_on" class="radioCheck">
						{l s='Yes'}
					</label>
					<input type="radio" name="allow_multiple_quantity" id="allow_multiple_quantity_off"{if !$product->allow_multiple_quantity}checked="checked"{/if} value="0"/>
					<label for="allow_multiple_quantity_off" class="radioCheck">
						{l s='No'}
					</label>
					<a class="slide-button btn"></a>
				</span>
			</div>
		</div>
		<div id="max_quantity_container" class="form-group" {if (!$product->available_for_order && $product->isAssociatedToShop()) || $product->auto_add_to_cart || !$product->allow_multiple_quantity}style="display:none;"{/if}>
			<label class="control-label col-lg-3" for="max_quantity">
				<span class="label-tooltip" data-toggle="tooltip"
					title="{l s='Enter max allowed quantity per room, enter 0 for unlimited.'}">
					{l s='Max quantity allow'}
				</span>
			</label>
			<div class="col-lg-3">
				<input type="text" id="max_quantity" name="max_quantity" value="{$product->max_quantity|escape:'html':'UTF-8'}" />
			</div>
		</div>
		<div class="form-group">
			<div class="col-lg-6 col-lg-offset-3">
				<div class="alert alert-info">
				{l s='By default all products have infinte quantity, Using this setting you can restrict customer to purchase only one product per room.'}
				</div>
			</div>
		</div>
	</div>


	<hr/>

	<div class="form-group">
		<div class="col-lg-1"><span class="pull-right">{include file="controllers/products/multishop/checkbox.tpl" field="description_short" type="tinymce" multilang="true"}</span></div>
		<label class="control-label col-lg-2" for="description_short_{$id_lang}">
			<span class="label-tooltip" data-toggle="tooltip" title="{l s='Appears in the product list(s), and at the top of the product page.'}">
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
	{* <div class="form-group">
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
	{/if}*}

	{* <div class="form-group">
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
	</div> *}
	<div class="panel-footer">
		<a href="{$link->getAdminLink('AdminNormalProducts')|escape:'html':'UTF-8'}{if isset($smarty.request.page) && $smarty.request.page > 1}&amp;submitFilterproduct={$smarty.request.page|intval}{/if}" class="btn btn-default"><i class="process-icon-cancel"></i> {l s='Cancel'}</a>
		<button type="submit" name="submitAddproduct" class="btn btn-default pull-right" disabled="disabled"><i class="process-icon-loading"></i> {l s='Save'}</button>
		<button type="submit" name="submitAddproductAndStay" class="btn btn-default pull-right" disabled="disabled"><i class="process-icon-loading"></i> {l s='Save and stay'}</button>
	</div>
</div>
<script type="text/javascript">
	hideOtherLanguage({$default_form_language});
	var missing_product_name = '{l s='Please fill product name input field' js=1}';
</script>
