$(document).ready(function() {
    //For Add Hotels
    // delete hotel image
	$('.deleteHtlImage').on('click', function(){
		var imgId = $(this).attr('id_htl_img');
		var $this = $(this);
		$.ajax({
			url: statebycountryurl,
			data: {
				id_htl_img: imgId,
				ajax: true,
				action: 'deleteHotelImage',
			},
			method: 'POST',
			success: function(data) {
				if (data == 1) {
					$this.closest('.img-container-div').remove();
					showSuccessMessage(htlImgDeleteSuccessMsg);
				} else {
					showErrorMessage(htlImgDeleteErrMsg);
				}
			},
			error: function(XMLHttpRequest, textStatus, errorThrown) {
				alert(textStatus);
			}
		});
	});

    $('#hotel_country').on('change', function() {
        $('#hotel_state').empty();
        $.ajax({
            data: {
                id_country: $(this).val(),
                ajax: true,
                action: 'StateByCountryId'
            },
            method: 'POST',
            dataType: 'JSON',
            url: statebycountryurl,
            success: function(data) {
                var html = "";
                if (data) {
                    $.each(data, function(index, value) {
                        html += "<option value=" + value.id + ">" + value.name + "</option>";
                    });
                }
                $('#hotel_state').append(html);
                if (html == '') {
                    $(".hotel_state_lbl, .hotel_state_dv").hide();
                    $(".country_import_note").show();
                } else {
                    $(".hotel_state_lbl, .hotel_state_dv").show();
                    $(".country_import_note").hide();
                }
            },
            error: function(XMLHttpRequest, textStatus, errorThrown) {
                alert(textStatus);
            }
        });
    });

    $("#check_in_time").timepicker({
        pickDate: false,
        datepicker: false,
        format: 'H:i'
    });

    $("#check_out_time").timepicker({
        pickDate: false,
        datepicker: false,
        format: 'H:i'
    });

    // For hotel Features
    function close_accordion_section() {
        $('.accordion .accordion-section-title').removeClass('active');
        $('.accordion .accordion-section-content').slideUp(300).removeClass('open');
    }

    $('.accordion-section-title').click(function(e) {
        // Grab current anchor value
        var currentAttrValue = $(this).attr('href');

        if ($(e.target).is('.active')) {
            $(this).find('span').removeClass('icon-minus');
            $(this).find('span').addClass('icon-plus');
            close_accordion_section();
        } else {
            close_accordion_section();
            // Add active class to section title
            $(this).addClass('active');
            $('.accordion-section-title').find('span').removeClass('icon-minus');
            $('.accordion-section-title').find('span').addClass('icon-plus');
            $(this).find('span').addClass('icon-minus');
            // Open up the hidden content panel
            $('.accordion ' + currentAttrValue).slideDown(300).addClass('open');
        }
        e.preventDefault();
    });

    $(".dlt-feature").on('click', function(e) {
        e.preventDefault();
        if (confirm(confirm_delete_msg)) {
            var ftr_id = $(this).attr('data-feature-id');
            $.ajax({
                url: delete_url,
                data: {
                    feature_id: ftr_id,
                    ajax: true,
                    action: 'deleteFeature',
                },
                method: 'POST',
                success: function(data) {
                    if (data == 'success') {
                        alert(success_delete_msg);
                        $('#grand_feature_div_' + ftr_id).remove();
                    } else {
                        alert(error_delete_msg);
                    }
                },
                error: function(XMLHttpRequest, textStatus, errorThrown) {
                    alert(textStatus);
                }
            });
        }
    });

    $('.add_feature_to_list').on('click', function() {
        if ($('.child_ftr_name').val() != '') {
            $("#chld_ftr_err_p").text('');

            var html = '<div class="row child_feature_row">';
                html += '<label class="col-sm-3 control-label text-right">';
                html += '</label>';
                html += '<div class="col-sm-4">';
                $.each(languages, function(key, language) {
                    html += '<input type="hidden" name="child_feature_id[]" value="0" />';
                    html += '<input type="text"';
                    html += ' value="'+$('.child_ftr_name').val()+'"';
                    html += ' name="child_features_'+language.id_lang+'[]"';
                    html += ' class="form-control wk_text_field_all wk_text_field_'+language.id_lang+'"';
                    html += ' maxlength="128"';
                    if (currentLang.id_lang != language.id_lang) {
                        html += ' style="display:none;"';
                    }
                    html += ' />';
                });
                html += '</div>';
                html += '<div class="col-sm-4">';
                    html += '<a href="#" class="remove-chld-ftr btn btn-default">';
                        html += '<i class="icon-trash"></i>';
                    html += '</a>';
                html += '</div>';
            html += '</div>';
            $('.added_child_features_container').append(html);
            $('.child_ftr_name').val('');
        } else {
            $("#chld_ftr_err_p").text(chld_ftr_text_err);
        }
    });

    $(".submit_feature").on('click', function(e) {
        $(".error_text").text('');
        if ($('.parent_ftr').val() == '') {
            $("#prnt_ftr_err_p").text(prnt_ftr_err);
            return false;
        }
        if ($('.position').val() != '' && !$.isNumeric($('.position').val())) {
            $("#pos_err_p").text(pos_numeric_err);
            return false;
        }
    });

    $('body').on('click', '.remove-chld-ftr', function(e) {
        e.preventDefault();
        $(this).parents('.child_feature_row').remove();
    });

    /* ---- Book Now page Admin ---- */
    if (typeof(booking_calendar_data) != 'undefined') {
        var calendar_data = JSON.parse(booking_calendar_data);
        $(".hotel_date").datepicker({
            defaultDate: new Date(),
            dateFormat: 'dd-mm-yy',
            minDate: 0,
            onChangeMonthYear: function(year, month) {
                if (check_calender_var)
                    $.ajax({
                        url: rooms_booking_url,
                        data: {
                            ajax: true,
                            action: 'getDataOnMonthChange',
                            month: month,
                            year: year,
                        },
                        method: 'POST',
                        async: false,
                        success: function(result) {
                            calendar_data = JSON.parse(result);
                        },
                        error: function(XMLHttpRequest, textStatus, errorThrown) {
                            alert(textStatus);
                        }
                    });
            },
            beforeShowDay: function(date) {
                var currentMonth = date.getMonth() + 1;
                var currentDate = date.getDate();
                if (currentMonth < 10) {
                    currentMonth = '0' + currentMonth;
                }
                if (currentDate < 10) {
                    currentDate = '0' + currentDate;
                }

                dmy = date.getFullYear() + "-" + currentMonth + "-" + currentDate;
                var flag = 0;

                $.each(calendar_data, function(key, value) {
                    if (key === dmy) {
                        if (value && typeof value.stats != 'undefined') {
                            msg = 'Total Available Rooms: ' + value.stats.num_avail + '&#10;Total Rooms In cart : ' + value.stats.num_cart + '&#10;Total Booked Rooms: ' + value.stats.num_booked + '&#10;Total Unvailable Rooms : ' + value.stats.num_part_avai;
                            flag = 1;
                        }
                        return 1;
                    }
                });
                if (flag) {
                    return [true, check_css_condition_var, msg];
                } else
                    return [true];
            }
        });
    } else {
        $(".hotel_date").datepicker({
            dateFormat: 'dd-mm-yy',
        });
    }

    $("#from_date").datepicker({
        showOtherMonths: true,
        dateFormat: 'dd-mm-yy',
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
        beforeShowDay: function (date) {
            return highlightDateBorder($("#to_date").val(), date);
        },
    });

    $("#hotel_id").on('change', function() {
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
                        $("#hotel_id option[value='0']").remove(); // to remove Select hotel option

                        $('#room_type').empty();
                        if (result) {
                            html = "<option value='0'>" + opt_select_all + "</option>";
                            $.each(result, function(key, value) {
                                html += "<option value='" + value.id_product + "'>" + value.room_type + "</option>";
                            });
                            $('#room_type').append(html);
                        } else {
                            html = "<option value='-1'>" + slt_another_htl + "</option>";
                            $('#room_type').append(html);
                        }
                    }
                });
            }
        }
    });

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
        var json_arr_rm_swp = JSON.parse(e.relatedTarget.dataset.avail_rm_swap);
        $.each(json_arr_rm_swp, function(key, val) {
            html += '<option class="swp_rm_opts" value="' + val.id_room + '" >' + val.room_num + '</option>';
        });
        if (html != '')
            $("#swap_avail_rooms").append(html);

        html = '';
        var json_arr_rm_realloc = JSON.parse(e.relatedTarget.dataset.avail_rm_realloc);
        $.each(json_arr_rm_realloc, function(key, val) {
            html += '<option class="realloc_rm_opts" value="' + val.id_room + '" >' + val.room_num + '</option>';
        });
        if (html != '')
            $("#realloc_avail_rooms").append(html);
    });

    $('body').on('click', '.avai_add_cart', function() {
        $current_btn = $(this);
        $current_btn.attr('disabled', 'disabled');
        var search_id_prod = $("#search_id_prod").val();
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
                search_id_prod: search_id_prod,
                search_date_from: search_date_from,
                search_date_to: search_date_to,
                opt: 1,
            },
            success: function(result) {
                if (result) {
                    if (result.rms_in_cart) {
                        $(".cart_booking_btn").removeAttr('disabled');
                        $current_btn.removeAttr('disabled');
                    }

                    btn.removeClass('btn-primary').removeClass('avai_add_cart').addClass('btn-danger').addClass('avai_delete_cart_data').html(remove);

                    btn.attr('data-id-cart', result.id_cart);
                    btn.attr('data-id-cart-book-data', result.id_cart_book_data);
                    html = "<tr>";
                    html += "<td>" + result.room_num + "</td>";
                    html += "<td>" + result.room_type + "</td>";
                    html += "<td>" + result.date_from + " To " + result.date_to + "</td>";
                    html += "<td>" + currency_prefix + result.amount + currency_suffix + "</td>";
                    html += "<td><button class='btn btn-default ajax_cart_delete_data' data-id-product='" + id_prod + "' data-id-hotel='" + id_hotel + "' data-id-cart='" + result.id_cart + "' data-id-cart-book-data='" + result.id_cart_book_data + "' data-date-from='" + date_from + "' data-date-to='" + date_to + "'><i class='icon-trash'></i></button></td>";
                    html += "</tr>";

                    $('.cart_tbody').append(html);

                    $('#cart_total_amt').html(currency_prefix + result.total_amount + currency_suffix);
                    // $('#cart_record').html(result.rms_in_cart);

                    // For Stats
                    $('#cart_record').html(result.booking_stats.stats.num_cart);
                    $("#num_avail").html(result.booking_stats.stats.num_avail);
                    $('#cart_stats').html(result.booking_stats.stats.num_cart);
                }
            }
        });
    });

    $('body').on('click', '.par_add_cart', function() {
        $current_btn = $(this);
        $current_btn.attr('disabled', 'disabled');
        var search_id_prod = $("#search_id_prod").val();
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
                search_id_prod: search_id_prod,
                search_date_from: search_date_from,
                search_date_to: search_date_to,
                opt: 1,
            },
            success: function(result) {
                if (result) {
                    if (result.rms_in_cart) {
                        $(".cart_booking_btn").removeAttr('disabled');
                        $current_btn.removeAttr('disabled');
                    }

                    btn.removeClass('btn-primary').removeClass('par_add_cart').addClass('btn-danger').addClass('part_delete_cart_data').html(remove);

                    btn.attr('data-id-cart', result.id_cart);
                    btn.attr('data-id-cart-book-data', result.id_cart_book_data);

                    html = "<tr>";
                    html += "<td>" + result.room_num + "</td>";
                    html += "<td>" + result.room_type + "</td>";
                    html += "<td>" + result.date_from + " To " + result.date_to + "</td>";
                    html += "<td>" + currency_prefix + result.amount + currency_suffix + "</td>";
                    html += "<td><button class='btn btn-default ajax_cart_delete_data' data-id-product='" + id_prod + "' data-id-hotel='" + id_hotel + "' data-id-cart='" + result.id_cart + "' data-id-cart-book-data='" + result.id_cart_book_data + "' data-date-from='" + date_from + "' data-date-to='" + date_to + "'><i class='icon-trash'></i></button></td>";
                    html += "</tr>";

                    $('.cart_tbody').append(html);

                    $('#cart_total_amt').html(currency_prefix + result.total_amount + currency_suffix);
                    // $('#cart_record').html(result.rms_in_cart);

                    // For Stats
                    $('#cart_record').html(result.booking_stats.stats.num_cart);
                    $('#cart_stats').html(result.booking_stats.stats.num_cart);
                    $("#num_part").html(result.booking_stats.stats.num_part_avai);
                }
            }
        });
    });

    $('body').on('click', '.ajax_cart_delete_data', function() {
        //for booking_data
        var search_id_prod = $("#search_id_prod").val();
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
                search_id_prod: search_id_prod,
                search_date_from: search_date_from,
                search_date_to: search_date_to,
                ajax_delete: ajax_delete,
                opt: 0,
            },
            success: function(result) {
                if (result) {
                    if (!(result.rms_in_cart)) {
                        $(".cart_booking_btn").attr('disabled', 'true');
                    }

                    btn.parent().parent().remove();
                    $('#cart_total_amt').html(currency_prefix + result.total_amount + currency_suffix);
                    // $('#cart_record').html(result.rms_in_cart);

                    // For Stats
                    $('#cart_record').html(result.booking_data.stats.num_cart);
                    $('#cart_stats').html(result.booking_data.stats.num_cart);
                    $("#num_avail").html(result.booking_data.stats.num_avail);
                    $("#num_part").html(result.booking_data.stats.num_part_avai);

                    var panel_btn = $(".tab-pane tr td button[data-id-cart-book-data='" + id_cart_book_data + "']");

                    panel_btn.attr('data-id-cart', '');
                    panel_btn.attr('data-id-cart-book-data', '');

                    if (panel_btn.hasClass('avai_delete_cart_data'))
                        panel_btn.removeClass('avai_delete_cart_data').addClass('avai_add_cart');
                    else if (panel_btn.hasClass('part_delete_cart_data'))
                        panel_btn.removeClass('part_delete_cart_data').addClass('par_add_cart');

                    panel_btn.removeClass('btn-danger').addClass('btn-primary').html(add_to_cart);

                    $("#htl_rooms_list").empty().append(result.room_tpl);
                }
            }
        });
    });

    $('body').on('click', '.avai_delete_cart_data, .part_delete_cart_data', function() {
        var search_id_prod = $("#search_id_prod").val();
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
                search_id_prod: search_id_prod,
                search_date_from: search_date_from,
                search_date_to: search_date_to,
                id_hotel: id_hotel,
                opt: 0,
            },
            success: function(result) {
                if (result) {
                    if (!(result.rms_in_cart)) {
                        $(".cart_booking_btn").attr('disabled', 'true');
                    }

                    $(".cart_tbody tr td button[data-id-cart-book-data='" + id_cart_book_data + "']").parent().parent().remove();
                    $('#cart_total_amt').html(currency_prefix + result.total_amount + currency_suffix);
                    // $('#cart_record').html(result.rms_in_cart);

                    //For Stats
                    $('#cart_record').html(result.booking_stats.stats.num_cart);
                    $('#cart_stats').html(result.booking_stats.stats.num_cart);
                    $("#num_avail").html(result.booking_stats.stats.num_avail);
                    $("#num_part").html(result.booking_stats.stats.num_part_avai);

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

    $('#search_hotel_list').on('click', function(e) {
        if ($('#from_date').val() == '') {
            alert(from_date_cond);
            return false;
        } else if ($('#to_date').val() == '') {
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

    /* ---- Book Now page Admin ---- */

    /* ----  HotelOrderRefundRulesController Admin ---- */

    $('#refund_payment_type').on('change', function() {
        if ($('#refund_payment_type').val() == 2) {
            $(".payment_type_icon").text(defaultcurrency_sign);
        } else if ($('#refund_payment_type').val() == 1) {
            $(".payment_type_icon").text('%');
        } else {
            $(".payment_type_icon").text(defaultcurrency_sign);
        }
    });

    //js for HotelOrderRefundRequestController
    $('#id_order_cancellation_stage').on('change', function() {
        if ($('#id_order_cancellation_stage').val() == 3) {
            $(".cancellation_charge_div").show();
        } else {
            $(".cancellation_charge_div").hide();
        }
    });

    /* ----  HotelOrderRefundRulesController Admin ---- */


    /* ----  HotelConfigurationSettingController Admin ---- */

    if ($('#WK_SHOW_MSG_ON_BO_on').prop('checked') === true) {
        $("#conf_id_WK_BO_MESSAGE").show();
    } else {
        $("#conf_id_WK_BO_MESSAGE").hide();
    }

    $('#WK_SHOW_MSG_ON_BO_on').click(function(e) {
        $("#conf_id_WK_BO_MESSAGE").show();
    });

    $('#WK_SHOW_MSG_ON_BO_off').click(function(e) {
        $("#conf_id_WK_BO_MESSAGE").hide();
    });

    /*For OrderRestrict Functionality*/
    $("#max_htl_book_date").datepicker({
        defaultDate: new Date(),
        dateFormat: 'dd-mm-yy',
        minDate: 0,
    });

    $("#max_global_book_date").datepicker({
        defaultDate: new Date(),
        dateFormat: 'dd-mm-yy',
        minDate: 0,
    });

    //$( "#max_global_book_date" ).datepicker( "option", "maxDate", '20 Mar 2016');
    /*END*/
    if ($('#googleMapContainer').length) {
        // Initiate Google map
        if (enabledDisplayMap) {
            var latitude = Number($("#loclatitude").val());
            var longitude = Number($("#loclongitude").val());
            var locPresent = 0;
            if (Number($("#loclatitude").val()) && Number($("#loclongitude").val())) {
                locPresent = 1;
                var formated_addr = $("#locformatedAddr").val();
            }
            initMap();
            function initMap() {
                // Google Map Variables
                var myLatLng;
                var map;
                var infowindow = new google.maps.InfoWindow();
                var geocoder = new google.maps.Geocoder();

                if (locPresent) {
                    myLatLng = {
                            lat: latitude,
                            lng: longitude
                        };
                }
                map = new google.maps.Map(document.getElementById('map'), {
                    zoom: 10
                });

                if (locPresent) {
                    // Set map center
                    map.setCenter(myLatLng);

                    // Set marker on map
                    var marker = new google.maps.Marker({
                      position: myLatLng,
                      map: map,
                      animation: google.maps.Animation.DROP,
                    });

                    // Set infor window
                    infowindow.setContent(formated_addr);
                    infowindow.open(map, marker);
                } else {
                    geocoder.geocode({
                        'address': defaultCountry
                    }, function(results, status) {
                        if (status === 'OK') {
                            map.setCenter(results[0].geometry.location);
                            myLatLng = results[0].geometry.location;
                        } else {
                            alert('Geocode was not successful for the following reason: ' + status);
                        }
                    });
                }

                var input = document.getElementById('pac-input');
                map.controls[google.maps.ControlPosition.TOP_LEFT].push(input);

                var autocomplete = new google.maps.places.Autocomplete(input);
                autocomplete.bindTo('bounds', map);

                if (!locPresent) {
                    var marker = new google.maps.Marker({
                      map: map,
                      animation: google.maps.Animation.DROP,
                      anchorPoint: new google.maps.Point(0, -29)
                    });
                }

                autocomplete.addListener('place_changed', function() {
                    infowindow.close();
                    marker.setVisible(false);
                    var place = autocomplete.getPlace();
                    if (!place.geometry) {
                        window.alert("Autocomplete's returned place contains no geometry");
                        return;
                    }

                    // If the place has a geometry, then present it on a map.
                    if (place.geometry.viewport) {
                        map.fitBounds(place.geometry.viewport);
                    } else {
                        map.setCenter(place.geometry.location);
                        map.setZoom(17);  // Why 17? Because it looks good.
                    }

                    // Marker Icons
                    // marker.setIcon(({
                    //   url: place.icon,
                    //   size: new google.maps.Size(71, 71),
                    //   origin: new google.maps.Point(0, 0),
                    //   anchor: new google.maps.Point(17, 34),
                    //   scaledSize: new google.maps.Size(35, 35)
                    // }));

                    marker.setPosition(place.geometry.location);
                    marker.setVisible(true);

                    var address = '';
                    if (place.address_components) {
                        address = [
                          (place.address_components[0] && place.address_components[0].short_name || ''),
                          (place.address_components[1] && place.address_components[1].short_name || ''),
                          (place.address_components[2] && place.address_components[2].short_name || '')
                        ].join(' ');
                    }

                    infowindow.setContent('<div><strong>' + place.name + '</strong><br>' + address);
                    infowindow.open(map, marker);

                    // Enter value in our hidden fields
                    $('#loclatitude').val(place.geometry.location.lat());
                    $('#loclongitude').val(place.geometry.location.lng());
                    $('#locformatedAddr').val('<div><strong>' + place.name + '</strong><br>' + address + '</div>');
                    $('#googleInputField').val($('#pac-input').val());
                });
            }
        }
    }


    /* ----  AdminHotelFeaturePricesSettingsController Admin ---- */

    $('#date_selection_type').on('change', function() {
        if ($('#date_selection_type').val() == 2) {
            $(".specific_date_type").show();
            $(".date_range_type").hide();
            $(".special_days_content").hide();
        } else if ($('#date_selection_type').val() == 1) {
            $(".specific_date_type").hide();
            $(".date_range_type").show();
            $(".special_days_content").show();
        } else {
            $(".specific_date_type").hide();
            $(".date_range_type").show();
            $(".special_days_content").show();
        }
    });


    $(".is_special_days_exists").on ('click', function() {
        if ($(this).is(':checked')) {
            $('.week_days').show();
        } else {
            $('.week_days').hide();
        }
    });

    $('#price_impact_type').on('change', function() {
        if ($('#price_impact_type').val() == 2) {
            $(".payment_type_icon").text(defaultcurrency_sign);
        } else if ($('#price_impact_type').val() == 1) {
            $(".payment_type_icon").text('%');
        } else {
            $(".payment_type_icon").text(defaultcurrency_sign);
        }
    });

    var ajax_pre_check_var = '';
    $('.room_type_search_results_ul').hide();

    function abortRunningAjax() {
        if (ajax_pre_check_var) {
            ajax_pre_check_var.abort();
        }
    }

    $(document).on('keyup', "#room_type_name", function(event) {
        if (($('.room_type_search_results_ul').is(':visible')) && (event.which == 40 || event.which == 38)) {
            $(this).blur();
            if (event.which == 40)
                $(".room_type_search_results_ul li:first").focus();
            else if (event.which == 38)
                $(".room_type_search_results_ul li:last").focus();
        } else {
            $('.room_type_search_results_ul').empty().hide();

            if ($(this).val() != '') {
                abortRunningAjax();
                ajax_pre_check_var = $.ajax({
                    url: autocomplete_room_search_url,
                    data: {
                        room_type_name : $(this).val(),
                        action : 'SearchProductByName',
                        ajax : true,
                    },
                    method: 'POST',
                    dataType: 'JSON',
                    success: function(data) {
                        var html = '';
                        if (data.status != 'failed') {
                            $.each(data, function(key, roomType) {
                                html += '<li data-id_product="'+roomType.id_product+'">'+roomType.name+'</li>';
                            });
                            $('.room_type_search_results_ul').html(html);
                            $('.room_type_search_results_ul').show();
                            $('.error-block').hide();
                        } else {
                            $('.error-block').show();
                        }
                    }
                });
            }
        }
    });

    $(document).on('click', '.room_type_search_results_ul li', function(event) {
        $('#room_type_name').attr('value', $(this).html());
        $('#room_type_id').val($(this).data('id_product'));

        $('.room_type_search_results_ul').empty().hide();
    });

    $("#feature_plan_date_from").datepicker({
	      showOtherMonths: true,
	      dateFormat: 'dd-mm-yy',
	      minDate: 0,
	      //for calender Css
	      beforeShowDay: function (date) {
	          return highlightDateBorder($("#feature_plan_date_from").val(), date);
	      },
	      onSelect: function(selectedDate) {
	          var date_format = selectedDate.split("-");
	          var selectedDate = new Date(date_format[2], date_format[1] - 1, date_format[0]);
	          selectedDate.setDate(selectedDate.getDate() + 1);
	          $("#feature_plan_date_to").datepicker("option", "minDate", selectedDate);
	      },
    });

    $("#specific_date").datepicker({
        showOtherMonths: true,
        dateFormat: 'dd-mm-yy',
        minDate: 0,
    });

    $("#feature_plan_date_to").datepicker({
        showOtherMonths: true,
        dateFormat: 'dd-mm-yy',
        beforeShow: function (input, instance) {
            var date_to = $('#feature_plan_date_from').val();
            if (typeof date_to != 'undefined' && date_to != '') {
                var date_format = date_to.split("-");
                var selectedDate = new Date($.datepicker.formatDate('yy-mm-dd', new Date(date_format[2], date_format[1] - 1, date_format[0])));
                selectedDate.setDate(selectedDate.getDate()+1);
                $("#feature_plan_date_to").datepicker("option", "minDate", selectedDate);
            } else {
                var date_format = new Date();
                var selectedDate = new Date($.datepicker.formatDate('yy-mm-dd', new Date()));
                selectedDate.setDate(selectedDate.getDate()+1);
                $("#feature_plan_date_to").datepicker("option", "minDate", selectedDate);
            }
        },
        //for calender Css
        beforeShowDay: function (date) {
            return highlightDateBorder($("#feature_plan_date_to").val(), date);
        },
        onSelect: function(selectedDate) {
            var date_format = selectedDate.split("-");
            var selectedDate = new Date(date_format[2], date_format[1] - 1, date_format[0]);
            selectedDate.setDate(selectedDate.getDate() - 1);
            $("#feature_plan_date_from").datepicker("option", "maxDate", selectedDate);
        }
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

    // search panel configuration
    $("input[name='WK_HOTEL_NAME_ENABLE']").on('change', function () {
        if (parseInt($(this).val())) {
            $("input[name='WK_HOTEL_LOCATION_ENABLE']").attr('disabled', false);
        } else {
            $("input[name='WK_HOTEL_LOCATION_ENABLE']").attr('disabled', 'disabled');
            $("input[name='WK_HOTEL_LOCATION_ENABLE']").attr('checked', "checked");
        }
    });

});

function showFeaturePriceRuleLangField(lang_iso_code, id_lang)
{
	$('#feature_price_rule_lang_btn').html(lang_iso_code + ' <span class="caret"></span>');
	$('.feature_price_name_all').hide();
	$('#feature_price_name_'+id_lang).show();
}


function showLangField(select_lang_name, id_lang)
{
    $('#multi_lang_btn').html(select_lang_name + ' <span class="caret"></span>');
    $('.wk_text_field_all').hide();
    $('.wk_text_field_' + id_lang).show();

    $('.all_lang_icon').attr('src', img_dir_l+id_lang+'.jpg');
    $('#choosedLangId').val(id_lang);
}

/* ----  HotelConfigurationSettingController Admin ---- */
$(function() {
    $('[data-toggle="popover"]').popover()
});