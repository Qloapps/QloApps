/*
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
*/

$(document).ready(function() {
    $("#date_from").datepicker({
        showOtherMonths: true,
        dateFormat: 'yy-mm-dd',
        onClose: function() {
            let dateFrom = $('#date_from').val().trim();
            let dateTo = $('#date_to').val().trim();

            if (dateFrom >= dateTo) {
                let objDateToMin = $.datepicker.parseDate('yy-mm-dd', dateFrom);
                objDateToMin.setDate(objDateToMin.getDate());

                $('#date_to').datepicker('option', 'minDate', objDateToMin);
            }
        },
    });

    $("#date_to").datepicker({
        showOtherMonths: true,
        dateFormat: 'yy-mm-dd',
        beforeShow: function() {
            let dateFrom = $('#date_from').val().trim();

            if (typeof dateFrom != 'undefined' && dateFrom != '') {
                let objDateToMin = $.datepicker.parseDate('yy-mm-dd', dateFrom);
                objDateToMin.setDate(objDateToMin.getDate());

                $('#date_to').datepicker('option', 'minDate', objDateToMin);
            }
        },
    });

    $('.change_status').on('click', function (e) {
        e.preventDefault();
        var id_order_slip = $.trim($(this).closest('tr').find('td[data-key="id_order_slip"]').text());
        processupdate(id_order_slip, $(this).attr('href'))
        return false;
    });

    function processupdate(id_order_slip, link)
    {
        confirmDelete(id_order_slip).then((toUpdate) => {
            if (toUpdate) {
                window.location = link;
            }
        });

    }

    function confirmDelete(id_order_slip)
    {
        return new Promise((resolve) => {
            $.ajax({
                type: 'POST',
                url: admin_order_slip_tab_link,
                dataType: 'JSON',
                cache: false,
                data: {
                    ajax: true,
                    action: 'initSlipStatusModal',
                    id_order_slip: id_order_slip
                },
                success: function(result) {
                    if (result.success && result.modalHtml) {
                        $('#moduleConfirmUpdate').remove();
                        $('#footer').next('.bootstrap').append(result.modalHtml);
                        $('#moduleConfirmUpdate').modal('show');
                        $('#moduleConfirmUpdate .process_update').click(() => {
                            resolve(true);
                        });
                        $('#moduleConfirmUpdate .btn-close').click(() => {
                            resolve(false);
                        });
                    } else {
                        resolve(true);
                    }
                },
                complete: function() {
                    $(".loading_overlay").hide();
                }
            });
        });
    }

    var allBookings = [];
    var currencySign = '';

    if (typeof $.fn.chosen !== 'undefined') {
        $('select[name="id_order"]').chosen('destroy').chosen({
            width: '100%',
            search_contains: true
        });
        $('select[name="id_room_type"]').chosen({
            width: '100%',
            search_contains: true
        });
        $('select[name="id_booking_detail"]').chosen({
            width: '100%',
            search_contains: true
        });
    }

    $('select[name="id_order"]').on('change', function () {
        var idOrder = $(this).val();
        var $roomTypeSelect = $('select[name="id_room_type"]');
        var $bookingSelect = $('select[name="id_booking_detail"]');

        allBookings = [];
        $roomTypeSelect.empty().trigger('chosen:updated');
        $bookingSelect.empty().trigger('chosen:updated');

        if (!idOrder) {
            $('#order-info').hide();
            $('#order-view-link').hide();
            return;
        }

        var selectedLabel = $('select[name="id_order"] option:selected').text();
        $('#order-info').text(selectedLabel).show();

        $('#order-view-link')
            .attr('href', admin_order_view_link + '&id_order=' + idOrder + '&vieworder')
            .show();

        $.ajax({
            url: ajax_booking_url,
            type: 'POST',
            dataType: 'json',
            data: {
                ajax: 1,
                action: 'GetBookingDetails',
                id_order: idOrder
            },
            success: function (response) {
                allBookings = response.bookings || [];

                var addedRoomTypes = {};
                $.each(allBookings, function (_, booking) {
                    if (!addedRoomTypes[booking.id_product]) {
                        addedRoomTypes[booking.id_product] = true;
                        $roomTypeSelect.append($('<option>', {
                            value: booking.id_product,
                            text: booking.room_type_name
                        }));
                    }
                });

                currencySign = response.currency.sign;
                $('#credit_slip_amount')
                    .closest('.input-group')
                    .find('.input-group-addon, .input-group-text')
                    .text(currencySign);

                $roomTypeSelect.trigger('chosen:updated');

                if (prevRoomType > 0) {
                    $roomTypeSelect.val(prevRoomType).trigger('chosen:updated');
                    prevRoomType = 0;
                }
                $roomTypeSelect.trigger('change');
            },
            error: function (_, status, error) {
                console.error('[CreditSlip] AJAX error:', status, error);
            }
        });
    });

    $('select[name="id_room_type"]').on('change', function () {
        var idProduct = $(this).val();
        var $bookingSelect = $('select[name="id_booking_detail"]');

        $bookingSelect.empty();

        if (!idProduct) {
            $bookingSelect.trigger('chosen:updated');
            return;
        }

        var filtered = $.grep(allBookings, function (booking) {
            return String(booking.id_product) === String(idProduct);
        });

        $.each(filtered, function (_, booking) {
            $bookingSelect.append($('<option>', {
                value: booking.id,
                text: booking.room_num + ' | ' + booking.date_from.substring(0, 10) + ' - ' + booking.date_to.substring(0, 10)
            }));
        });

        $bookingSelect.trigger('chosen:updated');

        if (prevBookingDetail > 0) {
            $bookingSelect.val(prevBookingDetail).trigger('chosen:updated');
            prevBookingDetail = 0;
        }
        $bookingSelect.trigger('change');
    });

    $('select[name="id_booking_detail"]').on('change', function () {
        var idBooking = $(this).val();
        var booking = $.grep(allBookings, function (b) {
            return String(b.id) === String(idBooking);
        })[0];

        if (booking) {
            var roomCost = parseFloat(booking.total_price_tax_incl);
            var serviceCost = parseFloat(booking.extra_service_total_price_tax_incl || 0);
            var total = roomCost + serviceCost;
            $('#booking-amount').text(currencySign + ' ' + total.toFixed(2));
        } else {
            $('#booking-amount').text('-');
        }
    });

    if ($('select[name="id_order"]').val()) {
        $('select[name="id_order"]').trigger('change');
    }
});