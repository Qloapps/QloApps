{*
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
<div id="product-seo" class="panel product-tab">
	<input type="hidden" name="submitted_tabs[]" value="Seo" />
	<h3>{l s='SEO'}</h3>
	{include file="controllers/products/multishop/check_fields.tpl" product_tab="Seo"}
	<div class="form-group">
		<div class="col-lg-1"><span class="pull-right">{include file="controllers/products/multishop/checkbox.tpl" field="meta_title" type="default" multilang="true"}</span></div>
		<label class="control-label col-lg-2" for="meta_title_{$id_lang}">
			<span class="label-tooltip" data-toggle="tooltip"
				title="{l s='Title shown in search results. Leave blank to use the room type name.'}">
				{l s='Meta title'}
			</span>
		</label>
		<div class="col-lg-8">
			{include file="controllers/products/input_text_lang.tpl"
				languages=$languages
				input_name='meta_title'
				input_value=$product->meta_title
				maxchar=128
			}
		</div>
	</div>
	<div class="form-group">
		<div class="col-lg-1"><span class="pull-right">{include file="controllers/products/multishop/checkbox.tpl" field="meta_description" type="default" multilang="true"}</span></div>
		<label class="control-label col-lg-2" for="meta_description_{$id_lang}">
			<span class="label-tooltip" data-toggle="tooltip"
				title="{l s='This description will appear in search engines. You need a single sentence, shorter than 255 characters (including spaces). Leave blank to use the room type short description.'}">
				{l s='Meta description'}
			</span>
		</label>
		<div class="col-lg-8">
			{include file="controllers/products/textarea_lang.tpl"
				languages=$languages
				input_name='meta_description'
				input_value=$product->meta_description
				maxchar=255
			}
		</div>
	</div>
	{* Removed for simplicity *}
	<div class="form-group hide">
		<div class="col-lg-1"><span class="pull-right">{include file="controllers/products/multishop/checkbox.tpl" field="meta_keywords" type="default" multilang="true"}</span></div>
		<label class="control-label col-lg-2" for="meta_keywords_{$id_lang}">
			<span class="label-tooltip" data-toggle="tooltip"
				title="{l s='Keywords for search engines, separated by commas.'}">
				{l s='Meta keywords'}
			</span>
		</label>
		<div class="col-lg-8">
			{include file="controllers/products/input_text_lang.tpl" languages=$languages
				input_value=$product->meta_keywords
				input_name='meta_keywords'}
		</div>
	</div>
	<div class="form-group">
		<div class="col-lg-1"><span class="pull-right">{include file="controllers/products/multishop/checkbox.tpl" field="link_rewrite" type="seo_friendly_url" multilang="true"}</span></div>
		<label class="control-label col-lg-2" for="link_rewrite_{$id_lang}">
			<span class="label-tooltip" data-toggle="tooltip"
				title="{l s='This is the human-readable URL, as generated from the room type\'s name. You can change it if you want.'}">
				{l s='Friendly URL:'}
			</span>

		</label>
		<div class="col-lg-6">
				{include file="controllers/products/input_text_lang.tpl"
					languages=$languages
					input_value=$product->link_rewrite
					input_name='link_rewrite'}
		</div>
		<div class="col-lg-2">
			<button type="button" class="btn btn-default" id="generate-friendly-url" onmousedown="updateFriendlyURLByName();"><i class="icon-random"></i> {l s='Generate'}</button>
		</div>
	</div>
	{include file="seo_preview.tpl"
    	languages = $languages
   		preview_link = $rewritten_links|default:''
   		inputs = ['meta_title'=>$product->meta_title ,'meta_description' => $product->meta_description,'link_rewrite' => $product->link_rewrite, 'name' => $product->name, 'description_short' => $product->description_short]
	}
	<div class="panel-footer">
		<a href="{$link->getAdminLink('AdminNormalProducts')|escape:'html':'UTF-8'}{if isset($smarty.request.page) && $smarty.request.page > 1}&amp;submitFilterproduct={$smarty.request.page|intval}{/if}" class="btn btn-default"><i class="process-icon-cancel"></i> {l s='Cancel'}</a>
		<button type="submit" name="submitAddproduct" class="btn btn-default pull-right" disabled="disabled"><i class="process-icon-loading"></i> {l s='Save'}</button>
		<button type="submit" name="submitAddproductAndStay" class="btn btn-default pull-right" disabled="disabled"><i class="process-icon-loading"></i> {l s='Save and stay'}</button>
	</div>
</div>
<script type="text/javascript">
	if (tabs_manager.allow_hide_other_languages)
		hideOtherLanguage({$default_form_language});
	// Recalculate autosize heights when language is switched (hidden textareas gain correct width)
	(function() {
		var _orig = window.hideOtherLanguage;
		window.hideOtherLanguage = function(id) {
			_orig && _orig.call(this, id);
			$(window).trigger('resize.autosize');
		};
	})();
</script>
