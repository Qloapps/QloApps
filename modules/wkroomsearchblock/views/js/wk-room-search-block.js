/**
* 2010-2020 Webkul.
*
* NOTICE OF LICENSE
*
* All right is reserved,
* Please go through this link for complete license : https://store.webkul.com/license.html
*
* DISCLAIMER
*
* Do not edit or add to this file if you wish to upgrade this module to newer
* versions in the future. If you wish to customize this module for your
* needs please refer to https://store.webkul.com/customisation-guidelines/ for more information.
*
*  @author    Webkul IN <support@webkul.com>
*  @copyright 2010-2020 Webkul IN
*  @license   https://store.webkul.com/license.html
*/

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
            padding: 0,
            autoScale: false,
            maxWidth: '100%',
            'hideOnContentClick': false,
            'afterClose': function() {
                $('.header-rmsearch-container').show();
                $('#xs_room_search_form').show();
            },
        });
    });

    /*END*/
    var ajax_check_var = '';
    $('.location_search_results_ul').hide();

    $("#check_out_time").datepicker({
        dateFormat: 'dd-mm-yy',
        dayNamesMin: [ "Sun", "Mon", "Tue", "Wed", "Thu", "Fri", "Sat"],
        //for calender Css
        beforeShowDay: function (date) {
            // get check-in date
            return highlightSelectedDateRange(date, $("#check_in_time").val(), $("#check_out_time").val());
        },
        beforeShow: function (input, instance) {
            // So that on translating page date is translated to NaN-NaN-NaN
            $('.ui-datepicker').addClass('notranslate');

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
        }
    });

    $("#check_in_time").datepicker({
        dateFormat: 'dd-mm-yy',
        minDate: 0,
        dayNamesMin: [ "Sun", "Mon", "Tue", "Wed", "Thu", "Fri", "Sat"],
        beforeShow: function (input, instance) {
            // So that on translating page date is translated to NaN-NaN-NaN
            $('.ui-datepicker').addClass('notranslate');
        },
        //for calender Css
        beforeShowDay: function (date) {
            // highlight dates of the selected date range
            return highlightSelectedDateRange(date, $("#check_in_time").val(), $("#check_out_time").val());
        },
        onClose: function() {
            // get checkout date before making any changes for the operations
            var checkOut = $("#check_out_time").val();
            var date = $("#check_in_time").val();
            var dateFormat = date.split("-");
            var selectedDate = new Date(
                $.datepicker.formatDate('yy-mm-dd', new Date(dateFormat[2], dateFormat[1] - 1, dateFormat[0]))
            );
            selectedDate.setDate(selectedDate.getDate() + 1);
            $("#check_out_time").datepicker("option", "minDate", selectedDate);

            /* open datepicker of chechout date only if
            checkout date is empty or checkin selected is equal or more than check out date */
            if (checkOut == '') {
                $("#check_out_time").datepicker( "show" );
            } else {
                // Lets make the date in the required format
                selectedDate.setDate(selectedDate.getDate() - 1);
                var currentDate = selectedDate.getDate();
                var currentMonth = selectedDate.getMonth() + 1;
                if (currentMonth < 10) {
                    currentMonth = '0' + currentMonth;
                }
                if (currentDate < 10) {
                    currentDate = '0' + currentDate;
                }

                dmy = selectedDate.getFullYear() + "-" + currentMonth + "-" + currentDate;
                checkOut = checkOut.split("-");
                checkOut = (checkOut[2]) + '-' + (checkOut[1]) + '-' + (checkOut[0]);

                if (checkOut <= dmy) {
                    $("#check_out_time").datepicker('show');
                }
            }
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

    // search location with users searched characters
    $(document).on('keyup', "#hotel_location", function(e) {
        if (($('.location_search_results_ul').is(':visible')) && (e.which == 40 || e.which == 38)) {
            $(this).blur();
            if (e.which == 40) {
                $(".location_search_results_ul li:first").focus();
            } else if (e.which == 38) {
                $(".location_search_results_ul li:last").focus();
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
                        if (result.status == 'success') {
                            $('.location_search_results_ul').html(result.data);
                            $('.location_search_results_ul').show();
                        }
                    }
                });
            }
        }
    });

    // if user clicks anywhere and location/hotel li is visible the select first li as selection
    $('body').on('click', function(e) {
        if ($('.location_search_results_ul').is(':visible') && e.target.className != "search_result_li" && e.target.id != "hotel_location") {
            $('.location_search_results_ul .search_result_li:first').click();
        }

        if ($('.hotel_dropdown_ul').is(':visible') && e.target.className != "search_result_li" && e.target.id != "hotel_location") {
            $('.hotel_dropdown_ul .search_result_li:first').click();
        }
    });

    // set data on clicking the searched location on dropdown
    $(document).on('click', '.location_search_results_ul li', function(e) {
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

    // navigate to prev and next li in the location/hotel dropdown
    $('body').on('keyup', 'li.search_result_li', function(e) {
        if (e.which == 40 || e.which == 38) {
            var ulElement = $(this).closest('ul');
            var ulLength = ulElement.find('li').length;
            $(this).blur();
            ulElement.scrollTop($(this).index() * $(this).outerHeight());
            if (e.which == 40) {
                if ($(this).index() != (ulLength - 1)) {
                    $(this).next('li.search_result_li').focus();
                } else {
                    ulElement.find("li:first").focus();
                }
            } else if (e.which == 38) {
                if ($(this).index()) {
                    $(this).prev('li.search_result_li').focus();
                } else {
                    ulElement.find("li:last").focus();
                }
            }
        }
    });

    // when focus goes from hotel button to li list of hotels
    $(document).on('keyup', "#id_hotel_button", function(e) {
        if ($('.hotel_dropdown_ul').is(':visible')) {
            if ($('.hotel_dropdown_ul .search_result_li').length) {
                $(".hotel_dropdown_ul li:first").focus();
            }
        }
    });

    // if user is selecting the location by keydown / up key
    $(document).on('keydown', 'body', function(e) {
        if ((e.which == 40 || e.which == 38) && ($('.location_search_results_ul li.search_result_li').is(':visible') || $('.hotel_dropdown_ul li.search_result_li').is(':visible'))) {
            return false;
        } else if (e.which == 13 && e.target.className == 'search_result_li') {
            e.target.click();
        } else if (e.which == 9 && $('.location_search_results_ul').is(':visible')) {
            if ($('.location_search_results_ul .search_result_li').length) {
                $('.location_search_results_ul li.search_result_li:first').click();
            }
        } else if (e.which == 9 && $('.hotel_dropdown_ul').is(':visible')) {
            if ($('.hotel_dropdown_ul .search_result_li').length) {
                $('.hotel_dropdown_ul li.search_result_li:first').click();
            }
        }
    });

    $(document).on('click', '.hotel_dropdown_ul li', function() {
        var max_order_date = $(this).attr('data-max_order_date');
        var max_date_from = new Date(max_order_date);
        max_date_from.setDate(max_date_from.getDate() - 1);
        var max_date_to = new Date(max_order_date);
        if($("#check_in_time").datepicker("getDate") > max_date_from) {
            $("#check_in_time").val('');
        }
        if($("#check_out_time").datepicker("getDate") > max_date_to) {
            $("#check_out_time").val('');
        }
        $("#check_in_time").datepicker("option", "maxDate", max_date_from);
        $("#check_out_time").datepicker("option", "maxDate", max_date_to);
        $("#max_order_date").val(max_order_date);
        $('#id_hotel').val($(this).attr('data-id-hotel'));
        $('#hotel_cat_id').val($(this).attr('data-hotel-cat-id'));
        $('#hotel_cat_name').html($(this).html());
    });

    // If only one hotel then set max order date on date pickers
    var max_order_date = $('#max_order_date').val();
    if (max_order_date != '') {
        var max_date_from = new Date(max_order_date);
        max_date_from.setDate(max_date_from.getDate() - 1);
        var max_date_to = new Date(max_order_date);
        if($("#check_in_time").datepicker("getDate") > max_date_from) {
            $("#check_in_time").val('');
        }
        if($("#check_out_time").datepicker("getDate") > max_date_to) {
            $("#check_out_time").val('');
        }
        $("#check_in_time").datepicker("option", "maxDate", max_date_from);
        $("#check_out_time").datepicker("option", "maxDate", max_date_to);
    }

    // validations on the submit of the search fields
    $('#search_room_submit').on('click', function(e) {
        var check_in_time = $("#check_in_time").val();
        var check_out_time = $("#check_out_time").val();

        var date_format_check_in = check_in_time.split("-");
        var new_chk_in = new Date($.datepicker.formatDate('yy-mm-dd', new Date(date_format_check_in[2], date_format_check_in[1] - 1, date_format_check_in[0])));
        var date_format_check_out = check_out_time.split("-");
        var new_chk_out = new Date($.datepicker.formatDate('yy-mm-dd', new Date(date_format_check_out[2], date_format_check_out[1] - 1, date_format_check_out[0])));
        var max_order_date = $("#max_order_date").val();
        var max_order_date_format = $.datepicker.formatDate('yy-mm-dd', new Date(max_order_date));
        var error = false;

        var locationCatId = $('#hotel_location').attr('city_cat_id');
        var hotelCatId = $('#hotel_cat_id').val();
        $('.header-rmsearch-input').removeClass("error_border");

        if (hotelCatId == '') {
            if (typeof(locationCatId) == 'undefined' || locationCatId == '') {
                $("#hotel_location").addClass("error_border");
                error = true;
            }
            $("#id_hotel_button").addClass("error_border");
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
});
