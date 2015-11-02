{if isset($message)}{$message}{/if}
<div class="panel">
	<h3><i class="icon-cogs"></i> {l s='New filters template' mod='blocklayered'}</h3>
	<form action="{$current_url}" method="post" class="form-horizontal" onsubmit="return checkForm();">
		<input type="hidden" name="id_layered_filter" id="id_layered_filter" value="{$id_layered_filter}" />
		<div class="form-group">
			<label class="control-label col-lg-3">{l s='Template name:' mod='blocklayered'}</label>
			<div class="col-lg-9">
				<input type="text" id="layered_tpl_name" name="layered_tpl_name" maxlength="64" value="{$template_name}" />
				<p class="help-block">{l s='Only as a reminder' mod='blocklayered'}</p>
			</div>
		</div>
		<div class="form-group">
			<label class="control-label col-lg-3">{l s='Categories used for this template:' mod='blocklayered'}</label>
			<div class="col-lg-9">
				{if trim($categories_tree) != ''}
					{$categories_tree}
				{else}
					<div class="alert alert-warning">
						{l s='Categories selection is disabled because you have no categories or you are in a "all shops" context.' mod='blocklayered'}
					</div>
				{/if}
			</div>
		</div>
		{if isset($asso_shops)}
		<div class="form-group">
			<label class="control-label col-lg-3">{l s='Choose shop association:' mod='blocklayered'}</label>
			<div class="col-lg-9">{$asso_shops}</div>
		</div>
		{/if}
		<div class="form-group">
			<label class="control-label col-lg-3">
				<span class="badge" id="selected_filters">0</span>
				<span class="label-tooltip" data-toggle="tooltip" title="" data-original-title="{l s='You can drag and drop filters to adjust position' mod='blocklayered'}">{l s='Filters:' mod='blocklayered'}</span>
			</label>
			<div class="col-lg-9">
				<section class="filter_panel">
					<header class="clearfix">
						<span class="badge pull-right">{l s='Total filters: %s'|sprintf:$total_filters mod='blocklayered'}</span>
					</header>
					<section class="filter_list">
						<ul class="list-unstyled sortable">
							<li class="filter_list_item" draggable="true">
								<div class="col-lg-2">
									<label class="switch-light prestashop-switch fixed-width-lg">
										<input name="layered_selection_subcategories" id="layered_selection_subcategories" type="checkbox" />
										<span>
											<span>{l s='Yes' mod='blocklayered'}</span>
											<span>{l s='No' mod='blocklayered'}</span>
										</span>
										<a class="slide-button btn"></a>
									</label>
								</div>
								<div class="col-lg-4">
									<h4>{l s='Sub-categories filter' mod='blocklayered'}</h4>
								</div>
								<div class="col-lg-3 pull-right">
									<label class="control-label col-lg-6">{l s='Filter result limit:' mod='blocklayered'}</label>
									<div class="col-lg-6">
										<select name="layered_selection_subcategories_filter_show_limit">
											<option value="0">{l s='No limit' mod='blocklayered'}</option>
											<option value="4">4</option>
											<option value="5">5</option>
											<option value="10">10</option>
											<option value="20">20</option>
										</select>
									</div>
								</div>
								<div class="col-lg-3 pull-right">
									<label class="control-label col-lg-6">{l s='Filter style:' mod='blocklayered'}</label>
									<div class="col-lg-6">
										<select name="layered_selection_subcategories_filter_type">
											<option value="0">{l s='Checkbox' mod='blocklayered'}</option>
											<option value="1">{l s='Radio button' mod='blocklayered'}</option>
											<option value="2">{l s='Drop-down list' mod='blocklayered'}</option>
										</select>
									</div>
								</div>
							</li>
							<li class="filter_list_item" draggable="true">
								<div class="col-lg-2">
									<label class="switch-light prestashop-switch fixed-width-lg">
										<input name="layered_selection_stock" id="layered_selection_stock" type="checkbox" />
										<span>
											<span>{l s='Yes' mod='blocklayered'}</span>
											<span>{l s='No' mod='blocklayered'}</span>
										</span>
										<a class="slide-button btn"></a>
									</label>
								</div>
								<div class="col-lg-4">
									<span class="module_name">{l s='Product stock filter' mod='blocklayered'}</span>
								</div>
								<div class="col-lg-3 pull-right">
									<label class="control-label col-lg-6">{l s='Filter result limit:' mod='blocklayered'}</label>
									<div class="col-lg-6">
										<select name="layered_selection_stock_filter_show_limit">
											<option value="0">{l s='No limit' mod='blocklayered'}</option>
											<option value="4">4</option>
											<option value="5">5</option>
											<option value="10">10</option>
											<option value="20">20</option>
										</select>
									</div>
								</div>
								<div class="col-lg-3 pull-right">
									<label class="control-label col-lg-6">{l s='Filter style:' mod='blocklayered'}</label>
									<div class="col-lg-6">
										<select name="layered_selection_stock_filter_type">
											<option value="0">{l s='Checkbox' mod='blocklayered'}</option>
											<option value="1">{l s='Radio button' mod='blocklayered'}</option>
											<option value="2">{l s='Drop-down list' mod='blocklayered'}</option>
										</select>
									</div>
								</div>
							</li>
							<li class="filter_list_item" draggable="true">
								<div class="col-lg-2">
									<label class="switch-light prestashop-switch fixed-width-lg">
										<input name="layered_selection_condition" id="layered_selection_condition" type="checkbox" />
										<span>
											<span>{l s='Yes' mod='blocklayered'}</span>
											<span>{l s='No' mod='blocklayered'}</span>
										</span>
										<a class="slide-button btn"></a>
									</label>
								</div>
								<div class="col-lg-4">
									<span class="module_name">{l s='Product condition filter' mod='blocklayered'}</span>
								</div>
								<div class="col-lg-3 pull-right">
									<label class="control-label col-lg-6">{l s='Filter result limit:' mod='blocklayered'}</label>
									<div class="col-lg-6">
										<select name="layered_selection_condition_filter_show_limit">
											<option value="0">{l s='No limit' mod='blocklayered'}</option>
											<option value="4">4</option>
											<option value="5">5</option>
											<option value="10">10</option>
											<option value="20">20</option>
										</select>
									</div>
								</div>
								<div class="col-lg-3 pull-right">
									<label class="control-label col-lg-6">{l s='Filter style:' mod='blocklayered'}</label>
									<div class="col-lg-6">
										<select name="layered_selection_condition_filter_type">
											<option value="0">{l s='Checkbox' mod='blocklayered'}</option>
											<option value="1">{l s='Radio button' mod='blocklayered'}</option>
											<option value="2">{l s='Drop-down list' mod='blocklayered'}</option>
										</select>
									</div>
								</div>
							</li>
							<li class="filter_list_item" draggable="true">
								<div class="col-lg-2">
									<label class="switch-light prestashop-switch fixed-width-lg">
										<input name="layered_selection_manufacturer" id="layered_selection_manufacturer" type="checkbox" />
										<span>
											<span>{l s='Yes' mod='blocklayered'}</span>
											<span>{l s='No' mod='blocklayered'}</span>
										</span>
										<a class="slide-button btn"></a>
									</label>
								</div>
								<div class="col-lg-4">
									<span class="module_name">{l s='Product manufacturer filter' mod='blocklayered'}</span>
								</div>
								<div class="col-lg-3 pull-right">
									<label class="control-label col-lg-6">{l s='Filter result limit:' mod='blocklayered'}</label>
									<div class="col-lg-6">
										<select name="layered_selection_manufacturer_filter_show_limit">
											<option value="0">{l s='No limit' mod='blocklayered'}</option>
											<option value="4">4</option>
											<option value="5">5</option>
											<option value="10">10</option>
											<option value="20">20</option>
										</select>
									</div>
								</div>
								<div class="col-lg-3 pull-right">
									<label class="control-label col-lg-6">{l s='Filter style:' mod='blocklayered'}</label>
									<div class="col-lg-6">
										<select name="layered_selection_manufacturer_filter_type">
											<option value="0">{l s='Checkbox' mod='blocklayered'}</option>
											<option value="1">{l s='Radio button' mod='blocklayered'}</option>
											<option value="2">{l s='Drop-down list' mod='blocklayered'}</option>
										</select>
									</div>
								</div>
							</li>
							<li class="filter_list_item" draggable="true">
								<div class="col-lg-2">
									<label class="switch-light prestashop-switch fixed-width-lg">
										<input name="layered_selection_weight_slider" id="layered_selection_weight_slider" type="checkbox" />
										<span>
											<span>{l s='Yes' mod='blocklayered'}</span>
											<span>{l s='No' mod='blocklayered'}</span>
										</span>
										<a class="slide-button btn"></a>
									</label>
								</div>
								<div class="col-lg-4">
									<span class="module_name">{l s='Product weight filter (slider)' mod='blocklayered'}</span>
								</div>
								<div class="col-lg-3 pull-right">
									<label class="control-label col-lg-6">{l s='Filter result limit:' mod='blocklayered'}</label>
									<div class="col-lg-6">
										<select name="layered_selection_weight_slider_filter_show_limit">
											<option value="0">{l s='No limit' mod='blocklayered'}</option>
											<option value="4">4</option>
											<option value="5">5</option>
											<option value="10">10</option>
											<option value="20">20</option>
										</select>
									</div>
								</div>
								<div class="col-lg-3 pull-right">
									<label class="control-label col-lg-6">{l s='Filter style:' mod='blocklayered'}</label>
									<div class="col-lg-6">
										<select name="layered_selection_weight_slider_filter_type">
											<option value="0">{l s='Slider' mod='blocklayered'}</option>
											<option value="1">{l s='Inputs area' mod='blocklayered'}</option>
											<option value="2">{l s='List of values' mod='blocklayered'}</option>
										</select>
									</div>
								</div>
							</li>
							<li class="filter_list_item" draggable="true">
								<div class="col-lg-2">
									<label class="switch-light prestashop-switch fixed-width-lg">
										<input name="layered_selection_price_slider" id="layered_selection_price_slider" type="checkbox" />
										<span>
											<span>{l s='Yes' mod='blocklayered'}</span>
											<span>{l s='No' mod='blocklayered'}</span>
										</span>
										<a class="slide-button btn"></a>
									</label>
								</div>
								<div class="col-lg-4">
									<span class="module_name">{l s='Product price filter (slider)' mod='blocklayered'}</span>
								</div>
								<div class="col-lg-3 pull-right">
									<label class="control-label col-lg-6">{l s='Filter result limit:' mod='blocklayered'}</label>
									<div class="col-lg-6">
										<select name="layered_selection_price_slider_filter_show_limit">
											<option value="0">{l s='No limit' mod='blocklayered'}</option>
											<option value="4">4</option>
											<option value="5">5</option>
											<option value="10">10</option>
											<option value="20">20</option>
										</select>
									</div>
								</div>
								<div class="col-lg-3 pull-right">
									<label class="control-label col-lg-6">{l s='Filter style:' mod='blocklayered'}</label>
									<div class="col-lg-6">
										<select name="layered_selection_price_slider_filter_type">
											<option value="0">{l s='Slider' mod='blocklayered'}</option>
											<option value="1">{l s='Inputs area' mod='blocklayered'}</option>
											<option value="2">{l s='List of values' mod='blocklayered'}</option>
										</select>
									</div>
								</div>
							</li>
							{if $attribute_groups|count > 0}
								{foreach $attribute_groups as $attribute_group}
								<li class="filter_list_item" draggable="true">
									<div class="col-lg-2">
										<label class="switch-light prestashop-switch fixed-width-lg">
											<input name="layered_selection_ag_{(int)$attribute_group['id_attribute_group']}" id="layered_selection_ag_{(int)$attribute_group['id_attribute_group']}" type="checkbox" />
											<span>
												<span>{l s='Yes' mod='blocklayered'}</span>
												<span>{l s='No' mod='blocklayered'}</span>
											</span>
											<a class="slide-button btn"></a>
										</label>
									</div>
									<div class="col-lg-4">
										<span class="module_name">
										{if $attribute_group['n'] > 1}
											{l s='Attribute group: %1$s (%2$d attributes)'|sprintf:$attribute_group['name']:$attribute_group['n'] mod='blocklayered'}
										{else}
											{l s='Attribute group: %1$s (%2$d attribute)'|sprintf:$attribute_group['name']:$attribute_group['n'] mod='blocklayered'}
										{/if}
										{if $attribute_group['is_color_group']}
											<img src="../img/admin/color_swatch.png" alt="" title="{l s='This group will allow user to select a color' mod='blocklayered'}" />
										{/if}
										</span>
									</div>
									<div class="col-lg-3 pull-right">
										<label class="control-label col-lg-6">{l s='Filter result limit:' mod='blocklayered'}</label>
										<div class="col-lg-6">
											<select name="layered_selection_ag_{(int)$attribute_group['id_attribute_group']}_filter_show_limit">
												<option value="0">{l s='No limit' mod='blocklayered'}</option>
												<option value="4">4</option>
												<option value="5">5</option>
												<option value="10">10</option>
												<option value="20">20</option>
											</select>
										</div>
									</div>
									<div class="col-lg-3 pull-right">
										<label class="control-label col-lg-6">{l s='Filter style:' mod='blocklayered'}</label>
										<div class="col-lg-6">
											<select name="layered_selection_ag_{(int)$attribute_group['id_attribute_group']}_filter_type">
												<option value="0">{l s='Checkbox' mod='blocklayered'}</option>
												<option value="1">{l s='Radio button' mod='blocklayered'}</option>
												<option value="2">{l s='Drop-down list' mod='blocklayered'}</option>
											</select>
										</div>
									</div>
								</li>
								{/foreach}
							{/if}

							{if $features|count > 0}
								{foreach $features as $feature}
								<li class="filter_list_item" draggable="true">
									<div class="col-lg-2">
										<label class="switch-light prestashop-switch fixed-width-lg">
											<input name="layered_selection_feat_{(int)$feature['id_feature']}" id="layered_selection_feat_{(int)$feature['id_feature']}" type="checkbox" />
											<span>
												<span>{l s='Yes' mod='blocklayered'}</span>
												<span>{l s='No' mod='blocklayered'}</span>
											</span>
											<a class="slide-button btn"></a>
										</label>
									</div>
									<div class="col-lg-4">
										<span class="module_name">
									{if $feature['n'] > 1}{l s='Feature: %1$s (%2$d values)'|sprintf:$feature['name']:$feature['n'] mod='blocklayered'}{else}{l s='Feature: %1$s (%2$d value)'|sprintf:$feature['name']:$feature['n'] mod='blocklayered'}{/if}
										</span>
									</div>
									<div class="col-lg-3 pull-right">
										<label class="control-label col-lg-6">{l s='Filter result limit:' mod='blocklayered'}</label>
										<div class="col-lg-6">
											<select name="layered_selection_feat_{(int)$feature['id_feature']}_filter_show_limit">
												<option value="0">{l s='No limit' mod='blocklayered'}</option>
												<option value="4">4</option>
												<option value="5">5</option>
												<option value="10">10</option>
												<option value="20">20</option>
											</select>
										</div>
									</div>
									<div class="col-lg-3 pull-right">
										<label class="control-label col-lg-6">{l s='Filter style:' mod='blocklayered'}</label>
										<div class="col-lg-6">
											<select name="layered_selection_feat_{(int)$feature['id_feature']}_filter_type">
												<option value="0">{l s='Checkbox' mod='blocklayered'}</option>
												<option value="1">{l s='Radio button' mod='blocklayered'}</option>
												<option value="2">{l s='Drop-down list' mod='blocklayered'}</option>
											</select>
										</div>
									</div>
								</li>
								{/foreach}
							{/if}
						</ul>
					</section>
				</section>
			</div>
		</div>
		<div class="panel-footer" id="toolbar-footer">
			<button class="btn btn-default pull-right" id="submit-filter" name="SubmitFilter" type="submit"><i class="process-icon-save"></i> <span>{l s='Save' mod='blocklayered'}</span></button>
			<a class="btn btn-default" href="{$current_url}">
				<i class="process-icon-cancel"></i> <span>{l s='Cancel' mod='blocklayered'}</span>
			</a>
		</div>
	</form>
</div>

<script type="text/javascript">
	var translations = new Array();
	{if isset($filters)}var filters = '{$filters}';{/if}
	translations['no_selected_categories'] = '{l s='You must select at least one category'|addslashes mod='blocklayered'}';
	translations['no_selected_filters'] = '{l s='You must select at least one filter'|addslashes mod='blocklayered'}';
</script>
