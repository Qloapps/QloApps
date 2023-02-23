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

{extends file="helpers/form/form.tpl"}

{block name="autoload_tinyMCE"}
	// Execute when tab Informations has finished loading
	tabs_manager.onLoad('Informations', function(){
		tinySetup({
			editor_selector :"autoload_rte",
			setup : function(ed) {
				ed.on('init', function(ed)
				{
					if (typeof ProductMultishop.load_tinymce[ed.target.id] != 'undefined')
					{
						if (typeof ProductMultishop.load_tinymce[ed.target.id])
							tinyMCE.get(ed.target.id).hide();
						else
							tinyMCE.get(ed.target.id).show();
					}
				});

				ed.on('keydown', function(ed, e) {
					tinyMCE.triggerSave();
					textarea = $('#'+tinymce.activeEditor.id);
					var max = textarea.parent('div').find('span.counter').data('max');
					if (max != 'none')
					{
						count = tinyMCE.activeEditor.getBody().textContent.length;
						rest = max - count;
						if (rest < 0)
							textarea.parent('div').find('span.counter').html('<span style="color:red;">{l s='Maximum'} '+ max +' {l s='characters'} : '+rest+'</span>');
						else
							textarea.parent('div').find('span.counter').html(' ');
					}
				});
			}
		});
	});
{/block}

{block name="defaultForm"}
	<div class="row">
		<div class="productTabs col-lg-2 col-md-3">
			<div class="list-group">
			{foreach $product_tabs key=numStep item=tab}
				{if $tab.name != "Pack"}
					<a class="list-group-item {if $tab.selected}active{/if}" id="link-{$tab.id}" href="{$tab.href|escape:'html':'UTF-8'}&amp;updateproduct">{$tab.name}</a>
				{/if}
			{/foreach}
			</div>
		</div>
		<script type="text/javascript">
			var id_product = {if isset($product->id)}{$product->id|intval}{else}0{/if};
			var id_lang_default = {$id_lang_default|intval};
			var product_type_pack = {Product::PTYPE_PACK|intval};
			var product_type_virtual = {Product::PTYPE_VIRTUAL|intval};
			var product_type_simple = {Product::PTYPE_SIMPLE|intval};

			var with_room_type = {Product::SERVICE_PRODUCT_WITH_ROOMTYPE|intval};
			var without_room_type = {Product::SERVICE_PRODUCT_WITHOUT_ROOMTYPE|intval};


			var has_combinations = {$has_combinations|intval};

			var empty_pack_msg = '{l s='This pack is empty. You will need to add at least one product to the pack before you can save.' js=1}';
			var empty_name_msg = '{l s='The product name is empty. You will at least need to enter a name for the default language before you can save the product.' js=1}';
			var empty_link_rewrite_msg = '{l s='The friendly URL is empty. You will at least need to enter a friendly URL for the default language before you can save the product.' slashes=1}';
			var reload_tab_title = '{l s='Confirmation' slashes=1}';
			var reload_tab_description = '{l s='A server error occurred while loading the tabs: some tabs could not be loaded.' js=1}'+'\n'+'{l s='Please try again by refreshing the page.' js=1}'+'\n'+'{l s='If you are still encountering this problem, please check your server logs or contact your hosting provider for assistance.' js=1}';

			$('#product-tab-content-wait').show();
			var post_data = {$post_data};
			var save_error = {if $save_error}true{else}false{/if};
			var error_heading_msg = '{l s='Error' js=1}';
			var error_continue_msg = '{l s='Continue' js=1}';

			var product_type = {$product_type};
			{*var mce_maximum = '{l s='Maximum'}';*}
			{if isset($display_multishop_checkboxes) && $display_multishop_checkboxes}
				var display_multishop_checkboxes = true;
			{else}
				var display_multishop_checkboxes = false;
			{/if}

			var tabs_to_preload = new Array();
			var mod_evasive = {if isset($mod_evasive) && $mod_evasive}true{else}false{/if};
			var mod_security = {if isset($mod_security) && $mod_security}true{else}false{/if};

			$(document).ready(function()
			{
				{* if (product_type == product_type_pack)
				{
					$('a[id*="VirtualProduct"]').hide();
					$('a[id*="Combinations"]').hide();
				}
				else if (product_type == product_type_virtual)
				{
					$('#hotel_selection').show();
					$('a[id*="Quantities"]').hide();
					$('a[id*="Configuration"]').show();
					$('a[id*="Booking"]').show();
					$('a[id*="Occupancy"]').show();
					$('a[id*="ModuleHotelreservationsystem"]').show();
					$('a[id*="Pack"]').hide();
					$('a[id*="Shipping"]').hide();
					$('a[id*="Combinations"]').hide();
				}
				else
				{
					$('#hotel_selection').hide();
					$('a[id*="Quantities"]').show();
					$('a[id*="Configuration"]').hide();
					$('a[id*="Occupancy"]').hide();
					$('a[id*="Booking"]').hide();
					$('a[id*="ModuleHotelreservationsystem"]').hide();
					$('a[id*="Pack"]').hide();
					$('a[id*="VirtualProduct"]').hide();
				}
				updateTextInfo(product_type); *}

				{* $('#desc-product-newCombination').hide(); *}

				{* submenu binding *}
				$(".productTabs a").click(function(e){
					e.preventDefault();
					// currentId is the current product tab id
					currentId = false;
					if ($(".productTabs a.active").length)
						currentId = $(".productTabs a.active").attr('id').substr(5);
					// id is the wanted producttab id
					id = $(this).attr('id').substr(5);

					// Update submit button value
					var split_position = id.indexOf('-') + 1;
					var btn_name = id.substr(split_position);

					if ((btn_name == 'VirtualProduct' || btn_name == 'Pack') && $('#name_' + id_lang_default).val() == '')
					{
						alert(missing_product_name);
						$('#name_' + id_lang_default).focus();
						return false;
					}

					$('#key_tab').val(btn_name);

					if ($(this).attr("id") != $(".productTabs a.active ").attr('id'))
					{
						$(".productTabs a").removeClass('active');
						$("#product-tab-content-"+currentId).hide();
					}

					// if the tab has not already been loaded, load it now
					tabs_manager.display(id, true);

					tabs_manager.onLoad(id, function(){
						$("#product-tab-content-"+id).show(0, function(){
							$(this).trigger('displayed');
						});
						$("#link-"+id).addClass('active');
					});

					var languages = new Array();
					if (btn_name == "Combinations")
					{
						$('#desc-product-new').hide();
						$('#desc-product-newCombination').show();
						populate_attrs();
					}
					else if (btn_name == "Attachments")
					{
						handleSaveButtons();
					}
					else
					{
						$('#desc-product-newCombination').hide();
						// if pack is enabled, save button are visible only if pack is valid
						if ($("input[name='id_product']").val() != 0 || btn_name != 'Informations')
							handleSaveButtons();
					}

					$('.label-tooltip').tooltip();
				});

				$(".productTabs a.active").click();

				// disable save if Associations tab is not loaded
				if ($('#product-tab-content-Associations').hasClass('not-loaded'))
					disableSave();

				tabs_manager.onLoad('Associations', function(){
					if ($("input[name='id_product']").val() != 0)
						handleSaveButtons();
				});

				$('.confirm_leave').live('click', function(){
					// Double quotes are necessary when the translated string has single quotes
					return confirm("{l s='You will lose all unsaved modifications. Are you sure that you want to proceed?' js=1}");
				});

				$('#toolbar-footer').appendTo($('#product-tab-content-Informations').children('.product-tab'));

				$('.product-tab-content').on('displayed', function(e) {
					$('#toolbar-footer').appendTo($(this).children('.product-tab'));
				});

			});

			// Listen to the load event that is fired each time an ajax call to load a tab has completed
			$(window).bind("load", function() {
				{* Fill an array with tabs that need to be preloaded *}
				var tabs_to_preload = new Array();
				{foreach $tabs_preloaded as $tab_name => $value}
					{* If the tab was not given a loading priority number it will not be preloaded *}
					{if (is_numeric($value))}
						if ($("#product-tab-content-"+'{$tab_name}').hasClass('not-loaded'))
							tabs_to_preload.push('{$tab_name}');
					{/if}
				{/foreach}
				tabs_manager.tabs_to_preload = tabs_to_preload.slice(0);
				tabs_manager.displayBulk(tabs_to_preload);
				$('.productTabs').show();
				$('#product-tab-content-wait').hide();

				function checkIfProductTypeIsPack() {
					var typeIsPack = $('#pack_product').is(':checked');
					if (typeIsPack && $('#inputPackItems').val()=='' ) {
						$('.pack-empty-warning').removeClass('alert-warning').addClass('alert-danger');
						$('#curPackItemName').select2('open');
					}
					return typeIsPack;
				}
				$("#product_form").validate({
					ignore: '.updateCurrentText',
					rules: {
						inputPackItems: {
							required: {
								depends: checkIfProductTypeIsPack
							},
						}
					},
					messages: {
						inputPackItems: {
							required: ""
						}
					},
					// override jquery validate plugin defaults for bootstrap 3
					highlight: function(element) {
						$(element).closest('.form-group').addClass('has-error');
					},
					unhighlight: function(element) {
						$(element).closest('.form-group').removeClass('has-error');
					},
					errorElement: 'span',
					errorClass: 'help-block',
					errorPlacement: function(error, element) {
						if(element.parent('.input-group').length) {
							error.insertAfter(element.parent());
						} else {
							error.insertAfter(element);
						}
					}
				});
			});
		</script>

		<script type="text/javascript">
			// <![CDATA[
				ThickboxI18nImage = "{l s='Image'}";
				ThickboxI18nOf = "{l s='of'}";
				ThickboxI18nClose = "{l s='Close'}";
				ThickboxI18nOrEscKey = "{l s='(or "Esc")'}";
				ThickboxI18nNext = "{l s='Next >'}";
				ThickboxI18nPrev = "{l s='< Previous'}";
				tb_pathToImage = "../img/loadingAnimation.gif";
			//]]>
		</script>

		<div id="product-tab-content-wait" style="display:none">
			<div id="loading"><i class="icon-refresh icon-spin"></i>&nbsp;{l s='Loading...'}</div>
		</div>

		<form id="product_form" class="form-horizontal col-lg-10 col-md-9" action="{$form_action|escape:'html':'UTF-8'}" method="post" enctype="multipart/form-data" name="product" novalidate>
			<input type="hidden" name="id_product" value="{$id_product}" />
			{* <input type="hidden" id="is_virtual" name="is_virtual" value="{$product->is_virtual|escape:'html':'UTF-8'}" /> *}
			<!-- changed input field to make product virtual by default -->
			<input type="hidden" id="is_virtual" name="is_virtual" value="1"/>

			{if isset($smarty.request.page) && $smarty.request.page > 1}
				<input type="hidden" id="page" name="page" value="{$smarty.request.page|intval}" />
			{/if}
			{if !$product->active && $product->isAssociatedToShop()}
			<div class="alert alert-info draft">
				{l s='Your product will be saved as a draft.'}
				<a href="#" class="btn btn-default" onclick="submitAddProduct()" ><i class="icon-save"></i>&nbsp;{l s='Save'}</a>
				<input type="hidden" name="submitAddProduct" id="submitAddProduct" />
			</div>
			{/if}
			{* all input are here *}
			{foreach $product_tabs key=numStep item=tab}
				{if $tab.id != "Pack" }
				<div id="product-tab-content-{$tab.id}" class="{if !$tab.selected}not-loaded{/if} product-tab-content" {if !$tab.selected}style="display:none"{/if}>
					{if $tab.selected}
						{$custom_form}
					{/if}
				</div>
				{/if}
			{/foreach}
			<input type="hidden" name="id_product_attribute" id="id_product_attribute" value="0" />
			<input type="hidden" name="key_tab" id="key_tab" value="Informations" />
		</form>
	</div>

{/block}

