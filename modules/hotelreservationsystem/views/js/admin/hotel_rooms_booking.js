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
    if ($('#fullcalendar').length) {
        var calendar = new FullCalendar.Calendar($('#fullcalendar').get(0), {
            initialView: 'dayGridMonth',
            initialDate: initialDate,
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
                    } else if ((info.event.extendedProps.data.stats.num_booked == info.event.extendedProps.data.stats.total_rooms) && info.event.extendedProps.data.stats.total_rooms != 0) {
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
                info.event.remove();
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
            },
            datesSet: function(arg) {
                if($('.fc-event').tooltip()) {
                    $('.fc-event').tooltip('destroy');
                }
            }
        });
        calendar.render();
    }

    function getSearchData()
    {
        return {
            search_id_room_type: $("#search_id_room_type").val(),
            search_id_hotel: $("#search_id_hotel").val(),
            search_date_from: $("#search_date_from").val(),
            search_date_to: $("#search_date_to").val(),
        }
    }

    // toggleSearchFields();
    // $('#booking_product').on('change', function() {
    //     toggleSearchFields();
    // });

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
                            if ($('#booking_product').val() == 1) {
                                showErrorMessage(noRoomTypeAvlTxt);
                            }
                            $('#id_room_type').append(html);
                        }
                    }
                });
            }
        }
    });

    function getBookingOccupancyDetails(bookingform)
    {
        let occupancy;
        if (occupancy_required_for_booking) {
            $('.booking_occupancy_wrapper').parent().removeClass('open');
            let selected_occupancy = $(bookingform).find(".occupancy_info_block.selected")
            if (selected_occupancy.length) {
                occupancy = [];
                $(selected_occupancy).each(function(ind, element) {
                    if (parseInt($(element).find('.num_adults').val())) {
                        let child_ages = [];
                        $(element).find('.guest_child_age').each(function(index) {
                            if ($(this).val() > -1) {
                                child_ages.push($(this).val());
                            }
                        });
                        if ($(element).find('.num_children').val()) {
                            if (child_ages.length != $(element).find('.num_children').val()) {
                                $(bookingform).find('.booking_occupancy_wrapper').parent().addClass('open');
                                occupancy = false;
                                return false;
                            }
                        }
                        occupancy.push({
                            'adults': $(element).find('.num_adults').val(),
                            'children': $(element).find('.num_children').val(),
                            'child_ages': child_ages
                        });
                    } else {
                        $(bookingform).find('.booking_occupancy_wrapper').parent().addClass('open');
                        occupancy = false;
                    }
                });
            } else {
                $(bookingform).find('.booking_occupancy_wrapper').parent().addClass('open');
                occupancy = false;
            }
        } else {
            return 1;
        }

        return occupancy;
    }

    // booking form
    $('body').on('click', '.avai_add_cart', function(e) {
        e.preventDefault();
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
        $(this).closest('tr').find('.booking_occupancy_wrapper').parent().removeClass('open');
        var occupancy = getBookingOccupancyDetails($(this).closest('tr').find('.booking_occupancy'));

        if (occupancy) {
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
                    occupancy: occupancy,
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
        } else {
            $current_btn.attr('disabled', false);
            setRoomTypeGuestOccupancy($(this).closest('tr').find('.booking_occupancy_wrapper'));
        }
    });

    $('body').on('click', '.par_add_cart', function(e) {
        e.preventDefault();
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
        $(this).closest('tr').find('.booking_occupancy_wrapper').parent().removeClass('open');
        var occupancy = getBookingOccupancyDetails($(this).closest('tr').find('.booking_occupancy'));

        if (occupancy) {
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
                    occupancy: occupancy,
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
        } else {
            $current_btn.attr('disabled', false);
            setRoomTypeGuestOccupancy($(this).closest('tr').find('.booking_occupancy_wrapper'));
        }
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

    $(document).on('click', '.booking_occupancy_wrapper .remove-room-link', function(e) {
        e.preventDefault();

		booking_occupancy_inner = $(this).closest('.booking_occupancy_inner');
        $(this).closest('.occupancy_info_block').remove();
		$(booking_occupancy_inner).find('.room_num_wrapper').each(function(key, val) {
            $(this).text(room_txt + ' - '+ (key+1) );
        });
        setRoomTypeGuestOccupancy($(booking_occupancy_inner).closest('.booking_occupancy_wrapper'));
    });

    $(document).on('change', '.num_occupancy', function(e) {

        let current_room_occupancy = 0;
		$(this).closest('.occupancy_info_block').find('.num_occupancy').each(function(){
            current_room_occupancy += parseInt($(this).val());
		});
        let max_guests_in_room = $(this).closest(".booking_occupancy_wrapper").find('.max_guests').val();
		let max_allowed_for_current = (max_guests_in_room - current_room_occupancy) + parseInt($(this).val());
        if ($(this).val() > $(this).attr('max')) {
            $(this).val($(this).attr('max'));
        }
        if ($(this).val() > max_allowed_for_current) {
            $(this).val(max_allowed_for_current);
        }

        if ($(this).hasClass('num_children')) {
            let totalChilds = $(this).closest('.occupancy_info_block').find('.guest_child_age').length;
            if (totalChilds < $(this).val()) {
                $(this).closest('.occupancy_info_block').find('.children_age_info_block').show();
                while ($(this).closest('.occupancy_info_block').find('.guest_child_age').length < $(this).val()) {
                    var roomBlockIndex = parseInt($(this).closest('.occupancy_info_block').attr('occ_block_index'));
                    var childAgeSelect = '<div class="form-group col-xs-12 col-sm-12 col-md-6 col-lg-6">';
                        childAgeSelect += '<select class="guest_child_age room_occupancies" name="occupancy[' +roomBlockIndex+ '][child_ages][]">';
                            childAgeSelect += '<option value="-1">' + select_age_txt + '</option>';
                            childAgeSelect += '<option value="0">' + under_1_age + '</option>';
                            for (let age = 1; age < max_child_age; age++) {
                                childAgeSelect += '<option value="'+age+'">'+age+'</option>';
                            }
                        childAgeSelect += '</select>';
                    childAgeSelect += '</div>';
                    $(this).closest('.occupancy_info_block').find('.children_ages').append(childAgeSelect);
                }
            } else {
                let child = $(this).val();
                $(this).closest('.occupancy_info_block').find('.guest_child_age').each(function(ind, element) {
                    if (child <= ind) {
                        $(element).parent().remove();
                    }
                });
                if (child == 0) {
                    $(this).closest('.occupancy_info_block').find('.children_age_info_block').hide();
                }

            }
        }

        setRoomTypeGuestOccupancy($(this).closest('.booking_occupancy_wrapper'));

    });

	$(document).on('click', '.booking_guest_occupancy', function(e) {
		$(this).parent().toggleClass('open');
    });

    $(document).on('click', function(e) {
        if ($('.booking_occupancy_wrapper:visible').length) {
			var occupancy_wrapper = $('.booking_occupancy_wrapper:visible');
			$(occupancy_wrapper).find(".occupancy_info_block").addClass('selected');
            setRoomTypeGuestOccupancy(occupancy_wrapper);

            if (!($(e.target).closest(".booking_occupancy_wrapper").length || $(e.target).closest(".booking_guest_occupancy").length || $(e.target).closest(".avai_add_cart").length || $(e.target).closest(".par_add_cart").length)) {
                let hasErrors = 0;

                let adults = $(occupancy_wrapper).find(".num_adults").map(function(){return $(this).val();}).get();
                let children = $(occupancy_wrapper).find(".num_children").map(function(){return $(this).val();}).get();
                let child_ages = $(occupancy_wrapper).find(".guest_child_age").map(function(){return $(this).val();}).get();

                // start validating above values
                if (!adults.length || (adults.length != children.length)) {
                    hasErrors = 1;
                    showErrorMessage(invalid_occupancy_txt);
                } else {
                    $(occupancy_wrapper).find('.occupancy_count').removeClass('error_border');

                    // validate values of adults and children
                    adults.forEach(function (item, index) {
                        if (isNaN(item) || parseInt(item) < 1) {
                            hasErrors = 1;
                            $(occupancy_wrapper).find(".num_adults").eq(index).closest('.occupancy_count_block').find('.occupancy_count').addClass('error_border');
                        }
                        if (isNaN(children[index])) {
                            hasErrors = 1;
                            $(occupancy_wrapper).find(".num_children").eq(index).closest('.occupancy_count_block').find('.occupancy_count').addClass('error_border');
                        }
                    });

                    // validate values of selected child ages
                    $(occupancy_wrapper).find('.guest_child_age').parent().removeClass('has-error');
                    child_ages.forEach(function (age, index) {
                        age = parseInt(age);
                        if (isNaN(age) || (age < 0) || (age >= parseInt(max_child_age))) {
                            hasErrors = 1;
                            $(occupancy_wrapper).find(".guest_child_age").eq(index).parent().addClass('has-error');
                        }
                    });
                }
                if (hasErrors == 0) {
					$(occupancy_wrapper).parent().removeClass('open');
					// $(occupancy_wrapper).siblings(".booking_guest_occupancy").parent().removeClass('has-error');

                    $(document).trigger( "QloApps:updateRoomOccupancy", [occupancy_wrapper]);
                } else {
                    // $(occupancy_wrapper).siblings(".booking_guest_occupancy").parent().addClass('has-error');
                }
			}
        }
    });

    $('.booking_occupancy_wrapper .add_new_occupancy_btn').on('click', function(e) {
        e.preventDefault();

        var booking_occupancy_wrapper = $(this).closest('.booking_occupancy_wrapper');
        var occupancy_block = '';
        var roomBlockIndex = parseInt($(booking_occupancy_wrapper).find(".occupancy_info_block").last().attr('occ_block_index'));
        roomBlockIndex += 1;


        var countRooms = parseInt($(booking_occupancy_wrapper).find('.occupancy_info_block').length);
        countRooms += 1
        occupancy_block += '<div class="occupancy_info_block col-sm-12" occ_block_index="'+roomBlockIndex+'">';
            occupancy_block += '<div class="occupancy_info_head col-sm-12"><label class="room_num_wrapper">'+ room_txt + ' - ' + countRooms + '</label><a class="remove-room-link pull-right" href="#">' + remove_txt + '</a></div>';
            occupancy_block += '<div class="col-sm-12">';
                occupancy_block += '<div class="row">';
                    occupancy_block += '<div class="form-group col-xs-6 occupancy_count_block">';
                        occupancy_block += '<label>' + adults_txt + '</label>';
                        occupancy_block += '<input type="number" class="form-control num_occupancy num_adults" name="occupancy['+roomBlockIndex+'][adults]" value="1" min="1">';
                    occupancy_block += '</div>';
                    occupancy_block += '<div class="form-group col-xs-6 occupancy_count_block">';
                        occupancy_block += '<label>' + children_txt + '<span class="label-desc-txt"></span></label>';
                        occupancy_block += '<input type="number" class="form-control num_occupancy num_children" name="occupancy['+roomBlockIndex+'][children]" value="0" min="0" max="'+max_child_in_room+'">(' + below_txt + ' ' + max_child_age + ' ' + years_txt + ')';
                    occupancy_block += '</div>';
                occupancy_block += '</div>';
                occupancy_block += '<div class="row children_age_info_block"  style="display:none">';
                    occupancy_block += '<div class="form-group col-sm-12">';
                        occupancy_block += '<label class="">' + all_children_txt + '</label>';
                        occupancy_block += '<div class="">';
                            occupancy_block += '<div class="row children_ages">';
                            occupancy_block += '</div>';
                        occupancy_block += '</div>';
                    occupancy_block += '</div>';
                occupancy_block += '</div>';
            occupancy_block += '</div>';
        occupancy_block += '</div>';
        occupancy_block += '<hr class="occupancy-info-separator col-sm-12">';

        $(booking_occupancy_wrapper).find('.booking_occupancy_inner').append(occupancy_block);

        setRoomTypeGuestOccupancy(booking_occupancy_wrapper);
    });

    function setRoomTypeGuestOccupancy(booking_occupancy_wrapper)
    {
        var adults = 0;
        var children = 0;
        var rooms = $(booking_occupancy_wrapper).find('.occupancy_info_block').length;

        $(booking_occupancy_wrapper).find(".num_adults" ).each(function(key, val) {
            adults += parseInt($(this).val());
        });
        $(booking_occupancy_wrapper).find(".num_children" ).each(function(key, val) {
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
        $(booking_occupancy_wrapper).siblings('.booking_guest_occupancy').find('span').text(guestButtonVal);
    }

    // normal products
    $('body').on('click', '.service_product_add_to_cart', function() {
        var current_btn = $(this);
        current_btn.attr('disabled', 'disabled');
        var search_id_prod = $("#search_id_prod").val();
        var id_product = $(this).data('id-product');
        var id_hotel = $(this).data('id-hotel');
        var qty = current_btn.closest('.product-container').find('.product_quantity').val();
        if (typeof(qty) == 'undefined') {
            qty = 1;
        }

        $.ajax({
            url: rooms_booking_url,
            type: 'POST',
            dataType: 'json',
            data: {
                ajax: true,
                action: 'updateProductInCart',
                id_product: id_product,
                id_hotel: id_hotel,
                qty: qty,
                search_id_prod: search_id_prod,
                opt: 'up',
            },
            success: function(result) {
                if (result.status) {
                    $(current_btn).closest('.product-info-container').find('.product_quantity').val('1');
                    refreshCartData();
                    showSuccessMessage(product_added_cart_txt)
                } else if (result.errors) {
                    showErrorMessage(result.errors);
                }
            },
            complete: function() {
                current_btn.attr('disabled', false);
            }
        });
    });

    $('body').on('click', '.service_product_delete', function() {
        var current_btn = $(this);
        current_btn.attr('disabled', 'disabled');
        var id_product = $(this).attr('data-id-product');
        var id_cart = $(this).attr('data-id-cart');
        var id_hotel = $(this).attr('data-id-hotel');


        $.ajax({
            url: rooms_booking_url,
            type: 'POST',
            dataType: 'json',
            data: {
                ajax: true,
                action: 'updateProductInCart',
                id_product: id_product,
                id_hotel: id_hotel,
                id_cart: id_cart,
                opt: 0,
            },
            success: function(result) {
                if (result) {
                    refreshCartData();
                }
            }
        });
    });

    function toggleSearchFields()
    {
        if ($('#booking_product').val() == 1) {
            $('#from_date').closest('.form-group').show('fast');
            $('#to_date').closest('.form-group').show('fast');
            $('#id_room_type').closest('.form-group').show('fast');
            $('#search_occupancy').closest('.form-group').show('fast');
        } else {
            $('#from_date').closest('.form-group').hide('fast');
            $('#to_date').closest('.form-group').hide('fast');
            $('#id_room_type').closest('.form-group').hide('fast');
            $('#search_occupancy').closest('.form-group').hide('fast');
        }
    }

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
                    $("#cart_record").html(result.total_products_in_cart);
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

    /*For swaping rooms in the modal*/
    $("#realloc_allocated_rooms").on('click', function(e) {
        $(".error_text").text('');
        if ($('#realloc_avail_rooms').val() == 0) {
            $("#realloc_sel_rm_err_p").text(slct_rm_err);
            return false;
        }
    });
    $("#swap_allocated_rooms").on('click', function(e) {
        $(".error_text").text('');
        if ($('#swap_avail_rooms').val() == 0) {
            $("#swap_sel_rm_err_p").text(slct_rm_err);
            return false;
        }
    });

    $('#mySwappigModal').on('hidden.bs.modal', function(e) {
        $(".modal_date_from").val('');
        $(".modal_date_to").val('');
        $(".modal_id_room").val('');
        $(".modal_curr_room_num").val('');
        $(".cust_name").text('');
        $(".cust_email").text('');
        $(".swp_rm_opts").remove();
        $(".realloc_rm_opts").remove();
    });

    $('#mySwappigModal').on('shown.bs.modal', function(e) {
        $(".modal_date_from").val(e.relatedTarget.dataset.date_from);
        $(".modal_date_to").val(e.relatedTarget.dataset.date_to);
        $(".modal_id_room").val(e.relatedTarget.dataset.id_room);
        $(".modal_curr_room_num").val(e.relatedTarget.dataset.room_num);
        $(".cust_name").text(e.relatedTarget.dataset.cust_name);
        $(".cust_email").text(e.relatedTarget.dataset.cust_email);
        html = '';
        if (e.relatedTarget.dataset.avail_rm_realloc) {
            var json_arr_rm_swp = JSON.parse(e.relatedTarget.dataset.avail_rm_swap);
            $.each(json_arr_rm_swp, function(key, val) {
                html += '<option class="swp_rm_opts" value="' + val.id_room + '" >' + val.room_num + '</option>';
            });
        }
        if (html != '') {
            $("#swap_avail_rooms").append(html);
        }

        html = '';
        if (e.relatedTarget.dataset.avail_rm_realloc) {
            var json_arr_rm_realloc = JSON.parse(e.relatedTarget.dataset.avail_rm_realloc);
            $.each(json_arr_rm_realloc, function(key, val) {
                html += '<option class="realloc_rm_opts" value="' + val.id_room + '" >' + val.room_num + '</option>';
            });
        }
        if (html != '') {
            $("#realloc_avail_rooms").append(html);
        }
    });
});