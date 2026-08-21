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

<style>
.ui-tooltip.price_info-tooltip { border: unset; padding: 10px; box-shadow: 0px 0px 15px 0px #00000026; }
.ui-tooltip.price_info-tooltip span { margin-left: 15px; }
.ui-tooltip.price_info-tooltip label { font-weight: 600; }
</style>

<div class="modal fade" id="exempt-tourism-tax-modal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal"><i class="icon-remove-sign"></i></button>
                <h4 class="modal-title"><i class="icon-ban"></i> {l s='Exempt Tourism Tax'}</h4>
            </div>
            <div class="modal-body">
                <div class="form-group">
                    <label class="control-label">{l s='Reason for Exemption'}</label>
                    <textarea rows="3" class="textarea-autosize" id="tt-exempt-note"></textarea>
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
    if ($('#customer_cart_details .price_info, #customer_products_details .price_info').length) {
        $('#customer_cart_details .price_info, #customer_products_details .price_info').each(function (i, element) {
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
            beforeSend: function () {
                $('#page-loader').show();
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

    var ttExemptTarget = null;

    $(document).on('click', '.tt-exempt-booking', function (e) {
        e.preventDefault();
        ttExemptTarget = { id_htl_booking: $(this).data('id_htl_booking') };
        $('#tt-exempt-note').val('');
        $('#exempt-tourism-tax-modal').modal('show');
    });

    $(document).on('click', '.tt-exempt-all-bookings', function (e) {
        e.preventDefault();
        ttExemptTarget = { id_order: $(this).data('id_order') };
        $('#tt-exempt-note').val('');
        $('#exempt-tourism-tax-modal').modal('show');
    });

    $(document).on('click', '.tt-exempt-service-line', function (e) {
        e.preventDefault();
        ttExemptTarget = { id_service_product_order_detail: $(this).data('id_service_product_order_detail') };
        $('#tt-exempt-note').val('');
        $('#exempt-tourism-tax-modal').modal('show');
    });

    $(document).on('click', '#tt-exempt-confirm', function (e) {
        if (!ttExemptTarget) {
            return;
        }
        $('#exempt-tourism-tax-modal').modal('hide');
        ttAjax('ExemptTourismTax', $.extend({ note: $('#tt-exempt-note').val() }, ttExemptTarget));
    });
});
</script>
