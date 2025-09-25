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

{block name="input_row"}
	{if $input.type == 'color' || $input.name == 'texture' || $input.name == 'current_texture'}
		<div class="colorAttributeProperties"{if !$colorAttributeProperties} style="display: none;"{/if}>
	{/if}
	{$smarty.block.parent}
	{if $input.type == 'color' || $input.name == 'texture' || $input.name == 'current_texture'}
		</div>
	{/if}
	{if $input.name == 'name'}
		{hook h="displayAttributeForm" id_attribute=$form_id}
	{/if}
{/block}

{block name="field"}
	{if $input.name == 'current_texture'}
		<div class="col-lg-9">
			{if isset($imageTextureExists) && $imageTextureExists}
				<img src="{$imageTexture}" alt="{l s='Texture'}" class="img-thumbnail" />
			{else}
				<p class="form-control-static">{l s='None'}</p>
			{/if}
		</div>
	{else}
		{$smarty.block.parent}
	{/if}
{/block}

{block name="script"}
	var attributesGroups = {ldelim}{$strAttributesGroups}{rdelim};

	var displayColorFieldsOption = function() {
		var val = $('#id_attribute_group').val();
		if (attributesGroups[val])
			$('.colorAttributeProperties').show();
		else
			$('.colorAttributeProperties').hide();
	};
	
	displayColorFieldsOption();
	
	$('#id_attribute_group').change(displayColorFieldsOption);

	var shop_associations = {$fields[0]['form']['shop_associations']};
	var changeAssociationGroup = function()
	{
		var id_attribute_group = $('#id_attribute_group').val();
		$('.input_shop').each(function(k, item)
		{
			var id_shop = $(item).attr('shop_id');
			if (typeof shop_associations[id_attribute_group] != 'undefined' && $.inArray(id_shop, shop_associations[id_attribute_group]) > -1)
				$(item).attr('disabled', false);
			else
			{
				$(item).attr('disabled', true);
				$(item).attr('checked', false);
			}
		});
	};
	$('#id_attribute_group').change(changeAssociationGroup);
	changeAssociationGroup();
{/block}
