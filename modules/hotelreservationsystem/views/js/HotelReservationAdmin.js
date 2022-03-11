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

var GoogleMapsManager = {
    defaultLatLng: null,
    defaultZoom: 10,
    map: null,
    markers: [],
    placesService: null,

    init: function(jQDiv) {
        this.mapDiv = jQDiv;
        this.geocoder = new google.maps.Geocoder();
    },
    setPlacesService: function() {
        if (!this.placesService && this.map) {
            this.placesService = new google.maps.places.PlacesService(this.map);
        }
    },
    setDefaultLatLng: function(cb) {
        if (!this.defaultLatLng) {
            var latitude = Number($("#loclatitude").val());
            var longitude = Number($("#loclongitude").val());
            var formattedAddress = $("#locformatedAddr").val();
            var that = this;
            if (latitude && longitude) {
                that.defaultLatLng = {lat: latitude, lng: longitude};
                that.defaultZoom = 10;
                that.formattedAddress = formattedAddress;
                if(cb && typeof cb === 'function') {
                    cb();
                }
            } else {
                that.geocoder.geocode({
                    address: defaultCountry
                }, function(results, status) {
                    if (status === google.maps.GeocoderStatus.OK) {
                        that.defaultLatLng = {
                            lat: results[0].geometry.location.lat(),
                            lng: results[0].geometry.location.lng(),
                        };
                        that.defaultZoom = 4;
                        if(cb && typeof cb === 'function') {
                            cb();
                        }
                    }
                });
            }
        }
    },
    initMap: function(cb) {
        if (!this.map) {
            var that = this;
            that.setDefaultLatLng(function() {
                that.map = new google.maps.Map($(that.mapDiv).get(0), {
                    zoom: that.defaultZoom
                });
                that.map.setCenter(that.defaultLatLng);
                if (that.defaultLatLng && that.formattedAddress) {
                    that.addMarker(that.defaultLatLng, null, that.formattedAddress);
                }
                that.setPlacesService();

                // register marker events
                that.map.addListener('click', function (e) {
                    var latLng = e.latLng;
                    that.geocoder.geocode({ location: latLng }, function (results, status) {
                        if (status == google.maps.GeocoderStatus.OK && results[0]) {
                            that.addMarker(latLng, results[0]);
                        }
                    });
                });
                if(cb && typeof cb === 'function') {
                    cb();
                }
            });
        } else {
            if(cb && typeof cb === 'function') {
                cb();
            }
        }

    },
    initAutocomplete: function(jQInput, cb) {
        var that = this;
        that.initMap(function() {
            that.autocompleteInput = jQInput;
            var input = $(that.autocompleteInput).get(0);
            that.map.controls[google.maps.ControlPosition.TOP_CENTER].push(input);
            that.autocomplete = new google.maps.places.Autocomplete(input);
            that.autocomplete.bindTo('bounds', that.map);

            that.autocomplete.addListener('place_changed', function() {
                that.clearAllMarkers();
                var place = that.autocomplete.getPlace();

                if (place.geometry.viewport) {
                    that.map.fitBounds(place.geometry.viewport);
                } else {
                    that.map.setCenter(place.geometry.location);
                    that.map.setZoom(18);
                }
                var latLng = {
                    lat: place.geometry.location.lat(),
                    lng: place.geometry.location.lng(),
                };
                that.addMarker(latLng, place);

                var content = '<div><strong>' + place.name + '</strong><br>' + place.formatted_address;
                that.setFormVars({
                    lat: latLng.lat,
                    lng: latLng.lng,
                    formattedAddress: content,
                    inputText: $('#pac-input').val(),
                });
            });

            google.maps.event.addDomListener(input, 'keydown', function (e) {
                if (e.keyCode === 13) {
                    e.preventDefault();
                }
            });

            if(cb && typeof cb === 'function') {
                cb();
            }
        });
    },
    addMarker: function(latLng, address = null, fa = null, cb = null) {
        var that = this;
        that.clearAllMarkers();
        var marker = new google.maps.Marker({
            position: latLng,
            map: that.map,
            draggable: true,
        });
        that.markers.push(marker);
        marker.addListener('dragend', function(e) {
            var latLng = {
                lat: e.latLng.lat(),
                lng: e.latLng.lng(),
            }
            that.geocoder.geocode({ location: latLng }, function (results, status) {
                if (status == google.maps.GeocoderStatus.OK && results[0]) {
                    that.addMarker(latLng, results[0]);
                }
            });
        });

        if (address === null && fa) {
            // open info window
            that.addInfoWindow(marker, fa);
        } else {
            var request = {
                placeId: address.place_id,
                fields: ['name'],
            };
            that.placesService.getDetails(request, function(place, status) {
                if (status === google.maps.places.PlacesServiceStatus.OK) {
                    // open info window
                    var content = '<div><strong>' + place.name + '</strong><br>' +
                    address.formatted_address + '</div>';
                    that.addInfoWindow(marker, content);
                }
            });
        }
    },
    clearAllMarkers: function() {
        for (var i = 0; i < this.markers.length; i++) {
            this.markers[i].setMap(null);
        }
        this.markers = [];
    },
    addInfoWindow: function(marker, content) {
        if (typeof google === 'object') {
            var that = this;
            var infoWindow = new google.maps.InfoWindow({
                content: content,
            });
            infoWindow.open({
                anchor: marker,
                map: that.map
            });
            var latLng = marker.getPosition();
            that.setFormVars({
                lat: latLng.lat(),
                lng: latLng.lng(),
                formattedAddress: content,
                inputText: $('#pac-input').val(),
            });
        }
    },
    setFormVars: function(params) {
        $('#loclatitude').val(params.lat);
        $('#loclongitude').val(params.lng);
        $('#locformatedAddr').val(params.formattedAddress);
        $('#googleInputField').val(params.inputText);
    },
}

function initGoogleMaps() {
    if (typeof enabledDisplayMap != 'undefined'
        && typeof google === 'object'
        && $('#googleMapContainer').length
    ) {
        GoogleMapsManager.init($('#map'));
        GoogleMapsManager.initMap();
        GoogleMapsManager.initAutocomplete($('#pac-input'));
    }
}

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

    $("input[name='active_refund']").on('change', function () {
        if (parseInt($(this).val())) {
            $('.refund_rules_container').show();
        } else {
            $('.refund_rules_container').hide();
        }
    });

    // For hotel Features
    function close_accordion_section() {
        $('.accordion .accordion-section-title').removeClass('active');
        $('.accordion .accordion-section-content').slideUp(300).removeClass('open');
    }

    $(document).on('click', '.accordion-section-title', function(e) {
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
                        html = "<option value='0'>" + opt_select_all + "</option>";
                        if (result.length) {
                            $.each(result, function(key, value) {
                                html += "<option value='" + value.id_product + "'>" + value.room_type + "</option>";
                            });
                            $('#room_type').append(html);
                        } else {
                            showErrorMessage(noRoomTypeAvlTxt);
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

    $('body').on('click', '.avai_add_cart', function(e) {
        e.preventDefault();
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
                            html += "<td class='text-center'>" + result.room_num + "</td>";
                            html += "<td class='text-center'>" + result.room_type + "</td>";
                            html += "<td class='text-center'>" + result.date_from + " - " + result.date_to + "</td>";
                            html += "<td class='text-center'>" + result.amount + "</td>";
                            html += "<td class='text-center'><button class='btn btn-default ajax_cart_delete_data' data-id-product='" + id_prod + "' data-id-hotel='" + id_hotel + "' data-id-cart='" + result.id_cart + "' data-id-cart-book-data='" + result.id_cart_book_data + "' data-date-from='" + date_from + "' data-date-to='" + date_to + "'><i class='icon-trash'></i></button></td>";
                        html += "</tr>";

                        $('.cart_tbody').append(html);

                        $('#cart_total_amt').html(result.total_amount);
                        // $('#cart_record').html(result.rms_in_cart);

                        // For Stats
                        $('#cart_record').html(result.booking_stats.stats.num_cart);
                        $("#num_avail").html(result.booking_stats.stats.num_avail);
                        $('#cart_stats').html(result.booking_stats.stats.num_cart);
                    }
                }
            });
        } else {
            $current_btn.attr('disabled', false);
            setRoomTypeGuestOccupancy($(this).closest('tr').find('.booking_occupancy_wrapper'));
        }
    });

    function getBookingOccupancyDetails(bookingform)
    {
        let occupancy;
        if (occupancy_wise_booking) {
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
                            'adult': $(element).find('.num_adults').val(),
                            'children': $(element).find('.num_children').val(),
                            'child_ages': child_ages
                        });
                    } else {
                        $(bookingform).find('.booking_occupancy_wrapper').parent().addClass('open');
                        occupancy = false;
                        return false;
                    }
                });
            } else {
                $(bookingform).find('.booking_occupancy_wrapper').parent().addClass('open');
                occupancy = false;
            }
        } else {
            return true;
        }

        return occupancy;
    }

    $('body').on('click', '.par_add_cart', function(e) {
        e.preventDefault();
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
                        html += "<td class='text-center'>" + result.room_num + "</td>";
                        html += "<td class='text-center'>" + result.room_type + "</td>";
                        html += "<td class='text-center'>" + result.date_from + " - " + result.date_to + "</td>";
                        html += "<td class='text-center'>" + result.amount + "</td>";
                        html += "<td class='text-center'><button class='btn btn-default ajax_cart_delete_data' data-id-product='" + id_prod + "' data-id-hotel='" + id_hotel + "' data-id-cart='" + result.id_cart + "' data-id-cart-book-data='" + result.id_cart_book_data + "' data-date-from='" + date_from + "' data-date-to='" + date_to + "'><i class='icon-trash'></i></button></td>";
                        html += "</tr>";

                        $('.cart_tbody').append(html);

                        $('#cart_total_amt').html(result.total_amount);
                        // $('#cart_record').html(result.rms_in_cart);

                        // For Stats
                        $('#cart_record').html(result.booking_stats.stats.num_cart);
                        $('#cart_stats').html(result.booking_stats.stats.num_cart);
                        $("#num_part").html(result.booking_stats.stats.num_part_avai);
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

                    btn.hide(400, function () {
                        btn.closest('tr').remove();
                    });
                    $('#cart_total_amt').html(result.total_amount);
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
                    debugger;
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

    //$( "#max_global_book_date" ).datepicker( "option", "maxDate", '20 Mar 2020');
    /*END*/

    /* ----  AdminHotelFeaturePricesSettingsController Admin ---- */

    $('#date_selection_type').on('change', function() {
        if ($('#date_selection_type').val() == date_selection_types.specific) {
            $(".specific_date_type").show(200);
            $(".date_range_type").hide(200);
            $(".special_days_content").hide(200);
        } else if ($('#date_selection_type').val() == date_selection_types.range) {
            $(".specific_date_type").hide(200);
            $(".date_range_type").show(200);
            $(".special_days_content").show(200);
        } else {
            $(".specific_date_type").hide(200);
            $(".date_range_type").show(200);
            $(".special_days_content").show(200);
        }
    });


    $(".is_special_days_exists").on ('click', function() {
        if ($(this).is(':checked')) {
            $('.week_days').show(200);
        } else {
            $('.week_days').hide(200);
        }
    });

    $('#price_impact_way').on('change', function() {
        if ($('#price_impact_way').val() == impact_ways.fix) {
            $('#price_impact_type option[value="' + impact_ways.increase + '"]').attr('selected', 'selected');
            $('#price_impact_type_input').removeAttr('disabled').val(impact_ways.increase);
            $('#price_impact_type').trigger('change').attr('disabled', 'disabled');
        } else {
            $('#price_impact_type_input').attr('disabled', 'disabled');
            $('#price_impact_type').removeAttr('disabled');
        }
    });

    $('#price_impact_type').on('change', function() {
        if ($('#price_impact_type').val() == impact_types.fixed) {
            $(".payment_type_icon").text(defaultcurrency_sign);
        } else if ($('#price_impact_type').val() == impact_types.percentage) {
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

    if (parseInt($("input[name='WK_ALLOW_ADVANCED_PAYMENT']:checked").val()) == 0) {
        $("input[name='WK_ADVANCED_PAYMENT_GLOBAL_MIN_AMOUNT']").closest('.form-group').hide();
        $("input[name='WK_ADVANCED_PAYMENT_INC_TAX']").closest('.form-group').hide();
    }
    $("input[name='WK_ALLOW_ADVANCED_PAYMENT']").on('change', function () {
        if (parseInt($(this).val())) {
            $("input[name='WK_ADVANCED_PAYMENT_GLOBAL_MIN_AMOUNT']").closest('.form-group').show();
            $("input[name='WK_ADVANCED_PAYMENT_INC_TAX']").closest('.form-group').show();
        } else {
            $("input[name='WK_ADVANCED_PAYMENT_GLOBAL_MIN_AMOUNT']").closest('.form-group').hide();
            $("input[name='WK_ADVANCED_PAYMENT_INC_TAX']").closest('.form-group').hide();
        }
    });

    $("#htl_header_image").on("change", function(event) {
		if (typeof this.files[0] != 'undefined') {
			if (this.files[0].size > maxSizeAllowed) {
				showErrorMessage(filesizeError);
				$('#htl_header_image').val(null);
			}
		}
    });

    // Display datatables in lead request page
    if ($("table.wk-htl-datatable").length) {
        wkDataTable = $('table.wk-htl-datatable').DataTable({
            "order": [],
            "columnDefs": [{
                "targets": 'no-sort',
                "orderable": false,
            }],
            "language": {
                "lengthMenu": display_name + " _MENU_ " + records_name,
                "zeroRecords": no_product,
                "info": show_page + " _PAGE_ " + show_of + " _PAGES_ ",
                "infoEmpty": no_record,
                "infoFiltered": "(" + filter_from + " _MAX_ " + t_record + ")",
                "sSearch": search_item,
                "oPaginate": {
                    "sPrevious": p_page,
                    "sNext": n_page
                }
            }
        });
    }

    // ui.sorttable drag drop
    if ($("#slides").length) {
        $(function() {
            var $mySlides = $("#slides");
            $mySlides.sortable({
                cursor: "move",
                update: function() {
                    var order = $(this).sortable("serialize") + "&action=updateSlidesPosition&ajax=true&id_hotel="+
                    $('#id-hotel').val();
                    $.post(sortRowsUrl, order);
                }
            });
            $mySlides.hover(function() {
                $(this).css("cursor","move");
                },
                function() {
                $(this).css("cursor","auto");
            });
        });
    }

    initGoogleMaps();


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

                let adult = $(occupancy_wrapper).find(".num_adults").map(function(){return $(this).val();}).get();
                let children = $(occupancy_wrapper).find(".num_children").map(function(){return $(this).val();}).get();
                let child_ages = $(occupancy_wrapper).find(".guest_child_age").map(function(){return $(this).val();}).get();

                // start validating above values
                if (!adult.length || (adult.length != children.length)) {
                    hasErrors = 1;
                    showErrorMessage(invalid_occupancy_txt);
                } else {
                    $(occupancy_wrapper).find('.occupancy_count').removeClass('error_border');

                    // validate values of adult and children
                    adult.forEach(function (item, index) {
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
                        occupancy_block += '<input type="number" class="form-control num_occupancy num_adults" name="occupancy['+roomBlockIndex+'][adult]" value="1" min="1">';
                    occupancy_block += '</div>';
                    occupancy_block += '<div class="form-group col-xs-6 occupancy_count_block">';
                        occupancy_block += '<label>' + child_txt + '<span class="label-desc-txt"></span></label>';
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
});

function setRoomTypeGuestOccupancy(booking_occupancy_wrapper)
{
    var adult = 0;
    var children = 0;
	var rooms = $(booking_occupancy_wrapper).find('.occupancy_info_block').length;

	$(booking_occupancy_wrapper).find(".num_adults" ).each(function(key, val) {
        adult += parseInt($(this).val());
    });
    $(booking_occupancy_wrapper).find(".num_children" ).each(function(key, val) {
        children += parseInt($(this).val());
    });

    var guestButtonVal = parseInt(adult) + ' ';
    if (parseInt(adult) > 1) {
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
