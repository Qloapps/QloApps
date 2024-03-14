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
                    zoom: that.defaultZoom,
                    clickableIcons: true,
                });
                that.map.setCenter(that.defaultLatLng);
                if (that.defaultLatLng && that.formattedAddress) {
                    that.addMarker(that.defaultLatLng, null, that.formattedAddress);
                }
                that.setPlacesService();

                // register marker events
                that.map.addListener('click', function (e) {
                    var latLng = e.latLng;

                    var isInfoWindowNeeded = true;
                    // if it is a Place Of Interest (POI), event contains the property 'placeId'
                    if (Object.hasOwn(e, 'placeId')) {
                        isInfoWindowNeeded = false;

                        that.geocoder.geocode({ location: latLng }, function (results, status) {
                            if (status == google.maps.GeocoderStatus.OK && results[0]) {
                                var request = {
                                    placeId: e.placeId,
                                    fields: ['name'],
                                };

                                that.placesService.getDetails(request, function(place, status) {
                                    if (status === google.maps.places.PlacesServiceStatus.OK) {
                                        var content = '<div><h6>' + place.name + '</h6><p>' +
                                        results[0].formatted_address + '</p></div>';

                                        var callback = function (latLng) {
                                            that.setFormVars({
                                                lat: latLng.lat(),
                                                lng: latLng.lng(),
                                                formattedAddress: content,
                                                inputText: $('#pac-input').val(),
                                            });
                                        }

                                        that.addMarker(
                                            latLng,
                                            results[0],
                                            null,
                                            isInfoWindowNeeded,
                                            callback(latLng)
                                        );
                                    }
                                });
                            }
                        });
                    } else {
                        that.geocoder.geocode({ location: latLng }, function (results, status) {
                            if (status == google.maps.GeocoderStatus.OK && results[0]) {
                                var request = {
                                    placeId: results[0].place_id,
                                    fields: ['name'],
                                };

                                that.placesService.getDetails(request, function(place, status) {
                                    if (status === google.maps.places.PlacesServiceStatus.OK) {
                                        var content = '<div><h6>' + place.name + '</h6><p>' +
                                        results[0].formatted_address + '</p></div>';

                                        var callback = function (latLng) {
                                            that.setFormVars({
                                                lat: latLng.lat(),
                                                lng: latLng.lng(),
                                                formattedAddress: content,
                                                inputText: $('#pac-input').val(),
                                            });
                                        }

                                        that.addMarker(
                                            latLng,
                                            results[0],
                                            null,
                                            isInfoWindowNeeded,
                                            callback(latLng)
                                        );
                                    }
                                });
                            }
                        });
                    }
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

                var content = '<div><h6>' + place.name + '</h6><p>' + place.formatted_address + '</p></div>';
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
    addMarker: function(latLng, address = null, fa = null, addInfoWindow = true, cb = null) {
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

        if (addInfoWindow) {
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
                        var content = '<div><h6>' + place.name + '</h6><p>' + address.formatted_address + '</p></div>';
                        that.addInfoWindow(marker, content);

                        if(cb && typeof cb === 'function') {
                            cb();
                        }
                    }
                });
            }
        } else {
            if(cb && typeof cb === 'function') {
                cb();
            }

            return marker;
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
                maxWidth: 200,
            });

            infoWindow.open({
                anchor: marker,
                map: that.map
            });

            google.maps.event.addListener(infoWindow, 'closeclick', function () {
                that.clearAllMarkers();
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

$(document).on('click', 'button.gm-ui-hover-effect', function () {
    GoogleMapsManager.clearAllMarkers();
});

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

    // hotel status change
    $("#form-htl_branch_info a.list-action-enable.action-enabled").on('click', function(e) {
        let id_hotel = $(this).closest('tr').find('.row-selector input').val();
        if (id_hotel == primaryHotelId) {
            if (!confirm(disableHotelMsg)) {
                e.preventDefault();
                e.stopImmediatePropagation();
                return false;
            }
        }
    });
    $("form#htl_branch_info_form").on('submit', function(e) {
        let id_hotel = $(this).find('#id-hotel').val();
        let enable = $(this).find('[name="ENABLE_HOTEL"]:checked').val();
        if (id_hotel == primaryHotelId && !parseInt(enable)) {
            if (!confirm(disableHotelMsg)) {
                e.preventDefault();
                e.stopImmediatePropagation();
                return false;
            }
        }
    });

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
                if (data.status && data.states.length) {
                    $.each(data.states, function(index, value) {
                        html += "<option value=" + value.id_state + ">" + value.name + "</option>";
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

    // manage hotel page
    $('#maximum_booking_date').datepicker({
        defaultDate: new Date(),
        dateFormat: 'dd-mm-yy',
        minDate: 0,
    });

    $('input[name="enable_use_global_max_order_date"]').on('change', function () {
        if (parseInt($(this).val())) {
            $('input[name="maximum_booking_date"]').closest('.form-group').hide(200);
        } else {
            $('input[name="maximum_booking_date"]').closest('.form-group').show(200);
        }
    });

    $('input[name="enable_use_global_preparation_time"]').on('change', function () {
        if (parseInt($(this).val())) {
            $('input[name="preparation_time"]').closest('.form-group').hide(200);
        } else {
            $('input[name="preparation_time"]').closest('.form-group').show(200);
        }
    });

    initGoogleMaps();
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

    var id_old_language = id_language;
    id_language = id_lang;

    if (id_old_language != id_lang) {
        changeEmployeeLanguage();
    }
}

/* ----  HotelConfigurationSettingController Admin ---- */
$(function() {
    $('[data-toggle="popover"]').popover()
});
