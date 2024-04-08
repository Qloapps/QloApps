/*
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
*/

// send a message in relation to the order with ajax
function sendOrderMessage() {
    let paramString = 'ajax=true&action=submitMessage';
    $('#sendOrderMessage').find('input, textarea, select').each(function () {
        paramString += '&' + $(this).attr('name') + '=' + encodeURIComponent($(this).val());
    });

    $.ajax({
        type: 'POST',
        headers: { 'cache-control': 'no-cache' },
        url: $('#sendOrderMessage').attr('action') + '?rand=' + new Date().getTime(),
        data: paramString,
        dataType: 'JSON',
        beforeSend: function () {
            $('.add-order-message .errors-block').hide(200).html('');
            $('.button[name=submitMessage]').prop('disabled', 'disabled');
        },
        success: function (response) {
            if (response.has_errors) {
                $('.add-order-message .errors-block').html(response.errors_html).show(200);
            } else if (response.status) {
                if (!$('.order-messages .messages-list .message').length) {
                    $('.order-messages').removeClass('hide');
                }
                $('.order-messages .messages-list').prepend(response.message_html);

                $('.add-order-message .select-room-type button span').html(order_message_choose_txt);
                $('.add-order-message .select-room-type .id_product').val(0);
                $('.add-order-message form textarea').val('');

                showSuccessMessage(order_message_success_txt);
            }
        },
        complete: function () {
            $('.button[name=submitMessage]').prop('disabled', false);
        }
    });
}

function initPriceTooltip() {
    if ($('.order-price-info').length) {
        $('.order-price-info').each(function () {
            $(this).tooltip({
                content: $(this).closest('dd').find('.price-info-container').html(),
                items: 'span',
                trigger: 'hover',
                tooltipClass: 'price-tootip',
                open: function (event, ui) {
                    if (typeof (event.originalEvent) === 'undefined') {
                        return false;
                    }

                    var $id = $(ui.tooltip).attr('id');

                    // close any lingering tooltips
                    if ($('div.ui-tooltip').not('#' + $id).length) {
                        return false;
                    }
                },
                close: function (event, ui) {
                    ui.tooltip.hover(function () {
                        $(this).stop(true).fadeTo(400, 1);
                    },
                    function () {
                        $(this).fadeOut('400', function () {
                            $(this).remove();
                        });
                    });
                }
            });
        });
    }
}

function initMap() {
    const hotelLocation = {
        lat: Number(hotel_location.latitude),
        lng: Number(hotel_location.longitude),
    };

    $('.booking-hotel-map-container').each(function (i, element) {
        const map = new google.maps.Map(element, {
            zoom: 10,
            center: hotelLocation,
            disableDefaultUI: true,
            fullscreenControl: true,
        });

        const marker = new google.maps.Marker({
            position: hotelLocation,
            map: map,
            title: hotel_name,
        });

        marker.addListener('click', function() {
            let query = '';
            if (hotel_location.map_input_text != '') {
                query = hotel_location.map_input_text;
            } else {
                query = hotel_location.latitude + ',' + hotel_location.longitude;
            }

            window.open('https://www.google.com/maps/search/?api=1&query='+encodeURIComponent(query), '_blank');
        });
    });
}

/* Refund management - start */
const BookingRefundManager = {
    show: function() {
        $.fancybox.open({
            href: '#create-new-refund-popup',
            wrapCSS: 'fancybox-order-detail',
            padding: 0,
        });
    },
    close: function() {
        $.fancybox.close();
    },
    beforeSubmit: function() {
        BookingRefundManager.hideGeneralErrors();
        BookingRefundManager.disableSubmitButton();
    },
    validate: function() {
        if (!$('#form-cancel-booking input.bookings_to_refund:not(:disabled):checked').length) {
            showErrorMessage(no_bookings_selected);
            return false;
        }

        return true;
    },
    submit: function() {
        BookingRefundManager.beforeSubmit();
        const formData = new FormData($('#form-cancel-booking').get(0));
        formData.append('ajax', true);
        formData.append('token', token);
        formData.append('action', 'SubmitRefundRequest');
        $.ajax({
            url: location.href,
            data: formData,
            processData: false,
            contentType: false,
            type: 'POST',
            dataType: 'JSON',
            success: function(response) {
                if (response.has_errors) {
                    BookingRefundManager.showGeneralErrors(response.errors_html);
                } else {
                    BookingRefundManager.close();
                    if (typeof response.order_cancelled != 'undefined' && response.order_cancelled) {
                        BookingRefundManager.showOrderCancelSuccessMessage();
                    } else {
                        BookingRefundManager.showRefundRequestSuccessMessage();
                    }
                    BookingRefundManager.reset();
                }
            },
            complete: function() {
                BookingRefundManager.enableSubmitButton();
            },
        });
    },
    reset: function () {
        $('#form-cancel-booking input.bookings_to_refund:not(:disabled):checked').closest('.room-details').addClass('cancelled');
        $('#form-cancel-booking input.bookings_to_refund:not(:disabled):checked').closest('span').removeClass('checked');
        $('#form-cancel-booking input.bookings_to_refund:not(:disabled):checked').attr('disabled', true);
        $('#form-cancel-booking .num-selected-rooms').html('00');
        $('#form-cancel-booking .count-total-rooms').html('00');
        $('#form-cancel-booking .cancellation_reason').val('');

        $('#form-cancel-booking .cancel-booking').show();
        $('#form-cancel-booking .cancel-booking-preview').hide();

        // disable next button if all rooms were selected cancelled
        let countRooms = $('#form-cancel-booking input.bookings_to_refund').length;
        let countDisabled = $('#form-cancel-booking input.bookings_to_refund:disabled').length;
        if (countRooms == countDisabled) {
            BookingRefundManager.disableNextButton();
        }
    },
    updateSelectedRooms: function() {
        let countSelected = $('#form-cancel-booking input.bookings_to_refund:not(:disabled):checked').length;
        countSelected = (countSelected <= 9) ? ('0' + countSelected) : countSelected;
        $('#form-cancel-booking .num-selected-rooms').html(countSelected);
        $('#form-cancel-booking .count-total-rooms').html(countSelected);
    },
    showGeneralErrors: function(errors) {
        $('.fancybox-wrap.fancybox-order-detail .cancel-booking-preview .errors').stop().html(errors);
        $('.fancybox-wrap.fancybox-order-detail .cancel-booking-preview .errors').show(200);
    },
    hideGeneralErrors: function() {
        $('.fancybox-wrap.fancybox-order-detail .cancel-booking-preview .errors').hide(200, function() {
            $('.fancybox-wrap.fancybox-order-detail .cancel-booking-preview .errors').html('');
        });
    },
    disableNextButton: function() {
        $('#form-cancel-booking .btn-next').addClass('disabled');
    },
    enableSubmitButton: function() {
        $('#form-cancel-booking .btn-submit').removeClass('disabled');
    },
    disableSubmitButton: function() {
        $('#form-cancel-booking .btn-submit').addClass('disabled');
    },
    showRefundRequestSuccessMessage: function() {
        $.fancybox.open({
            href: '#popup-cancellation-submit-success',
            wrapCSS: 'fancybox-order-detail feedback',
            padding: 0,
        });
    },
    showOrderCancelSuccessMessage: function() {
        $.fancybox.open({
            href: '#popup-cancellation-order-cancel-success',
            wrapCSS: 'fancybox-order-detail feedback',
            padding: 0,
        });
    },
}

$(document).on('click', '#order_refund_request', function(e) {
    e.preventDefault();
    BookingRefundManager.show();
});

$(document).on('click', '.fancybox-order-detail .cancel-booking .btn-cancel', function(e) {
    e.preventDefault();
    BookingRefundManager.close();
});

$(document).on('click', '.fancybox-order-detail .cancel-booking .btn-next', function(e) {
    e.preventDefault();

    if (BookingRefundManager.validate()) {
        $('.fancybox-order-detail .cancel-booking').hide();
        $('.fancybox-order-detail .cancel-booking-preview').show();
    }
});

$(document).on('click', '.fancybox-order-detail .cancel-booking-preview .btn-back', function(e) {
    e.preventDefault();
    $('.fancybox-order-detail .cancel-booking-preview').hide();
    $('.fancybox-order-detail .cancel-booking').show();
});

$(document).on('click', '.fancybox-order-detail .cancel-booking-preview .btn-submit', function(e) {
    e.preventDefault();
    BookingRefundManager.submit();
});

$(document).on('click', '.fancybox-order-detail input.bookings_to_refund', function() {
    BookingRefundManager.updateSelectedRooms();
});

/* Refund management - end */

$(document).on('click', '.btn-view-extra-services', function(e) {
    e.preventDefault();

    let idProduct = $(this).data('id_product');
    let idOrder = $(this).data('id_order');
    let dateFrom = $(this).data('date_from');
    let dateTo = $(this).data('date_to');

    $.ajax({
        type: 'POST',
        headers: {
            'cache-control': 'no-cache',
        },
        url: window.location,
        dataType: 'json',
        cache: false,
        data: {
            date_from: dateFrom,
            date_to: dateTo,
            id_product: idProduct,
            id_order: idOrder,
            action: 'getRoomTypeBookingDemands',
            ajax: true,
            token: static_token,
        },
        success: function(result) {
            if (result.extra_demands) {
                $('#popup-view-extra-services').html('');
                $('#popup-view-extra-services').append(result.extra_demands);

                $.fancybox.open({
                    href: '#popup-view-extra-services',
                    wrapCSS: 'fancybox-order-detail',
                    padding: 0,
                });
            }
        },
    });
});

$(document).ready(function () {
    $(document).on('click', '.add-order-message .dropdown-menu li a', function (e) {
        e.preventDefault();

        $('.add-order-message .select-room-type button span').html($(this).html());
        $('.add-order-message .select-room-type .id_product').val($(this).attr('value'));
    });

    $(document).on('click', '#submitMessage', function (e) {
        e.preventDefault();

        sendOrderMessage();
    });

    initPriceTooltip();

    if (typeof google === 'object'
        && typeof google.maps === 'object'
        && typeof hotel_location === 'object'
    ) {
        initMap();
    }
});
