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

{capture name=path}
    <a href="{$link->getPageLink('my-account', true)|escape:'html':'UTF-8'}">
        {l s='My account'}
    </a>
    <span class="navigation-pipe">
        {$navigationPipe}
    </span>
    <a href="{$link->getPageLink('history', true)|escape:'html':'UTF-8'}">
        {l s='Bookings'}
    </a>
    <span class="navigation-pipe">
        {$navigationPipe}
    </span>
    <span class="navigation_page">
        {l s='Booking details'}
    </span>
{/capture}

<h1 class="page-heading bottom-indent">
    {l s='Booking Details'}
</h1>

{include file="$tpl_dir./errors.tpl"}

{if isset($order) && $order}
    <div class="row">
        <div class="col-lg-12">
            <div class="well well-md well-order-date">
                {l s='Booking Reference ' sprintf=$order->getUniqReference()}<strong>{$order->getUniqReference()}</strong>{l s=' - placed on'}
                <span title="{dateFormat date=$order->date_add full=1}">{dateFormat date=$order->date_add}</span>
            </div>
        </div>
    </div>

    {$HOOK_ORDERDETAILDISPLAYED}

    <div class="row" id="order_detail_container">
        <div class="col-md-8">
            {hook h='displayOrderDetailTopLeft' id_order=$order->id}

            <div class="card hotel-details">
                <div class="card-header">
                    {l s='Hotel Details'}
                </div>
                <div class="card-body">
                    {if Validate::isLoadedObject($obj_hotel_branch_information)}
                        <div class="description-list">
                            <dl class="">
                                <div class="row">
                                    <dt class="col-xs-6 col-sm-3">{l s='Hotel Name'}</dt>
                                    <dd class="col-xs-6 col-sm-3">{$obj_hotel_branch_information->hotel_name}</dd>
                                    <dt class="col-xs-6 col-sm-3">{l s='Phone Number'}</dt>
                                    <dd class="col-xs-6 col-sm-3">
                                        <a href="tel:{if $hotel_address_info.phone_mobile}{$hotel_address_info.phone_mobile}{else}{$hotel_address_info.phone}{/if}">
                                            {if $hotel_address_info.phone_mobile}{$hotel_address_info.phone_mobile}{else}{$hotel_address_info.phone}{/if}
                                        </a>
                                    </dd>
                                    <dt class="col-xs-6 col-sm-3">{l s='Email'}</dt>
                                    <dd class="col-xs-6 col-sm-3">
                                        <a href="mailto:{$obj_hotel_branch_information->email}" class="hotel-email">{$obj_hotel_branch_information->email}</a>
                                    </dd>
                                    {hook h='displayOrderDetailHotelDetailsAfter' id_order=$order->id}
                                </div>
                            </dl>
                        </div>
                    {else}
                        <div class="card-text">{l s='Hotel details not available.'}</div>
                    {/if}
                </div>
            </div>

            <div class="card payment-details visible-xs visible-sm hidden-md hidden-lg">
                <div class="card-header">
                    {l s='Payment Details'}
                </div>
                <div class="card-body">
                    <div class="detail-row">
                        <div class=" title">{l s='Payment Method'}</div>
                        <div class=" value payment-method">
                            {if $invoice && $invoiceAllowed}
                                <span class="icon-pdf"></span>
                                <a target="_blank" href="{$link->getPageLink('pdf-invoice', true)}?id_order={$order->id|intval}{if $is_guest}&amp;secure_key={$order->secure_key|escape:'html':'UTF-8'}{/if}" title="{l s='Click here to download invoice.'}">
                                    <span>{$order->payment|escape:'html':'UTF-8'}</span>
                                </a>
                            {else}
                                {$order->payment|escape:'html':'UTF-8'}
                            {/if}
                        </div>
                    </div>

                    <div class="detail-row">
                        <div class="pull-left title">{l s='Status'}</div>
                        <div class="pull-right value status">
                            {if isset($order_history[0]) && $order_history[0]}
                                <span{if isset($order_history[0].color) && $order_history[0].color} style="background-color:{$order_history[0].color|escape:'html':'UTF-8'}30; border: 1px solid {$order_history[0].color|escape:'html':'UTF-8'};" {/if} class="label">
                                    {if $order_history[0].id_order_state|in_array:$overbooking_order_states}
                                        {l s='Order Not Confirmed'}
                                    {else}
                                        {$order_history[0].ostate_name|escape:'html':'UTF-8'}
                                    {/if}
                                </span>
                            {else}
                                <span class="processing">{l s='Processing'}</span>
                            {/if}
                        </div>
                    </div>

                    {hook h='displayOrderDetailPaymentDetailsRow' id_order=$order->id}
                </div>
            </div>

            <div class="card hotel-location visible-xs visible-sm hidden-md hidden-lg">
                <div class="card-header">
                    {l s='Hotel Location'}
                </div>
                <div class="card-body">
                    <p class="card-subtitle">
                        {l s='Address'}
                    </p>

                    {if isset($hotel_address_info) && $hotel_address_info}
                        <p class="hotel-address">
                            {$hotel_address_info['address1']},
                            {if {$hotel_address_info['address2']}}{$hotel_address_info['address2']},{/if}
                            {$hotel_address_info['city']},
                            {if {$hotel_address_info['state']}}{$hotel_address_info['state']},{/if}
                            {$hotel_address_info['country']}, {$hotel_address_info['postcode']}
                        </p>
                    {else}
                        <div class="card-text">{l s='Hotel location not available.'}</div>
                    {/if}

                    {if ($obj_hotel_branch_information->latitude|floatval != 0 && $obj_hotel_branch_information->longitude|floatval != 0)}
                        <div class="hotel-location-map">
                            <div class="booking-hotel-map-container"></div>
                        </div>
                    {/if}

                    {hook h='displayOrderDetailHotelLocationAfter' id_order=$order->id}
                </div>
            </div>

            {if isset($refundReqBookings) && $refundReqBookings}
                <div class="alert alert-info-light cancel_requests_link_wrapper">
                    <i class="icon-info-circle"></i>
                    {if count($refundReqBookings) <= 1}
                        <span>{l s='Your cancellation request for 1 room is being processed. To check request status'} <a target="_blank" href="{$link->getPageLink('order-follow')|escape:'html':'UTF-8'}?id_order={$order->id|escape:'html':'UTF-8'}">{l s='click here.'}</a>
                    {else}
                        <span>{l s='Your cancellation request for %d rooms is being processed. To check request statuses' sprintf=[count($refundReqBookings)]} <a target="_blank" href="{$link->getPageLink('order-follow')|escape:'html':'UTF-8'}?id_order={$order->id|escape:'html':'UTF-8'}">{l s='click here.'}</a>
                    {/if}
                </div>
            {/if}

            {hook h='displayOrderDetailRoomDetailsBefore' id_order=$order->id}
            <div class="card room-details">
                <div class="card-header">
                    {l s='Room Details'}
                    <div class="booking-actions-wrap">
                        <div class="row">
                            <div class="col-xs-12 clearfix">
                                {if $refund_allowed}
                                    {if !$completeRefundRequestOrCancel}
                                        <a id="order_refund_request" class="btn btn-default pull-right" href="#" title="{l s='Proceed to refund'}"><span>{l s='Cancel Bookings'}</span></a>
                                    {/if}
                                    {if isset($id_cms_refund_policy) && $id_cms_refund_policy}
                                        <a target="_blank" class="btn btn-default pull-right refund_policy_link" href="{$link->getCMSLink($id_cms_refund_policy)|escape:'html':'UTF-8'}">{l s='Refund Policies'}</a>
                                    {/if}
                                {/if}
                                {hook h='displayBookingAction' id_order=$order->id}
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    {if isset($cart_htl_data) && $cart_htl_data}
                        <div class="rooms-list">
                            {foreach from=$cart_htl_data key=data_k item=data_v}
                                {foreach from=$data_v['date_diff'] key=rm_k item=rm_v}
                                    {include file='./_partials/order-room-detail.tpl'}
                                {/foreach}
                            {/foreach}

                            {hook h='displayOrderDetailRoomDetailsRoomsAfter' id_order=$order->id}
                        </div>
                    {else}
                        <div class="no-rooms card-text">{l s='Room details not available.'}</div>
                    {/if}
                </div>
            </div>

            <div class="card payment-summary visible-xs visible-sm hidden-md hidden-lg">
                <div class="card-header">
                    {l s='Payment Summary'}
                </div>
                <div class="card-body">
                    <div class="prices-breakdown-table">
                        <table class="table table-sm table-responsive table-summary">
                            <tbody>
                                {assign var=room_price_tax_excl value=$order->getTotalProductsWithoutTaxes(false, true)}
                                {assign var=room_price_tax_incl value=$order->getTotalProductsWithTaxes(false, true)}
                                {assign var=additional_service_price_tax_excl value=($order->getTotalProductsWithoutTaxes(false, false, Product::SERVICE_PRODUCT_WITH_ROOMTYPE) + $total_demands_price_te)}
                                {assign var=additional_service_price_tax_incl value=($order->getTotalProductsWithTaxes(false, false, Product::SERVICE_PRODUCT_WITH_ROOMTYPE) + $total_demands_price_ti)}
                                {if $priceDisplay && $use_tax && $room_price_tax_excl}
                                    <tr>
                                        <td>{l s='Total rooms cost (tax excl.)'}</td>
                                        <td class="text-right">
                                            <span class="price">{displayWtPriceWithCurrency price=($room_price_tax_excl + $additional_service_price_tax_excl - $total_convenience_fee_te) currency=$currency}</span>
                                        </td>
                                    </tr>
                                {else}
                                    <tr>
                                        <td>{l s='Total Rooms Cost'} {if $use_tax}{l s='(tax incl.)'}{/if} </td>
                                        <td class="text-right">
                                            <span class="price">{displayWtPriceWithCurrency price=($room_price_tax_incl + $additional_service_price_tax_incl - $total_convenience_fee_ti) currency=$currency}</span>
                                        </td>
                                    </tr>
                                {/if}

                                {if $order->total_discounts > 0}
                                    <tr>
                                        <td>{l s='Total Vouchers'}</td>
                                        <td class="text-right">
                                            <span class="price price-discount">-{displayWtPriceWithCurrency price=$order->total_discounts currency=$currency convert=1}</span>
                                        </td>
                                    </tr>
                                {/if}
                                {if $priceDisplay && $use_tax && $total_convenience_fee_te}
                                    <tr>
                                        <td>{l s='Total Convenience Fees (tax excl.)'}</td>
                                        <td class="text-right">
                                            <span class="price">{displayWtPriceWithCurrency price=$total_convenience_fee_te currency=$currency}</span>
                                        </td>
                                    </tr>
                                {else if $total_convenience_fee_ti}
                                    <tr>
                                        <td>{l s='Total Convenience Fees (tax incl.)'}</td>
                                        <td class="text-right">
                                            <span class="price">{displayWtPriceWithCurrency price=$total_convenience_fee_ti currency=$currency}</span>
                                        </td>
                                    </tr>
                                {/if}

                                <tr class="totalprice item">
                                    <td>{l s='Total Tax'}</td>
                                    <td class="text-right">
                                        <span class="price">{displayWtPriceWithCurrency price=($order->total_paid - $order->total_paid_tax_excl) currency=$currency}</span>
                                    </td>
                                </tr>
                                <tr class="totalprice item">
                                    <td><strong>{l s='Final Booking Total'}<strong></td>
                                    <td class="text-right">
                                        <strong><span class="price">{displayWtPriceWithCurrency price=$order->total_paid currency=$currency}</span></strong>
                                    </td>
                                </tr>

                                {if isset($refundReqBookings) && $refundReqBookings}
                                    <tr class="totalprice item">
                                        <td>{l s='* Refunded Amount'}</td>
                                        <td class="text-right">
                                            <span class="price">{displayWtPriceWithCurrency price=$refundedAmount currency=$currency}</span>
                                        </td>
                                    </tr>
                                {/if}

                                {if $order->total_paid_tax_incl > $order->total_paid_real}
                                    <tr class="totalprice item">
                                        <td>{l s='Due Amount'}</td>
                                        <td class="text-right">
                                            <span class="price">{displayWtPriceWithCurrency price=($order->total_paid_tax_incl - $order->total_paid_real) currency=$currency}</span>
                                        </td>
                                    </tr>
                                {/if}

                                {hook h='displayOrderDetailPaymentSummaryRow' id_order=$order->id}
                            </tbody>
                        </table>
                    </div>

                    {hook h='displayOrderDetailPaymentSummaryAfter' id_order=$order->id}
                </div>
            </div>

            <div class="card guest-details visible-xs visible-sm hidden-md hidden-lg">
                <div class="card-header">
                    {l s='Guest Details'}
                </div>
                <div class="card-body">
                    <div class="guest-details-table">
                        <table class="table table-sm table-responsive table-summary">
                            <tbody>
                                {if $customerGuestDetail}
                                    {if isset($customerGuestDetail->firstname) && $customerGuestDetail->firstname}
                                        <tr>
                                            <td>{l s='Name'}</td>
                                            <td class="text-right">{$customerGuestDetail->firstname|escape:'html':'UTF-8'} {$customerGuestDetail->lastname|escape:'html':'UTF-8'}</td>
                                        </tr>
                                    {/if}
                                    {if isset($customerGuestDetail->email) && $customerGuestDetail->email}
                                        <tr>
                                            <td>{l s='Email'}</td>
                                            <td class="text-right">{$customerGuestDetail->email|escape:'html':'UTF-8'}</td>
                                        </tr>
                                    {/if}
                                    {if isset($customerGuestDetail->phone) && $customerGuestDetail->phone}
                                        <tr>
                                            <td>{l s='Mobile'}</td>
                                            <td class="text-right">{$customerGuestDetail->phone|escape:'html':'UTF-8'}</td>
                                        </tr>
                                    {/if}
                                {else}
                                    <tr>
                                        <td>{l s='Name'}</td>
                                        <td class="text-right">
                                            {if isset($address_invoice->firstname) && $address_invoice->firstname}
                                                {$address_invoice->firstname|escape:'html':'UTF-8'} {$address_invoice->lastname|escape:'html':'UTF-8'}
                                            {elseif isset($guestInformations['firstname']) && $guestInformations['firstname']}
                                                {$guestInformations['firstname']|escape:'html':'UTF-8'} {$guestInformations['lastname']|escape:'html':'UTF-8'}
                                            {/if}
                                        </td>
                                    </tr>

                                    <tr>
                                        <td>{l s='Email'}</td>
                                        <td class="text-right">{$guestInformations['email']|escape:'html':'UTF-8'}</td>
                                    </tr>

                                    {if isset($address_invoice->phone_mobile) && $address_invoice->phone_mobile}
                                        <tr>
                                            <td>{l s='Mobile'}</td>
                                            <td class="text-right">{$address_invoice->phone_mobile|escape:'html':'UTF-8'}</td>
                                        </tr>
                                    {/if}

                                    {if isset($address_invoice->phone) && $address_invoice->phone}
                                        <tr>
                                            <td>{l s='Phone'}</td>
                                            <td class="text-right">{$address_invoice->phone|escape:'html':'UTF-8'}</td>
                                        </tr>
                                    {/if}
                                {/if}

                                {hook h='displayOrderDetailGuestDetailsRow' id_order=$order->id}
                            </tbody>
                        </table>
                    </div>

                    {hook h='displayOrderDetailGuestDetailsAfter' id_order=$order->id}
                </div>
            </div>

            {assign var=has_general_hotel_policies value=(isset($obj_hotel_branch_information->policies) && $obj_hotel_branch_information->policies)}
            {assign var=has_refund_hotel_policies value=($obj_hotel_branch_information->isRefundable() && $hotel_refund_rules)}
            {if $has_general_hotel_policies || $has_refund_hotel_policies}
                <div class="card hotel-policies card-tabs">
                    <div class="card-header">
                        <ul class="nav nav-tabs">
                            {if $has_general_hotel_policies}
                                <li class="active">
                                    <a href="#tab-hotel-policies-general" data-toggle="tab">{l s='Hotel Policies'}</a>
                                </li>
                            {/if}
                            {if $has_refund_hotel_policies}
                                <li {if !$has_general_hotel_policies}class="active"{/if}>
                                    <a href="#tab-hotel-policies-refund" data-toggle="tab">{l s='Refund Policies'}</a>
                                </li>
                            {/if}
                            {hook h='displayOrderDetailPoliciesTab' id_order=$order->id}
                        </ul>
                    </div>
                    <div class="card-body">
                        <div class="tab-content">
                            {if $has_general_hotel_policies}
                                <div id="tab-hotel-policies-general" class="tab-pane active">
                                    <div class="card-text">{$obj_hotel_branch_information->policies}</div>
                                </div>
                            {/if}
                            {if $has_refund_hotel_policies}
                                <div id="tab-hotel-policies-refund" class="tab-pane{if !$has_general_hotel_policies}active{/if}">
                                    <div class="refund-policies-list">
                                        {foreach from=$hotel_refund_rules item=hotel_refund_rule name=foreach_refund_rules}
                                            <div class="refund-policy">
                                                <p class="refund-rule-name">{l s='%s. ' sprintf=[$smarty.foreach.foreach_refund_rules.iteration]}{$hotel_refund_rule.name|escape:'html':'UTF-8'}</p>
                                                <div class="card-text refund-rule-description">{$hotel_refund_rule.description|escape:'html':'UTF-8'}</div>
                                            </div>
                                        {/foreach}
                                    </div>
                                </div>
                            {/if}
                            {hook h='displayOrderDetailPoliciesTabContent' id_order=$order->id}
                        </div>
                    </div>
                </div>
            {/if}

            {if !$is_guest}
                <div class="card order-messages {if !count($messages)}hide{/if}">
                    <div class="card-header">
                        {l s='Messages'}
                    </div>

                    <div class="card-body">
                        <div class="messages-list card-text">
                            {foreach from=$messages item=message}
                                {include file='./_partials/order-message.tpl'}
                            {/foreach}
                        </div>
                    </div>
                </div>
            {/if}

            {hook h='displayOrderDetailMessagesBefore' id_order=$order->id}

            {if !$is_guest}
                <div class="card add-order-message" id="add-order-message">
                    <div class="card-header">
                        {l s='Add a Message'}

                        <p class="card-subheader text-muted">
                            {l s='If you would like to add a comment about your booking, please write it in the field below.'}
                        </p>
                    </div>

                    <div class="card-body">
                        <div class="errors-block" style="display: none;"></div>

                        <form action="{$link->getPageLink('order-detail', true)|escape:'html':'UTF-8'}" method="post" class="std" id="sendOrderMessage">
                            <div class="form-group select-room-type">
                                <label for="id_product">{l s='Room Type'}</label>

                                <select name="id_product" class="form-control">
                                    <option value="0">{l s='-- Choose --'}</option>
                                    {foreach from=$roomTypes item=product}
                                        {if $product.is_booking_product}
                                            <option value="{$product.product_id}">{$product.product_name|escape:'html':'UTF-8'}</option>
                                        {/if}
                                    {/foreach}
                                </select>
                            </div>

                            <p class="form-group textarea">
                                <textarea class="form-control" rows="3" name="msgText"></textarea>
                            </p>

                            <div class="submit">
                                <input type="hidden" name="id_order" value="{$order->id|intval|escape:'html':'UTF-8'}" />
                                <input type="submit" class="unvisible" name="submitMessage" value="{l s='Send'}" />
                                <button type="submit" name="submitMessage" id="submitMessage" class="button btn button-medium"><span>{l s='Send'}</span></button>
                            </div>
                        </form>
                    </div>
                </div>
            {/if}

            {hook h='displayOrderDetailBottomLeft' id_order=$order->id}
        </div>
        <div class="col-md-4">
            {hook h='displayOrderDetailTopRight' id_order=$order->id}

            <div class="card payment-details hidden-xs hidden-sm visible-md">
                <div class="card-header">
                    {l s='Payment Details'}
                </div>
                <div class="card-body">
                    <div class="detail-row">
                        <div class=" title">{l s='Payment Method'}</div>
                        <div class=" value payment-method">
                            {if $invoice && $invoiceAllowed}
                                <span class="icon-pdf"></span>
                                <a target="_blank" href="{$link->getPageLink('pdf-invoice', true)}?id_order={$order->id|intval}{if $is_guest}&amp;secure_key={$order->secure_key|escape:'html':'UTF-8'}{/if}" title="{l s='Click here to download invoice.'}">
                                    <span>{$order->payment|escape:'html':'UTF-8'}</span>
                                </a>
                            {else}
                                {$order->payment|escape:'html':'UTF-8'}
                            {/if}
                        </div>
                    </div>

                    <div class="detail-row">
                        <div class="pull-left title">{l s='Status'}</div>
                        <div class="pull-right value status">
                            {if isset($order_history[0]) && $order_history[0]}
                                <span{if isset($order_history[0].color) && $order_history[0].color} style="background-color:{$order_history[0].color|escape:'html':'UTF-8'}30; border: 1px solid {$order_history[0].color|escape:'html':'UTF-8'};" {/if} class="label">
                                    {if $order_history[0].id_order_state|in_array:$overbooking_order_states}
                                        {l s='Order Not Confirmed'}
                                    {else}
                                        {$order_history[0].ostate_name|escape:'html':'UTF-8'}
                                    {/if}
                                </span>
                            {else}
                                <span class="processing">{l s='Processing'}</span>
                            {/if}
                        </div>
                    </div>

                    {hook h='displayOrderDetailPaymentDetailsRow' id_order=$order->id}
                </div>
            </div>

            <div class="card hotel-location hidden-xs hidden-sm visible-md">
                <div class="card-header">
                    {l s='Hotel Location'}
                </div>
                <div class="card-body">
                    <p class="card-subtitle">
                        {l s='Address'}
                    </p>

                    {if isset($hotel_address_info) && $hotel_address_info}
                        <p class="hotel-address">
                            {$hotel_address_info['address1']},
                            {if {$hotel_address_info['address2']}}{$hotel_address_info['address2']},{/if}
                            {$hotel_address_info['city']},
                            {if {$hotel_address_info['state']}}{$hotel_address_info['state']},{/if}
                            {$hotel_address_info['country']}, {$hotel_address_info['postcode']}
                        </p>
                    {else}
                        <div class="card-text">{l s='Hotel location not available.'}</div>
                    {/if}

                    {if ($obj_hotel_branch_information->latitude|floatval != 0 && $obj_hotel_branch_information->longitude|floatval != 0)}
                        <div class="hotel-location-map">
                            <div class="booking-hotel-map-container"></div>
                        </div>
                    {/if}

                    {hook h='displayOrderDetailHotelLocationAfter' id_order=$order->id}
                </div>
            </div>

            <div class="card payment-summary hidden-xs hidden-sm visible-md">
                <div class="card-header">
                    {l s='Payment Summary'}
                </div>
                <div class="card-body">
                    <div class="prices-breakdown-table">
                        <table class="table table-sm table-responsive table-summary">
                            <tbody>
                                {assign var=room_price_tax_excl value=$order->getTotalProductsWithoutTaxes(false, true)}
                                {assign var=room_price_tax_incl value=$order->getTotalProductsWithTaxes(false, true)}
                                {assign var=additional_service_price_tax_excl value=($order->getTotalProductsWithoutTaxes(false, false, Product::SERVICE_PRODUCT_WITH_ROOMTYPE) + $total_demands_price_te)}
                                {assign var=additional_service_price_tax_incl value=($order->getTotalProductsWithTaxes(false, false, Product::SERVICE_PRODUCT_WITH_ROOMTYPE) + $total_demands_price_ti)}
                                {if $priceDisplay && $use_tax && $room_price_tax_excl}
                                    <tr>
                                        <td>{l s='Total rooms cost (tax excl.)'}</td>
                                        <td class="text-right">
                                            <span class="price">{displayWtPriceWithCurrency price=($room_price_tax_excl + $additional_service_price_tax_excl - $total_convenience_fee_te) currency=$currency}</span>
                                        </td>
                                    </tr>
                                {else}
                                    <tr>
                                        <td>{l s='Total Rooms Cost'} {if $use_tax}{l s='(tax incl.)'}{/if} </td>
                                        <td class="text-right">
                                            <span class="price">{displayWtPriceWithCurrency price=($room_price_tax_incl + $additional_service_price_tax_incl - $total_convenience_fee_ti) currency=$currency}</span>
                                        </td>
                                    </tr>
                                {/if}

                                {if $order->total_discounts > 0}
                                    <tr>
                                        <td>{l s='Total Vouchers'}</td>
                                        <td class="text-right">
                                            <span class="price price-discount">-{displayWtPriceWithCurrency price=$order->total_discounts currency=$currency convert=1}</span>
                                        </td>
                                    </tr>
                                {/if}
                                {if $priceDisplay && $use_tax && $total_convenience_fee_te}
                                    <tr>
                                        <td>{l s='Total Convenience Fees (tax excl.)'}</td>
                                        <td class="text-right">
                                            <span class="price">{displayWtPriceWithCurrency price=$total_convenience_fee_te currency=$currency}</span>
                                        </td>
                                    </tr>
                                {else if $total_convenience_fee_ti}
                                    <tr>
                                        <td>{l s='Total Convenience Fees (tax incl.)'}</td>
                                        <td class="text-right">
                                            <span class="price">{displayWtPriceWithCurrency price=$total_convenience_fee_ti currency=$currency}</span>
                                        </td>
                                    </tr>
                                {/if}

                                <tr class="totalprice item">
                                    <td>{l s='Total Tax'}</td>
                                    <td class="text-right">
                                        <span class="price">{displayWtPriceWithCurrency price=($order->total_paid - $order->total_paid_tax_excl) currency=$currency}</span>
                                    </td>
                                </tr>
                                <tr class="totalprice item">
                                    <td><strong>{l s='Final Booking Total'}<strong></td>
                                    <td class="text-right">
                                        <strong><span class="price">{displayWtPriceWithCurrency price=$order->total_paid currency=$currency}</span></strong>
                                    </td>
                                </tr>

                                {if isset($refundReqBookings) && $refundReqBookings}
                                    <tr class="totalprice item">
                                        <td>{l s='* Refunded Amount'}</td>
                                        <td class="text-right">
                                            <span class="price">{displayWtPriceWithCurrency price=$refundedAmount currency=$currency}</span>
                                        </td>
                                    </tr>
                                {/if}

                                {if $order->total_paid_tax_incl > $order->total_paid_real}
                                    <tr class="totalprice item">
                                        <td>{l s='Due Amount'}</td>
                                        <td class="text-right">
                                            <span class="price">{displayWtPriceWithCurrency price=($order->total_paid_tax_incl - $order->total_paid_real) currency=$currency}</span>
                                        </td>
                                    </tr>
                                {/if}

                                {hook h='displayOrderDetailPaymentSummaryRow' id_order=$order->id}
                            </tbody>
                        </table>
                    </div>

                    {hook h='displayOrderDetailPaymentSummaryAfter' id_order=$order->id}
                </div>
            </div>

            <div class="card guest-details hidden-xs hidden-sm visible-md">
                <div class="card-header">
                    {l s='Guest Details'}
                </div>
                <div class="card-body">
                    <div class="guest-details-table">
                        <table class="table table-sm table-responsive table-summary">
                            <tbody>
                                {if $customerGuestDetail}
                                    {if isset($customerGuestDetail->firstname) && $customerGuestDetail->firstname}
                                        <tr>
                                            <td>{l s='Name'}</td>
                                            <td class="text-right">{$customerGuestDetail->firstname|escape:'html':'UTF-8'} {$customerGuestDetail->lastname|escape:'html':'UTF-8'}</td>
                                        </tr>
                                    {/if}
                                    {if isset($customerGuestDetail->email) && $customerGuestDetail->email}
                                        <tr>
                                            <td>{l s='Email'}</td>
                                            <td class="text-right">{$customerGuestDetail->email|escape:'html':'UTF-8'}</td>
                                        </tr>
                                    {/if}
                                    {if isset($customerGuestDetail->phone) && $customerGuestDetail->phone}
                                        <tr>
                                            <td>{l s='Mobile'}</td>
                                            <td class="text-right">{$customerGuestDetail->phone|escape:'html':'UTF-8'}</td>
                                        </tr>
                                    {/if}
                                {else}
                                    <tr>
                                        <td>{l s='Name'}</td>
                                        <td class="text-right">
                                            {if isset($address_invoice->firstname) && $address_invoice->firstname}
                                                {$address_invoice->firstname|escape:'html':'UTF-8'} {$address_invoice->lastname|escape:'html':'UTF-8'}
                                            {elseif isset($guestInformations['firstname']) && $guestInformations['firstname']}
                                                {$guestInformations['firstname']|escape:'html':'UTF-8'} {$guestInformations['lastname']|escape:'html':'UTF-8'}
                                            {/if}
                                        </td>
                                    </tr>

                                    <tr>
                                        <td>{l s='Email'}</td>
                                        <td class="text-right">{$guestInformations['email']|escape:'html':'UTF-8'}</td>
                                    </tr>

                                    {if isset($address_invoice->phone_mobile) && $address_invoice->phone_mobile}
                                        <tr>
                                            <td>{l s='Mobile'}</td>
                                            <td class="text-right">{$address_invoice->phone_mobile|escape:'html':'UTF-8'}</td>
                                        </tr>
                                    {/if}

                                    {if isset($address_invoice->phone) && $address_invoice->phone}
                                        <tr>
                                            <td>{l s='Phone'}</td>
                                            <td class="text-right">{$address_invoice->phone|escape:'html':'UTF-8'}</td>
                                        </tr>
                                    {/if}
                                {/if}

                                {hook h='displayOrderDetailGuestDetailsRow' id_order=$order->id}
                            </tbody>
                        </table>
                    </div>

                    {hook h='displayOrderDetailGuestDetailsAfter' id_order=$order->id}
                </div>
            </div>

            {hook h='displayOrderDetailBottomRight' id_order=$order->id}
        </div>
    </div>

    {if $is_guest}
        <div class="row">
            <div class="col-sm-8">
                <p class="alert alert-info"><i class="icon-info-sign"></i> {l s='You cannot request refund with a guest account.'}</p>
            </div>
        </div>
    {/if}

    {if isset($refund_allowed) && $refund_allowed}
        <div style="display: none;">
            <div id="create-new-refund-popup">
                <form id="form-cancel-booking">
                    <input type="hidden" name="id_order" value="{$order->id}">
                    <div class="card cancel-booking">
                        <div class="card-header">
                            {l s='Cancel Bookings'}
                        </div>
                        <div class="card-body">
                            <div class="errors" style="display: none;"></div>

                            <div class="col-xs-12">
                                <div class="row no-gutters">
                                    <div class="col-xs-4">
                                        <ul class="nav nav-tabs nav-stacked">
                                            {assign var='flag_is_first_iteration' value=true}
                                            {foreach from=$cart_htl_data key=data_k item=data_v}
                                                {foreach from=$data_v['date_diff'] key=rm_k item=rm_v}
                                                    <li class="{if $flag_is_first_iteration}active{/if}">
                                                        <a href="#room-info-tab-{$data_v.id_product}-{$rm_k}" class="" data-toggle="tab">
                                                            <div class="room-type-name">{$data_v.name}</div>
                                                            <div class="duration">{dateFormat date=$rm_v.data_form} - {dateFormat date=$rm_v.data_to}</div>
                                                        </a>
                                                    </li>
                                                    {if $flag_is_first_iteration}{assign var='flag_is_first_iteration' value=false}{/if}
                                                {/foreach}
                                            {/foreach}
                                        </ul>
                                    </div>
                                    <div class="col-xs-8">
                                        <div class="tab-content clearfix">
                                            {assign var='flag_is_first_iteration' value=true}
                                            {foreach from=$cart_htl_data key=data_k item=data_v}
                                                {foreach from=$data_v['date_diff'] key=rm_k item=rm_v}
                                                    <div id="room-info-tab-{$data_v.id_product}-{$rm_k}" class="tab-pane {if $flag_is_first_iteration}active{/if}">
                                                        <div class="room-type-summary clearfix">
                                                            <p class="room-type-name">{$data_v.name}</p>
                                                            <div class="col-xs-3">
                                                                <p>{l s='Total Rooms'}</p>
                                                                <strong>{$rm_v.num_rm|string_format:'%02d'}</strong>
                                                            </div>
                                                            <div class="col-xs-3">
                                                                <p>{l s='Cancelled Rooms'}</p>
                                                                <strong>{($rm_v.count_cancelled + $rm_v.count_refunded)|string_format:'%02d'}</strong>
                                                            </div>
                                                        </div>
                                                        <div class="rooms-summary">
                                                            {foreach from=$rm_v['hotel_booking_details'] item=$hotel_booking_detail name=foreachRefundRooms}
                                                                {assign var=is_room_cancelled value=(isset($refundReqBookings) && in_array($hotel_booking_detail.id_htl_booking, $refundReqBookings))}
                                                                <div class="room-details {if $is_room_cancelled || ($hotel_booking_detail.id_status != $ROOM_STATUS_ALLOTED)}cancelled{/if} clearfix">
                                                                    <div class="occupancy-wrap">
                                                                        <div class="checkbox">
                                                                            <label for="bookings_to_refund_{$hotel_booking_detail.id_htl_booking}">
                                                                                <input type="checkbox" class="bookings_to_refund" id="bookings_to_refund_{$hotel_booking_detail.id_htl_booking}" name="bookings_to_refund[]" value="{$hotel_booking_detail.id_htl_booking|escape:'html':'UTF-8'}" {if $is_room_cancelled || ($hotel_booking_detail.id_status != $ROOM_STATUS_ALLOTED)}disabled{/if}/>
                                                                                {l s='Room'} - {$smarty.foreach.foreachRefundRooms.iteration|string_format:'%02d'}
                                                                            </label>

                                                                            <span>({$hotel_booking_detail.adults|string_format:'%02d'} {if $hotel_booking_detail.adults > 1}{l s='Adults'}{else}{l s='Adult'}{/if}{if $hotel_booking_detail.children > 0}{l s=', '}{$hotel_booking_detail.children|string_format:'%02d'} {if $hotel_booking_detail.children > 1}{l s='Children'}{else}{l s='Child'}{/if}{/if})</span>
                                                                            {if $hotel_booking_detail.is_cancelled}<span class="badge badge-danger badge-cancelled">{l s='Cancelled'}</span>{else if $hotel_booking_detail.is_refunded}<span class="badge badge-danger badge-cancelled">{l s='Refunded'}</span>{else if $hotel_booking_detail.refund_denied}<span class="badge badge-danger badge-cancelled">{l s='Refund denied'}</span> <i class="icon-info-circle refund-denied-info" data-refund_denied_info="{l s='Refund for this booking is denied. Please contact admin for more detail.'}"></i>{else if $hotel_booking_detail.id_status != $ROOM_STATUS_ALLOTED}<span class="badge badge-danger badge-cancelled">{if $hotel_booking_detail.id_status == $ROOM_STATUS_CHECKED_OUT}{l s='Checked-Out'}{else}{l s='Checked-In'}{/if}</span>{/if}
                                                                        </div>
                                                                    </div>

                                                                    {* Services are indexed with id_htl_booking *}
                                                                    {assign var='has_services' value=(isset($rm_v.additional_services) && isset($rm_v.additional_services[$hotel_booking_detail.id_htl_booking]) && isset($rm_v.additional_services[$hotel_booking_detail.id_htl_booking]['additional_services']))}
                                                                    {* Additional Facilities are indexed with id_room *}
                                                                    {assign var='has_facilities' value=(isset($rm_v.extra_demands) && isset($rm_v.extra_demands[$hotel_booking_detail.id_room]) && isset($rm_v.extra_demands[$hotel_booking_detail.id_room]['extra_demands']))}
                                                                    {if $has_services || $has_facilities}
                                                                        <div class="extra-services-wrap clearfix">
                                                                            {if $has_services}
                                                                                <div class="services-wrap clearfix">
                                                                                    <div class="col-xs-3">
                                                                                        <strong>{l s='Services'}</strong>
                                                                                    </div>
                                                                                    <div class="col-xs-9">
                                                                                        {foreach from=$rm_v.additional_services[$hotel_booking_detail.id_htl_booking]['additional_services'] item=service}
                                                                                            <span class="service">{$service.name}</span>
                                                                                        {/foreach}
                                                                                    </div>
                                                                                </div>
                                                                            {/if}
                                                                            {if $has_facilities}
                                                                                <div class="facilities-wrap clearfix">
                                                                                    <div class="col-xs-3">
                                                                                        <strong>{l s='Facilities'}</strong>
                                                                                    </div>
                                                                                    <div class="col-xs-9">
                                                                                        {foreach from=$rm_v.extra_demands[$hotel_booking_detail.id_room]['extra_demands'] item=facility}
                                                                                            <span class="facility">{$facility.name}</span>
                                                                                        {/foreach}
                                                                                    </div>
                                                                                </div>
                                                                            {/if}
                                                                        </div>
                                                                    {else}
                                                                        <div class="extra-services-wrap clearfix">
                                                                            <p class="text-muted">{l s='No extra services added for this room.'}</p>
                                                                        </div>
                                                                    {/if}
                                                                </div>
                                                            {/foreach}
                                                        </div>
                                                    </div>
                                                    {if $flag_is_first_iteration}{assign var='flag_is_first_iteration' value=false}{/if}
                                                {/foreach}
                                            {/foreach}
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="card-footer">
                            <div class="selected-rooms-wrap">
                                {l s='Selected Rooms: '}<span class="num-selected-rooms">{l s='00'}</span>
                            </div>
                            <div class="actions-wrap">
                                <button class="btn btn-secondary btn-cancel">
                                    {l s='Cancel'}
                                </button>
                                <button class="btn btn-primary btn-next">
                                    {l s='Next'}
                                </button>
                            </div>
                        </div>
                    </div>

                    <div class="card cancel-booking-preview" style="display:none;">
                        <div class="card-header">
                            {l s='Cancellation Reason'}
                        </div>
                        <div class="card-body">
                            <div class="errors" style="display: none;"></div>

                            <div class="well well-sm">
                                <p class="text">{l s='Total Rooms to Cancel:'} <span class="count-total-rooms">{l s='00'}</span></p>
                            </div>

                            <div class="form-group">
                                <label class="label">{l s='Mention reason for cancellation'}<sup>{l s='*'}</sup></label>
                                <textarea class="form-control cancellation_reason" name="cancellation_reason" rows="4" placeholder="{l s='Type here...'}"></textarea>
                            </div>
                        </div>
                        <div class="card-footer clearfix">
                            <div class="pull-right">
                                <button class="btn btn-secondary btn-back">
                                    {l s='Back'}
                                </button>
                                <button class="btn btn-primary btn-submit">
                                    {l s='Submit'}
                                </button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <div id="popup-cancellation-submit-success" class="popup-cancellation-submit-success" style="display: none;">
            <div class="card">
                <div class="text-center">
                    <div><i class="icon icon-check-circle text-success"></i></div>
                    <h3><b>{l s='Request submitted successfully'}</b></h3>
                    <h4>{l s='Your cancellation request has been submitted successfully. Go to Booking Refund Requests page for further updates.'}</h4>
                </div>
            </div>
        </div>

        <div id="popup-cancellation-order-cancel-success" class="popup-cancellation-submit-success" style="display: none;">
            <div class="card">
                <div class="text-center">
                    <div><i class="icon icon-check-circle text-success"></i></div>
                    <h3><b>{l s='Booking cancelled successfully'}</b></h3>
                    <h4>{l s='Your booking has been cancelled successfully.'}</h4>
                </div>
            </div>
        </div>
    {/if}

    <div id="popup-view-extra-services" class="popup-view-extra-services" style="display: none;"></div>
{/if}

{strip}
    {addJsDef historyUrl=$link->getPageLink('orderdetail', true)|escape:'quotes':'UTF-8'}
    {addJsDefL name=req_sent_msg}{l s='Request Sent..' js=1}{/addJsDefL}
    {addJsDefL name=wait_stage_msg}{l s='Waiting' js=1}{/addJsDefL}
    {addJsDefL name=pending_state_msg}{l s='Pending...' js=1}{/addJsDefL}
    {addJsDefL name=mail_sending_err}{l s='Some error occurred while sending mail to the customer' js=1}{/addJsDefL}
    {addJsDefL name=refund_request_sending_error}{l s='Some error occurred while processing request for booking cancellation.' js=1}{/addJsDefL}
    {addJsDefL name=no_bookings_selected}{l s='Please select at least one room to proceed for cancellation.' js=1}{/addJsDefL}
    {addJsDefL name=refund_request_success_txt}{l s='Request for booking cancellation is successffully created.' js=1}{/addJsDefL}
    {addJsDefL name=order_message_choose_txt}{l s='-- Choose --' js=1}{/addJsDefL}
    {addJsDefL name=order_message_success_txt}{l s='Order message sent successfully.' js=1}{/addJsDefL}
    {addJsDefL name=cancel_req_txt}{l s='Cancel Request' js=1}{/addJsDefL}
    {addJsDefL name=cancel_booking_txt}{l s='Cancel Bookings' js=1}{/addJsDefL}
{/strip}
