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

<input type="hidden" name="supplier_loaded" value="1">
{if isset($product->id)}
<div id="product-suppliers" class="panel product-tab">
	<input type="hidden" name="submitted_tabs[]" value="Suppliers" />
	<h3>{l s='Suppliers of the current product'}</h3>
	<div class="alert alert-info">
		{l s='This interface allows you to specify the suppliers of the current product and eventually its combinations.'}<br />
		{l s='It is also possible to specify supplier references according to previously associated suppliers.'}<br />
		<br />
		{l s='When using the advanced stock management tool (see Preferences/Products), the values you define (prices, references) will be used in supply orders.'}
	</div>
	<label>{l s='Please choose the suppliers associated with this product. Please select a default supplier, as well.'}</label>
	<table class="table">
		<thead>
			<tr>
				<th class="fixed-width-xs"><span class="title_box">{l s='Selected'}</span></th>
				<th><span class="title_box">{l s='Supplier Name'}</span></th>
				<th class="fixed-width-xs"><span class="title_box">{l s='Default'}</span></th>
			</tr>
		</thead>
		<tbody>
		{foreach from=$suppliers item=supplier}
			<tr>
				<td><input type="checkbox" class="supplierCheckBox" name="check_supplier_{$supplier['id_supplier']}" {if $supplier['is_selected'] == true}checked="checked"{/if} value="{$supplier['id_supplier']}" /></td>
				<td>{$supplier['name']}</td>
				<td><input type="radio" id="default_supplier_{$supplier['id_supplier']}" name="default_supplier" value="{$supplier['id_supplier']}" {if $supplier['is_selected'] == false}disabled="disabled"{/if} {if $supplier['is_default'] == true}checked="checked"{/if} /></td>
			</tr>
		{/foreach}
		</tbody>
	</table>
	<a class="btn btn-link bt-icon confirm_leave" href="{$link->getAdminLink('AdminSuppliers')|escape:'html':'UTF-8'}&addsupplier">
		<i class="icon-plus"></i> {l s='Create a new supplier'} <i class="icon-external-link-sign"></i>
	</a>
	<div class="panel-footer">
		<a href="{$link->getAdminLink('AdminProducts')|escape:'html':'UTF-8'}{if isset($smarty.request.page) && $smarty.request.page > 1}&amp;submitFilterproduct={$smarty.request.page|intval}{/if}" class="btn btn-default"><i class="process-icon-cancel"></i> {l s='Cancel'}</a>
		<button type="submit" name="submitAddproduct" class="btn btn-default pull-right" disabled="disabled"><i class="process-icon-loading"></i> {l s='Save'}</button>
		<button type="submit" name="submitAddproductAndStay" class="btn btn-default pull-right" disabled="disabled"><i class="process-icon-loading"></i> {l s='Save and stay'}</button>
	</div>
</div>
<div class="panel">
	<h3>{l s='Supplier reference(s)'}</h3>
	<div class="alert alert-info">
		{if $associated_suppliers|@count == 0}
			{l s='You must specify the suppliers associated with this product. You must also select the default product supplier before setting references.'}
		{else}
			{l s='You can specify product reference(s) for each associated supplier.'}
		{/if}
		{l s='Click "Save and Stay" after changing selected suppliers to display the associated product references.'}
	</div>
	<div class="panel-group" id="accordion-supplier">
		{foreach from=$associated_suppliers item=supplier name=data}
		<div class="panel">
			<div class="panel-heading">
				<a class="accordion-toggle" data-toggle="collapse" data-parent="#accordion-supplier" href="#supplier-{$supplier->id}">{if isset($supplier->name)}{$supplier->name}{/if}</a>
			</div>
			<div id="supplier-{$supplier->id}">
				<div class="panel-body">
					<table class="table">
						<thead>
							<tr>
								<th><span class="title_box">{l s='Product name'}</span></th>
								<th><span class="title_box">{l s='Supplier reference'}</span></th>
								<th><span class="title_box">{l s='Unit price tax excluded'}</span></th>
								<th><span class="title_box">{l s='Unit price currency'}</span></th>
							</tr>
						</thead>
						<tbody>
						{foreach $attributes AS $index => $attribute}
							{assign var=reference value=''}
							{assign var=price_te value=''}
							{assign var=id_currency value=$id_default_currency}
							{foreach from=$associated_suppliers_collection item=asc}
								{if $asc->id_product == $attribute['id_product'] && $asc->id_product_attribute == $attribute['id_product_attribute'] && $asc->id_supplier == $supplier->id_supplier}
									{assign var=reference value=$asc->product_supplier_reference}
									{assign var=price_te value=Tools::ps_round($asc->product_supplier_price_te, 2)}
									{if $asc->id_currency}
										{assign var=id_currency value=$asc->id_currency}
									{/if}
								{/if}
							{/foreach}
							<tr {if $index is odd}class="alt_row"{/if}>
								<td>{$product_designation[$attribute['id_product_attribute']]}</td>
								<td>
									<input type="text" value="{$reference|escape:'html':'UTF-8'}" name="supplier_reference_{$attribute['id_product']}_{$attribute['id_product_attribute']}_{$supplier->id_supplier}" />
								</td>
								<td>
									<input type="text" value="{$price_te|htmlentities}" name="product_price_{$attribute['id_product']}_{$attribute['id_product_attribute']}_{$supplier->id_supplier}" />
								</td>
								<td>
									<select name="product_price_currency_{$attribute['id_product']}_{$attribute['id_product_attribute']}_{$supplier->id_supplier}">
										{foreach $currencies AS $currency}
											<option value="{$currency['id_currency']}"
												{if $currency['id_currency'] == $id_currency}selected="selected"{/if}
											>{$currency['name']}</option>
										{/foreach}
									</select>
								</td>
							</tr>
						{/foreach}
						</tbody>
					</table>
				</div>
			</div>
		</div>
		{/foreach}
	</div>
	<div class="panel-footer">
		<a href="{$link->getAdminLink('AdminProducts')|escape:'html':'UTF-8'}{if isset($smarty.request.page) && $smarty.request.page > 1}&amp;submitFilterproduct={$smarty.request.page|intval}{/if}" class="btn btn-default"><i class="process-icon-cancel"></i> {l s='Cancel'}</a>
		<button type="submit" name="submitAddproduct" class="btn btn-default pull-right" disabled="disabled"><i class="process-icon-loading"></i> {l s='Save'}</button>
		<button type="submit" name="submitAddproductAndStay" class="btn btn-default pull-right" disabled="disabled"><i class="process-icon-loading"></i> {l s='Save and stay'}</button>
	</div>
</div>
{/if}
