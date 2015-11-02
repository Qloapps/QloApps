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

{block name="other_fieldsets"}
<div id="conditions">
	<div id="condition_group_list"></div>
</div>

<a class="btn btn-default" href="#" id="add_condition_group">
	<i class="icon-plus-sign"></i> {l s='Add a new condition group'}
</a>
<div class="clearfix">&nbsp;</div>
<div class="panel" id="conditions-panel" style="display:none;">
	<h3><i class="icon-tasks"></i> {l s='Conditions'}</h3>
	<div class="form-group">
		<label for="id_category" class="control-label col-lg-3">{l s='Category'}</label>
		<div class="col-lg-9">
			<div class="col-lg-8">
				<select id="id_category" name="id_category">
					{foreach from=$categories item='category'}
					<option value="{$category.id_category|intval}">({$category.id_category|intval}) {$category.name}</option>
					{/foreach}
				</select>
			</div>
			<div class="col-lg-1">
				<a class="btn btn-default" href="#" id="add_condition_category">
					<i class="icon-plus-sign"></i> {l s='Add condition'}
				</a>
			</div>
		</div>
	</div>
	<div class="form-group">
		<label for="id_manufacturer" class="control-label col-lg-3">{l s='Manufacturer'}</label>
		<div class="col-lg-9">
			<div class="col-lg-8">
				<select id="id_manufacturer" name="id_manufacturer">
					{foreach from=$manufacturers item='manufacturer'}
						<option value="{$manufacturer.id_manufacturer}">{$manufacturer.name}</option>
					{/foreach}
				</select>
			</div>
			<div class="col-lg-1">
				<a class="btn btn-default" href="#" id="add_condition_manufacturer">
					<i class="icon-plus-sign"></i> {l s='Add condition'}
				</a>
			</div>
		</div>
	</div>
	<div class="form-group">
		<label for="id_supplier" class="control-label col-lg-3">{l s='Supplier'}</label>
		<div class="col-lg-9">
			<div class="col-lg-8">
				<select id="id_supplier" name="id_supplier">
					{foreach from=$suppliers item='supplier'}
						<option value="{$supplier.id_supplier}">{$supplier.name}</option>
					{/foreach}
				</select>
			</div>
			<div class="col-lg-1">
				<a class="btn btn-default" href="#" id="add_condition_supplier">
					<i class="icon-plus-sign"></i> {l s='Add condition'}
				</a>
			</div>
		</div>
	</div>
	<div class="form-group">
		<label for="id_attribute_group" class="control-label col-lg-3">{l s='Attributes'}</label>
		<div class="col-lg-9">
			<div class="col-lg-4">
				<select id="id_attribute_group">
					{foreach from=$attributes_group item='attribute_group'}
						<option value="{$attribute_group.id_attribute_group}">{$attribute_group.name}</option>
					{/foreach}
				</select>
			</div>
			<div class="col-lg-4">
				{foreach from=$attributes_group item='attribute_group'}
					<select class="id_attribute" style="display:none;" id="id_attribute_{$attribute_group.id_attribute_group}">
						{foreach from=$attribute_group.attributes item='attribute'}
							<option value="{$attribute.id_attribute}">{$attribute.name}</option>
						{/foreach}
					</select>
				{/foreach}
			</div>
			<div class="col-lg-1">
				<a class="btn btn-default" href="#" id="add_condition_attribute">
					<i class="icon-plus-sign"></i> {l s='Add condition'}
				</a>
			</div>
		</div>
	</div>
	<div class="form-group">
		<label for="id_feature" class="control-label col-lg-3">{l s='Features'}</label>
		<div class="col-lg-9">
			<div class="col-lg-4">
				<select id="id_feature">
					{foreach from=$features item='feature'}
						<option value="{$feature.id_feature}">{$feature.name}</option>
					{/foreach}
				</select>
			</div>
			<div class="col-lg-4">
				{foreach from=$features item='feature'}
					<select class="id_feature_value" style="display:none;" id="id_feature_{$feature.id_feature}">
						{foreach from=$feature.values item='value'}
							<option value="{$value.id_feature_value}">{$value.value}</option>
						{/foreach}
					</select>
				{/foreach}
			</div>
			<div class="col-lg-1">
				<a class="btn btn-default" href="#" id="add_condition_feature">
					<i class="icon-plus-sign"></i> {l s='Add condition'}
				</a>
			</div>
		</div>
	</div>
{if !$is_multishop}
	<input type="hidden" name="id_shop" value="1" />
{/if}
</div>
{/block}

{block name="script"}
var current_id_condition_group = 0;
var last_condition_group = 0;
var conditions = new Array();

function toggle_condition_group(id_condition_group)
{
	$('.condition_group').removeClass('alert-info');
	$('.condition_group > table').removeClass('alert-info');
	$('#condition_group_'+id_condition_group+' > table').addClass('alert-info');
	$('#condition_group_'+id_condition_group).addClass('alert-info');
	current_id_condition_group = id_condition_group;
}

function add_condition(id_condition_group, type, value)
{
	var id_condition = id_condition_group+'_'+type+'_'+value;
	if (typeof conditions[id_condition] != 'undefined')
		return false;
	var condition = new Array();
	condition.type = type;
	condition.value = value;
	condition.id_condition_group = id_condition_group;
	conditions[id_condition] = condition;
	return id_condition;
}

function delete_condition(condition)
{
	delete conditions[condition];

	to_delete = $('#'+condition).prev();
	if ($(to_delete).children().hasClass('btn_delete_condition'))
		$(to_delete).remove();
	else
		$('#'+condition).next().remove();

	$('#'+condition).remove();
	return false;
}

function new_condition_group()
{
	$('#conditions-panel').show();
	var html = '';

	if (last_condition_group > 0)
		html += '<div class="row condition_separator text-center">{l s='OR'}</div><div class="clearfix">&nbsp;</div>';

	last_condition_group++;
	html += '<div id="condition_group_'+last_condition_group+'" class="panel condition_group alert-info"><h3><i class="icon-tasks"></i> {l s='Condition group'} '+last_condition_group+'</h3>';
		html += '<table class="table alert-info"><thead><tr><th class="fixed-width-md"><span class="title_box">{l s='Type'}</span></th><th><span class="title_box">{l s='Value'}</span></th><th></th></tr></thead><tbody></tbody></table>';
		html += '</div>';
	$('#condition_group_list').append(html);
	toggle_condition_group(last_condition_group);
}

function appendConditionToGroup(html)
{
	if ($('#condition_group_'+current_id_condition_group+' table tbody tr').length > 0)
		$('#condition_group_'+current_id_condition_group+' table tbody').append('<tr><td class="text-center btn_delete_condition" colspan="3"><b>{l s='AND' js=1}</b></td></tr>');
	$('#condition_group_'+current_id_condition_group+' table tbody').append(html);
}

$(document).ready(function() {
	$('#leave_bprice_on').click(function() {
		if (this.checked)
			$('#price').attr('disabled', 'disabled');
		else
			$('#price').removeAttr('disabled');
	});

	$('#specific_price_rule_form').live('submit', function(e) {
		var html = '';
		for (i in conditions)
			html += '<input type="hidden" name="condition_group_'+conditions[i].id_condition_group+'[]" value="'+conditions[i].type+'_'+conditions[i].value+'" />';
		$('#conditions').append(html);
	});

	$('#id_feature').change(function() {
		$('.id_feature_value').hide();
		$('#id_feature_'+$(this).val()).show();
	});

	$('#id_attribute_group').change(function() {
		$('.id_attribute').hide();
		$('#id_attribute_'+$(this).val()).show();
	});

	$('#add_condition_category').click(function() {
		var id_condition = add_condition(current_id_condition_group, 'category', $('#id_category option:selected').val());
		if (!id_condition)
			return false;

		var html = '<tr id="'+id_condition+'"><td>{l s='Category'}</td><td>'+$('#id_category option:selected').html()+'</td><td><a href="#" onclick="delete_condition(\''+id_condition+'\');" class="btn btn-default"><i class="icon-remove"></i> {l s='Delete'}</a></td></tr>';
		appendConditionToGroup(html);

		return false;
	});

	$('#add_condition_manufacturer').click(function() {
		var id_condition = add_condition(current_id_condition_group, 'manufacturer', $('#id_manufacturer option:selected').val());
		if (!id_condition)
			return false;

		var html = '<tr id="'+id_condition+'"><td>{l s='Manufacturer'}</td><td>'+$('#id_manufacturer option:selected').html()+'</td><td><a href="#" onclick="delete_condition(\''+id_condition+'\');" class="btn btn-default"><i class="icon-remove"></i> {l s='Delete'}</a></td></tr>';
		appendConditionToGroup(html);

		return false;
	});

	$('#add_condition_supplier').click(function() {
		var id_condition = add_condition(current_id_condition_group, 'supplier', $('#id_supplier option:selected').val());
		if (!id_condition)
			return false;

		var html = '<tr id="'+id_condition+'"><td>{l s='Supplier'}</td><td>'+$('#id_supplier option:selected').html()+'</td><td><a href="#" onclick="delete_condition(\''+id_condition+'\');" class="btn btn-default"><i class="icon-remove"></i> {l s='Delete'}</a></td></tr>';
		appendConditionToGroup(html);

		return false;
	});

	$('#add_condition_attribute').click(function() {
		var id_condition = add_condition(current_id_condition_group, 'attribute', $('#id_attribute_'+$('#id_attribute_group option:selected').val()+' option:selected').val());
		if (!id_condition)
			return false;

		var html = '<tr id="'+id_condition+'"><td>{l s='Attribute'}</td><td>'+$('#id_attribute_group option:selected').html()+': '+$('#id_attribute_'+$('#id_attribute_group option:selected').val()+' option:selected').html()+'</td><td><a href="#" onclick="delete_condition(\''+id_condition+'\');" class="btn btn-default"><i class="icon-remove"></i> {l s='Delete'}</a></td></tr>';
		appendConditionToGroup(html);

		return false;
	});

	$('#add_condition_feature').click(function() {
		var id_condition = add_condition(current_id_condition_group, 'feature', $('#id_feature_'+$('#id_feature option:selected').val()+' option:selected').val());
		if (!id_condition)
			return false;

		var html = '<tr id="'+id_condition+'"><td>{l s='Feature'}</td><td>'+$('#id_feature option:selected').html()+': '+$('#id_feature_'+$('#id_feature option:selected').val()+' option:selected').html()+'</td><td><a href="#" onclick="delete_condition(\''+id_condition+'\');" class="btn btn-default"><i class="icon-remove"></i> {l s='Delete'}</a></td></tr>';
		appendConditionToGroup(html);

		return false;
	});

	$('#add_condition_group').click(function() {
		new_condition_group();
		return false;
	});

	$('.condition_group').live('click', function() {
		var id = this.id.split('_');
		toggle_condition_group(id[2]);
		return false;
	});

	{foreach from=$conditions key='id_group_condition' item='condition_group'}
		new_condition_group();
		{foreach from=$condition_group item='condition'}
			{if $condition.type == 'attribute'}
				$('#id_attribute_group option[value="{$condition.id_attribute_group}"]').attr('selected', true);
				$('#id_attribute_{$condition.id_attribute_group} option[value="{$condition.value}"]').attr('selected', true);
			{elseif $condition.type == 'feature'}
				$('#id_feature option[value="{$condition.id_feature}"]').attr('selected', true);
				$('#id_feature_{$condition.id_feature} option[value="{$condition.value}"]').attr('selected', true);
			{else}
				$('#id_{$condition.type} option[value="{$condition.value}"]').attr('selected', true);
			{/if}
			$('#add_condition_{$condition.type}').click();
		{/foreach}
	{/foreach}
	$('#id_attribute_group').change();
	$('#id_feature').change();
});
{/block}
