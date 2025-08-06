{*
* Since 2010 Webkul.
*
* NOTICE OF LICENSE
*
* All right is reserved,
* Please go through this link for complete license : https://store.webkul.com/license.html
*
* DISCLAIMER
*
* Do not edit or add to this file if you wish to upgrade this module to newer
* versions in the future. If you wish to customize this module for your
* needs please refer to https://store.webkul.com/customisation-guidelines/ for more information.
*
*  @author    Webkul IN <support@webkul.com>
*  @copyright Since 2010 Webkul IN
*  @license   https://store.webkul.com/license.html
*}

<div class="modal-body">
    <form id="form_add_payment" method="post" action="{$current_index}&amp;vieworder&amp;id_order={$order->id}&amp;token={$smarty.get.token|escape:'html':'UTF-8'}">
        <div class="form-group row">
            <div class="col-sm-6">
                <label class="control-label">{l s='Date'}</label>
                <div class="input-group">
                    <input type="text" name="payment_date" class="datepicker" value="{date('Y-m-d')}" />
                    <div class="input-group-addon">
                        <i class="icon-calendar-o"></i>
                    </div>
                </div>
            </div>
            <div class="col-sm-6">
                <label class="control-label">{l s='Payment method'}</label>
                <div>
                    <input name="payment_method" list="payment_method" class="form-control payment_method">
                    <datalist id="payment_method">
                        {foreach from=$payment_methods item=payment_method}
                            <option value="{$payment_method}">
                        {/foreach}
                    </datalist>
                </div>
            </div>
        </div>
        <div class="form-group row">
            <div class="col-sm-6">
                <label class="control-label">{l s='Payment source'}</label>
                <select name="payment_type" class="payment_type form-control">
                    {foreach from=$payment_types item=payment_type}
                        <option value="{$payment_type['value']}">{$payment_type['name']}</option>
                    {/foreach}
                </select>
            </div>
            <div class="col-sm-6">
                <label class="control-label">{l s='Transaction ID'}</label>
                <input type="text" name="payment_transaction_id" value="" class="form-control"/>
            </div>
        </div>
        <div class="form-group row">
            <div class="col-sm-6">
                <label class="control-label">{l s='Amount'}</label>
                <input type="text" name="payment_amount" value="" class="form-control" />
            </div>
            <div class="col-sm-6">
                <label class="control-label">{l s='Currency'}</label>
                <select name="payment_currency" class="payment_currency form-control pull-left">
                    {foreach from=$currencies item=current_currency}
                        <option value="{$current_currency['id_currency']}"{if $current_currency['id_currency'] == $currency->id} selected="selected"{/if}>{$current_currency['sign']}</option>
                    {/foreach}
                </select>
            </div>
        </div>
        {if count($invoices_collection) > 0}
            <div class="form-group row">
                <div class="col-sm-6">
                    <label class="control-label">{l s='Invoice'}</label>
                    <select name="payment_invoice" id="payment_invoice">
                        {foreach from=$invoices_collection item=invoice}
                            <option value="{$invoice->id}" selected="selected">{$invoice->getInvoiceNumberFormatted($current_id_lang, $order->id_shop)}</option>
                        {/foreach}
                    </select>
                </div>
            </div>
        {/if}
        <button style="display:none" class="btn btn-primary pull-right" type="submit" name="submitAddPayment" id="submitAddPayment">
            {l s='Add payment'}
        </button>
    </form>

    {if isset($loaderImg) && $loaderImg}
        <div class="loading_overlay">
            <img src='{$loaderImg}' class="loading-img"/>
        </div>
    {/if}
</div>
