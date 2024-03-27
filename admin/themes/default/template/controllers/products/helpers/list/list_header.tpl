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

{extends file="helpers/list/list_header.tpl"}

{block name="tableFilter"}{/block}
{block name="override_form_extra"}
	{if !$simple_header && $show_filters}
		<script>
			$(function() {
				if ($("#list_filters_panel .datepicker").length > 0) {
					$("#list_filters_panel .datepicker").datepicker({
						prevText: '',
						nextText: '',
						altFormat: 'yy-mm-dd'
					});
				}
			});
		</script>
		<div class="list_action_wrapper">
			<div class="row">
				<div class="col-xs-4 col-sm-3 col-md-2">
					<div class="list_availibility_container">
						<button type="button" class="btn btn-default btn-left btn-block dropdown-toggle" data-toggle="dropdown">
							<span>{l s='Available Fields'}
							<i class="icon-caret-down pull-right"></i>
						</button>
						<ul id="optional-list-toggle" class="dropdown-menu">
							{foreach $fields_optional as $key => $field}
								<li>
									<label>
										<input type="checkbox" name="list_fields_visibility" value="{$key}" {if isset($field['selected']) && $field['selected']}checked="checked"{/if}>
										{$field['title']}
									</label>
								</li>
							{/foreach}
							{assign var="fields_optional" value=array()}
						</ul>
					</div>
				</div>
				<div class="col-xs-3 col-xs-offset-5 col-sm-2 col-sm-offset-7 col-md-1 col-md-offset-9">
					<div class="list_filter_container">
						<button type="button" class="btn btn-default btn-block" data-toggle="collapse" data-target="#list_filters_panel">
							<i class="icon-sliders"></i>
							<span>{l s='Filters'}
						</button>
					</div>
				</div>
			</div>
			<div class="row">
				<div class="col-sm-12">
					{if $filters_has_value}
						<div id="selected_filter_container">
							{l s='Filters: '}
							<span class="selected_filters">
								{foreach $fields_display AS $key => $params}
									{if (!isset($params.search) || $params.search) && $params.value != ''}
										{if ($params.type == 'date' || $params.type == 'datetime') && $params.value|is_array}
											{assign var="filter_value" value=''}
											{foreach $params.value as $value}
												{if ($value|is_string && !empty($value))}
													{if empty($filter_value)}
														{assign var="filter_value" value=$value}
													{else}
														{assign var="filter_value" value="`$filter_value` - `$value`"}
													{/if}
												{/if}
											{/foreach}
											{if !empty($filter_value)}
												<span data-filter_key="{if isset($params.name_date)}{$params.name_date}{else}{$key}{/if}" data-filter_type="{$params.type}">
													{$params['title']|escape:'html':'UTF-8'}: <span class="filter_value">{$filter_value|escape:'html':'UTF-8'}</span>
													<i class="icon-times"></i>
												</span>
											{/if}
										{elseif $params.type == 'range' && $params.value|is_array}
											{assign var="filter_value" value=''}
											{foreach $params.value as $value}
												{if (isset($value) && ($value !== '' || $value === 0))}
													{if !(isset($filter_value) && ($filter_value !== '' || $filter_value === 0))}
														{assign var="filter_value" value=$value}
													{else}
														{assign var="filter_value" value="`$filter_value` - `$value`"}
													{/if}
												{/if}
											{/foreach}
											{if (isset($filter_value) && ($filter_value !== '' || $filter_value === 0))}
												<span data-filter_key="{if isset($params.filter_key)}{$params.filter_key}{else}{$key}{/if}" data-filter_type="{$params.type}">
													{$params['title']|escape:'html':'UTF-8'}: <span class="filter_value">{$filter_value|escape:'html':'UTF-8'}</span>
													<i class="icon-times"></i>
												</span>
											{/if}
										{elseif $params.type == 'select'}
											{if isset($params.multiple) && $params.multiple}
												<span data-filter_key="{if isset($params.filter_key)}{$params.filter_key}{else}{$key}{/if}" data-filter_type="{$params.type}" data-multiple="{$params.multiple|intval}" data-operator="{$params.operator}">
													{$params['title']|escape:'html':'UTF-8'}:
													<span class="filter_value">
														{foreach from=$params.value item=option name=foreachInfo}
															{$params.list[$option]}{if !$smarty.foreach.foreachInfo.last}{if $params.operator == 'or'} | {else}, {/if}{/if}
														{/foreach}
													</span>
													<i class="icon-times"></i>
												</span>
											{else}
												<span data-filter_key="{if isset($params.filter_key)}{$params.filter_key}{else}{$key}{/if}" data-filter_type="{$params.type}">
													{$params['title']|escape:'html':'UTF-8'}: <span class="filter_value">{$params['list'][$params['value']]|replace: '&nbsp;' : ''}</span>
													<i class="icon-times"></i>
												</span>
											{/if}
										{elseif $params.type == 'bool' && $key == 'advance_payment'}
											<span data-filter_key="{if isset($params.filter_key)}{$params.filter_key}{else}{$key}{/if}" data-filter_type="{$params.type}">
												{$params['title']|escape:'html':'UTF-8'}: <span class="filter_value">{if $params['value'] == 1}{l s='Yes'}{elseif $params['value'] == 0}{l s='No'}{/if}</span>
												<i class="icon-times"></i>
											</span>
										{else}
											<span data-filter_key="{if isset($params.filter_key)}{$params.filter_key}{else}{$key}{/if}" data-filter_type="{$params.type}">
												{$params['title']|escape:'html':'UTF-8'}: <span class="filter_value">{$params['value']|escape:'html':'UTF-8'}</span>
												<i class="icon-times"></i>
											</span>
										{/if}
									{/if}
								{/foreach}
							</span>
						</div>
					{/if}
					<div class="panel collapse" id="list_filters_panel">
						<div class="row">
							<div class="col-sm-12">
								<div class="list_filters">
									{foreach $fields_display AS $key => $params}
										{if !isset($params.search) || $params.search}
											<div class="row">
												<label class="col-xs-3" for="filter_input_{$key}">{$params['title']|escape:'html':'UTF-8'}</label>
												<div class="col-xs-9">
													{if $params.type == 'range'}
														<div class="input_range">
															<input type="text" class="filter form-control" name="{$list_id}Filter_{if isset($params.filter_key)}{$params.filter_key}{else}{$key}{/if}[0]" placeholder="{l s='From'}" value="{if isset($params.value.0)}{$params.value.0}{/if}">
															<input type="text" class="filter form-control" name="{$list_id}Filter_{if isset($params.filter_key)}{$params.filter_key}{else}{$key}{/if}[1]" placeholder="{l s='To'}" value="{if isset($params.value.1)}{$params.value.1}{/if}">
														</div>
													{else if $params.type == 'bool'}
														<select id="filter_input_{$key}" class="filter center" name="{$list_id}Filter_{if isset($params.filter_key)}{$params.filter_key}{else}{$key}{/if}">
															<option value="">-</option>
															<option value="1" {if $params.value == 1} selected="selected" {/if}>{l s='Yes'}</option>
															<option value="0" {if $params.value == 0 && $params.value != ''} selected="selected" {/if}>{l s='No'}</option>
														</select>
													{elseif $params.type == 'date' || $params.type == 'datetime'}
														<div class="date_range">
															<div class="input-group center">
																<input type="text" class="filter datepicker date-input form-control" id="local_{$params.id_date}_0" name="local_{$params.name_date}[0]"  placeholder="{l s='From'}" autocomplete="off"/>
																<input type="hidden" id="{$params.id_date}_0" name="{$params.name_date}[0]" value="{if isset($params.value.0)}{$params.value.0}{/if}">
																<span class="input-group-addon">
																	<i class="icon-calendar"></i>
																</span>
															</div>
															<div class="input-group center">
																<input type="text" class="filter datepicker date-input form-control" id="local_{$params.id_date}_1" name="local_{$params.name_date}[1]"  placeholder="{l s='To'}"  autocomplete="off"/>
																<input type="hidden" id="{$params.id_date}_1" name="{$params.name_date}[1]" value="{if isset($params.value.1)}{$params.value.1}{/if}">
																<span class="input-group-addon">
																	<i class="icon-calendar"></i>
																</span>
															</div>
															<script>
																$(function() {
																	var dateStart = parseDate($("#{$params.id_date}_0").val());
																	var dateEnd = parseDate($("#{$params.id_date}_1").val());
																	$("#local_{$params.id_date}_0").datepicker("option", "altField", "#{$params.id_date}_0");
																	$("#local_{$params.id_date}_1").datepicker("option", "altField", "#{$params.id_date}_1");
																	if (dateStart !== null){
																		$("#local_{$params.id_date}_0").datepicker("setDate", dateStart);
																	}
																	if (dateEnd !== null){
																		$("#local_{$params.id_date}_1").datepicker("setDate", dateEnd);
																	}

																	$('#local_{$params.id_date}_0').datepicker('option', {
																		prevText: '',
																		nextText: '',
																		dateFormat: 'yy-mm-dd',
																		beforeShow: function() {
																			let dateTo = $('#local_{$params.id_date}_1').val().trim();
																			if (typeof dateTo != 'undefined' && dateTo != '') {
																				let objDateToMax = $.datepicker.parseDate('yy-mm-dd', dateTo);
																				objDateToMax.setDate(objDateToMax.getDate() - 1);
																				$('#local_{$params.id_date}_0').datepicker('option', 'maxDate', objDateToMax);
																			}
																		},
																		onClose: function() {
																			let dateFrom = $('#local_{$params.id_date}_0').val().trim();
																			let dateTo = $('#local_{$params.id_date}_1').val().trim();

																			if (dateFrom >= dateTo) {
																				let objDateToMin = $.datepicker.parseDate('yy-mm-dd', dateFrom);
																				objDateToMin.setDate(objDateToMin.getDate() + 1);

																				$('#local_{$params.id_date}_1').datepicker('option', 'minDate', objDateToMin);
																			}
																		},
																	});

																	$('#local_{$params.id_date}_1').datepicker('option', {
																		prevText: '',
																		nextText: '',
																		dateFormat: 'yy-mm-dd',
																		beforeShow: function() {
																			let dateFrom = $('#local_{$params.id_date}_0').val().trim();

																			if (typeof dateFrom != 'undefined' && dateFrom != '') {
																				let objDateToMin = $.datepicker.parseDate('yy-mm-dd', dateFrom);
																				objDateToMin.setDate(objDateToMin.getDate() + 1);

																				$('#local_{$params.id_date}_1').datepicker('option', 'minDate', objDateToMin);
																			}
																		},
																	});
																});
															</script>
														</div>
													{elseif $params.type == 'select'}
														{if isset($params.multiple) && $params.multiple}
															<select id="filter_input_{$key}" class="filter{if isset($params.align) && $params.align == 'center'}center{/if} select_multiple_{$params.operator} chosen" multiple name="{$list_id}Filter_{$params.filter_key}[]" {if isset($params.width)} style="width:{$params.width}px"{/if}>
																{if isset($params.list) && is_array($params.list)}
																	{foreach $params.list AS $option_value => $option_display}
																		<option value="{$option_value}" {if isset($params.value) && $params.value}{if in_array($option_value, $params.value)} selected="selected"{/if}{/if}>{$option_display}</option>
																	{/foreach}
																{/if}
															</select>
														{else}
															{if isset($params.filter_key)}
																<select id="filter_input_{$key}" class="filter{if isset($params.align) && $params.align == 'center'}center{/if} {if isset($params.class)}{$params.class}{/if}" {if !isset($params.remove_onchange) || !$params.remove_onchange}onchange="$('#submitFilterButton{$list_id}').focus();$('#submitFilterButton{$list_id}').click();"{/if} name="{$list_id}Filter_{$params.filter_key}" {if isset($params.width)} style="width:{$params.width}px"{/if}>
																	<option value="" {if $params.value == ''} selected="selected" {/if}>-</option>
																	{if isset($params.list) && is_array($params.list)}
																		{foreach $params.list AS $option_value => $option_display}
																			<option value="{$option_value}" {if (string)$option_display === (string)$params.value ||  (string)$option_value === (string)$params.value} selected="selected"{/if}>{$option_display}</option>
																		{/foreach}
																	{/if}
																</select>
															{/if}
														{/if}
													{else}
														<input type="text" class="filter" name="{$list_id}Filter_{if isset($params.filter_key)}{$params.filter_key}{else}{$key}{/if}" value="{$params.value|escape:'html':'UTF-8'}" {if isset($params.width) && $params.width != 'auto'} style="width:{$params.width}px"{/if} />
													{/if}
												</div>
											</div>
										{/if}
									{/foreach}
									{if $has_actions || $show_filters}
										<div class="actions">
											<hr>
											{if $show_filters}
											<span class="pull-right">
												<button type="submit" id="submitFilterButton{$list_id}" name="submitFilter" class="btn btn-default" data-list-id="{$list_id}">
													<i class="icon-search"></i> {l s='Search'}
												</button>
												{if $filters_has_value}
													<button type="submit" name="submitReset{$list_id}" class="btn btn-warning">
														<i class="icon-eraser"></i> {l s='Reset'}
													</button>
												{/if}
											</span>
											{/if}
										</div>
									{/if}
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
		<script>
			$(document).ready(function() {
				$('#selected_filter_container .selected_filters span i').on('click', function() {
					let form = $(this).closest('form');
					let type = $(this).parent().data('filter_type');
					$(this).parent().remove();
					if (type == 'date' || type == 'datetime') {
						$('#list_filters_panel').find('[name*="'+$(this).parent().data('filter_key')+'"]').val('');
					} else if (type == 'range') {
						$('#list_filters_panel').find('[name*="productFilter_'+$(this).parent().data('filter_key')+'"]').val('');
					} else if (type == 'select') {
						$('#list_filters_panel').find('select[name*="productFilter_'+$(this).parent().data('filter_key')+'"] option:selected').prop('selected', false);
					} else if (type == 'bool' && $(this).parent().data('filter_key') == 'advance_payment') {
						$('#list_filters_panel').find('select[name*="productFilter_'+$(this).parent().data('filter_key')+'"] option:selected').prop('selected', false);
					} else {
						$('#list_filters_panel').find('input[name*="productFilter_'+$(this).parent().data('filter_key')+'"]').val('');
					}

					// set post data for empty multi-select filters
					$(form).find('select[multiple]').each(function (i, selectElement) {
						if ($(selectElement).find('option:selected').length == 0) {
							$(form).append('<input type="hidden" name="' + $(selectElement).attr('name').slice(0, -2) + '" value="">');
						}
					});

					form.submit();
				});

				$('#form-product').submit(function () {
					let form = $(this);
					$(form).find('select[multiple]').each(function (i, selectElement) {
						if ($(selectElement).val() == null) {
							$(form).append('<input type="hidden" name="' + $(selectElement).attr('name').slice(0, -2) + '" value="">');
						}
					});

					return true;
				});
			});
		</script>
		<style>
			.list_action_wrapper {
				margin-bottom: 15px;
			}
			.list_availibility_container {
				position: relative;}
				.list_availibility_container .dropdown-menu {
					right: 0;
					padding: 8px 12px;}
					.list_availibility_container .dropdown-menu label {
						font-weight: 400;}
			#selected_filter_container {
				background-color: #fff;
				padding: 8px 20px;
				border: solid 1px #d3d8db;
				border-radius: 5px 5px 0 0;
				font-size: 14px;
				min-height: 52px;
				margin-top: 15px;}
				#selected_filter_container .selected_filters > span {
					font-size: 12px;
					padding: 6px 10px;
					display: inline-block;
					background: #F5F8F9;
					border: 1px solid #A0D0EB;
					border-radius: 3px;}
					#selected_filter_container .selected_filters > span + span {
						margin-left: 12px;}
					#selected_filter_container .selected_filters > span .filter_value {
						font-weight: 700;
					}
					#selected_filter_container .selected_filters > span i {
						cursor: pointer;}

			#content.bootstrap #list_filters_panel{
				margin-top: 15px;
				margin-bottom: 0;
			}
			#content.bootstrap  #selected_filter_container + #list_filters_panel {
				margin-top: 0;
			}
			#content.bootstrap  #selected_filter_container + div.panel {
				border-top: transparent;
				border-radius: 0 0 5px 5px;
			}

			.list_filters {
				padding: 0 30px;
				display: grid;
				grid-template-columns: repeat(3, minmax(0, 1fr));
				gap: 8px 40px;}
				.list_filters > .row {
					display: flex;
					align-items: center;}
				.list_filters .date_range, .list_filters .input_range {
					display: flex;
					justify-content: space-between;
					gap: 15px;}
				.list_filters .date_range .input-group:first-child {
					margin-bottom: 0;}
				.list_filters label {
					text-align: right;
					font-weight: 400;}
				.list_filters .actions {
					grid-column-start: 1;
					grid-column-end: 4;}
			@media (max-width: 992px) {
				.list_filters {
					grid-template-columns: repeat(2, 1fr);}
					.list_filters .actions {
					grid-column-start: 1;
					grid-column-end: 3;}
			}
			@media (max-width: 768px) {
				.list_filters {
					grid-template-columns: repeat(1, 1fr);}
					.list_filters .actions {
					grid-column-start: 1;
					grid-column-end: 2;}
					.list_filters label {
						text-align: left;
					}
			}

		</style>
	{/if}
{/block}