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

<script type="text/javascript">
	var attrs = new Array();
	attrs[0] = new Array(0, '---');

	{foreach $attribute_js as $idgrp => $group}
		{assign var="row" value="attrs[{$idgrp}] = new Array(0, '---'"}

		{foreach $group as $idattr => $attrname}
			{assign var="row" value="{$row}, {$idattr}, '{$attrname|escape}'"}
		{/foreach}

		{assign var="row" value="{$row});"}
		{$row}
	{/foreach}

	i18n_tax_exc = '{l s='Tax Excluded'} ';
	i18n_tax_inc = '{l s='Tax Included'} ';

	var product_tax = '{$tax_rates}';
	function calcPrice(element, element_has_tax)
	{
			var element_price = element.val().replace(/,/g, '.');
			var other_element_price = 0;

			if (!isNaN(element_price))
			{
				if (element_has_tax)
					other_element_price = parseFloat(element_price / ((product_tax / 100) + 1)).toFixed(6);
				else
					other_element_price = ps_round(parseFloat(element_price * ((product_tax / 100) + 1)), 2).toFixed(2);
			}

			$('#related_to_'+element.attr('name')).val(other_element_price);
	}

	$(document).ready(function() { $('.price_impact').each(function() { calcPrice($(this), false); }); });
</script>

<div class="leadin">{block name="leadin"}{/block}</div>

{if $generate}<div class="alert alert-success clearfix">{l s='%d product(s) successfully created.' sprintf=$combinations_size}</div>{/if}
<form enctype="multipart/form-data" method="post" id="generator" action="{$url_generator}">
	<div class="panel">
		<h3>
			<i class="icon-asterisk"></i>
			{l s='Attributes generator'}
		</h3>
		<div class="row">
			<div class="col-lg-3">
				<div class="form-group">
					<select multiple name="attributes[]" id="attribute_group" style="height: 500px">
						{foreach $attribute_groups as $k => $attribute_group}
							{if isset($attribute_js[$attribute_group['id_attribute_group']])}
								<optgroup name="{$attribute_group['id_attribute_group']}" id="{$attribute_group['id_attribute_group']}" label="{$attribute_group['name']|escape:'html':'UTF-8'}">
									{foreach $attribute_js[$attribute_group['id_attribute_group']] as $k => $v}
										<option name="{$k}" id="attr_{$k}" value="{$v|escape:'html':'UTF-8'}" title="{$v|escape:'html':'UTF-8'}">{$v|escape:'html':'UTF-8'}</option>
									{/foreach}
								</optgroup>
							{/if}
						{/foreach}
					</select>
				</div>
				<div class="form-group">
					<button type="button" class="btn btn-default" onclick="del_attr_multiple();"><i class="icon-minus-sign"></i> {l s='Delete'}</button>
					<button type="button" class="btn btn-default pull-right" onclick="add_attr_multiple();"><i class="icon-plus-sign"></i> {l s='Add'}</button>
				</div>
			</div>
			<div class="col-lg-8 col-lg-offset-1">
				<div class="alert alert-info">{l s='The Combinations Generator is a tool that allows you to easily create a series of combinations by selecting the related attributes. For example, if you\'re selling t-shirts in three different sizes and two different colors, the generator will create six combinations for you.'}</div>

				<div class="alert alert-info">{l s='You\'re currently generating combinations for the following product:'} <b>{$product_name|escape:'html':'UTF-8'}</b></div>

				<div class="alert alert-info"><strong>{l s='Step 1: On the left side, select the attributes you want to use (Hold down the "Ctrl" key on your keyboard and validate by clicking on "Add")'}</strong></div>

				{foreach $attribute_groups as $k => $attribute_group}
					{if isset($attribute_js[$attribute_group['id_attribute_group']])}
					<div class="row">
						<table class="table" style="display:none">
							<thead>
								<tr>
									<th id="tab_h1" class="fixed-width-md"><span class="title_box">{$attribute_group['name']|escape:'html':'UTF-8'}</span></th>
									<th id="tab_h2" colspan="2"><span class="title_box">{l s='Impact on the product price'} ({$currency_sign})</span></th>
									<th><span class="title_box">{l s='Impact on the product weight'} ({$weight_unit})</span></th>
								</tr>
							</thead>
							<tbody id="table_{$attribute_group['id_attribute_group']}" name="result_table">
							</tbody>
						</table>
					</div>
						{if isset($attributes[$attribute_group['id_attribute_group']])}
							{foreach $attributes[$attribute_group['id_attribute_group']] AS $k => $attribute}
								<script type="text/javascript">
									$('#table_{$attribute_group['id_attribute_group']}').append(create_attribute_row({$k}, {$attribute_group['id_attribute_group']}, '{$attribute['attribute_name']|addslashes}', {$attribute['price']}, {$attribute['weight']}));
									toggle(getE('table_' + {$attribute_group['id_attribute_group']}).parentNode, true);
								</script>
							{/foreach}
						{/if}
					{/if}
				{/foreach}
				<div class="alert alert-info">{l s='Select a default quantity, and reference, for each combination the generator will create for this product.'}</div>
				<table class="table">
					<tbody>
						<tr>
							<td>{l s='Default Quantity:'}</td>
							<td><input type="text" name="quantity" value="0" /></td>
						</tr>
						<tr>
							<td>{l s='Default Reference:'}</td>
							<td><input type="text" name="reference" value="{$product_reference|escape:'html':'UTF-8'}" /></td>
						</tr>
					</tbody>
				</table>
				<div class="alert alert-info">{l s='Please click on "Generate these Combinations"'}</div>
				<button type="submit" class="btn btn-default" name="generate"><i class="icon-random"></i> {l s='Generate these Combinations'}</button>
			</div>
		</div>
	</div>
</form>
