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

{block name="label"}
	{if $input.name == 'zipcode' && isset($input.label)}
		<label id="zipcode-label" class="control-label col-lg-3">{$input.label}</label>
	{elseif $input.name == 'states[]'}
		<label id="states-label" class="control-label col-lg-3">{$input.label}</label>
	{else}
		{$smarty.block.parent}
	{/if}
{/block}

{block name="script"}
	$(document).ready(function() {
		$('#country').change(function() {
			populateStates($(this).val(), '');
		});

		$('#id_tax_rules_group').clone().attr('id', '').insertAfter('#id_tax_rule');


		if ($('#id_tax_rules_group').val() != '' && $('table.tax_rule tbody tr').length == 0)
		{
			initForm();
			$('#tax_rule_form').show();
			$('#country').focus();
		}
		else
		{
			$('#tax_rule_form').hide();
			$('#page-header-desc-tax_rule-new').click(function() {
				initForm();
				$('#tax_rule_form').slideToggle();
				return false;
			});
		}
	});

	function populateStates(id_country, id_state)
	{
		if ($("#country option:selected").size() > 1)
		{
			$("#zipcode-label").hide();
			$("#zipcode").hide();

			$("#states").parent().hide();
			$("#states-label").hide();
		} else {
			$.ajax({
				url: "index.php",
				cache: false,
				data: "ajax=1&tab=AdminStates&token={getAdminToken tab='AdminStates'}&action=states&id_country="+id_country+"&id_state="+id_state+"&empty_value={l s='All'}",
				success: function(html){
					if (html == "false")
					{
						$("#states").parent().hide();
						$("#states-label").hide();
						$("#states").html('');
					}
					else
					{
						$("#states").parent().show();
						$("#states-label").show();
						$("#states").html(html);
					}
				}
			});

			$("#zipcode-label").show();
			$("#zipcode").show();
		}
	}

	function loadTaxRule(id_tax_rule)
	{
		$.ajax({
			type: 'POST',
			url: 'index.php',
			async: true,
			dataType: 'json',
			data: 'ajax=1&tab=AdminTaxRulesGroup&token={getAdminToken tab='AdminTaxRulesGroup'}&ajaxStates=1&action=updateTaxRule&id_tax_rule='+id_tax_rule,
			success: function(data){
				$('#tax_rule_form').show();
				$('#id_tax_rule').val(data.id);
				$('#country').val(data.id_country);
				$('#state').val(data.id_state);

				zipcode = 0;
				if (data.zipcode_from != 0)
				{
					zipcode = data.zipcode_from;

					if (data.zipcode_to != 0)
						zipcode = zipcode +"-"+data.zipcode_to
				}

				$('#zipcode').val(zipcode);
				$('#behavior').val(data.behavior);
				$('#id_tax').val(data.id_tax);
				$('#description').val(data.description);

				populateStates(data.id_country, data.id_state);
			}
		});
	}

	function initForm()
	{
		$('#id_tax_rule').val('');
		$('#country').val(0);
		$('#state').val(0);
		$('#zipcode').val(0);
		$('#behavior').val(0);
		$('#tax').val(0);
		$('#description').val('');

		populateStates(0,0);
	}
{/block}
