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
                            <th><span class="title_box">{l s='Room No.'}</span></th>
                            <th><span class="title_box">{l s='Image'}</th>
                            <th><span class="title_box">{l s='Room Type'}</span></th>
                            <th><span class="title_box">{l s='Duration'}</span></th>
                            <th class="fixed-width-lg"><span class="title_box">{l s='Occupancy'}</span></th>
                            <th><span class="title_box">{l s='Room Price (Tax excl.)'}</span></th>
                            <th><span class="title_box">{l s='Extra Services/Fee (Tax excl.)'}</span></th>
                            <th><span class="title_box">{l s='Total Tax'}</span></th>
                            <th><span class="title_box">{l s='Total Price (Tax incl.)'}</span></th>
                            {if (isset($refundReqBookings) && $refundReqBookings)}
                                <th><span class="title_box">{l s='Refund/Cancel Status'}</span></th>
                                <th><span class="title_box">{l s='Refunded amount'}</span></th>
                            {/if}
                            {if (isset($can_edit) && $can_edit && !$order->hasBeenDelivered())}
                            <th class="fixed-width-md"><span class="title_box">{l s='Actions'}</th>
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

{if isset($use_tourism_tax) && $use_tourism_tax}
<style>
.ui-tooltip.price_info-tooltip { border: unset; padding: 10px; box-shadow: 0px 0px 15px 0px #00000026; }
.ui-tooltip.price_info-tooltip span { margin-left: 15px; }
.ui-tooltip.price_info-tooltip label { font-weight: 600; }
</style>
<script>
$(document).ready(function () {
    if ($('#customer_cart_details .price_info').length) {
        $('#customer_cart_details .price_info').each(function (i, element) {
            $(this).find('img').tooltip({
                content: $(this).closest('td').find('.price_info_container').html(),
                items: 'img',
                trigger: 'hover',
                tooltipClass: 'price_info-tooltip',
                open: function (event, ui) {
                    if (typeof(event.originalEvent) === 'undefined') {
                        return false;
                    }
                    var $id = $(ui.tooltip).attr('id');
                    if ($('div.ui-tooltip').not('#' + $id).length) {
                        return false;
                    }
                },
                close: function (event, ui) {
                    ui.tooltip.hover(function () {
                        $(this).stop(true).fadeTo(400, 1);
                    }, function () {
                        $(this).fadeOut('400', function () {
                            $(this).remove();
                        });
                    });
                }
            });
        });
    }

    function ttAjax(action, data) {
        $.ajax({
            type: 'POST',
            url: admin_order_tab_link,
            data: $.extend({ ajax: 1, action: action }, data),
            dataType: 'json',
            success: function (resp) {
                if (resp.hasError && resp.errors && resp.errors.length) {
                    showErrorMessage(resp.errors.join('<br>'));
                } else {
                    window.location.reload();
                }
            },
            error: function () {
                showErrorMessage('{l s='Tourism tax request failed. Please try again.' js=1}');
            }
        });
    }

    $(document).on('click', '.tt-apply-booking', function (e) {
        e.preventDefault();
        ttAjax('ApplyTourismTax', { id_htl_booking: $(this).data('id_htl_booking') });
    });

    $(document).on('click', '.tt-exempt-booking', function (e) {
        e.preventDefault();
        ttAjax('ExemptTourismTax', { id_htl_booking: $(this).data('id_htl_booking') });
    });

    $(document).on('click', '.tt-apply-all-bookings', function (e) {
        e.preventDefault();
        ttAjax('ApplyTourismTax', { id_order: $(this).data('id_order') });
    });

    $(document).on('click', '.tt-exempt-all-bookings', function (e) {
        e.preventDefault();
        ttAjax('ExemptTourismTax', { id_order: $(this).data('id_order') });
    });
});
</script>
{/if}