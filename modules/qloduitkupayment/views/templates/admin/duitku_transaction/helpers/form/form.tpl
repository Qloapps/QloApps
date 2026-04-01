{*
* NOTICE OF LICENSE
*
* This source file is subject to the Academic Free License (AFL 3.0)
* that is bundled with this package in the file LICENSE.md
* It is also available through the world-wide-web at this URL:
* https://opensource.org/licenses/afl-3.0.php
* If you did not receive a copy of the license and are unable to
* obtain it through the world-wide-web, please send an email
* to support@qloapps.com so we can send you a copy immediately.
*
* DISCLAIMER
*
* Do not edit or add to this file if you wish to upgrade this module to a newer
* versions in the future. If you wish to customize this module for your needs
* please refer to https://store.webkul.com/customisation-guidelines for more information.
*
* @author Webkul IN
* @copyright Since 2010 Webkul
* @license https://opensource.org/licenses/afl-3.0.php Academic Free License 3.0
*}


<div class="panel">
    <div class="panel-heading">
        <i class="icon-money"></i>
        {l s='Duitku Transaction Details' mod='qloduitkupayment'}
    </div>
    <div class="panel-body">
        <div class="table-responsive col-sm-12">
            {if isset($transaction_info) && $transaction_info}
                <table class="table table-bordered table-hover table-striped row">
                    {if $transaction_info}
                        <tr class="row">
                            <th class="col-sm-2"><strong>{l s='Payment Environment' mod='qloduitkupayment'}</strong></th>
                            <td class="col-sm-10">
                                {if $transaction_info['environment'] == $QDP_DUITKU_ENVIRONMENT_SANDBOX}
                                    {l s='Sandbox' mod='qloduitkupayment'}
                                {else}
                                    {l s='Production' mod='qloduitkupayment'}
                                {/if}
                            </td>
                        </tr>
                        <tr class="row">
                            <th class="col-sm-2"><strong>{l s='Duitku Transaction ID' mod='qloduitkupayment'}</strong></th>
                            <td class="col-sm-10">{$transaction_info['id_transaction']|escape:'html':'UTF-8'}</td>
                        </tr>
                        <tr class="row">
                            <th class="col-sm-2"><strong>{l s='Order Reference' mod='qloduitkupayment'}</strong></th>
                            <td class="col-sm-10">{$transaction_info['reference']|escape:'htmlall':'UTF-8'}</td>
                        </tr>
                        <tr class="row">
                            <th class="col-sm-2"><strong>{l s='Transaction Amount' mod='qloduitkupayment'}</strong></th>
                            <td class="col-sm-10">{displayPrice price=$transaction_info['cart_total'] currency=$currency->id}
                            </td>
                        </tr>
                        <tr class="row">
                            <th class="col-sm-2"><strong>{l s='Customer Name' mod='qloduitkupayment'}</strong></th>
                            <td class="col-sm-10">
                                {$transaction_info['customer_name']|escape:'html':'UTF-8'}
                                <a class="btn btn-default btn-xs" target="_blank"
                                    href="{$transaction_info['customer_link']|escape:'html':'UTF-8'}"
                                    title="{l s='View Customer' mod='qloduitkupayment'}">
                                    <i class="icon-eye"></i>
                                    {l s='View' mod='qloduitkupayment'}
                                </a>
                            </td>
                        </tr>
                        <tr class="row">
                            <th class="col-sm-2"><strong>{l s='Payment Status' mod='qloduitkupayment'}</strong></th>
                            <td class="col-sm-10">
                                {if $transaction_info.status == $TRANSACTION_COMPLETED}
                                    <label class="label label-success">{l s='Completed' mod='qloduitkupayment'}</label>
                                {elseif $transaction_info.status == $TRANSACTION_AWAITING}
                                    <label class="label label-primary">{l s='Awaiting' mod='qloduitkupayment'}</label>
                                {elseif $transaction_info.status == $TRANSACTION_FAILED}
                                    <div class="row ppstatusDetail">
                                        <label class="label label-danger">{l s='Failed' mod='qloduitkupayment'}</label>
                                    </div>
                                {/if}
                            </td>
                        </tr>
                        <tr class="row">
                            <th class="col-sm-2"><strong>{l s='Payment Date' mod='qloduitkupayment'}</strong></th>
                            <td class="col-sm-10">{$transaction_info['date_add']|escape:'html':'UTF-8'}</td>
                        </tr>

                    {/if}
                </table>
            {else}
                <div class="alert alert-warning">
                    {l s='Transaction information not found. Please try again.' mod='qloduitkupayment'}
                </div>
            {/if}
        </div>
    </div>
</div>