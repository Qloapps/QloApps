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

{if isset($obj->id)}
<div id="product-attachements" class="panel product-tab">
	<input type="hidden" name="submitted_tabs[]" value="Attachments" />
	<h3>{l s='Attachment'}</h3>

	<div class="form-group">
		<label class="control-label col-lg-3 required" for="attachment_name_{$id_lang}">
			<span class="label-tooltip" data-toggle="tooltip"
			title="{l s='Maximum 32 characters.'}">
			{l s='Filename'}
			</span>
		</label>
		<div class="col-lg-9">
			{include file="controllers/products/input_text_lang.tpl"
				languages=$languages
				input_value=$attachment_name
				input_name="attachment_name"
			}
		</div>
	</div>

	<div class="form-group">
		<label class="control-label col-lg-3" for="attachment_description_{$id_lang}">
			{l s='Description'}
		</label>
		<div class="col-lg-9">
			{include
				file="controllers/products/textarea_lang.tpl"
				languages=$languages
				input_name="attachment_description"
				input_value=$attachment_description
			}
		</div>
	</div>

	<div class="form-group">
		<label class="control-label col-lg-3" for="attachement_filename">
			<span class="label-tooltip" data-toggle="tooltip"
				title="{l s='Upload a file from your computer'} ({Configuration::get('PS_ATTACHMENT_MAXIMUM_SIZE')|string_format:'%.2f'} {l s='MB max.'})">
				{l s='File'}
			</span>
		</label>
		{$attachment_uploader}
		<div class="col-lg-3">
		&nbsp;
		</div>
		<div class="col-lg-8">
			<p class="help-block">{l s='Upload a file from your computer'} ({Configuration::get('PS_ATTACHMENT_MAXIMUM_SIZE')|string_format:'%.2f'} {l s='MB max.'})</p>
		</div>
	</div>

	<hr/>

	<div class="form-group">
		<div class="col-lg-9 col-lg-offset-3">
			<div class="row">
				<div class="col-lg-6">
					<p>{l s='Available attachments:'}</p>
					<select multiple id="selectAttachment2">
						{foreach $attach2 as $attach}
							<option value="{$attach.id_attachment}">{$attach.name}</option>
						{/foreach}
					</select>
					<a href="#" id="addAttachment" class="btn btn-default btn-block">
						{l s='Add'}
						<i class="icon-arrow-right"></i>
					</a>
				</div>
				<div class="col-lg-6">
					<p>{l s='Attachments for this product:'}</p>
					<select multiple id="selectAttachment1" name="attachments[]">
						{foreach $attach1 as $attach}
							<option value="{$attach.id_attachment}">{$attach.name}</option>
						{/foreach}
					</select>
					<a href="#" id="removeAttachment" class="btn btn-default btn-block">
						<i class="icon-arrow-left"></i>
						{l s='Remove'}
					</a>
				</div>
			</div>
		</div>
	</div>
	<div class="panel-footer">
		<a href="{$link->getAdminLink('AdminProducts')|escape:'html':'UTF-8'}{if isset($smarty.request.page) && $smarty.request.page > 1}&amp;submitFilterproduct={$smarty.request.page|intval}{/if}" class="btn btn-default"><i class="process-icon-cancel"></i> {l s='Cancel'}</a>
		<button type="submit" name="submitAddproduct" class="btn btn-default pull-right" disabled="disabled"><i class="process-icon-loading"></i> {l s='Save'}</button>
		<button type="submit" name="submitAddproductAndStay" class="btn btn-default pull-right" disabled="disabled"><i class="process-icon-loading"></i> {l s='Save and stay'}</button>
	</div>

	<input type="hidden" name="arrayAttachments" id="arrayAttachments" value="{foreach $attach1 as $attach}{$attach.id_attachment},{/foreach}" />

	<script type="text/javascript">
		var iso = '{$iso_tiny_mce}';
		var pathCSS = '{$smarty.const._THEME_CSS_DIR_}';
		var ad = '{$ad}';

		if (tabs_manager.allow_hide_other_languages)
			hideOtherLanguage({$default_form_language});
	</script>
</div>
{/if}
