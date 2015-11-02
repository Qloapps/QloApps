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

<input type="hidden" name="warehouse_loaded" value="1">
{if isset($product->id)}
<div id="product-warehouses" class="panel product-tab">
	<input type="hidden" name="submitted_tabs[]" value="Warehouses" />
	<h3>{l s='Product location in warehouses'}</h3>
	<div class="row">
		<div class="alert alert-info" style="display:block; position:'auto';">
			<p>{l s='This interface allows you to specify the warehouse in which the product is stocked.'}</p>
			<p>{l s='You can also specify product/product combinations as it relates to warehouse location. '}</p>
		</div>
		<p>{l s='Please choose the warehouses associated with this product.'}</p>
	</div>
	<div class="row">
		<a class="btn btn-link confirm_leave" href="{$link->getAdminLink('AdminWarehouses')|escape:'html':'UTF-8'}&addwarehouse">{l s='Create a new warehouse'} <i class="icon-external-link-sign"></i></a>
	</div>
	<div class="row">
		<div class="panel-group" id="warehouse-accordion">
			{foreach from=$warehouses item=warehouse name=data}
			    <div class="panel panel-default">
					<div class="panel-heading">
							<a class="accordion-toggle" data-toggle="collapse" data-parent="#warehouse-accordion" href="#warehouse-{$warehouse['id_warehouse']}">{$warehouse['name']}</a>
					</div>
					<div id="warehouse-{$warehouse['id_warehouse']}">
							<table class="table">
								<thead>
									<tr>
										<th class="fixed-width-xs" align="center"><span class="title_box">{l s='Stored'}</span></th>
										<th><span class="title_box">{l s='Product'}</span></th>
										<th><span class="title_box">{l s='Location (optional)'}</span></th>
									</tr>
								</thead>
								<tbody>
								{foreach $attributes AS $index => $attribute}
									{assign var=location value=''}
									{assign var=selected value=''}
									{foreach from=$associated_warehouses item=aw}
										{if $aw->id_product == $attribute['id_product'] && $aw->id_product_attribute == $attribute['id_product_attribute'] && $aw->id_warehouse == $warehouse['id_warehouse']}
											{assign var=location value=$aw->location}
											{assign var=selected value=true}
										{/if}
									{/foreach}
									<tr {if $index is odd}class="alt_row"{/if}>
										<td class="fixed-width-xs" align="center"><input type="checkbox"
											name="check_warehouse_{$warehouse['id_warehouse']}_{$attribute['id_product']}_{$attribute['id_product_attribute']}"
											{if $selected == true}checked="checked"{/if}
											value="1" />
										</td>
										<td>{$product_designation[$attribute['id_product_attribute']]}</td>
										<td><input type="text"
											name="location_warehouse_{$warehouse['id_warehouse']}_{$attribute['id_product']}_{$attribute['id_product_attribute']}"
											value="{$location|escape:'html':'UTF-8'}"
											size="20" />
										</td>
									</tr>
								{/foreach}
							</table>
							{if $attributes|@count gt 1}
							<button type="button" class="btn btn-default check_all_warehouse" value="check_warehouse_{$warehouse['id_warehouse']}"><i class="icon-check-sign"></i> {l s='Mark / Unmark all product combinations as stored in this warehouse'}</button>
							<!--<tr>
								<td><input type="checkbox" class="check_all_warehouse" value="check_warehouse_{$warehouse['id_warehouse']}" /></td>
								<td colspan="2"><i></i></td>
							</tr>-->
							{/if}
						</div>
				</div>
			{/foreach}
		</div>
	</div>
	<div class="panel-footer">
		<a href="{$link->getAdminLink('AdminProducts')|escape:'html':'UTF-8'}{if isset($smarty.request.page) && $smarty.request.page > 1}&amp;submitFilterproduct={$smarty.request.page|intval}{/if}" class="btn btn-default"><i class="process-icon-cancel"></i> {l s='Cancel'}</a>
		<button type="submit" name="submitAddproduct" class="btn btn-default pull-right" disabled="disabled"><i class="process-icon-loading"></i> {l s='Save'}</button>
		<button type="submit" name="submitAddproductAndStay" class="btn btn-default pull-right" disabled="disabled"><i class="process-icon-loading"></i> {l s='Save and stay'}</button>
	</div>
</div>
{/if}
