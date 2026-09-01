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

{if $order_detail_data}
    <div class="row">
        <div class="col-lg-12">
            <div class="table-responsive">
                <table class="table" id="customer_cart_details">
                    <thead>
                        <tr>
                            <th class="center"><span ><p>{l s='Room'}</p></span></th>
                            <th class="center"><span >{l s='Image'}</span></th>
                            <th class="center"><span class="title_box help-tooltip" data-toggle="tooltip" title="" data-original-title="{l s='Booking check-in and check-out dates'}">{l s='Duration'}</span></th>
                            <th class="center"><span class="title_box help-tooltip" data-toggle="tooltip" title="" data-original-title="{l s='Number of adults and children'}">{l s='Occupancy'}</span></th>
                            <th class="center"><span class="title_box help-tooltip" data-toggle="tooltip" title="" data-original-title="{l s='Guest check-in and check-out date/time'}">{l s='Check-in/Check-out'}</span></th>
                            <th class="center"><span class="title_box help-tooltip" data-toggle="tooltip" title="" data-original-title="{l s='Whether the room was auto-allotted or manually assigned with a remark.'}">{l s='Allotment'}</span></th>
                            <th class="center"><span class="title_box help-tooltip" data-toggle="tooltip" title="" data-original-title="{l s='Base room price for the stay duration, excluding tax'}"><div>{l s='Room Price'}</div><div>{l s='(Tax excl.)'}</div></span></th>
                            <th class="center"><span class="title_box help-tooltip" data-toggle="tooltip" title="" data-original-title="{l s='Total cost of extra services, auto added services and convenience fee for this room, excluding tax'}"><div>{l s='Extra Services'}</div><div>{l s='(Tax excl.)'}</div></span></th>
                            <th class="center"><span class="title_box help-tooltip" data-toggle="tooltip" title="" data-original-title="{l s='Total tax applied on this room booking'}">{l s='Total Tax'}</span></th>
                            <th class="center"><span class="title_box help-tooltip" data-toggle="tooltip" title="" data-original-title="{l s='Total price of the room including extra services/fees, tax included'}"><div>{l s='Total Price'}</div><div>{l s='(Tax incl.)'}</div></span></th>
                            {if (isset($refundReqBookings) && $refundReqBookings)}
                                <th class="center"><span class="title_box help-tooltip" data-toggle="tooltip" title="" data-original-title="{l s='Refund or cancellation status and refunded amount for this room booking'}">{l s='Refund'}</span></th>
                            {/if}
                            {if ($can_edit && !$order->hasBeenDelivered())}
                            <th class="fixed-width-md center"><span >{l s='Actions'}</span></th>
                            {/if}
                        </tr>
                    </thead>
                    <tbody>
                        {foreach from=$order_detail_data item=data}
                            {* Include product line partial *}
                            {include file='controllers/orders/_product_line.tpl'}
                        {/foreach}
                    </tbody>
                </table>
            </div>
        </div>
    </div>
{else}
    <div class="list-empty">
        <div class="list-empty-msg">
            <i class="icon-warning-sign list-empty-icon"></i>
            {l s='Room information not available.'}
        </div>
    </div>
{/if}