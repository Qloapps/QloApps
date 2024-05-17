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
{if $ajax}
	<script type="text/javascript">
		$(function () {
			$("#form-{$list_id} .ajax_table_link").click(function () {
				var link = $(this);
				$.post($(this).attr('href'), function (data) {
					if (data.success == 1) {
						showSuccessMessage(data.text);
						if (link.hasClass('action-disabled')){
							link.removeClass('action-disabled').addClass('action-enabled');
						} else {
							link.removeClass('action-enabled').addClass('action-disabled');
						}
						link.children().each(function () {
							if ($(this).hasClass('hidden')) {
								$(this).removeClass('hidden');
							} else {
								$(this).addClass('hidden');
							}
						});
					} else {
						showErrorMessage(data.text);
					}
				}, 'json');
				return false;
			});
		});
	</script>
{/if}
{* Display column names and arrows for ordering (ASC, DESC) *}
{if $is_order_position}
	<script type="text/javascript" src="../js/jquery/plugins/jquery.tablednd.js"></script>
	<script type="text/javascript">
		var come_from = '{$list_id|addslashes}';
		var alternate = {if $order_way == 'DESC'}'1'{else}'0'{/if};
	</script>
	<script type="text/javascript" src="../js/admin/dnd.js"></script>
{/if}
{if !$simple_header}
	<script type="text/javascript">
		$(function() {
			$('table.{$list_id} .filter').keypress(function(e){
				var key = (e.keyCode ? e.keyCode : e.which);
				if (key == 13)
				{
					e.preventDefault();
					formSubmit(e, 'submitFilterButton{$list_id}');
				}
			})
			$('#submitFilterButton{$list_id}').click(function() {
				$('#submitFilter{$list_id}').val(1);
			});

			if ($("form .datepicker").length > 0) {
				$("form .datepicker").datepicker({
					prevText: '',
					nextText: '',
					altFormat: 'yy-mm-dd'
				});
			}
		});
	</script>
{/if}

{if !$simple_header}
	<div class="leadin">
		{block name="leadin"}{/block}
	</div>
{/if}

{block name="override_header"}{/block}

{hook h='displayAdminListBefore'}

{if isset($name_controller)}
	{capture name=hookName assign=hookName}display{$name_controller|ucfirst}ListBefore{/capture}
	{hook h=$hookName}
{elseif isset($smarty.get.controller)}
	{capture name=hookName assign=hookName}display{$smarty.get.controller|ucfirst|htmlentities}ListBefore{/capture}
	{hook h=$hookName}
{/if}

<div class="alert alert-warning" id="{$list_id}-empty-filters-alert" style="display:none;">{l s='Please fill at least one field to perform a search in this list.'}</div>
{if isset($sql) && $sql}
	<form id="sql_form_{$list_id|escape:'html':'UTF-8'}" action="{$link->getAdminLink('AdminRequestSql')|escape}&amp;addrequest_sql" method="post" class="hide">
		<input type="hidden" id="sql_query_{$list_id|escape:'html':'UTF-8'}" name="sql" value="{$sql|escape}"/>
		<input type="hidden" id="sql_name_{$list_id|escape:'html':'UTF-8'}" name="name" value=""/>
	</form>
{/if}

{block name="startForm"}
	<form method="post" action="{$action|escape:'html':'UTF-8'}" class="form-horizontal clearfix" id="form-{$list_id}">
{/block}

{if !$simple_header}
	<input type="hidden" id="submitFilter{$list_id}" name="submitFilter{$list_id}" value="0"/>
	<input type="hidden" name="page" value="{$page|intval}"/>
	<input type="hidden" name="selected_pagination" value="{$selected_pagination|intval}"/>
	{block name="override_form_extra"}{/block}
	{if !$simple_header && $show_filters}
		<script>
			$(document).ready(function(){
				$('#selected_filter_container .selected_filters span i').on('click', function() {
					let form = $(this).closest('form');
					let type = $(this).parent().data('filter_type');
					$(this).parent().remove();
					if (type == 'date' || type == 'datetime') {
						$(form).find('#list_filters_panel [name*="'+$(this).parent().data('filter_key')+'"]').val('');
					} else if (type == 'range') {
						$(form).find('#list_filters_panel [name*="{$list_id}Filter_'+$(this).parent().data('filter_key')+'"]').val('');
					} else if (type == 'select' || type == 'bool') {
						$(form).find('#list_filters_panel select[name*="{$list_id}Filter_'+$(this).parent().data('filter_key')+'"] option:selected').prop('selected', false);
					} else {
						$(form).find('#list_filters_panel input[name="{$list_id}Filter_'+$(this).parent().data('filter_key')+'"]').val('');
					}

					// set post data for empty multi-select filters
					$(form).find('select[multiple]').each(function (i, selectElement) {
						if ($(selectElement).find('option:selected').length == 0) {
							$(form).append('<input type="hidden" name="' + $(selectElement).attr('name').slice(0, -2) + '" value="">');
						}
					});

					form.submit();
				});
			});
		</script>
	{/if}
	<script>
		$(document).ready(function(){
			{if $fields_optional|count}
				toggleVisibleColumns($('#form-{$list_id}'));
			{/if}
			$('#optional-list-toggle').on('click', function(e) {
				e.stopPropagation();
			})
			$('#form-{$list_id} input[name="list_fields_visibility"]').on('change', function(){
				toggleVisibleColumns($('#form-{$list_id}'));
			})
		});

		function toggleVisibleColumns(form) {
			var list_fields_visibility = [];
			$(form).find('input[name="list_fields_visibility"]:checked').each(function(i, field){
				list_fields_visibility.push($(field).val());
			});

			$(form).find('table.table .field_optional').hide();
			$(form).find('table.table .field_optional').each(function(i, val) {
				if (list_fields_visibility.includes($(this).data('key'))) {
					$(this).show();
				}
			});
			updateListVisibility(list_fields_visibility)
		}
		function updateListVisibility(list_fields_visibility) {
			$.ajax({
				type: 'POST',
				headers: { "cache-control": "no-cache" },
				url: '{$action}',
				data: {
					ajax: 1,
					action: 'updateListVisivility',
					list_fields_visibility: list_fields_visibility
				},
				cache: false,
				dataType: 'json'
			});
		}
	</script>
	{block name="updatelist"}
		{if $new_list_header_design}
			<div class="list_action_wrapper">
				<div class="row">
					{if $fields_optional|count}
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
								</ul>
							</div>
						</div>
					{/if}
					{if !$simple_header && $show_filters}
						<div class="col-xs-3 col-xs-offset-{if $fields_optional|count}}5{else}9{/if} col-sm-2 col-sm-offset-{if $fields_optional|count}}7{else}10{/if} col-md-1 col-md-offset-{if $fields_optional|count}9{else}11{/if}">
							<div class="list_filter_container">
								<button type="button" class="btn btn-default btn-block" data-toggle="collapse" data-target="#list_filters_panel">
									<i class="icon-sliders"></i>
									<span>{l s='Filters'}
								</button>
							</div>
						</div>
					{/if}
				</div>
				<div class="row">
					<div class="col-sm-12">
						{if $filters_has_value}
							<div id="selected_filter_container">
								{l s='Filters: '}
								<span class="selected_filters">
									{foreach $fields_display AS $key => $params}
										{if (!isset($params.search) || $params.search) && $params.value != ''}
											{if ($params.type == 'date' || $params.type == 'datetime' || $params.type == 'range') && $params.value|is_array}
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
													<span data-filter_key="{if isset($params.name_date)}{$params.name_date}{else}{$key}{/if}" data-filter_type="{$params.type}">
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
											{elseif $params.type == 'bool'}
												<span data-filter_key="{if isset($params.filter_key)}{$params.filter_key}{else}{$key}{/if}" data-filter_type="{$params.type}">
													{$params['title']|escape:'html':'UTF-8'}: <span class="filter_value">{if $params['value'] == 1}{l s='Yes'}{else}{l s='No'}{/if}</span>
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
														{elseif $params.type == 'bool'}
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
																			onClose: function() {
																				let dateFrom = $('#local_{$params.id_date}_0').val().trim();
																				let dateTo = $('#local_{$params.id_date}_1').val().trim();

																				if ((dateFrom && dateTo) && (dateFrom >= dateTo)) {
																					let objDateToMin = $.datepicker.parseDate('yy-mm-dd', dateFrom);
																					objDateToMin.setDate(objDateToMin.getDate());

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
																					objDateToMin.setDate(objDateToMin.getDate());

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
																	<select id="filter_input_{$key}" class="filter{if isset($params.align) && $params.align == 'center'}center{/if} {if isset($params.class)}{$params.class}{/if}" name="{$list_id}Filter_{$params.filter_key}" {if isset($params.width)} style="width:{$params.width}px"{/if}>
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
															<input type="text" id="filter_input_{$key}" class="filter" name="{$list_id}Filter_{if isset($params.filter_key)}{$params.filter_key}{else}{$key}{/if}" value="{$params.value|escape:'html':'UTF-8'}" {if isset($params.width) && $params.width != 'auto'} style="width:{$params.width}px"{/if} />
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
		{/if}
	{/block}
	<div class="panel col-lg-12">
		{block name="panel_heading"}
			<div class="panel-heading">
				{if isset($icon)}<i class="{$icon}"></i> {/if}{if is_array($title)}{$title|end}{else}{$title}{/if}
				{if isset($toolbar_btn) && count($toolbar_btn) >0}
					<span class="badge">{$list_total}</span>
					{block name="panel_heading_action"}
						<span class="panel-heading-action">
						{foreach from=$toolbar_btn item=btn key=k}
							{if $k != 'modules-list' && $k != 'back'}
								<a id="desc-{$table}-{if isset($btn.imgclass)}{$btn.imgclass}{else}{$k}{/if}" class="list-toolbar-btn{if isset($btn.target) && $btn.target} _blank{/if}"{if isset($btn.href)} href="{$btn.href|escape:'html':'UTF-8'}"{/if}{if isset($btn.js) && $btn.js} onclick="{$btn.js}"{/if}>
									<span title="" data-toggle="tooltip" class="label-tooltip" data-original-title="{l s=$btn.desc}" data-html="true" data-placement="top">
										<i class="process-icon-{if isset($btn.imgclass)}{$btn.imgclass}{else}{$k}{/if}{if isset($btn.class)} {$btn.class}{/if}"></i>
									</span>
								</a>
							{/if}
						{/foreach}
							{if $fields_optional|count && !$new_list_header_design}
								<a class="list-toolbar-btn dropdown-toggle" data-toggle="dropdown" data-target="#dropdown-option-toggle">
									<span title="" data-toggle="tooltip" class="label-tooltip" data-original-title="{l s='Toggle list'}" data-html="true" data-placement="top">
									<i class="process-icon-cogs"></i>
								</a>
								<ul id="optional-list-toggle" class="dropdown-menu">
									{foreach $fields_optional as $key => $field}
										<li>
											<label>
												<input type="checkbox" name="list_fields_visibility" value="{$key}" {if isset($field['selected']) && $field['selected']}checked="checked"{/if}>
												{$field['title']}
											</label>
										</li>
									{/foreach}
								</ul>
							{/if}
							<a class="list-toolbar-btn" href="javascript:location.reload();">
								<span title="" data-toggle="tooltip" class="label-tooltip" data-original-title="{l s='Refresh list'}" data-html="true" data-placement="top">
									<i class="process-icon-refresh"></i>
								</span>
							</a>
						{if isset($sql) && $sql}
							{assign var=sql_manager value=Profile::getProfileAccess(Context::getContext()->employee->id_profile, Tab::getIdFromClassName('AdminRequestSql'))}

							{if $sql_manager.view == 1}
								<a class="list-toolbar-btn" href="javascript:void(0);" onclick="$('.leadin').first().append('<div class=\'alert alert-info\'>' + $('#sql_query_{$list_id|escape:'html':'UTF-8'}').val() + '</div>'); $(this).attr('onclick', '');">
									<span class="label-tooltip" data-toggle="tooltip" data-original-title="{l s='Show SQL query'}" data-html="true" data-placement="top" >
										<i class="process-icon-terminal"></i>
									</span>
								</a>
								<a class="list-toolbar-btn" href="javascript:void(0);" onclick="$('#sql_name_{$list_id|escape:'html':'UTF-8'}').val(createSqlQueryName()); $('#sql_query_{$list_id|escape:'html':'UTF-8'}').val($('#sql_query_{$list_id|escape:'html':'UTF-8'}').val().replace(/\s+limit\s+[0-9,\s]+$/ig, '').trim()); $('#sql_form_{$list_id|escape:'html':'UTF-8'}').submit();">
									<span class="label-tooltip" data-toggle="tooltip" data-original-title="{l s='Export to SQL Manager'}" data-html="true" data-placement="top" >
										<i class="process-icon-database"></i>
									</span>
								</a>
							{/if}
						{/if}
						</span>
					{/block}
				{/if}
			</div>
		{/block}
		{if $show_toolbar}
			<script type="text/javascript">
				//<![CDATA[
				var submited = false;
				$(function() {
					//get reference on save link
					btn_save = $('i[class~="process-icon-save"]').parent();
					//get reference on form submit button
					btn_submit = $('#{$table}_form_submit_btn');
					if (btn_save.length > 0 && btn_submit.length > 0) {
						//get reference on save and stay link
						btn_save_and_stay = $('i[class~="process-icon-save-and-stay"]').parent();
						//get reference on current save link label
						lbl_save = $('#desc-{$table}-save div');
						//override save link label with submit button value
						if (btn_submit.val().length > 0) {
							lbl_save.html(btn_submit.attr("value"));
						}
						if (btn_save_and_stay.length > 0) {
							//get reference on current save link label
							lbl_save_and_stay = $('#desc-{$table}-save-and-stay div');
							//override save and stay link label with submit button value
							if (btn_submit.val().length > 0 && lbl_save_and_stay && !lbl_save_and_stay.hasClass('locked')) {
								lbl_save_and_stay.html(btn_submit.val() + " {l s='and stay'} ");
							}
						}
						//hide standard submit button
						btn_submit.hide();
						//bind enter key press to validate form
						$('#{$table}_form').keypress(function (e) {
							if (e.which == 13 && e.target.localName != 'textarea') {
								$('#desc-{$table}-save').click();
							}
						});
						//submit the form
						{block name=formSubmit}
							btn_save.click(function() {
								// Avoid double click
								if (submited) {
									return false;
								}
								submited = true;
								//add hidden input to emulate submit button click when posting the form -> field name posted
								btn_submit.before('<input type="hidden" name="'+btn_submit.attr("name")+'" value="1" />');
								$('#{$table}_form').submit();
								return false;
							});
							if (btn_save_and_stay) {
								btn_save_and_stay.click(function() {
									//add hidden input to emulate submit button click when posting the form -> field name posted
									btn_submit.before('<input type="hidden" name="'+btn_submit.attr("name")+'AndStay" value="1" />');
									$('#{$table}_form').submit();
									return false;
								});
							}
						{/block}
					}
				});
				//]]>
			</script>
		{/if}
{elseif $simple_header}
	<div class="panel col-lg-12">
		{if isset($title)}<h3>{if isset($icon)}<i class="{$icon}"></i> {/if}{if is_array($title)}{$title|end}{else}{$title}{/if}</h3>{/if}
{/if}


	{if $bulk_actions && $has_bulk_actions}
		{assign var=y value=2}
	{else}
		{assign var=y value=1}
	{/if}
	<style>
	@media (max-width: 992px) {
		{foreach from=$fields_display item=param name=params}
			{if isset($params.displayed) && $params.displayed === false}{continue}{/if}
			.table-responsive-row td:nth-of-type({math equation="x+y" x=$smarty.foreach.params.index y=$y}):before {
				content: "{$param.title}";
			}
		{/foreach}
	}
	</style>

	{block name="preTable"}{/block}
	<div class="table-responsive-row clearfix{if isset($use_overflow) && $use_overflow} overflow-y{/if}">
		<table{if $table_id} id="table-{$table_id}"{/if} class="table{if $table_dnd} tableDnD{/if} {$table}" >
			<thead>
			{block name="tableHeadings"}
				<tr class="nodrag nodrop">
					{if $bulk_actions && $has_bulk_actions}
						<th class="center fixed-width-xs"></th>
					{/if}
					{foreach $fields_display AS $key => $params}
					{if isset($params.displayed) && $params.displayed === false}{continue}{/if}
					<th class="{if isset($params.optional) && $params.optional}field_optional{/if}{if isset($params.class)} {$params.class}{/if}{if isset($params.align)} {$params.align}{/if}" data-key="{$key}">
						<span class="title_box{if isset($order_by) && ($key == $order_by)} active{/if}">
							{if isset($params.hint)}
								<span class="label-tooltip" data-toggle="tooltip"
									title="
										{if is_array($params.hint)}
											{foreach $params.hint as $hint}
												{if is_array($hint)}
													{$hint.text}
												{else}
													{$hint}
												{/if}
											{/foreach}
										{else}
											{$params.hint}
										{/if}
									">
									{$params.title}
								</span>
							{else}
								{$params.title}
							{/if}
							{if (!isset($params.orderby) || $params.orderby) && !$simple_header && $show_filters}
								<a {if isset($order_by) && ($key == $order_by) && ($order_way == 'DESC')}class="active"{/if} href="{$currentIndex|escape:'html':'UTF-8'}&amp;{$list_id}Orderby={$key|urlencode}&amp;{$list_id}Orderway=desc&amp;token={$token|escape:'html':'UTF-8'}{if isset($smarty.get.$identifier)}&amp;{$identifier}={$smarty.get.$identifier|intval}{/if}">
									<i class="icon-caret-down"></i>
								</a>
								<a {if isset($order_by) && ($key == $order_by) && ($order_way == 'ASC')}class="active"{/if} href="{$currentIndex|escape:'html':'UTF-8'}&amp;{$list_id}Orderby={$key|urlencode}&amp;{$list_id}Orderway=asc&amp;token={$token|escape:'html':'UTF-8'}{if isset($smarty.get.$identifier)}&amp;{$identifier}={$smarty.get.$identifier|intval}{/if}">
									<i class="icon-caret-up"></i>
								</a>
							{/if}
						</span>
					</th>
					{/foreach}
					{if $shop_link_type}
						<th>
							<span class="title_box">
							{if $shop_link_type == 'shop'}
								{l s='Shop'}
							{else}
								{l s='Shop group'}
							{/if}
							</span>
						</th>
					{/if}
					{if $has_actions || $show_filters}
						<th>{if !$simple_header}{/if}</th>
					{/if}
				</tr>
			{/block}
			{block name="tableFilter"}
				{if !$simple_header && $show_filters && !$new_list_header_design}
					<tr class="nodrag nodrop filter {if $row_hover}row_hover{/if}">
						{if $has_bulk_actions}
							<th class="text-center">
								--
							</th>
						{/if}
						{* Filters (input, select, date or bool) *}
						{foreach $fields_display AS $key => $params}
							{if isset($params.displayed) && $params.displayed === false}{continue}{/if}
							<th class="{if isset($params.optional) && $params.optional}field_optional{/if}{if isset($params.align)} {$params.align}{/if}" data-key="{$key}">
								{if isset($params.search) && !$params.search}
									--
								{else}
									{if $params.type == 'range'}
										<div class="input_range">
											<input type="text" class="filter form-control" name="{$list_id}Filter_{if isset($params.filter_key)}{$params.filter_key}{else}{$key}{/if}[0]" placeholder="{l s='From'}" value="{if isset($params.value.0)}{$params.value.0}{/if}">
											<input type="text" class="filter form-control" name="{$list_id}Filter_{if isset($params.filter_key)}{$params.filter_key}{else}{$key}{/if}[1]" placeholder="{l s='To'}" value="{if isset($params.value.1)}{$params.value.1}{/if}">
										</div>
									{else if $params.type == 'bool'}
										<select class="filter fixed-width-sm center" name="{$list_id}Filter_{if isset($params.filter_key)}{$params.filter_key}{else}{$key}{/if}">
											<option value="">-</option>
											<option value="1" {if $params.value == 1} selected="selected" {/if}>{l s='Yes'}</option>
											<option value="0" {if $params.value == 0 && $params.value != ''} selected="selected" {/if}>{l s='No'}</option>
										</select>
									{elseif $params.type == 'date' || $params.type == 'datetime'}
										<div class="date_range row">
											<div class="input-group fixed-width-md center">
												<input type="text" class="filter datepicker date-input form-control" id="local_{$params.id_date}_0" name="local_{$params.name_date}[0]"  placeholder="{l s='From'}" />
												<input type="hidden" id="{$params.id_date}_0" name="{$params.name_date}[0]" value="{if isset($params.value.0)}{$params.value.0}{/if}">
												<span class="input-group-addon">
													<i class="icon-calendar"></i>
												</span>
											</div>
											<div class="input-group fixed-width-md center">
												<input type="text" class="filter datepicker date-input form-control" id="local_{$params.id_date}_1" name="local_{$params.name_date}[1]"  placeholder="{l s='To'}" />
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
														onClose: function() {
															let dateFrom = $('#local_{$params.id_date}_0').val().trim();
															let dateTo = $('#local_{$params.id_date}_1').val().trim();

															if ((dateFrom && dateTo) && (dateFrom >= dateTo)) {
																let objDateToMin = $.datepicker.parseDate('yy-mm-dd', dateFrom);
																objDateToMin.setDate(objDateToMin.getDate());

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
																objDateToMin.setDate(objDateToMin.getDate());

																$('#local_{$params.id_date}_1').datepicker('option', 'minDate', objDateToMin);
															}
														},
													});
												});
											</script>
										</div>
									{elseif $params.type == 'select'}
										{if isset($params.filter_key)}
											<select class="filter{if isset($params.align) && $params.align == 'center'}center{/if}" name="{$list_id}Filter_{$params.filter_key}" {if isset($params.width)} style="width:{$params.width}px"{/if}>
												<option value="" {if $params.value == ''} selected="selected" {/if}>-</option>
												{if isset($params.list) && is_array($params.list)}
													{foreach $params.list AS $option_value => $option_display}
														<option value="{$option_value}" {if (string)$option_display === (string)$params.value ||  (string)$option_value === (string)$params.value} selected="selected"{/if}>{$option_display}</option>
													{/foreach}
												{/if}
											</select>
										{/if}
									{else}
										<input type="text" class="filter" name="{$list_id}Filter_{if isset($params.filter_key)}{$params.filter_key}{else}{$key}{/if}" value="{$params.value|escape:'html':'UTF-8'}" {if isset($params.width) && $params.width != 'auto'} style="width:{$params.width}px"{/if} />
									{/if}
								{/if}
							</th>
						{/foreach}

						{if $shop_link_type}
							<th>--</th>
						{/if}
						{if $has_actions || $show_filters}
							<th class="actions">
								{if $show_filters}
								<span class="pull-right">
									{*Search must be before reset for default form submit*}
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
							</th>
						{/if}
					</tr>
				{/if}
			{/block}
			</thead>
