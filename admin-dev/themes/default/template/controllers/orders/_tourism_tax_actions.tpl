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

{* Shared tourism tax price-breakdown tooltip + apply/exempt modal & handlers — included wherever a
   room table (_rooms_informaion_table.tpl) and/or a products table (_hotel_service_products_table.tpl /
   _standalone_service_products_table.tpl) with tourism tax columns is rendered. *}

{assign var='ttExemptRooms' value=[]}
{if isset($use_tourism_tax) && $use_tourism_tax && isset($order_detail_data) && $order_detail_data}
    {foreach from=$order_detail_data item=ttRoom}
        {if isset($ttRoom.tourism_tax_status) && $ttRoom.tourism_tax_status == $tourism_tax_status_applied && $ttRoom.id_status != $ROOM_STATUS_CANCELLED && !$ttRoom.is_refunded}
            {append var='ttExemptRooms' value=$ttRoom}
        {/if}
    {/foreach}
{/if}
{assign var='ttExemptProducts' value=[]}
{if isset($use_tourism_tax) && $use_tourism_tax}
    {if isset($hotel_service_products) && $hotel_service_products}
        {foreach from=$hotel_service_products item=ttProduct}
            {if isset($ttProduct.tourism_tax_status) && $ttProduct.tourism_tax_status == $tourism_tax_status_applied && !$ttProduct.is_refunded && !$ttProduct.is_cancelled}
                {append var='ttExemptProducts' value=$ttProduct}
            {/if}
        {/foreach}
    {/if}
    {if isset($standalone_service_products) && $standalone_service_products}
        {foreach from=$standalone_service_products item=ttProduct}
            {if isset($ttProduct.tourism_tax_status) && $ttProduct.tourism_tax_status == $tourism_tax_status_applied && !$ttProduct.is_refunded && !$ttProduct.is_cancelled}
                {append var='ttExemptProducts' value=$ttProduct}
            {/if}
        {/foreach}
    {/if}
{/if}

<div class="modal fade" id="exempt-tourism-tax-modal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-md order_detail_modal" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal"><i class="icon-remove-sign"></i></button>
                <h4 class="modal-title"><i class="icon-ban"></i> {l s='Exempt Tourism Tax'}</h4>
            </div>
            <div class="modal-body">
                {if $ttExemptRooms|count > 0 && $ttExemptProducts|count > 0}
                    <ul class="nav nav-tabs" role="tablist" id="tt-exempt-tabs">
                        <li role="presentation" class="active">
                            <a href="#tt_exempt_rooms_tab" role="tab" data-toggle="tab">{l s='Rooms'}</a>
                        </li>
                        <li role="presentation">
                            <a href="#tt_exempt_products_tab" role="tab" data-toggle="tab">{l s='Products'}</a>
                        </li>
                    </ul>
                {/if}
                {if $ttExemptRooms|count > 0 && $ttExemptProducts|count > 0}
                    <div class="form-group tab-content clearfix">
                {/if}
                    {if $ttExemptRooms|count}
                        <div id="tt_exempt_rooms_tab" class="form-group table-responsive tab-pane active">
                            <table class="table" id="tt-exempt-rooms-table">
                                <thead>
                                    <tr>
                                        <th></th>
                                        <th>{l s='Room No.'}</th>
                                        <th>{l s='Room Type'}</th>
                                        <th>{l s='Property Name'}</th>
                                        <th>{l s='Duration'}</th>
                                        <th>{l s='Total Price (Tax incl.)'}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    {foreach from=$ttExemptRooms item=ttRoom}
                                        <tr class="tt-exempt-row" data-id_htl_booking="{$ttRoom.id|intval}">
                                            <td>
                                                <input type="checkbox" name="tt_exempt_id_htl_booking[]" value="{$ttRoom.id|intval}" checked="checked"/>
                                            </td>
                                            <td><b>{$ttRoom.room_num|escape:'html':'UTF-8'}</b></td>
                                            <td>{$ttRoom.room_type_name|escape:'html':'UTF-8'}</td>
                                            <td>{$ttRoom.hotel_name|escape:'html':'UTF-8'}</td>
                                            {assign var="is_full_date" value=($show_full_date && ($ttRoom['date_from']|date_format:'%D' == $ttRoom['date_to']|date_format:'%D'))}
                                            <td>{dateFormat date=$ttRoom.date_from full=$is_full_date} - {dateFormat date=$ttRoom.date_to full=$is_full_date}</td>
                                            <td>{convertPriceWithCurrency price=$ttRoom.total_room_price_ti currency=$currency->id}</td>
                                        </tr>
                                    {/foreach}
                                </tbody>
                            </table>
                        </div>
                    {/if}
                    {if $ttExemptProducts|count}
                        <div id="tt_exempt_products_tab" class="form-group table-responsive tab-pane{if !$ttExemptRooms|count} active{/if}">
                            <table class="table" id="tt-exempt-products-table">
                                <thead>
                                    <tr>
                                        <th></th>
                                        <th>{l s='Name'}</th>
                                        <th>{l s='Quantity'}</th>
                                        <th>{l s='Total Price (Tax incl.)'}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    {foreach from=$ttExemptProducts item=ttProduct}
                                        <tr class="tt-exempt-row" data-id_service_product_order_detail="{$ttProduct.id_service_product_order_detail|intval}">
                                            <td>
                                                <input type="checkbox" name="tt_exempt_id_service_product_order_detail[]" value="{$ttProduct.id_service_product_order_detail|intval}" checked="checked"/>
                                            </td>
                                            <td><b>{$ttProduct.name|escape:'html':'UTF-8'}{if $ttProduct.option_name} : {$ttProduct.option_name|escape:'html':'UTF-8'}{/if}</b></td>
                                            <td>{if $ttProduct.allow_multiple_quantity}{$ttProduct.quantity|escape:'html':'UTF-8'}{else}--{/if}</td>
                                            <td>{convertPriceWithCurrency price=$ttProduct.total_price_tax_incl currency=$currency->id}</td>
                                        </tr>
                                    {/foreach}
                                </tbody>
                            </table>
                        </div>
                    {/if}
                {if $ttExemptRooms|count > 0 && $ttExemptProducts|count > 0}
                    </div>
                {/if}
                <div class="form-group">
                    <label class="control-label">{l s='Reason for Exemption'}</label>
                    <textarea rows="2" id="tt-exempt-note"></textarea>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary" id="tt-exempt-confirm">
                    <i class="icon-ban"></i> {l s='Exempt Tourism Tax'}
                </button>
            </div>
        </div>
    </div>
</div>

<script>
$(document).ready(function () {
    function ttAjax(action, data, $button) {
        $.ajax({
            type: 'POST',
            url: admin_order_tab_link,
            data: $.extend({ ajax: 1, action: action }, data),
            dataType: 'json',
            beforeSend: function () {
                $('#page-loader').show();
                if ($button) {
                    $button.prop('disabled', true);
                }
            },
            success: function (resp) {
                if (resp.hasError && resp.errors && resp.errors.length) {
                    showErrorMessage(resp.errors.join('<br>'));
                } else {
                    window.location.href = admin_order_tab_link + '&conf=4&vieworder&id_order=' + id_order;
                }
            },
            error: function () {
                showErrorMessage('{l s='Tourism tax request failed. Please try again.' js=1}');
            },
            complete: function () {
                $('#page-loader').hide();
                if ($button) {
                    $button.prop('disabled', false);
                }
            }
        });
    }

    $(document).on('click', '.tt-apply-booking', function (e) {
        e.preventDefault();
        ttAjax('ApplyTourismTax', { id_htl_booking: $(this).data('id_htl_booking') });
    });

    $(document).on('click', '.tt-apply-all-bookings', function (e) {
        e.preventDefault();
        ttAjax('ApplyTourismTax', { id_order: $(this).data('id_order') });
    });

    $(document).on('click', '.tt-apply-service-line', function (e) {
        e.preventDefault();
        ttAjax('ApplyTourismTax', { id_service_product_order_detail: $(this).data('id_service_product_order_detail') });
    });

    function ttShowExemptModal(onlyRow) {
        var $modal = $('#exempt-tourism-tax-modal');
        var $rows = $modal.find('.tt-exempt-row');
        $rows.show().find('input[type=checkbox]').prop('checked', true);
        if (onlyRow) {
            $rows.each(function () {
                var $row = $(this);
                var isTarget = (onlyRow.id_htl_booking && $row.data('id_htl_booking') == onlyRow.id_htl_booking)
                    || (onlyRow.id_service_product_order_detail && $row.data('id_service_product_order_detail') == onlyRow.id_service_product_order_detail);
                if (!isTarget) {
                    $row.hide().find('input[type=checkbox]').prop('checked', false);
                }
            });
        }
        // show the tab holding the picked row, and hide the tab bar when a single row was picked
        var $tabs = $('#tt-exempt-tabs');
        if ($tabs.length) {
            $tabs.toggle(!onlyRow);
            var activeTab = (onlyRow && onlyRow.id_service_product_order_detail) ? '#tt_exempt_products_tab' : '#tt_exempt_rooms_tab';
            $tabs.find('a[href="' + activeTab + '"]').tab('show');
        }
        $('#tt-exempt-note').val('');
        $modal.modal('show');
    }

    $(document).on('click', '.tt-exempt-booking', function (e) {
        e.preventDefault();
        ttShowExemptModal({ id_htl_booking: $(this).data('id_htl_booking') });
    });

    $(document).on('click', '.tt-exempt-all-bookings', function (e) {
        e.preventDefault();
        ttShowExemptModal(null);
    });

    $(document).on('click', '.tt-exempt-service-line', function (e) {
        e.preventDefault();
        ttShowExemptModal({ id_service_product_order_detail: $(this).data('id_service_product_order_detail') });
    });

    $(document).on('click', '#tt-exempt-confirm', function (e) {
        var idsHtlBooking = $('#exempt-tourism-tax-modal input[name="tt_exempt_id_htl_booking[]"]:checked').map(function () {
            return $(this).val();
        }).get();
        var idsServiceLine = $('#exempt-tourism-tax-modal input[name="tt_exempt_id_service_product_order_detail[]"]:checked').map(function () {
            return $(this).val();
        }).get();
        if (!idsHtlBooking.length && !idsServiceLine.length) {
            showErrorMessage('{l s='No booking/products has been selected.' js=1}');
            return;
        }
        ttAjax('ExemptTourismTax', {
            id_order: id_order,
            id_htl_booking: idsHtlBooking,
            id_service_product_order_detail: idsServiceLine,
            note: $('#tt-exempt-note').val()
        }, $(this));
    });
});
</script>
