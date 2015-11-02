{if isset($message)}{$message}{/if}
<fieldset>
	<legend><img src="{$uri}/img/cogs.gif" alt="{l s='Configuration' mod='blocklayered'}" /> {l s='New filters template' mod='blocklayered'}</legend>
	<form action="{$current_url}" method="post" class="form-horizontal" onsubmit="return checkForm();">
		<input type="hidden" name="id_layered_filter" id="id_layered_filter" value="{$id_layered_filter}" />
		<table class="table-configurations">
			<tbody>
				<tr>
					<td class="label">{l s='Template name:' mod='blocklayered'}</td>
					<td>
						<input type="text" id="layered_tpl_name" name="layered_tpl_name" maxlength="64" value="{$template_name}" />
						<p class="help-block">{l s='Only as a reminder' mod='blocklayered'}</p>	
					</td>
				</tr>
				<tr>
					<td class="label">{l s='Categories used for this template:' mod='blocklayered'}</td>
					<td>{$categories_tree}</td>
				</tr>
				{if isset($asso_shops)}
				<tr>
					<td class="label">
						{l s='Choose shop association:' mod='blocklayered'}
					</td>
					<td>{$asso_shops}</td>
				</tr>
				{/if}
				<tr>
					<td class="label">
						{l s='Filters:' mod='blocklayered'}
						<p class="help-block">{l s='You can drag and drop filters to adjust position' mod='blocklayered'}</p>
					</td>
					<td>
						<section class="filter_panel">
							<header>
								{l s='Total filters: %s'|sprintf:$total_filters mod='blocklayered'} - {l s='Selected filters: <span id=\'selected_filters\'>%s</span>'|sprintf:0 mod='blocklayered'}
							</header>
							<section class="filter_list">
								<ul class="list-unstyled sortable">
									<li class="filter_list_item" draggable="true">
										<table>
											<tbody>
												<tr>
													<td><input name="layered_selection_subcategories" id="layered_selection_subcategories" type="checkbox" /></td>
													<td class="filter-title">{l s='Sub-categories filter' mod='blocklayered'}</td>
													<td>{l s='Filter result limit:' mod='blocklayered'}</td>
													<td>
														<select name="layered_selection_subcategories_filter_show_limit">
															<option value="0">{l s='No limit' mod='blocklayered'}</option>
															<option value="4">4</option>
															<option value="5">5</option>
															<option value="10">10</option>
															<option value="20">20</option>
														</select>
													</td>
													<td>{l s='Filter style:' mod='blocklayered'}</td>
													<td>
														<select name="layered_selection_subcategories_filter_type">
															<option value="0">{l s='Checkbox' mod='blocklayered'}</option>
															<option value="1">{l s='Radio button' mod='blocklayered'}</option>
															<option value="2">{l s='Drop-down list' mod='blocklayered'}</option>
														</select>
													</td>
												</tr>
											</tbody>
										</table>
									</li>
									<li class="filter_list_item" draggable="true">
										<table>
											<tbody>
												<tr>
													<td><input name="layered_selection_stock" id="layered_selection_stock" type="checkbox" /></td>
													<td class="filter-title">{l s='Product stock filter' mod='blocklayered'}</td>
													<td>{l s='Filter result limit:' mod='blocklayered'}</td>
													<td>
														<select name="layered_selection_stock_filter_show_limit">
															<option value="0">{l s='No limit' mod='blocklayered'}</option>
															<option value="4">4</option>
															<option value="5">5</option>
															<option value="10">10</option>
															<option value="20">20</option>
														</select>
													</td>
													<td>{l s='Filter style:' mod='blocklayered'}</td>
													<td>
														<select name="layered_selection_stock_filter_type">
															<option value="0">{l s='Checkbox' mod='blocklayered'}</option>
															<option value="1">{l s='Radio button' mod='blocklayered'}</option>
															<option value="2">{l s='Drop-down list' mod='blocklayered'}</option>
														</select>
													</td>
												</tr>
											</tbody>
										</table>
									</li>
									<li class="filter_list_item" draggable="true">
										<table>
											<tbody>
												<tr>
													<td><input name="layered_selection_condition" id="layered_selection_condition" type="checkbox" /></td>
													<td class="filter-title">{l s='Product condition filter' mod='blocklayered'}</td>
													<td>{l s='Filter result limit:' mod='blocklayered'}</td>
													<td>
														<select name="layered_selection_condition_filter_show_limit">
															<option value="0">{l s='No limit' mod='blocklayered'}</option>
															<option value="4">4</option>
															<option value="5">5</option>
															<option value="10">10</option>
															<option value="20">20</option>
														</select>
													</td>
													<td>{l s='Filter style:' mod='blocklayered'}</td>
													<td>
														<select name="layered_selection_condition_filter_type">
															<option value="0">{l s='Checkbox' mod='blocklayered'}</option>
															<option value="1">{l s='Radio button' mod='blocklayered'}</option>
															<option value="2">{l s='Drop-down list' mod='blocklayered'}</option>
														</select>
													</td>
												</tr>
											</tbody>
										</table>
									</li>
									<li class="filter_list_item" draggable="true">
										<table>
											<tbody>
												<tr>
													<td><input name="layered_selection_manufacturer" id="layered_selection_manufacturer" type="checkbox" /></td>
													<td class="filter-title">{l s='Product manufacturer filter' mod='blocklayered'}</td>
													<td>{l s='Filter result limit:' mod='blocklayered'}</td>
													<td>
														<select name="layered_selection_manufacturer_filter_show_limit">
															<option value="0">{l s='No limit' mod='blocklayered'}</option>
															<option value="4">4</option>
															<option value="5">5</option>
															<option value="10">10</option>
															<option value="20">20</option>
														</select>
													</td>
													<td>{l s='Filter style:' mod='blocklayered'}</td>
													<td>
														<select name="layered_selection_manufacturer_filter_type">
															<option value="0">{l s='Checkbox' mod='blocklayered'}</option>
															<option value="1">{l s='Radio button' mod='blocklayered'}</option>
															<option value="2">{l s='Drop-down list' mod='blocklayered'}</option>
														</select>
													</td>
												</tr>
											</tbody>
										</table>
									</li>
									<li class="filter_list_item" draggable="true">
										<table>
											<tbody>
												<tr>
													<td><input name="layered_selection_weight_slider" id="layered_selection_weight_slider" type="checkbox" /></td>
													<td class="filter-title">{l s='Product weight filter (slider)' mod='blocklayered'}</td>
													<td>{l s='Filter result limit:' mod='blocklayered'}</td>
													<td>
														<select name="layered_selection_weight_slider_filter_show_limit">
															<option value="0">{l s='No limit' mod='blocklayered'}</option>
															<option value="4">4</option>
															<option value="5">5</option>
															<option value="10">10</option>
															<option value="20">20</option>
														</select>
													</td>
													<td>{l s='Filter style:' mod='blocklayered'}</td>
													<td>
														<select name="layered_selection_weight_slider_filter_type">
															<option value="0">{l s='Slider' mod='blocklayered'}</option>
															<option value="1">{l s='Inputs area' mod='blocklayered'}</option>
															<option value="2">{l s='List of values' mod='blocklayered'}</option>
														</select>
													</td>
												</tr>
											</tbody>
										</table>
									</li>
									<li class="filter_list_item" draggable="true">
										<table>
											<tbody>
												<tr>
													<td><input name="layered_selection_price_slider" id="layered_selection_price_slider" type="checkbox" /></td>
													<td class="filter-title">{l s='Product price filter (slider)' mod='blocklayered'}</td>
													<td>{l s='Filter result limit:' mod='blocklayered'}</td>
													<td>
														<select name="layered_selection_price_slider_filter_show_limit">
															<option value="0">{l s='No limit' mod='blocklayered'}</option>
															<option value="4">4</option>
															<option value="5">5</option>
															<option value="10">10</option>
															<option value="20">20</option>
														</select>
													</td>
													<td>{l s='Filter style:' mod='blocklayered'}</td>
													<td>
														<select name="layered_selection_price_slider_filter_type">
															<option value="0">{l s='Slider' mod='blocklayered'}</option>
															<option value="1">{l s='Inputs area' mod='blocklayered'}</option>
															<option value="2">{l s='List of values' mod='blocklayered'}</option>
														</select>
													</td>
												</tr>
											</tbody>
										</table>
									</li>
									{if $attribute_groups|count > 0}
										{foreach $attribute_groups as $attribute_group}
										<li class="filter_list_item" draggable="true">
											<table>
												<tbody>
													<tr>
														<td><input name="layered_selection_ag_{(int)$attribute_group['id_attribute_group']}" id="layered_selection_ag_{(int)$attribute_group['id_attribute_group']}" type="checkbox" /></td>
														<td class="filter-title">
															{if $attribute_group['n'] > 1}
																{l s='Attribute group: %1$s (%2$d attributes)'|sprintf:$attribute_group['name']:$attribute_group['n'] mod='blocklayered'}
															{else}
																{l s='Attribute group: %1$s (%2$d attribute)'|sprintf:$attribute_group['name']:$attribute_group['n'] mod='blocklayered'}
															{/if}
															{if $attribute_group['is_color_group']}
																<img src="../img/admin/color_swatch.png" alt="" title="{l s='This group will allow user to select a color' mod='blocklayered'}" />
															{/if}
														</td>
														<td>{l s='Filter result limit:' mod='blocklayered'}</td>
														<td>
															<select name="layered_selection_ag_{(int)$attribute_group['id_attribute_group']}_filter_show_limit">
																<option value="0">{l s='No limit' mod='blocklayered'}</option>
																<option value="4">4</option>
																<option value="5">5</option>
																<option value="10">10</option>
																<option value="20">20</option>
															</select>
														</td>
														<td>{l s='Filter style:' mod='blocklayered'}</td>
														<td>
															<select name="layered_selection_ag_{(int)$attribute_group['id_attribute_group']}_filter_type">
																<option value="0">{l s='Checkbox' mod='blocklayered'}</option>
																<option value="1">{l s='Radio button' mod='blocklayered'}</option>
																<option value="2">{l s='Drop-down list' mod='blocklayered'}</option>
															</select>
														</td>
													</tr>
												</tbody>
											</table>
										</li>
										{/foreach}
									{/if}

									{if $features|count > 0}
										{foreach $features as $feature}
										<li class="filter_list_item" draggable="true">
											<table>
												<tbody>
													<tr>
														<td><input name="layered_selection_feat_{(int)$feature['id_feature']}" id="layered_selection_feat_{(int)$feature['id_feature']}" type="checkbox" /></td>
														<td class="filter-title">
															{if $feature['n'] > 1}{l s='Feature: %1$s (%2$d values)'|sprintf:$feature['name']:$feature['n'] mod='blocklayered'}{else}{l s='Feature: %1$s (%2$d value)'|sprintf:$feature['name']:$feature['n'] mod='blocklayered'}{/if}
														</td>
														<td>{l s='Filter result limit:' mod='blocklayered'}</td>
														<td>
															<select name="layered_selection_feat_{(int)$feature['id_feature']}_filter_show_limit">
																<option value="0">{l s='No limit' mod='blocklayered'}</option>
																<option value="4">4</option>
																<option value="5">5</option>
																<option value="10">10</option>
																<option value="20">20</option>
															</select>
														</td>
														<td>{l s='Filter style:' mod='blocklayered'}</td>
														<td>
															<select name="layered_selection_feat_{(int)$feature['id_feature']}_filter_type">
																<option value="0">{l s='Checkbox' mod='blocklayered'}</option>
																<option value="1">{l s='Radio button' mod='blocklayered'}</option>
																<option value="2">{l s='Drop-down list' mod='blocklayered'}</option>
															</select>
														</td>
													</tr>
												</tbody>
											</table>
										</li>
										{/foreach}
									{/if}
								</ul>
							</section>
						</section>
					</td>
				</tr>
				<tr>
					<td>&nbsp;</td>
					<td>
						<button class="btn btn-default pull-right" id="submit-filter" name="SubmitFilter" type="submit"><i class="process-icon-save"></i> <span>{l s='Save' mod='blocklayered'}</span></button>
						<a class="button" href="{$current_url}">
							<i class="process-icon-cancel"></i> <span>{l s='Cancel' mod='blocklayered'}</span>
						</a>
					</td>
				</tr>
			</tbody>
		</table>
	</form>
</fieldset>

<script type="text/javascript">
	var translations = new Array();
	{if isset($filters)}var filters = '{$filters}';{/if}
	translations['no_selected_categories'] = '{l s='You must select at least one category'|addslashes mod='blocklayered'}';
	translations['no_selected_filters'] = '{l s='You must select at least one filter'|addslashes mod='blocklayered'}';
	translations['selected_filters'] = '{l s='Selected filters: %s'|addslashes mod='blocklayered'}';
</script>
