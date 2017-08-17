$(document).ready(function() {
    // for screen size changes for room search
    var window_width = $(window).width();
    if (window_width >= 767) {
        $('.fancy_search_header_xs').hide();
    }

    if ($("body").length) {
        $(window).resize(function() {
            var window_width = $(window).width();
            if (window_width >= 767) {
                $.fancybox.close();
                $('.fancy_search_header_xs').hide();
            } else {
                $('.fancy_search_header_xs').show();
            }
        });
    }
    $(function() {
        $('#xs_room_search').fancybox({
            minWidth: 200,
            autoSize: true,
            autoScale: false,
            maxWidth: '100%',
            'hideOnContentClick': false,
            'afterClose': function() {
                $('.header-rmsearch-container').show();
                $('#xs_room_search_form').show();
            },
        });
    });

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

    /*END*/
    var ajax_check_var = '';
    $('.location_search_results_ul').hide();

    $("#check_in_time").datepicker({
        showOtherMonths: true,
        dateFormat: 'dd-mm-yy',
        minDate: 0,
        //for calender Css
        beforeShowDay: function (date) {
            return highlightDateBorder($("#check_in_time").val(), date);
        },
        onSelect: function(selectedDate) {
            var date_format = selectedDate.split("-");
            var selectedDate = new Date($.datepicker.formatDate('yy-mm-dd', new Date(date_format[2], date_format[1] - 1, date_format[0])));
            selectedDate.setDate(selectedDate.getDate() + 1);
            $("#check_out_time").datepicker("option", "minDate", selectedDate);
        },
    });

    $("#check_out_time").datepicker({
        showOtherMonths: true,
        dateFormat: 'dd-mm-yy',
        //for calender Css
        beforeShowDay: function (date) {
            return highlightDateBorder($("#check_out_time").val(), date);
        },
        beforeShow: function (input, instance) {
            var date_to = $('#check_in_time').val();
            if (typeof date_to != 'undefined' && date_to != '') {
                var date_format = date_to.split("-");
                var selectedDate = new Date($.datepicker.formatDate('yy-mm-dd', new Date(date_format[2], date_format[1] - 1, date_format[0])));
                selectedDate.setDate(selectedDate.getDate()+1);
                $("#check_out_time").datepicker("option", "minDate", selectedDate);
            } else {
                var date_format = new Date();
                var selectedDate = new Date($.datepicker.formatDate('yy-mm-dd', new Date()));
                selectedDate.setDate(selectedDate.getDate()+1);
                $("#check_out_time").datepicker("option", "minDate", selectedDate);
            }
        },
        onSelect: function(selectedDate) {
            var date_format = selectedDate.split("-");
            var selectedDate = new Date($.datepicker.formatDate('yy-mm-dd', new Date(date_format[2], date_format[1] - 1, date_format[0])));
            selectedDate.setDate(selectedDate.getDate() - 1);
            $("#check_in_time").datepicker("option", "maxDate", selectedDate);
        }
    });

    if (typeof max_order_date != 'undefined' && typeof booking_date_to != 'undefined') {
        max_order_date_cal = new Date(max_order_date);
        $("#check_out_time").datepicker("option", "maxDate", new Date(max_order_date));
        max_order_date_cal.setDate(max_order_date_cal.getDate() - 1);
        $("#check_in_time").datepicker("option", "maxDate", max_order_date_cal);
    }

    function abortRunningAjax() {
        if (ajax_check_var) {
            ajax_check_var.abort();
        }
    }

    $('body').on('click', function(event) {
        if ($('.location_search_results_ul').is(':visible') && event.target.className != "search_result_li" && event.target.id != "hotel_location") {
            $('.location_search_results_ul').empty().hide();
        }
    });

    $(document).on('keyup', "#hotel_location", function(event) {
        if (($('.location_search_results_ul').is(':visible')) && (event.which == 40 || event.which == 38)) {
            $(this).blur();
            if (event.which == 40)
                $(".location_search_results_ul li:first").focus();
            else if (event.which == 38)
                $(".location_search_results_ul li:last").focus();
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
                        if (result.status == 'success') {
                            $('.location_search_results_ul').html(result.data);
                            $('.location_search_results_ul').show();
                        }
                    }
                });
            }
        }
    });

    $(document).on('click', '.location_search_results_ul li', function(event) {
        $('#hotel_location').attr('value', $(this).html());
        $('#hotel_location').attr('city_cat_id', $(this).val());

        $('.location_search_results_ul').empty().hide();

        $.ajax({
            url: autocomplete_search_url,
            data: {
                hotel_city_cat_id: $('#hotel_location').attr('city_cat_id'),
            },
            method: 'POST',
            dataType: 'json',
            success: function(result) {
                if (result.status == 'success') {
                    $('#hotel_cat_id').val('');
                    $('#hotel_cat_name').html('Select Hotel');
                    $('.hotel_dropdown_ul').empty();
                    $('.hotel_dropdown_ul').html(result.data);
                } else {
                    alert(no_results_found_cond);
                }
            }
        });
    });

    $(document).on('click', '.hotel_dropdown_ul li', function() {
        var hotel_cat_id = $(this).attr('data-hotel-cat-id');
        var id_hotel = $(this).attr('data-id-hotel');
        var hotel_name = $(this).html();

        $.ajax({
            url: autocomplete_search_url,
            data: {
                id_hotel: id_hotel,
                is_order_restrict_process: 1,
            },
            method: 'POST',
            dataType: 'json',
            success: function(result) {
                if (result.status == 'success') {
                    $("#check_in_time").datepicker("option", "maxDate", new Date(result.max_order_date));
                    $("#check_out_time").datepicker("option", "maxDate", new Date(result.max_order_date));
                    $("#max_order_date").val(result.max_order_date);
                } else {
                    alert(no_results_found_cond);
                }
            }
        });
        $('#id_hotel').val(id_hotel);
        $('#hotel_cat_id').val(hotel_cat_id);
        $('#hotel_cat_name').html(hotel_name);
    });

    $(".hotel_cat_id_btn").on("click", function() {
        if ($(this).hasClass("error_border")) {
            $(this).removeClass("error_border");
            $("#select_htl_error_p").empty();
        }
    });

    $("#check_in_time, #check_out_time").on("focus", function() {
        if ($(this).hasClass("error_border")) {
            $(this).removeClass("error_border");

            if ($(this).attr("name") == "check_in_time")
                $("#check_in_time_error_p").empty();
            else if ($(this).attr("name") == "check_out_time")
                $("#check_out_time_error_p").empty();
        }
    });

    $('#search_room_submit, #filter_search_btn').on('click', function(e) {
        var check_in_time = $("#check_in_time").val();
        var check_out_time = $("#check_out_time").val();

        var date_format_check_in = check_in_time.split("-");
        var new_chk_in = new Date($.datepicker.formatDate('yy-mm-dd', new Date(date_format_check_in[2], date_format_check_in[1] - 1, date_format_check_in[0])));
        var date_format_check_out = check_out_time.split("-");
        var new_chk_out = new Date($.datepicker.formatDate('yy-mm-dd', new Date(date_format_check_out[2], date_format_check_out[1] - 1, date_format_check_out[0])));
        var max_order_date = $("#max_order_date").val();
        var max_order_date_format = $.datepicker.formatDate('yy-mm-dd', new Date(max_order_date));
        var error = false;
        if ($('#hotel_cat_id').val() == '') {
            $(".header-rmsearch-input").addClass("error_border");
            $('#select_htl_error_p').text(hotel_name_cond);
            error = true;
        }
        if (check_in_time == '') {
            $("#check_in_time").addClass("error_border");
            $('#check_in_time_error_p').text(check_in_time_cond);
            error = true;
        } else if (new_chk_in < $.datepicker.formatDate('yy-mm-dd', new Date())) {
            $("#check_in_time").addClass("error_border");
            $('#check_in_time_error_p').text(less_checkin_date);
            error = true;
        }
        if (check_out_time == '') {
            $("#check_out_time").addClass("error_border");
            $('#check_out_time_error_p').text(check_out_time_cond);
            error = true;
        } else if (new_chk_out < new_chk_in) {
            $("#check_out_time").addClass("error_border");
            $('#check_out_time_error_p').text(more_checkout_date);
            error = true;
        } else if (max_order_date_format < new_chk_in) {
            $("#check_in_time").addClass("error_border");
            $('#check_in_time_error_p').text(max_order_date_err + ' ' + max_order_date);
            error = true;
        } else if (max_order_date_format < new_chk_out) {
            $("#check_out_time").addClass("error_border");
            $('#check_out_time_error_p').text(max_order_date_err + ' ' + max_order_date);
            error = true;
        }
        if (error)
            return false;
        else
            return true;
    });

    $(document).on('keydown', 'body', function(e) {
        if ((e.which == 40 || e.which == 38) && $('.location_search_results_ul li.search_result_li').is(':focus')) {
            e.preventDefault();
            return false;
        }
    });

    $('body').on('keyup', '.search_result_li', function(event) {
        var ul_len = $('.location_search_results_ul li').length;
        if (event.which == 40 || event.which == 38) {
            $(this).blur();
            $(this).closest('ul').scrollTop($(this).index() * $(this).outerHeight());
            if (event.which == 40) {
                if ($(this).index() != (ul_len - 1))
                    $(this).next('li.search_result_li').focus();
                else
                    $(".location_search_results_ul li:first").focus();
            } else if (event.which == 38) {
                if ($(this).index())
                    $(this).prev('li.search_result_li').focus();
                else
                    $(".location_search_results_ul li:last").focus();
            }
        } else if (event.which == 13) {
            $(this).click();
        }
    });
});