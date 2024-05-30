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

<div class="modal-body">
    <form id="order_discount_form" action="{$current_index}&amp;vieworder&amp;token={$smarty.get.token|escape:'html':'UTF-8'}&amp;id_order={$order->id|intval}" method="post">
        <div class="form-group">
            <label class="control-label">{l s='Name'}</label>
            <input class="form-control" type="text" name="discount_name" value="" />
        </div>

        <div class="form-group row">
            <div class="col-sm-6">
                <label class="control-label">{l s='Type'}</label>
                <select class="form-control" name="discount_type" id="discount_type">
                    <option value="1">{l s='Percent'}</option>
                    <option value="2">{l s='Amount'}</option>
                </select>
            </div>
            <div class="col-sm-6" id="discount_value_field">
                <label class="control-label">{l s='Value'}</label>
                <div>
                    <div class="input-group">
                        <div class="input-group-addon">
                            <span id="discount_currency_sign" style="display: none;">{$currency->sign}</span>
                            <span id="discount_percent_symbol">%</span>
                        </div>
                        <input class="form-control" type="text" name="discount_value"/>
                    </div>
                    <p class="text-muted" id="discount_value_help" style="display: none;">
                        {l s='This value must include taxes.'}
                    </p>
                </div>
            </div>
        </div>
        {if $order->hasInvoice()}
            <div class="row">
                <div class="col-sm-12">
                    <label class="control-label">{l s='Invoice'}</label>
                    <select name="discount_invoice">
                        {foreach from=$invoices_collection item=invoice}
                        <option value="{$invoice->id}" selected="selected">
                            {$invoice->getInvoiceNumberFormatted($current_id_lang)} - {displayPrice price=$invoice->total_paid_tax_incl currency=$order->id_currency}
                        </option>
                        {/foreach}
                    </select>
                </div>
            </div>
            <div class="row">
                <div class="col-sm-12">
                    <p class="checkbox">
                        <label class="control-label" for="discount_all_invoices">
                            <input type="checkbox" name="discount_all_invoices" id="discount_all_invoices" value="1" />
                            {l s='Apply on all invoices'}
                        </label>
                    </p>
                    <p class="help-block">
                        {l s='If you chooses to create this discount for all invoices, only one discount will be created per order invoice.'}
                    </p>
                </div>
            </div>
        {/if}
        <button class="btn btn-default" type="submit" name="submitNewVoucher" style="display:none" id="submitNewVoucher"></button>
    </form>

    {if isset($loaderImg) && $loaderImg}
        <div class="loading_overlay">
            <img src='{$loaderImg}' class="loading-img"/>
        </div>
    {/if}
</div>
