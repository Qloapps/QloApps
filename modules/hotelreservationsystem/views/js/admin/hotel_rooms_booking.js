/**
* 2010-2022 Webkul.
*
* NOTICE OF LICENSE
*
* All right is reserved,
* Please go through LICENSE.txt file inside our module
*
* DISCLAIMER
*
* Do not edit or add to this file if you wish to upgrade this module to newer
* versions in the future. If you wish to customize this module for your
* needs please refer to CustomizationPolicy.txt file inside our module for more information.
*
* @author Webkul IN
* @copyright 2010-2022 Webkul IN
* @license LICENSE.txt
*/

$(document).ready(function() {

    // calender
    var calendar = new FullCalendar.Calendar($('#fullcalendar').get(0), {
        initialView: 'dayGridMonth',
        events: {
            url: rooms_booking_url,
            method: 'POST',
            extraParams: function() {
                return $.extend(
                    {
                        ajax: true,
                        action: 'getCalenderData',
                    },
                    getSearchData()
                )
            },


        },
        eventContent: function(info) {
            if (info.event.extendedProps.is_notification) {
                return false;
            }
        },
        eventDidMount: function(info) {
            if (info.event.extendedProps.is_notification) {
                if (info.event.extendedProps.data.stats.num_avail > 0) {
                    $(info.el).closest('td').find('.day-info svg circle').attr('fill', '#7EC77B');
                } else if (info.event.extendedProps.data.stats.num_part_avai > 0) {
                    $(info.el).closest('td').find('.day-info svg circle').attr('fill', '#FFC224');
                } else if (info.event.extendedProps.data.stats.num_booked == info.event.extendedProps.data.stats.total_rooms) {
                    $(info.el).closest('td').find('.day-info svg circle').attr('fill', '#00AFF0');
                } else {
                    $(info.el).closest('td').find('.day-info svg circle').attr('fill', '#FF3838');
                }
                $(info.el).closest('td').find('.day-info').tooltip({
                    content: function()
                    {
                        $('#date-stats-tooltop .tip_date').text(info.event.extendedProps.data.date_format);
                        $.each(info.event.extendedProps.data.stats, function(elem, val) {
                            if (elem == 'num_part_avai') {
                                $('#date-stats-tooltop').find('.'+elem).hide().find('.tip_element_value').text('');
                            } else {
                                $('#date-stats-tooltop').find('.'+elem).show().find('.tip_element_value').text(val);
                            }
                        });
                        return $('#date-stats-tooltop').html();
                    },
                    items: "div",
                    trigger : 'hover',
                    show: {
                        delay: 100,
                    },
                    hide: {
                        delay: 300,
                    },
                    open: function(event, ui)
                    {
                        if(event.buttons == 1 || event.buttons == 3){
                            ui.tooltip.remove();
                        }

                        if (typeof(event.originalEvent) === 'undefined') {
                            return false;
                        }

                        var $id = $(ui.tooltip).attr('id');

                        // close any lingering tooltips
                        if ($('div.ui-tooltip').not('#' + $id).length) {
                            return false;
                        }

                        // ajax function to pull in data and add it to the tooltip goes here
                    },
                    close: function(event, ui)
                    {
                        ui.tooltip.hover(function() {
                            $(this).stop(true).fadeTo(300, 1);
                        },
                        function() {
                            $(this).fadeOut('300', function()
                            {
                                $(this).remove();
                            });
                        });
                    }
                });
            } else {
                $(info.el).tooltip({
                    content: function()
                    {
                        $('#date-stats-tooltop .tip_date').text(info.event.extendedProps.data.date_from_format + ' - ' +info.event.extendedProps.data.date_to_format);
                        $.each(info.event.extendedProps.data.stats, function(elem, val) {
                            if (elem == 'num_part_avai') {
                                if (val > 0) {
                                    $('#date-stats-tooltop').find('.'+elem).show().find('.tip_element_value').text(val);
                                } else {
                                    $('#date-stats-tooltop').find('.'+elem).hide().find('.tip_element_value').text('');
                                }
                            } else {
                                $('#date-stats-tooltop').find('.'+elem).find('.tip_element_value').text(val);
                            }
                        });
                        return $('#date-stats-tooltop').html();
                    },
                    items: "div",
                    trigger : 'hover',
                    show: {
                        delay: 100,
                    },
                    hide: {
                        delay: 300,
                    },
                    open: function(event, ui)
                    {
                        if(event.buttons == 1 || event.buttons == 3){
                            ui.tooltip.remove();
                        }

                        if (typeof(event.originalEvent) === 'undefined') {
                            return false;
                        }

                        var $id = $(ui.tooltip).attr('id');

                        // close any lingering tooltips
                        if ($('div.ui-tooltip').not('#' + $id).length) {
                            return false;
                        }

                        // ajax function to pull in data and add it to the tooltip goes here
                    },
                    close: function(event, ui)
                    {
                        ui.tooltip.hover(function() {
                            $(this).stop(true).fadeTo(300, 1);
                        },
                        function() {
                            $(this).fadeOut('300', function()
                            {
                                $(this).remove();
                            });
                        });
                    }
                });
            }
        },
        dayCellDidMount: (arg)  => {

            let svg = $('#svg-icon').html();
            $(arg.el).find('.fc-daygrid-day-top').append('<a class="day-info">'+svg+'</a>');
        }

    });
    calendar.render();

    function getSearchData()
    {
        return {
            search_id_room_type: $("#search_id_room_type").val(),
            search_id_hotel: $("#search_id_hotel").val(),
            search_date_from: $("#search_date_from").val(),
            search_date_to: $("#search_date_to").val(),
        }
    }

    // search form changes
    $('#search_hotel_list').on('click', function(e) {
        if ($('#date_from').val() == '') {
            alert(from_date_cond);
            return false;
        } else if ($('#date_to').val() == '') {
            alert(to_date_cond);
            return false;
        } else if ($('#hotel-id').val() == '') {
            alert(hotel_name_cond);
            return false;
        } else if ($('#num-rooms').val() == '') {
            alert(num_rooms_cond);
            return false;
        }
    });

    $("#from_date").datepicker({
        showOtherMonths: true,
        dateFormat: 'dd-mm-yy',
        altFormat: 'yy-mm-dd',
        altField: '#date_from',
        beforeShowDay: function (date) {
            return highlightDateBorder($("#from_date").val(), date);
        },
        onSelect: function(selectedDate) {
            var date_format = selectedDate.split("-");
            var selectedDate = new Date($.datepicker.formatDate('yy-mm-dd', new Date(date_format[2], date_format[1] - 1, date_format[0])));
            selectedDate.setDate(selectedDate.getDate() + 1);
            $("#to_date").datepicker("option", "minDate", selectedDate);
        },
    });

    $("#to_date").datepicker({
        showOtherMonths: true,
        dateFormat: 'dd-mm-yy',
        altFormat: 'yy-mm-dd',
        altField: '#date_to',
        beforeShowDay: function (date) {
            return highlightDateBorder($("#to_date").val(), date);
        },
    });

    $("#id_hotel").on('change', function() {
        var hotel_id = $(this).val();
        if (!isNaN(hotel_id)) {
            if (hotel_id > 0) {
                $.ajax({
                    url: rooms_booking_url,
                    type: 'POST',
                    dataType: 'json',
                    data: {
                        ajax: true,
                        action: 'getRoomType',
                        hotel_id: hotel_id,
                    },
                    success: function(result) {
                        $("#id_hotel option[value='0']").remove(); // to remove Select hotel option
                        $('#id_room_type').empty();
                        html = "<option value='0'>" + opt_select_all + "</option>";
                        if (result.length) {
                            $.each(result, function(key, value) {
                                html += "<option value='" + value.id_product + "'>" + value.room_type + "</option>";
                            });
                            $('#id_room_type').append(html);
                        } else {
                            showErrorMessage(noRoomTypeAvlTxt);
                            $('#id_room_type').append(html);
                        }
                    }
                });
            }
        }
    });

    // booking form
    $('body').on('click', '.avai_add_cart', function() {
        $current_btn = $(this);
        $current_btn.attr('disabled', 'disabled');
        var search_id_room_type = $("#search_id_room_type").val();
        var search_id_hotel = $("#search_id_hotel").val();
        var search_date_from = $("#search_date_from").val();
        var search_date_to = $("#search_date_to").val();

        var id_prod = $(this).attr('data-id-product');
        var id_room = $(this).attr('data-id-room');
        var id_hotel = $(this).attr('data-id-hotel');
        var date_from = $(this).attr('data-date-from');
        var date_to = $(this).attr('data-date-to');
        var booking_type = $("input[name='bk_type_" + id_room + "']:checked").val();
        var comment = $("#comment_" + id_room).val();
        var btn = $(this);

        $.ajax({
            url: rooms_booking_url,
            type: 'POST',
            dataType: 'json',
            data: {
                ajax: true,
                action: 'addDataToCart',
                id_prod: id_prod,
                id_room: id_room,
                id_hotel: id_hotel,
                date_from: date_from,
                date_to: date_to,
                booking_type: booking_type,
                comment: comment,
                search_id_hotel: search_id_hotel,
                search_id_room_type: search_id_room_type,
                search_date_from: search_date_from,
                search_date_to: search_date_to,
                opt: 1,
            },
            success: function(result) {
                if (result) {
                    if (result.success) {
                        $(".cart_booking_btn").removeAttr('disabled');
                        $current_btn.removeAttr('disabled');
                    }

                    btn.removeClass('btn-primary').removeClass('avai_add_cart').addClass('btn-danger').addClass('avai_delete_cart_data').html(remove);

                    btn.attr('data-id-cart', result.data.id_cart);
                    btn.attr('data-id-cart-book-data', result.data.id_cart_book_data);
                    refreshCartData();
                    refreshStatsData();
                    calendar.refetchEvents();
                }
            }
        });
    });

    $('body').on('click', '.par_add_cart', function() {
        $current_btn = $(this);
        $current_btn.attr('disabled', 'disabled');
        var search_id_room_type = $("#search_id_room_type").val();
        var search_id_hotel = $("#search_id_hotel").val();
        var search_date_from = $("#search_date_from").val();
        var search_date_to = $("#search_date_to").val();

        var id_prod = $(this).attr('data-id-product');
        var id_room = $(this).attr('data-id-room');
        var id_hotel = $(this).attr('data-id-hotel');
        var date_from = $(this).attr('data-date-from');
        var date_to = $(this).attr('data-date-to');

        var sub_key = $(this).attr('data-sub-key');
        var booking_type = $("input[name='bk_type_" + id_room + "_" + sub_key + "']:checked").val();
        var comment = $("#comment_" + id_room + "_" + sub_key).val();
        var btn = $(this);

        $.ajax({
            url: rooms_booking_url,
            type: 'POST',
            dataType: 'json',
            data: {
                ajax: true,
                action: 'addDataToCart',
                id_prod: id_prod,
                id_room: id_room,
                id_hotel: id_hotel,
                date_from: date_from,
                date_to: date_to,
                booking_type: booking_type,
                comment: comment,
                search_id_hotel: search_id_hotel,
                search_id_room_type: search_id_room_type,
                search_date_from: search_date_from,
                search_date_to: search_date_to,
                opt: 1,
            },
            success: function(result) {
                if (result) {
                    if (result.success) {
                        $(".cart_booking_btn").removeAttr('disabled');
                        $current_btn.removeAttr('disabled');
                    }

                    btn.removeClass('btn-primary').removeClass('par_add_cart').addClass('btn-danger').addClass('part_delete_cart_data').html(remove);

                    btn.attr('data-id-cart', result.data.id_cart);
                    btn.attr('data-id-cart-book-data', result.data.id_cart_book_data);
                    refreshCartData();
                    refreshStatsData();
                    calendar.refetchEvents();
                }
            }
        });
    });

    $('body').on('click', '.ajax_cart_delete_data', function() {
        //for booking_data
        var search_id_room_type = $("#search_id_room_type").val();
        var search_id_hotel = $("#search_id_hotel").val();
        var search_date_from = $("#search_date_from").val();
        var search_date_to = $("#search_date_to").val();

        var ajax_delete = 1;
        var id_product = $(this).attr('data-id-product');
        var id_cart = $(this).attr('data-id-cart');
        var id_cart_book_data = $(this).attr('data-id-cart-book-data');
        var date_from = $(this).attr('data-date-from');
        var date_to = $(this).attr('data-date-to');
        var id_hotel = $(this).attr('data-id-hotel');
        var btn = $(this);

        $.ajax({
            url: rooms_booking_url,
            type: 'POST',
            dataType: 'json',
            async: false,
            data: {
                ajax: true,
                action: 'addDataToCart',
                id_prod: id_product,
                id_cart: id_cart,
                id_cart_book_data: id_cart_book_data,
                date_from: date_from,
                date_to: date_to,
                id_hotel: id_hotel,
                search_id_hotel: search_id_hotel,
                search_id_room_type: search_id_room_type,
                search_date_from: search_date_from,
                search_date_to: search_date_to,
                ajax_delete: ajax_delete,
                opt: 0,
            },
            success: function(result) {
                if (result) {
                    if (!(result.success)) {
                        $(".cart_booking_btn").attr('disabled', 'true');
                    }
                    $("#htl_rooms_list").empty().append(result.data.room_tpl);
                    refreshCartData();
                    refreshStatsData();
                    calendar.refetchEvents();
                    initBookingList();

                    var panel_btn = $(".tab-pane tr td button[data-id-cart-book-data='" + id_cart_book_data + "']");

                    panel_btn.attr('data-id-cart', '');
                    panel_btn.attr('data-id-cart-book-data', '');

                    if (panel_btn.hasClass('avai_delete_cart_data'))
                        panel_btn.removeClass('avai_delete_cart_data').addClass('avai_add_cart');
                    else if (panel_btn.hasClass('part_delete_cart_data'))
                        panel_btn.removeClass('part_delete_cart_data').addClass('par_add_cart');

                    panel_btn.removeClass('btn-danger').addClass('btn-primary').html(add_to_cart);

                }
            }
        });
    });

    $('body').on('click', '.avai_delete_cart_data, .part_delete_cart_data', function() {
        var search_id_room_type = $("#search_id_room_type").val();
        var search_id_hotel = $("#search_id_hotel").val();
        var search_date_from = $("#search_date_from").val();
        var search_date_to = $("#search_date_to").val();

        var id_product = $(this).attr('data-id-product');
        var id_cart = $(this).attr('data-id-cart');
        var id_cart_book_data = $(this).attr('data-id-cart-book-data');
        var date_from = $(this).attr('data-date-from');
        var date_to = $(this).attr('data-date-to');
        var id_hotel = $(this).attr('data-id-hotel');
        var btn = $(this);

        $.ajax({
            url: rooms_booking_url,
            type: 'POST',
            dataType: 'json',
            data: {
                ajax: true,
                action: 'addDataToCart',
                id_prod: id_product,
                id_cart: id_cart,
                id_cart_book_data: id_cart_book_data,
                date_from: date_from,
                date_to: date_to,
                search_id_hotel: search_id_hotel,
                search_id_room_type: search_id_room_type,
                search_date_from: search_date_from,
                search_date_to: search_date_to,
                id_hotel: id_hotel,
                opt: 0,
            },
            success: function(result) {
                if (result) {
                    if (!(result.success)) {
                        $(".cart_booking_btn").attr('disabled', 'true');
                    }

                    $(".cart_tbody tr td button[data-id-cart-book-data='" + id_cart_book_data + "']").parent().parent().remove();
                    refreshCartData();
                    refreshStatsData();
                    calendar.refetchEvents();

                    btn.attr('data-id-cart', '');
                    btn.attr('data-id-cart-book-data', '');

                    if (btn.hasClass('avai_delete_cart_data'))
                        btn.removeClass('avai_delete_cart_data').addClass('avai_add_cart');
                    else if (btn.hasClass('part_delete_cart_data'))
                        btn.removeClass('part_delete_cart_data').addClass('par_add_cart');

                    btn.removeClass('btn-danger').addClass('btn-primary').html(add_to_cart);
                }
            }
        });
    });

    function refreshCartData()
    {
        $.ajax({
            url: rooms_booking_url,
            type: 'POST',
            dataType: 'JSON',
            data: {
                ajax: true,
                action: 'updateCartData',
            },
            success: function(result) {
                if (result) {
                    if (result.cart_content) {
                        $("#cartModal").html(result.cart_content);
                    }
                    $("#cart_record").html(result.rms_in_cart);
                }
            }
        });
    }

    function refreshStatsData()
    {
        var search_id_room_type = $("#search_id_room_type").val();
        var search_id_hotel = $("#search_id_hotel").val();
        var search_date_from = $("#search_date_from").val();
        var search_date_to = $("#search_date_to").val();

        $.ajax({
            url: rooms_booking_url,
            type: 'POST',
            dataType: 'JSON',
            data: {
                ajax: true,
                action: 'getBookingStats',
                search_id_room_type: search_id_room_type,
                search_id_hotel: search_id_hotel,
                search_date_from: search_date_from,
                search_date_to: search_date_to,
            },
            success: function(result) {
                if (result.success) {
                    $(".htl_room_data_cont").html(result.data.stats_panel);
                }
            }
        });
    }

    var allotmentTypes = {
		auto: ALLOTMENT_AUTO,
		manual: ALLOTMENT_MANUAL,
	};
    initBookingList();
    function initBookingList() {
        $('.avai_comment, .par_comment').hide();
        $('.avai_bk_type').on('change', function() {
            var id_room = $(this).attr('data-id-room');
            var booking_type = $(this).val();

            if (booking_type == allotmentTypes.auto) {
                $('#comment_'+id_room).hide().val('');
            } else if (booking_type == allotmentTypes.manual) {
                $('#comment_'+id_room).show();
            }
        });

        $('.par_bk_type').on('change', function() {
            var id_room = $(this).attr('data-id-room');
            var sub_key = $(this).attr('data-sub-key');
            var booking_type = $(this).val();

            if (booking_type == allotmentTypes.auto) {
                $('#comment_'+id_room+'_'+sub_key).hide().val('');
            } else if (booking_type == allotmentTypes.manual) {
                $('#comment_'+id_room+'_'+sub_key).show();
            }
        });
    }

    function highlightDateBorder(elementVal, date)
    {
        if (elementVal) {
            var currentDate = date.getDate();
            var currentMonth = date.getMonth()+1;
            if (currentMonth < 10) {
                currentMonth = '0' + currentMonth;
            }
            if (currentDate < 10) {
                currentDate = '0' + currentDate;
            }
            dmy = date.getFullYear() + "-" + currentMonth + "-" + currentDate;
            var date_format = elementVal.split("-");
            var check_in_time = (date_format[2]) + '-' + (date_format[1]) + '-' + (date_format[0]);
            if (dmy == check_in_time) {
                return [true, "selectedCheckedDate", "Check-In date"];
            } else {
                return [true, ""];
            }
        } else {
            return [true, ""];
        }
    }
});