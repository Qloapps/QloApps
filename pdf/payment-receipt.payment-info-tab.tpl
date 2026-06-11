{*
* NOTICE OF LICENSE
*
* This source file is subject to the Open Software License version 3.0
* that is bundled with this package in the file LICENSE.md
* It is also available through the world-wide-web at this URL:
* https://opensource.org/license/osl-3-0-php
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
* @license https://opensource.org/license/osl-3-0-php Open Software License version 3.0
*}

{if isset($payment_list) && $payment_list}
    <table class="bordered-table" width="100%" cellpadding="4" cellspacing="0">
        <thead>
            <tr>
                <th class="header" colspan="5">{l s='Payment Details' pdf='true'}</th>
            </tr>
            <tr class="header left">
                <th class="product header-left small ">{l s='Payment Method' pdf='true'}</th>
                <th class="product header-left small ">{l s='Payment Type' pdf='true'}</th>
                <th class="product header-left small ">{l s='Transaction ID' pdf='true'}</th>
                <th class="product header-left small ">{l s='Amount' pdf='true'}</th>
                <th class="product header-left small">{l s='Payment Date' pdf='true'}</th>
            </tr>
        </thead>
        <tbody>
            {foreach from=$payment_list key=rm_k item=payment}
                {cycle values=["color_line_even", "color_line_odd"] assign=bgcolor_class}
                <tr class="white {$bgcolor_class}">
                    <td class="product {if $payment->payment_method|default:'-' == '-'}center{else}left{/if}">{$payment->payment_method|default:'-'}</td>
                    <td class="product {if $payment->payment_type|default:'-' == '-'}center{else}left{/if}">{$payment->payment_type.name|default:'-'}</td>
                    <td class="product {if $payment->transaction_id|default:'-' == '-'}center{else}left{/if}">{$payment->transaction_id|default:'-'}</td>
                    <td class="product {if $payment->amount|default:'-' == '-'}center{else}left{/if}">{displayPrice currency=$payment->id_currency price=$payment->amount}</td>
                    <td class="product {if $payment->date_add|default:'-' == '-'}center{else}left{/if}">{dateFormat date=$payment->date_add full=true}</td>
                </tr>
            {/foreach}
        </tbody>
    </table>
{/if}