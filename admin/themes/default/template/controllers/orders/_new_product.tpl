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
<tr id="new_product" style="display:none">
	<td style="display:none;" colspan="2" class="text-center">
		<input type="hidden" id="add_product_product_id" name="add_product[product_id]" value="0" />

		<div class="form-group">
			<label>{l s='Room Type:'}</label>
			<div class="input-group">
				<input type="text" id="add_product_product_name" value=""/>
				<div class="input-group-addon">
					<i class="icon-search"></i>
				</div>
			</div>
		</div>
		<!-- <div id="add_product_product_attribute_area" class="form-group" style="display: none;">
			<label>{l s='Combinations'}</label>
			<select name="add_product[product_attribute_id]" id="add_product_product_attribute_id"></select>
		</div>

		<div id="add_product_product_warehouse_area" class="form-group" style="display: none;">
			<label>{l s='Warehouse'}</label>
			<select  id="add_product_warehouse" name="add_product_warehouse"></select>
		</div> -->
	</td>
	<td style="display:none;" class="bookingDuration text-center" colspan="2">
		<center>
			<div class="room_check_in_div">
				<div class="input-group fixed-width-xl">
					<div class="input-group-addon">{l s='Check In'}</div>
					<input type="text" class="form-control add_product_date_from" name="add_product[date_from]" readonly />
					<div class="input-group-addon"><i class="icon-calendar"></i></div>
				</div>
			</div>
			<br/>
			<div class="room_check_out_div">
				<div class="input-group fixed-width-xl">
					<div class="input-group-addon">{l s='Check Out'}</div>
					<input type="text" class="form-control add_product_date_to" name="add_product[date_to]" readonly/>
					<div class="input-group-addon"><i class="icon-calendar"></i></div>
				</div>
			</div>
		</center>
	</td>
	<td style="display:none;" class="text-center" colspan="2">
		<center>
			<div class="row">
				<div class="input-group fixed-width-xl">
					{if $currency->format % 2}<div class="input-group-addon">{$currency->sign} {l s='tax excl.'}</div>{/if}
					<input type="text" name="add_product[product_price_tax_excl]" id="add_product_product_price_tax_excl" value="" readonly="true" />
					{if !($currency->format % 2)}<div class="input-group-addon">{$currency->sign} {l s='tax excl.'}</div>{/if}
				</div>
			</div>
			<br/>
			<div class="row">
				<div class="input-group fixed-width-xl">
					{if $currency->format % 2}<div class="input-group-addon">{$currency->sign} {l s='tax incl.'}</div>{/if}
					<input type="text" name="add_product[product_price_tax_incl]" id="add_product_product_price_tax_incl" value="" readonly="true" />
					{if !($currency->format % 2)}<div class="input-group-addon">{$currency->sign} {l s='tax incl.'}</div>{/if}
				</div>
			</div>
		</center>
	</td>
	<td style="display:none;" class="productQuantity text-center">
		<center>
			<input type="number" class="form-control fixed-width-sm" name="add_product[product_quantity]" id="add_product_product_quantity" value="1" disabled="disabled" min="1"/>
		</center>
	</td>
	{*{if ($order->hasBeenPaid())}<td style="display:none;" class="productQuantity"></td>{/if}
	{if $display_warehouse}<td></td>{/if}
	{if ($order->hasBeenDelivered())}<td style="display:none;" class="productQuantity"></td>{/if}
	<td style="display:none;" class="productQuantity" id="add_product_product_stock">0</td>*}
	<td style="display:none;" id="add_product_product_total"  class="text-center">{displayPrice price=0 currency=$currency->id}</td>
	<td style="display:none;" colspan="2"  class="text-center">
		{if sizeof($invoices_collection)}
		<select class="form-control" name="add_product[invoice]" id="add_product_product_invoice" disabled="disabled">
			<optgroup class="existing" label="{l s='Existing'}">
				{foreach from=$invoices_collection item=invoice}
				<option value="{$invoice->id}">{$invoice->getInvoiceNumberFormatted($current_id_lang)}</option>
				{/foreach}
			</optgroup>
			<optgroup label="{l s='New'}">
				<option value="0">{l s='Create a new invoice'}</option>
			</optgroup>
		</select>
		{/if}
	</td>
	<td style="display:none;"  class="text-center">
		<button type="button" class="btn btn-default" id="cancelAddProduct">
			<i class="icon-remove text-danger"></i>
			{l s='Cancel'}
		</button>
		<button type="button" class="btn btn-default" id="submitAddProduct" disabled="disabled">
			<i class="icon-ok text-success"></i>
			{l s='Add'}
		</button>
	</td>
</tr>

<!-- <tr id="new_invoice" style="display:none">
	<td colspan="10">
		<h4>{l s='New invoice information'}</h4>
		<div class="form-horizontal">
			<div class="form-group">
				<label class="control-label col-lg-3">{l s='Carrier'}</label>
				<div class="col-lg-9">
					<p class="form-control-static"><strong>{$carrier->name}</strong></p>
				</div>
			</div>
			<div class="form-group">
				<label class="control-label col-lg-3">{l s='Shipping Cost'}</label>
				<div class="col-lg-9">
					<div class="checkbox">
						<label>
							<input type="checkbox" name="add_invoice[free_shipping]" value="1" />
							{l s='Free shipping'}
						</label>
						<p class="help-block">{l s='If you don\'t select "Free shipping," the normal shipping cost will be applied.'}</p>
					</div>
				</div>
			</div>
		</div>
	</td>
</tr>
 -->
