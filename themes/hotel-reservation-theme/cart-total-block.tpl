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


<div class="col-sm-12 card cart_total_detail_block">
    {if $total_rooms_wt + $total_extra_demands_wt + $total_additional_services_wt + $total_additional_services_auto_add_wt}
        <p>
            <span>
                {l s='Total rooms cost'}
                {if $display_tax_label}
                    {if $use_taxes && $priceDisplay == 0}
                        {l s='(tax incl)'}
                    {else}
                        {l s='(tax excl)'}
                    {/if}
                {/if}
            </span>
            <span class="cart_total_values">
                {if $use_taxes && $priceDisplay == 0}
                    {assign var='total_rooms_cost' value=($total_rooms_wt + $total_extra_demands_wt + $total_additional_services_wt + $total_additional_services_auto_add_wt)}
                {else}
                    {assign var='total_rooms_cost' value=($total_rooms + $total_extra_demands + $total_additional_services + $total_additional_services_auto_add)}
                {/if}
                {displayPrice price=$total_rooms_cost}
            </span>
        </p>
    {/if}
    {if $total_standalone_service_products}
        <p>
            <span>
                {l s='Total products'}
                {if $display_tax_label}
                    {if $use_taxes && $priceDisplay == 0}
                        {l s='(tax incl)'}
                    {else}
                        {l s='(tax excl)'}
                    {/if}
                {/if}
            </span>
            <span class="cart_total_values">
                {if $use_taxes && $priceDisplay == 0}
                    {displayPrice price=$total_standalone_service_products_wt}
                {else}
                    {displayPrice price=$total_standalone_service_products}
                {/if}
            </span>
        </p>
    {/if}
    {if $convenience_fee_wt}
        <p>
            <span>
                {l s='Convenience Fees'}
                {if $display_tax_label}
                    {if $use_taxes && $priceDisplay == 0}
                        {l s='(tax incl)'}
                    {else}
                        {l s='(tax excl)'}
                    {/if}
                {/if}
            </span>
            <span class="cart_total_values">
            {if $use_taxes && $priceDisplay == 0}
                {displayPrice price=$convenience_fee_wt}
            {else}
                {displayPrice price=$convenience_fee}
            {/if}
            </span>
        </p>
    {/if}
    {block name='displayBeforeCartTotalTax'}
        {hook h='displayBeforeCartTotalTax'}
    {/block}
    {if $show_taxes}
        <p class="cart_total_tax">
            <span>{l s='Total tax'}</span>
            <span class="cart_total_values">{displayPrice price=($total_tax_without_discount)}</span>
        </p>
    {/if}
    <p class="total_discount_block {if $total_discounts == 0}unvisible{/if}">
        <span>
            {if $display_tax_label}
                {if $use_taxes && $priceDisplay == 0}
                    {l s='Total Discount (tax incl)'}
                {else}
                    {l s='Total Discount (tax excl)'}
                {/if}
            {else}
                {l s='Total Discount'}
            {/if}
        </span>
        <span class="cart_total_values">
            {if $use_taxes && $priceDisplay == 0}
                {assign var='total_discounts_negative' value=$total_discounts * -1}
            {else}
                {assign var='total_discounts_negative' value=$total_discounts_tax_exc * -1}
            {/if}
            {displayPrice price=$total_discounts_negative}
        </span>
    </p>
        <hr>
        <p {if !isset($is_advance_payment) || !$is_advance_payment}class="cart_final_total_block"{/if}>
            <span class="strong">{l s='Total'}</span>
            {block name='displayCartTotalPriceLabelTotal'}
                {hook h="displayCartTotalPriceLabel" type='total'}
            {/block}
        <span class="cart_total_values {if isset($is_advance_payment) && $is_advance_payment} strong{/if}">
                {if $use_taxes}
                    {displayPrice price=$total_price}
                {else}
                    {displayPrice price=$total_price_without_tax}
                {/if}
            </span>
        </p>
        {if isset($is_advance_payment) && $is_advance_payment}
            <hr>
            <p>
                <span>{l s='Due Amount'}</span>
                <span class="cart_total_values">{displayPrice price=$dueAmount}</span>
            </p>
            <p class="cart_final_total_block">
                <span class="strong">{l s='Partially Payable Total'}</span>
                {block name='displayCartTotalPriceLabelPartial'}
                    {hook h="displayCartTotalPriceLabel" type='partial'}
                {/block}
                <span class="cart_total_values">{displayPrice price=$advPaymentAmount}</span>
            </p>
        {/if}
</div>