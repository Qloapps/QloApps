{*
* 2007-2015 PrestaShop
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
*  @copyright  2007-2015 PrestaShop SA
*  @license    http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*}
{extends file="helpers/form/form.tpl"}

{block name="field"}
	{$smarty.block.parent}
{/block}

{block name="input"}
	{if $input.type == 'default_tab'}
	<select id="{$input.name}" name="{$input.name}" class="chosen fixed-width-xxl">
		{foreach $input.options AS $option}
			{if isset($option.children) && $option.children|@count}
				<optgroup label="{$option.name|escape:'html':'UTF-8'}"></optgroup>
				{foreach $option.children AS $children}
					<option value="{$children.id_tab}" {if $fields_value[$input.name] == $children.id_tab}selected="selected"{/if}>{$children.name|escape:'html':'UTF-8'}</option>
				{/foreach}
			{else}
				<option value="{$option.id_tab}" {if $fields_value[$input.name] == $option.id_tab}selected="selected"{/if}>{$option.name|escape:'html':'UTF-8'}</option>
			{/if}
		{/foreach}
	</select>
	{else}
		{$smarty.block.parent}
	{/if}
{/block}

{block name=script}
	$(document).ready(function(){
		$('select[name=id_profile]').change(function(){
			ifSuperAdmin($(this));

			$.ajax({
				url: "{$link->getAdminLink('AdminEmployees')|addslashes}",
				cache: false,
				data : {
					ajax : '1',
					action : 'getTabByIdProfile',
					id_profile : $(this).val()
				},
				dataType : 'json',
				success : function(resp,textStatus,jqXHR)
				{
					if (resp != false)
					{
						$('select[name=default_tab]').html('');
						$.each(resp, function(key, r){
							if (r.id_parent == 0)
							{
								$('select[name=default_tab]').append('<optgroup label="'+r.name+'"></optgroup>');
								$.each(r.children, function(k, value){
									$('select[name=default_tab]').append('<option value="'+r.id_tab+'">'+value.name+'</option>')
								});
							}
						});
					}
				}
			});
		});
		ifSuperAdmin($('select[name=id_profile]'));
	});

	function ifSuperAdmin(el)
	{
		var val = $(el).val();

		if (!val || val == {$smarty.const._PS_ADMIN_PROFILE_})
		{
			$('.assoShop input[type=checkbox]').attr('disabled', true);
			$('.assoShop input[type=checkbox]').attr('checked', true);
		}
		else
			$('.assoShop input[type=checkbox]').attr('disabled', false);
	}
{/block}
