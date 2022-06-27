{**
* 2010-2021 Webkul.
*
* NOTICE OF LICENSE
*
* All right is reserved,
* Please go through LICENSE.txt file inside our module
*
* DISCLAIMER
*
* Do not edit or add to this file if you wish to upgrade this module to newer
* versions in the future. If you wish to customize this module for your
* needs please refer to CustomizationPolicy.txt file inside our module for more information.
*
* @author Webkul IN
* @copyright 2010-2021 Webkul IN
* @license LICENSE.txt
*}

<div class="panel col-lg-12">
    <div class="panel-heading">
        <i class="icon-credit-card"></i>
        {l s='PayPal Transaction Details' mod='qlopaypalcommerce'}
    </div>
    <div class="panel-body">
        <div class="table-responsive col-sm-12">
            <table class="table table-bordered table-hover table-striped row">
                {if $transaction_data}
                    <tr class="row">
                        <th class="col-sm-2"><strong>{l s='Payment Environment' mod='qlopaypalcommerce'}</strong></th>
                        <td class="col-sm-10">
                            {if $transaction_data.environment == 'sandbox'}
                                {l s='Sandbox' mod='qlopaypalcommerce'}
                            {else}
                                {l s='Production' mod='qlopaypalcommerce'}
                            {/if}
                        </td>
                    </tr>
                    <tr class="row">
                        <th class="col-sm-2"><strong>{l s='PayPal Reference ID' mod='qlopaypalcommerce'}</strong></th>
                        <td class="col-sm-10">{$transaction_data.pp_reference_id|escape:'html':'UTF-8'}</td>
                    </tr>
                    <tr class="row">
                        <th class="col-sm-2"><strong>{l s='PayPal Transaction ID' mod='qlopaypalcommerce'}</strong></th>
                        <td class="col-sm-10">{$transaction_data.pp_transaction_id|escape:'html':'UTF-8'}</td>
                    </tr>
                    <tr class="row">
                        <th class="col-sm-2"><strong>{l s='PayPal Order ID' mod='qlopaypalcommerce'}</strong></th>
                        <td class="col-sm-10">{$transaction_data.pp_order_id|escape:'html':'UTF-8'}</td>
                    </tr>
                    <tr class="row">
                        <th class="col-sm-2"><strong>{l s='Transaction Amount' mod='qlopaypalcommerce'}</strong></th>
                        <td class="col-sm-10">{$transaction_data.pp_paid_total_formated|escape:'html':'UTF-8'}</td>
                    </tr>
                    <tr class="row">
                        <th class="col-sm-2"><strong>{l s='QloApps Order Reference' mod='qlopaypalcommerce'}</strong></th>
                        <td class="col-sm-10">
                            {$transaction_data.order_reference|escape:'html':'UTF-8'}
                        </td>
                    </tr>
                    <tr class="row">
                        <th class="col-sm-2"><strong>{l s='Customer Name' mod='qlopaypalcommerce'}</strong></th>
                        <td class="col-sm-10">{$transaction_data.customer_name|escape:'html':'UTF-8'}</td>
                    </tr>
                    <tr class="row">
                        <th class="col-sm-2"><strong>{l s='Customer Email' mod='qlopaypalcommerce'}</strong></th>
                        <td class="col-sm-10">
                            {$transaction_data.email|escape:'html':'UTF-8'}&nbsp;
                            <a class="btn btn-default btn-xs" target="_blank" href="{$transaction_data.customer_link|escape:'html':'UTF-8'}" title="{l s='View Customer' mod='qlopaypalcommerce'}">
                                <i class="icon-eye"></i>
                                {l s='View' mod='qlopaypalcommerce'}
                            </a>
                        </td>
                    </tr>
                    <tr class="row">
                        <th class="col-sm-2"><strong>{l s='Payment Status' mod='qlopaypalcommerce'}</strong></th>
                        <td class="col-sm-10">
                            {if $transaction_data.pp_payment_status == 'COMPLETED'}
                                <label class="label label-success">{l s='COMPLETED' mod='qlopaypalcommerce'}</label>
                            {else}
                                <div class="row ppstatusDetail">
                                    <label class="label label-danger col-md-1 col-sm-2 col-xs-4">{$transaction_data.pp_payment_status|escape:'html':'UTF-8'}</label>{if isset($ppstatusDetailMsg) && $ppstatusDetailMsg} &nbsp;<label class="ppstatusDetailMsg label label-warning  col-md-10 col-sm-9 col-xs-7"><i class="icon-info-circle"></i> {$ppstatusDetailMsg|escape:'htmlall':'UTF-8'}</label>{/if}
                                </div>
                            {/if}
                        </td>
                    </tr>
                    <tr class="row">
                        <th class="col-sm-2"><strong>{l s='Payment Date' mod='qlopaypalcommerce'}</strong></th>
                        <td class="col-sm-10">{$transaction_data.order_date|escape:'html':'UTF-8'}</td>
                    </tr>
                    {if $refund_data && $refunded_amount}
                        <tr class="row">
                            <th class="col-sm-2"><strong>{l s='Total refunded Amount' mod='qlopaypalcommerce'}</strong></th>
                            <td class="col-sm-10">{$refunded_amount|escape:'html':'UTF-8'}</td>
                        </tr>
                    {/if}
                {/if}
            </table>
        </div>
    </div>
</div>

{* If transaction is completed then admin can refund the transaction *}
{if $transaction_data and $transaction_data.pp_payment_status == 'COMPLETED'}
    {* show refund form only if there is any remaing amount for refund *}
    {if $remaining_refund > 0}
        <form action="" method="post" class="form-horizontal" id="refund_form">
            <div class="panel col-lg-12">
                <div class="panel-heading">
                    <i class="icon-reply"></i>
                    {l s='Refund Transaction' mod='qlopaypalcommerce'}
                </div>
                <div class="form-wrapper">
                    <div class="alert alert-info">
                        {if $refund_data && $refunded_amount}
                            <p>{l s='Total refunded amount' mod='qlopaypalcommerce'} : <strong>{$refunded_amount|escape:'html':'UTF-8'}</strong><p>
                        {/if}
                        <p>{l s='Total available amount to refund' mod='qlopaypalcommerce'}: <strong>{$remaining_refund_format|escape:'html':'UTF-8'}</strong><p>
                    </div>

                    <div class="form-group">
                        <label class="col-sm-3 control-label">
                            <span class="label-tooltip" data-toggle="tooltip" data-html="true" data-original-title="{l s='Select the type of the refund. Select partial refund for refunding partial amount from the transaction. Select full refund to refund total transaction amount.' mod='qlopaypalcommerce'}">{l s='Refund Type' mod='qlopaypalcommerce'}</span>
                        </label>
                        <div class="col-lg-5">
                            <select class="form-control" name="refund_type" id="pp_refund_type" required>
                                <option value="">{l s='Select Refund Type' mod='qlopaypalcommerce'}</option>
                                <option value="{$WK_PAYPAL_COMMERCE_REFUND_TYPE_FULL|escape:'htmlall':'UTF-8'}">{l s='Full Refund' mod='qlopaypalcommerce'}</option>
                                <option value="{$WK_PAYPAL_COMMERCE_REFUND_TYPE_PARTIAL|escape:'htmlall':'UTF-8'}">{l s='Partial Refund' mod='qlopaypalcommerce'}</option>
                            </select>
                        </div>
                    </div>
                    <div class="form-group" id="pp-amount-block" style="display:none;">
                        <label class="col-sm-3 control-label required">
                            <span class="label-tooltip" data-toggle="tooltip" data-html="true" data-original-title="{l s='Enter the amount you want to refund from the transaction amount.' mod='qlopaypalcommerce'}">{l s='Refund Amount' mod='qlopaypalcommerce'} ({$currency->iso_code|escape:'htmlall':'UTF-8'})</span>
                        </label>
                        <div class="col-lg-5">
                            <input type="number" class="form-control" name="refund_amount" min="0.01" max="{$remaining_refund|escape:'htmlall':'UTF-8'}" step=".01" id="pp_refund_amount" placeholder="{l s='max.' mod='qlopaypalcommerce'} {$remaining_refund_format|escape:'html':'UTF-8'}">
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-3 control-label required">
                            <span class="label-tooltip" data-toggle="tooltip" data-html="true" data-original-title="{l s='Enter a remark for this refund.' mod='qlopaypalcommerce'}">{l s='Refund Remark' mod='qlopaypalcommerce'}</span>
                        </label>
                        <div class="col-lg-5">
                            <input type="text" class="form-control" name="refund_reason" maxlength="255" required>
                        </div>
                    </div>
                </div>

                <input type="hidden" name="refund_paypal_form" value="1" >
                <input type="hidden" name="id_paypal_commerce_order" value="{$transaction_data.id_paypal_commerce_order|escape:'html':'UTF-8'}" >
                <div class="panel-footer">
                    <button type="submit" value="1" id="refund_form_submit_btn" name="submitAddPaypalRefund" class="btn btn-default pull-right">
                        <i class="process-icon-save"></i> {l s='Refund' mod='qlopaypalcommerce'}
                    </button>
                </div>
            </div>
        </form>
    {/if}

    {* list of the refunds from this transaction *}
    <div class="panel col-lg-12">
        <div class="panel-heading">
            <i class="icon-list"></i>
            {l s='Refund List' mod='qlopaypalcommerce'}
        </div>
        <div class="panel-body">
            <div class="table-responsive">
                <table class="table table-bordered table-hover table-striped">
                    <tr>
                        <th style="text-align: center">{l s='Transaction ID' mod='qlopaypalcommerce'}</th>
                        <th style="text-align: center">{l s='Amount' mod='qlopaypalcommerce'}</th>
                        <th style="text-align: center">{l s='Type' mod='qlopaypalcommerce'}</th>
                        <th style="text-align: center">{l s='Status' mod='qlopaypalcommerce'}</th>
                        <th style="text-align: center">{l s='Date' mod='qlopaypalcommerce'}</th>
                    </tr>
                    {if $refund_data}
                        {foreach $refund_data as $key => $refund}
                            <tr>
                                <td style="text-align: center">{$refund.paypal_refund_id|escape:'html':'UTF-8'}</td>
                                <td style="text-align: center">{$refund.amount_refunded_formatted|escape:'html':'UTF-8'}</td>
                                <td style="text-align: center">
                                    {if $refund.refund_type == 1}
                                        {l s='Full Refund' mod='qlopaypalcommerce'}
                                    {else}
                                        {l s='Partial Refund' mod='qlopaypalcommerce'}
                                    {/if}
                                </td>
                                <td style="text-align: center">
                                    {if $refund.refund_status == 'COMPLETED'}
                                        <label class="label label-success">{l s='COMPLETED' mod='qlopaypalcommerce'}</label>
                                    {else}
                                        <label class="label label-danger">{$refund.refund_status|escape:'html':'UTF-8'}</label>
                                    {/if}
                                </td>
                                <td style="text-align: center">{$refund.date_add|escape:'html':'UTF-8'}</td>
                            </tr>
                        {/foreach}
                    {else}
                        <tr>
                            <td class="list-empty" colspan="6">
                                <div class="list-empty-msg">
                                    <i class="icon-warning-sign list-empty-icon"></i>
                                    {l s='No Refunds Created yet.' mod='qlopaypalcommerce'}
                                </div>
                            </td>
                        </tr>
                    {/if}
                </table>
                {if $refund_data && $refunded_amount}
                    <div style="text-align: center;">
                       <h4>{l s='Total refunded amount: ' mod='qlopaypalcommerce'} {$refunded_amount|escape:'html':'UTF-8'}</h4>
                    </div>
                {/if}
            </div>
        </div>
    </div>
{/if}
