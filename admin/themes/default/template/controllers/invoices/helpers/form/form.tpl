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

{block name=script}
	$(document).ready(function() {
		var btn_save_date = $('span[class~="process-icon-save-date"]').parent();
		var btn_submit_date = $('#submitPrint');
		if (btn_save_date.length > 0 && btn_submit_date.length > 0)
		{
			btn_submit_date.hide();
			btn_save_date.find('span').removeClass('process-icon-save-date');
			btn_save_date.find('span').addClass('process-icon-save-calendar');
			btn_save_date.click(function() {
				btn_submit_date.before('<input type="hidden" name="'+btn_submit_date.attr("name")+'" value="1" />');

				$('#invoice_date_form').submit();
			});
		}
		var btn_save_status = $('span[class~="process-icon-save-status"]').parent();
		var btn_submit_status = $('#submitPrint2');
		if (btn_save_status.length > 0 && btn_submit_status.length > 0)
		{
			btn_submit_status.hide();
			btn_save_status.find('span').removeClass('process-icon-save-status');
			btn_save_status.find('span').addClass('process-icon-save');
			btn_save_status.click(function() {
				btn_submit_status.before('<input type="hidden" name="'+btn_submit_status.attr("name")+'" value="1" />');
				$('#invoice_status_form').submit();
			});
		}
	});
{/block}

{block name="input"}
	{if $input.type == 'checkboxStatuses'}
		{foreach $input.values.query as $value}
			{assign var=id_checkbox value=$input.name|cat:'_'|cat:intval($value[$input.values.id])}
			<p class="checkbox">
				<label for="{$id_checkbox}" class="control-label">
					<input type="checkbox" name="{$input.name}[]" id="{$id_checkbox}" value="{$value[$input.values.id]|intval}" {if isset($fields_value[$id_checkbox]) && $fields_value[$id_checkbox]}checked="checked"{/if} />
					{if $value['invoice']}
						<span class="badge badge-success">
						{if isset($statusStats[$value['id_order_state']]) && $statusStats[$value['id_order_state']]}
							{$statusStats[$value['id_order_state']]}
						{else}
							0
						{/if}
						</span>
						{$value[$input.values.name]}
					{else}
						<span class="badge badge-danger">
						{if isset($statusStats[$value['id_order_state']]) && $statusStats[$value['id_order_state']]}
							{$statusStats[$value['id_order_state']]}
						{else}
							0
						{/if}
						</span>
						{$value[$input.values.name]}
					{/if}
				</label>
			</p>
		{/foreach}
	{else}
		{$smarty.block.parent}
	{/if}
{/block}
