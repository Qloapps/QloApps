/**
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

const BookingSearchManager = {
    inputSteps: {
        'location': {
            init: function() {
                $('.location_search_results_ul').hide();

                // search location with users searched characters
                $(document).on('keyup', '#hotel_location', function(e) {
                    BookingSearchManager.inputSteps['location'].onInput.call(this, e);
                });

                // set data on clicking the searched location on dropdown
                $(document).on('click', '.location_search_results_ul li', function(e) {
                    e.preventDefault();

                    BookingSearchManager.inputSteps['location'].onInputComplete.call(this, e);
                });

                // handle Tab key press on hotel location field
                $(document).on('keydown', '#hotel_location', function (e) {
                    if (e.which == 9) { // Tab key
                        e.preventDefault();

                        if ($('.location_search_results_ul').is(':visible')) {
                            $('.location_search_results_ul li:first').focus().click();
                        } else {
                            BookingSearchManager.activateStep('hotel');
                        }
                    }
                });

                // handle Tab key press on hotel location list
                $(document).on('keydown', '.location_search_results_ul li', function (e) {
                    if (e.which == 9) { // Tab key
                        e.preventDefault();
                        $(this).click();
                    }
                });

                // navigate to prev and next li in the location dropdown
                $('body').on('keydown', '.location_search_results_ul li', function(e) {
                    if (e.which == 40 || e.which == 38) {
                        var ulElement = $(this).closest('ul');
                        var ulLength = ulElement.find('li').length;
                        $(this).blur();
                        ulElement.scrollTop($(this).index() * $(this).outerHeight());
                        if (e.which == 40) {
                            if ($(this).index() != (ulLength - 1)) {
                                $(this).next('li.search_result_li').focus();
                            } else {
                                ulElement.find('li:first').focus();
                            }
                        } else if (e.which == 38) {
                            if ($(this).index()) {
                                $(this).prev('li.search_result_li').focus();
                            } else {
                                ulElement.find('li:last').focus();
                            }
                        }
                    }
                });
            },
            activate: function() {
                $('#hotel_location').focus();
            },
            inputHasValue: function () {
                return ($('#hotel_location').val() != '') && ($('#location_category_id').val() != '');
            },
            onInput: function (e) {
                if (e.which == 13) {
                    return;
                }

                if (($('.location_search_results_ul').is(':visible')) && (e.which == 40 || e.which == 38)) {
                    $(this).blur();
                    if (e.which == 40) {
                        $('.location_search_results_ul li:first').focus();
                    } else if (e.which == 38) {
                        $('.location_search_results_ul li:last').focus();
                    }
                } else {
                    $('.location_search_results_ul').empty().hide();
                    if ($(this).val() != '') {
                        abortRunningAjax();
                        ajax_check_var = $.ajax({
                            url: autocomplete_search_url,
                            data: {
                                to_search_data: $(this).val(),
                            },
                            method: 'POST',
                            dataType: 'json',
                            success: function(result) {
                                if (result.status) {
                                    $('.location_search_results_ul').html(result.data);
                                    $('.location_search_results_ul').show();
                                }
                            }
                        });
                    } else {
                        $('#location_category_id').val('');
                    }
                }
            },
            onInputComplete: function (e) {
                e.preventDefault();

                $('.location_search_results_ul').empty().hide();
                $('#hotel_location').attr('value', $(this).html());
                $('#location_category_id').val($(this).val());

                // fetch hotels for selected location
                $.ajax({
                    url: autocomplete_search_url,
                    data: {
                        location_category_id: $('#location_category_id').val(),
                    },
                    method: 'POST',
                    dataType: 'json',
                    success: function(result) {
                        if (result.status) {
                            $('#hotel_cat_id').val('');
                            $('#id_hotel_button').html(result.html_hotel_options);
                            $('#id_hotel_button').trigger('chosen:updated');
                            // Resetting the data from previously selected hotel
                            $('#min_booking_offset').val(0);
                            var max_order_date = $('#max_order_date').val();
                            var min_booking_offset = 0;
                            createDateRangePicker(max_order_date, min_booking_offset, $('#check_in_time').val(), $('#check_out_time').val());
                            if (search_auto_focus_next_field) {
                                BookingSearchManager.activateStep('hotel');
                            }
                        } else {
                            alert(no_results_found_cond);
                        }
                    }
                });
            },
        },
        'hotel': {
            init: function() {
                $('select#id_hotel_button').chosen({
                    search_contains: true,
                    disable_search: !hotel_name_has_search,
                    width: '100%',
                });

                $(document).on('change', '#id_hotel_button', function() {
                    if ($(this).find('option:selected').val().trim() != '') {
                        $('#id_hotel_button_chosen .chosen-search input').blur();
                        BookingSearchManager.inputSteps['hotel'].onInputComplete.call(this);
                    }
                });

                // prevents calendar from closing on hotel selection
                $(document).on('click', '#id_hotel_button_chosen li', function(e) {
                    e.stopPropagation();
                });

                $(document).on('keydown', '#id_hotel_button_chosen.chosen-container-active', function (e) {
                    if (e.which == 9) { // Tab key
                        e.preventDefault();
                        if ($('#daterange_value').length) {
                            $('#daterange_value').focus();
                            $('#daterange_value').click();
                        }
                    }
                });

                $(document).on('keydown', '#id_hotel_button_chosen .chosen-search input', function (e) {
                    if (e.which == 9 && $('#daterange_value').siblings('.date-picker-wrapper').is(':visible')) { // Tab key
                        e.preventDefault();
                        $('#daterange_value').data('dateRangePicker').close();

                        if (is_occupancy_wise_search) {
                            BookingSearchManager.activateStep('occupancy');
                        } else {
                            BookingSearchManager.activateStep('submit');
                        }
                    }
                });

                // if chosen will not be initialized add placeholder to hotel select element as first option
                if (!BookingSearchManager.isBrowserSupported()) {
                    $('select#id_hotel_button option').first().html(select_htl_txt);
                }

                if (hotel_name_has_search) {
                    $('select#id_hotel_button').on('chosen:showing_dropdown', function() {
                        $(this).siblings('.chosen-container').find('.chosen-single').addClass('invisible')
                    });

                    $('select#id_hotel_button').on('chosen:hiding_dropdown', function() {
                        $(this).siblings('.chosen-container').find('.chosen-single').removeClass('invisible')
                    });
                }
            },
            activate: function() {
                $('#id_hotel_button').trigger('chosen:open');
            },
            inputHasValue: function () {
                return ($('#id_hotel').val() != '') && ($('#hotel_cat_id').val() != '');
            },
            onInputComplete: function () {
                $('#id_hotel_button_chosen .chosen-search input').blur();
                const selectedHotel = $(this).find('option:selected');

                if ($(selectedHotel).val().trim() != '') {
                    const maxOrderDate = $(selectedHotel).attr('data-max_order_date');
                    const minBookingOffset = $(selectedHotel).attr('data-min_booking_offset')

                    createDateRangePicker(maxOrderDate, minBookingOffset, $('#check_in_time').val(), $('#check_out_time').val());

                    $('#max_order_date').val(maxOrderDate);
                    $('#min_booking_offset').val(minBookingOffset);
                    $('#id_hotel').val($(selectedHotel).attr('data-id-hotel'));
                    $('#hotel_cat_id').val($(selectedHotel).attr('data-hotel-cat-id'));

                    if (search_auto_focus_next_field) {
                        setTimeout(function () {
                            if ($('#id_hotel_button').data('chosen') != undefined) {
                                $('#id_hotel_button').data('chosen').close_field();
                            }

                            BookingSearchManager.activateStep('date_range');
                        }, 10);
                    }
                } else {
                    $('#id_hotel').val('');
                    $('#hotel_cat_id').val('');
                }
            },
        },
        'date_range': {
            init: function() {
                $(document).on('keydown', '#daterange_value', function (e) {
                    if (e.which == 9) { // Tab key
                        e.preventDefault();
                        $('#daterange_value').removeClass('focused').blur();

                        if ($('#daterange_value').data('dateRangePicker') != undefined) {
                            $('#daterange_value').data('dateRangePicker').close();
                        }

                        if (is_occupancy_wise_search) {
                            BookingSearchManager.activateStep('occupancy');
                        } else {
                            BookingSearchManager.activateStep('submit');
                        }
                    }
                });
            },
            activate: function() {
                $('#daterange_value').data('dateRangePicker').open();
            },
            inputHasValue: function () {
                return ($('#check_in_time').val() != '') && ($('#check_out_time').val() != '');
            },
        },
        'occupancy': {
            init: function() {
                $(document).on('keydown', '#guest_occupancy', function (e) {
                    if (e.which == 9) { // Tab key
                        e.preventDefault();
                        if ($('#search_occupancy_wrapper').css('display') != 'none') {
                            $('#guest_occupancy').click();
                        }

                        BookingSearchManager.activateStep('submit');
                    }
                });
            },
            activate: function() {
                if ($('#search_occupancy_wrapper').css('display') == 'none') {
                    $('#guest_occupancy').click().focus();
                }
            },
            inputHasValue: function () {
                return false;
            },
        },
        'submit': {
            activate: function() {
                $('#search_room_submit').focus();
            },
            inputHasValue: function () {
                return false;
            },
        },
    },
    init: function () {
        this.inputSteps['location'].init();
        this.inputSteps['hotel'].init();
        this.inputSteps['date_range'].init();
        if (is_occupancy_wise_search) {
            this.inputSteps['occupancy'].init();
        }
    },
    activateStep: function (step) {
        if (step in this.inputSteps) {
            this.inputSteps[step].activate();
        }
    },
    allFieldsFilled: function () {
        return this.inputSteps['hotel'].inputHasValue()
            && this.inputSteps['date_range'].inputHasValue()
    },
    isBrowserSupported: function() { // defined as it is from chosen to decide if chosen will be initialized
        if (window.navigator.appName === "Microsoft Internet Explorer") {
            return document.documentMode >= 8;
        }
        if (/iP(od|hone)/i.test(window.navigator.userAgent)) {
            return false;
        }
        if (/Android/i.test(window.navigator.userAgent)) {
            if (/Mobile/i.test(window.navigator.userAgent)) {
                return false;
            }
        }

        return true;
    },
}

$(document).ready(function() {
    // initialize booking search fields
    BookingSearchManager.init();

    // for screen size changes for room search
    var window_width = $(window).width();
    if (window_width > 767) {
        $('.fancy_search_header_xs').hide();
    }

    if ($("body").length) {
        $(window).resize(function() {
            var window_width = $(window).width();
            if (window_width > 767) {
                $.fancybox.close();
                $('.fancy_search_header_xs').hide();
            } else {
                $('.fancy_search_header_xs').show();
            }
        });
    }

    const roomSearchForm = $('form#search_hotel_block_form');
    const mdFormWrapper = $('.header-rmsearch-inner-wrapper');
    const roomSearchModal = $('#rmsearchmodal .modal-body');
    const mobileBreakpoint = 768;
    function relocateSearchForm() {
        if (window.innerWidth < mobileBreakpoint) {
            if (!roomSearchModal.find(roomSearchForm).length) {
                roomSearchForm.appendTo(roomSearchModal);
            }
        } else {
            if (!mdFormWrapper.find(roomSearchForm).length) {
                roomSearchForm.appendTo(mdFormWrapper);
            }
            $('#rmsearchmodal').modal('hide');
        }
    }
    relocateSearchForm();
    $(window).on('resize', function () {
        relocateSearchForm();
     });

   $(document).on('click', '#xs_room_search', function () {
        if (window.innerWidth < 768) {
            $('#rmsearchmodal').modal('show');
        }
    });

    /*END*/
    var ajax_check_var = '';

    createDateRangePicker = function (max_order_date, min_booking_offset, dateFrom, dateTo) {
        let start_date = new Date();
        if (min_booking_offset) {
            start_date.setDate(start_date.getDate() + parseInt(min_booking_offset));
            start_date.setHours(0, 0, 0, 0);
        }

        // Using the Date object will also add extra hours according to the timezone.
        let selectedDateFrom = $.datepicker.parseDate('yy-mm-dd', dateFrom);
        let selectedDateTo = $.datepicker.parseDate('yy-mm-dd', dateTo);
        if (max_order_date) {
            max_order_date = $.datepicker.parseDate('yy-mm-dd', max_order_date);
        } else {
            max_order_date = false;
        }

        if (selectedDateFrom < start_date
            || selectedDateTo < start_date
            || (max_order_date && (max_order_date < selectedDateTo))
        ) {
            $('#check_in_time').val('');
            $('#check_out_time').val('');
        }

        if (typeof $('#daterange_value').data('dateRangePicker') != 'undefined') {
            if (max_order_date) {
                if ($.datepicker.parseDate('yy-mm-dd', $('#check_out_time').val()) < max_order_date) {
                    dateFrom = dateFrom ? dateFrom :$('#check_in_time').val();
                    dateTo = dateTo ? dateTo : $('#check_out_time').val();
                } else {
                    dateFrom = false;
                    dateTo = false;
                }
            }
            $('#daterange_value').data('dateRangePicker').clear();
            $('#daterange_value').data('dateRangePicker').destroy();
            $("#daterange_value").off("datepicker-change");
        }

        if (max_order_date) {
            max_order_date = $.datepicker.formatDate('dd-mm-yy', max_order_date);
        }

        if (typeof(multiple_dates_input) != 'undefined' && multiple_dates_input) {
            $('#daterange_value').dateRangePicker({
                startDate: $.datepicker.formatDate('dd-mm-yy', new Date()),
                separator : ' to ',
                setValue: function(s,s1,s2)
                {
                    if (s1) {
                        $('#daterange_value_from').find('span').html(s1);
                    } else {
                        $(daterange_value_from).find('span').html(
                            RangePickerCheckin
                        );
                    }
                    if (s2) {
                        $('#daterange_value_to').find('span').html(s2);
                    } else {
                        $('#daterange_value_to').find('span').html(
                            RangePickerCheckin
                        );
                    }
                },
                endDate: max_order_date,
                customOpenAnimation: function(cb)
                {
                    $(this).show(10, cb);
                },
                customCloseAnimation: function(cb)
                {
                    $(this).hide(10, cb);
                }
            }).on('datepicker-first-date-selected', function() {
                calendarFirstDateSelected = true;
            }).on('datepicker-change', function(event,obj){
                $('#check_in_time').val($.datepicker.formatDate('yy-mm-dd', obj.date1));
                $('#check_out_time').val($.datepicker.formatDate('yy-mm-dd', obj.date2));
                focusNextOnCalendarClose = true;
                calendarFirstDateSelected = false;
            }).on('datepicker-open', function() {
                $('#daterange_value').addClass('focused').focus();
            }).on('datepicker-close', function() {
                $('#daterange_value').removeClass('focused').blur();
            }).on('datepicker-closed', function() {
                if (search_auto_focus_next_field && focusNextOnCalendarClose) {
                    if (is_occupancy_wise_search) {
                        if ($('#check_in_time').val() != '' && $('#check_out_time').val() != '') {
                            BookingSearchManager.activateStep('occupancy');
                        }
                    } else {
                        if (BookingSearchManager.allFieldsFilled()) {
                            BookingSearchManager.activateStep('submit');
                        }
                    }

                    focusNextOnCalendarClose = false;
                }
            });
        } else {
            $('#daterange_value').dateRangePicker({
                startDate: start_date,
                endDate: max_order_date,
                customOpenAnimation: function(cb)
                {
                    $(this).show(10, cb);
                },
                customCloseAnimation: function(cb)
                {
                    $(this).hide(10, cb);
                }
            }).on('datepicker-first-date-selected', function() {
                calendarFirstDateSelected = true;
            }).on('datepicker-change', function(event,obj){
                $('#check_in_time').val($.datepicker.formatDate('yy-mm-dd', obj.date1));
                $('#check_out_time').val($.datepicker.formatDate('yy-mm-dd', obj.date2));
                focusNextOnCalendarClose = true;
                calendarFirstDateSelected = false;
            }).on('datepicker-open', function() {
                $('#daterange_value').addClass('focused').focus();
            }).on('datepicker-close', function() {
                $('#daterange_value').removeClass('focused').blur();
                calendarFirstDateSelected = false;
            }).on('datepicker-closed', function() {
                if (search_auto_focus_next_field && focusNextOnCalendarClose) {
                    if (is_occupancy_wise_search) {
                        if ($('#check_in_time').val() != '' && $('#check_out_time').val() != '') {
                            BookingSearchManager.activateStep('occupancy');
                        }
                    } else {
                        if (BookingSearchManager.allFieldsFilled()) {
                            BookingSearchManager.activateStep('submit');
                        }
                    }

                    focusNextOnCalendarClose = false;
                }
            });
        }

        if (dateFrom && dateTo) {
            $('#daterange_value').data('dateRangePicker').setDateRange(
                $.datepicker.formatDate('dd-mm-yy', $.datepicker.parseDate('yy-mm-dd', dateFrom)),
                $.datepicker.formatDate('dd-mm-yy', $.datepicker.parseDate('yy-mm-dd', dateTo))
            );
        }
    }

    abortRunningAjax = function () {
        if (ajax_check_var) {
            ajax_check_var.abort();
        }
    }

    // If only one hotel then set max order date on date pickers
    var max_order_date = $('#max_order_date').val();
    var min_booking_offset = $('#min_booking_offset').val();
    createDateRangePicker(max_order_date, min_booking_offset, $('#check_in_time').val(), $('#check_out_time').val());

    // validations on the submit of the search fields
    $(document).on('click', '#search_room_submit', function() {
        var check_in_time = $("#check_in_time").val();
        var check_out_time = $("#check_out_time").val();
        var max_order_date = $("#max_order_date").val();
        var error = false;

        var location_category_id = $('#location_category_id').val();
        var hotelCatId = $('#hotel_cat_id').val();
        $('.header-rmsearch-input').removeClass("border-danger");

        if (hotelCatId == '') {
            if (typeof(location_category_id) == 'undefined' || location_category_id == '') {
                $("#hotel_location").addClass("border-danger");
                error = true;
            }
            $("#id_hotel_button_chosen").addClass("border-danger");
            $('#select_htl_error_p').text(hotel_name_cond);
            error = true;
        }
        var date_selector
        if (typeof(multiple_dates_input) != 'undefined' && multiple_dates_input) {
            date_selector =  '#daterange_value_from, #daterange_value_to';
        } else {
            date_selector =  '#daterange_value';
        }
        if (check_in_time == '') {
            $(date_selector).addClass("border-danger");
            $('#daterange_value_error_p').text(check_in_time_cond);
            error = true;
        } else if (check_in_time < $.datepicker.formatDate('yy-mm-dd', new Date())) {
            $(date_selector).addClass("border-danger");
            $('#daterange_value_error_p').text(less_checkin_date);
            error = true;
        }
        if (check_out_time == '') {
            $(date_selector).addClass("border-danger");
            $('#daterange_value_error_p').text(check_out_time_cond);
            error = true;
        } else if (check_out_time < check_in_time) {
            $(date_selector).addClass("border-danger");
            $('#daterange_value_error_p').text(more_checkout_date);
            error = true;
        } else if (max_order_date < check_in_time) {
            $(date_selector).addClass("border-danger");
            $('#daterange_value_error_p').text(max_order_date_err + ' ' + max_order_date);
            error = true;
        } else if (max_order_date < check_out_time) {
            $(date_selector).addClass("border-danger");
            $('#daterange_value_error_p').text(max_order_date_err + ' ' + max_order_date);
            error = true;
        }

        if (error)
            return false;
        else
            return true;
    });

    // Occupancy field dropdown
    // add occupancy info block
    $(document).on('click', '#search_occupancy_wrapper .add_new_occupancy_btn', function(e) {
        e.preventDefault();

        var roomBlockIndex = parseInt($("#search_occupancy_wrapper .occupancy_info_block").last().attr('occ_block_index'));
        roomBlockIndex += 1;

        var countRooms = parseInt($('#search_occupancy_wrapper .occupancy_info_block').length);
        countRooms += 1

        var occupancy_adults_block = $('<div>', { class: 'form-group occupancy_count_block col-sm-5 col-6' }).append(
            $('<label>').text(adults_txt),
            $('<div>', { class: 'clearfix d-flex' }).append(
                $('<input>', {
                    type: 'hidden',
                    class: 'num_occupancy num_adults room_occupancies',
                    name: 'occupancy[' + roomBlockIndex + '][adults]',
                    value: 1
                }),
                $('<div>', {
                    class: 'occupancy_count border p-small rounded float-left mr-1 max-width-7'
                }).append($('<span>', {class : 'min-width-3 d-block text-center'}).text('1')),
                $('<div>', { class: 'qty_direction d-flex flex-column gap-1' }).append(
                    $('<a>', { href: '#', class: 'p-0 border rounded occupancy_quantity_up' }).append($('<i>', { class: 'icon-plus' })),
                    $('<a>', { href: '#', class: 'p-0 border rounded occupancy_quantity_down' }).append($('<i>', { class: 'icon-minus' }))
                )
            )
        );

        var occupancy_children_block = $('<div>', { class: 'form-group occupancy_count_block col-sm-7 col-6' }).append(
            $('<label>').text(children_txt),
            $('<div>', { class: 'clearfix d-flex' }).append(
                $('<input>', {
                    type: 'hidden',
                    class: 'num_occupancy num_children room_occupancies',
                    name: 'occupancy[' + roomBlockIndex + '][children]',
                    value: 0
                }),
                $('<div>', {
                    class: 'occupancy_count border p-small rounded float-left mr-1 max-width-7'
                }).append($('<span>', {class: 'min-width-3 d-block text-center'}).text('0')),
                $('<div>', { class: 'qty_direction d-flex flex-column gap-1' }).append(
                    $('<a>', { href: '#', class: 'p-0 border rounded occupancy_quantity_up' }).append($('<i>', { class: 'icon-plus' })),
                    $('<a>', { href: '#', class: 'p-0 border rounded occupancy_quantity_down' }).append($('<i>', { class: 'icon-minus' }))
                )
            ),
            $('<div>', {
                class: 'label-desc-txt help_block',
                text: '(' + below_txt + ' ' + max_child_age + ' ' + years_txt + ')'
            })
        );

        var occupancy_row = $('<div>', { class: 'row' }).append(occupancy_adults_block, occupancy_children_block);

        var occupancy_children_age_block = $('<div>', { class: 'row' }).append(
            $('<div>', { class: 'form-group children_age_info_block col-sm-12' }).append(
                $('<label>', {class: 'help_block'}).text(all_children_txt),
                $('<div>', { class: 'children_ages d-grid gap-1 justify-content-between' })
            ).css('display', 'none')
        );

        var occupancy_info_block = $('<div>', {
            class: 'occupancy_info_block',
            occ_block_index: roomBlockIndex
        }).append(occupancy_row, occupancy_children_age_block);

        var occupancy_header = $('<div>', { class: 'occupancy_info_head font-weight-bold mb-2' }).append(
            $('<span>', {
                class: 'room_num_wrapper',
                text: room_txt + ' - ' + countRooms
            }),
            $('<a>', {
                href: '#',
                class: 'remove-room-link float-right text-danger text-decoration-none',
                text: remove_txt
            })
        );

        var occupancy_block = $('<div>', { class: 'occupancy-room-block' })
            .append(occupancy_header, occupancy_info_block, $('<hr>', { class: 'occupancy-info-separator ml-n3 mr-n3' }));

        $('#occupancy_inner_wrapper').append(occupancy_block);

        // scroll to the latest added room
        $("#search_occupancy_wrapper").animate({ scrollTop: $("#search_occupancy_wrapper").prop('scrollHeight') }, "slow");

        setGuestOccupancy();
    });

    // remove occupancy info block
    $(document).on('click', '#search_occupancy_wrapper .remove-room-link', function(e) {
        e.preventDefault();
        $(this).closest('#search_occupancy_wrapper .occupancy-room-block').remove();

        $( "#search_occupancy_wrapper .room_num_wrapper" ).each(function(key, val) {
            $(this).text(room_txt + ' - '+ (key+1) );
        });

        setGuestOccupancy();
    });

    // increase the quantity of adults and child
    $(document).on('click', '#search_occupancy_wrapper .occupancy_quantity_up', function(e) {
        e.preventDefault();
        // set input field value
        var element = $(this).closest('.occupancy_count_block').find('.num_occupancy');
        var elementVal = parseInt(element.val()) + 1;

        var childElement = $(this).closest('.occupancy_count_block').find('.num_children').length;
        if (childElement) {
            var totalChilds = $(this).closest('.occupancy_info_block').find('.guest_child_age').length;

            if (max_child_in_room == 0 || totalChilds < max_child_in_room) {
                element.val(elementVal);
                $(this).closest('.occupancy_info_block').find('.children_age_info_block').show();

                var roomBlockIndex = parseInt($(this).closest('.occupancy_info_block').attr('occ_block_index'));
                var childAgeSelectWrapper = $('<div>');

                var ageSelect = $('<select>', {
                    class: 'guest_child_age custom-select small room_occupancies',
                    name: 'occupancy[' + roomBlockIndex + '][child_ages][]'
                });

                ageSelect.append($('<option>', { value: -1, text: select_age_txt }), $('<option>', { value: 0, text: under_1_age }));
                for (let age = 1; age < max_child_age; age++) {
                    ageSelect.append(
                        $('<option>', {
                            value: age,
                            text: age
                        })
                    );
                }

                childAgeSelectWrapper.append(ageSelect);
                $(this).closest('.occupancy_info_block').find('.children_ages').append(childAgeSelectWrapper);

                // set input field value
                $(this).closest('.occupancy_count_block').find('.occupancy_count > span').text(elementVal);
            } else {
                if (elementVal >= max_child_in_room) {
                    if (elementVal == 0) {
                        showOccupancyError(no_children_allowed_txt, $(this).closest(".occupancy_info_block"));
                    } else {
                        showOccupancyError(max_children_txt, $(this).closest(".occupancy_info_block"));
                    }
                } else {
                    showOccupancyError(max_occupancy_reached_txt, $(this).closest(".occupancy_info_block"));
                }
            }
        } else {
            element.val(elementVal);

            // set input field value
            $(this).closest('.occupancy_count_block').find('.occupancy_count > span').text(elementVal);
        }

        setGuestOccupancy();
    });

    var errorMsgTime;
    function showOccupancyError(msg, occupancy_info_block)
    {
        var errorMsgBlock = $(occupancy_info_block).find('.occupancy-input-errors')
        $(errorMsgBlock).html(msg).parent().show('fast');
        clearTimeout(errorMsgTime);
        errorMsgTime = setTimeout(function() {
            $(errorMsgBlock).parent().hide('fast');
        }, 1000);

    }

    $(document).on('click', '#search_occupancy_wrapper .occupancy_quantity_down', function(e) {
        e.preventDefault();
        // set input field value
        var element = $(this).closest('.occupancy_count_block').find('.num_occupancy');
        var elementVal = parseInt(element.val()) - 1;
        var childElement = $(this).closest('.occupancy_count_block').find('.num_children').length;

        if (childElement) {
            if (elementVal < 0) {
                elementVal = 0;
            } else {
                $(this).closest('.occupancy_info_block').find('.children_ages select').last().closest('div').remove();
                if (elementVal <= 0) {
                    $(this).closest('.occupancy_info_block').find('.children_age_info_block').hide();
                }
            }
        } else {
            if (elementVal == 0) {
                elementVal = 1;
            }
        }

        element.val(elementVal);
        // set input field value
        $(this).closest('.occupancy_count_block').find('.occupancy_count > span').text(elementVal);

        setGuestOccupancy();
    });

    // toggle occupancy block
    $('#guest_occupancy').on('click', function(e) {
        e.stopPropagation();
        if ($('#daterange_value').siblings('.date-picker-wrapper').is(':visible')) {
            $('#daterange_value').data('dateRangePicker').close();
        }
        if (validateOccupancies() ){
            $("#search_occupancy_wrapper").toggle();
        }
    });

    function validateOccupancies() {
        let hasErrors = 0;
        if ($('#search_occupancy_wrapper:visible').length) {
            let adults = $("#search_occupancy_wrapper").find(".num_adults").map(function(){return $(this).val();}).get();
            let children = $("#search_occupancy_wrapper").find(".num_children").map(function(){return $(this).val();}).get();
            let child_ages = $("#search_occupancy_wrapper").find(".guest_child_age").map(function(){return $(this).val();}).get();

            // start validating above values
            if (!adults.length || (adults.length != children.length)) {
                hasErrors = 1;
                showErrorMessage(invalid_occupancy_txt);
            } else {
                $("#search_occupancy_wrapper").find('.occupancy_count').removeClass('border-danger');

                // validate values of adults and children
                adults.forEach(function (item, index) {
                    if (isNaN(item) || parseInt(item) < 1) {
                        hasErrors = 1;
                        $("#search_occupancy_wrapper .num_adults").eq(index).closest('.occupancy_count_block').find('.occupancy_count').addClass('border-danger');
                    }
                    if (isNaN(children[index])) {
                        hasErrors = 1;
                        $("#search_occupancy_wrapper .num_children").eq(index).closest('.occupancy_count_block').find('.occupancy_count').addClass('border-danger');
                    }
                });

                // validate values of selected child ages
                $("#search_occupancy_wrapper").find('.guest_child_age').removeClass('border-danger');
                child_ages.forEach(function (age, index) {
                    age = parseInt(age);
                    if (isNaN(age) || (age < 0) || (age >= parseInt(max_child_age))) {
                        hasErrors = 1;
                        $("#search_occupancy_wrapper .guest_child_age").eq(index).addClass('border-danger');
                    }
                });
            }

            if (hasErrors == 0) {
                $("#search_occupancy_wrapper").hide();
                $("#search_hotel_block_form #guest_occupancy").removeClass('border-danger');
            } else {
                $("#search_hotel_block_form #guest_occupancy").addClass('border-danger');
                return false;
            }
        }


        return true;
    }

    // Body Events - start
    var focusNextOnCalendarClose = true;
    var calendarFirstDateSelected = false;
    $('body').on('click', function(e) {
        // if user clicks anywhere and location li is visible then close it
        if ($('.location_search_results_ul').is(':visible')
            && e.target.className != 'search_result_li'
            && e.target.id != 'hotel_location'
        ) {
            $('.location_search_results_ul').hide();
            $('#hotel_location').attr('placeholder', hotel_location_txt);
        }

        // check if user clicked outside calendar
        if ($('#daterange_value').siblings('.date-picker-wrapper').is(':visible')) {
            if (!$(e.target).closest('.form-group').find('.date-picker-wrapper').length) {
                focusNextOnCalendarClose = false;
            }
        }

        // close the occupancy block when clink anywhere in the body outside occupancy block
        if ($('#search_occupancy_wrapper').length) {
            if ($('#search_occupancy_wrapper').css('display') !== 'none') {
                if (!($(e.target).closest("#search_occupancy_wrapper").length)) {
                    // Before closing the occupancy block validate the values inside
                    return validateOccupancies();
                }
            }
        }
    });

    $(document).on('click', '#search_occupancy_wrapper .submit_occupancy_btn', function(e) {
        e.preventDefault();
        if ($('#search_occupancy_wrapper').length) {
            if ($('#search_occupancy_wrapper').css('display') !== 'none') {
                return validateOccupancies();
            }
        }
    });

    var isEnterKeyHeldOnLocation = false;
    $('body').on('keydown', function(e) {
        let preventDefault = false;

        // fix for: if user pressed Tab after selecting only first date
        if (e.which == 9
            && $('#daterange_value').siblings('.date-picker-wrapper').is(':visible')
            && calendarFirstDateSelected
        ) {
            preventDefault = true;
            $('#daterange_value').data('dateRangePicker').close();

            setTimeout(function () {
                if (is_occupancy_wise_search) {
                    BookingSearchManager.activateStep('occupancy');
                } else {
                    BookingSearchManager.activateStep('submit');
                }
            }, 10);
        }

        // if user is selecting the location by Up, Down, Enter or Tab keys
        if ((e.which == 40 || e.which == 38) && $('.location_search_results_ul li.search_result_li').is(':visible')) {
            return false;
        } else if (e.which == 13 && e.target.className == 'search_result_li') {
            // select location only after held Enter key is unheld
            preventDefault = true;
            isEnterKeyHeldOnLocation = true;
        } else if (e.which == 9 && $('.location_search_results_ul').is(':visible')) {
            preventDefault = true;
            $(e.target).click();
        }

        // check if submit button must be focused
        if (e.which == 9 && $('#search_occupancy_wrapper:visible').length) {
            preventDefault = true;
            if (validateOccupancies()) {
                BookingSearchManager.activateStep('submit');
            }
        }

        if (preventDefault) {
            e.preventDefault();
        }
    });

    // select location only after held Enter key is unheld
    $('body').on('keyup', function(e) {
        if ($(e.target).is('.location_search_results_ul li')
            && isEnterKeyHeldOnLocation
            && e.which == 13
        ) {
            $(e.target).click();
        }
    });
    // Body Events - end

    // set positions of popups when required
    if (page_name == 'index') {
        $('#hotel_location, #id_hotel_button, #guest_occupancy').focus(function () {
            setBookingSearchPositions();
        });

        // after chosen has been initialized
        $('select#id_hotel_button').on('chosen:ready', function() {
            $('#id_hotel_button_chosen .chosen-search input').focus(function () {
                setBookingSearchPositions();
            });
        });

        $('#daterange_value').click(function () {
            setBookingSearchPositions();
        });
    }
});

// function to set occupancy infor in guest occupancy field(search form)
function setGuestOccupancy()
{
    var adults = 0;
    var children = 0;
    var rooms = $('#search_occupancy_wrapper .occupancy_info_block').length;
    $( "#search_occupancy_wrapper .num_adults" ).each(function(key, val) {
        adults += parseInt($(this).val());
    });
    $( "#search_occupancy_wrapper .num_children" ).each(function(key, val) {
        children += parseInt($(this).val());
    });
    var guestButtonVal = parseInt(adults) + ' ';
    if (parseInt(adults) > 1) {
        guestButtonVal += adults_txt;
    } else {
        guestButtonVal += adult_txt;
    }
    if (parseInt(children) > 0) {
        if (parseInt(children) > 1) {
            guestButtonVal += ', ' + parseInt(children) + ' ' + children_txt;
        } else {
            guestButtonVal += ', ' + parseInt(children) + ' ' + child_txt;
        }
    }
    if (parseInt(rooms) > 1) {
        guestButtonVal += ', ' + parseInt(rooms) + ' ' + rooms_txt;
    } else {
        guestButtonVal += ', ' + parseInt(rooms) + ' ' + room_txt;
    }
    $('#guest_occupancy > span').text(guestButtonVal);
}

// position dropdowns
function setBookingSearchPositions() {
    // calculate available spaces
    let searchForm = $('#search_hotel_block_form');

    let inputFieldsAndDropdowns = [
        { input: $('#hotel_location'), dropdown: $('.location_search_results_ul')},
        { input: $('.hotel-selector-wrap'), dropdown: $('#id_hotel_button_chosen .chosen-drop')},
        { input: $('#guest_occupancy'), dropdown: $('#search_occupancy_wrapper')},
    ];

    let positionClass = 'bottom';
    if (!searchForm.closest('.fancybox-wrap').length) {
        let searchFormHeight = searchForm.outerHeight();
        let spaceTop = searchForm.offset().top - $(window).scrollTop();
        let spaceBottom = $(window).height() - searchFormHeight - spaceTop;

        // calculate max height for dropdowns
        let maxHeightNeeded = 0;
        $(inputFieldsAndDropdowns).each(function (i, inputFieldAndDropdown) {
            if (!inputFieldAndDropdown.input.length) return false;

            // find needed space height
            let cssMaxHeight = parseInt(inputFieldAndDropdown.dropdown.css('max-height'));
            if (Number.isInteger(cssMaxHeight)) {
                maxHeightNeeded = Math.max(maxHeightNeeded, cssMaxHeight);
            }
        });

        // determine position class
        if (spaceBottom < maxHeightNeeded && spaceTop > spaceBottom) {
            positionClass = 'top';
        }
    }

    // position dropdowns
    $(inputFieldsAndDropdowns).each(function (i, inputFieldAndDropdown) {
        inputFieldAndDropdown.dropdown.removeClass('top bottom').addClass(positionClass);
    });
}