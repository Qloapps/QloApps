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

var GoogleMapsManager = {
    defaultLatLng: null,
    defaultZoom: 10,
    map: null,
    markers: [],
    placeService: null,

    init: function(jQDiv) {
        this.mapDiv = jQDiv;
        this.geocoder = new google.maps.Geocoder();
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
    fetchFields: async function(request) {
        if (!request.placeId) return;
        const place = new google.maps.places.Place({
            id: request.placeId
        });

        const fieldsRequest = {
            fields: ["displayName", "formattedAddress", "location"]
        };

        try {
            await place.fetchFields(fieldsRequest);
            this.placeService = place;
            return place;
        } catch (error) {
            console.log(error);
        }
    },
    initMap: function(cb) {
        if (!this.map) {
            var that = this;
            that.setDefaultLatLng(function() {
                that.map = new google.maps.Map($(that.mapDiv).get(0), {
                    zoom: that.defaultZoom,
                    clickableIcons: true,
                    mapId: PS_MAP_ID
                });
                that.map.setCenter(that.defaultLatLng);
                if (that.defaultLatLng && that.formattedAddress) {
                    that.addMarker(that.defaultLatLng, null, that.formattedAddress);
                }
                // register marker events
                that.map.addListener('click', function (e) {
                    var latLng = e.latLng;
                    // if it is a Place Of Interest (POI), event contains the property 'placeId'
                    if (Object.hasOwn(e, 'placeId')) {
                        that.fetchFields({ placeId: e.placeId }).then(place => {
                            if (place && place.location) {
                               that.addMarker(place.location, null, place.formattedAddress);
                            }
                        });
                    } else {
                        that.geocoder.geocode({ location: latLng }, function(results, status) {
                            if (status === 'OK' && results[0]) {
                                that.addMarker(latLng, null, results[0].formatted_address);
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
            that.autocomplete = new google.maps.places.PlaceAutocompleteElement({
                locationRestriction: that.map.getBounds()
            });

            that.autocomplete.id = "place-autocomplete-input";
            input.appendChild(that.autocomplete);

            that.map.controls[google.maps.ControlPosition.TOP_LEFT].push(input);
            that.infoWindow = new google.maps.InfoWindow({});
            that.autocomplete.addEventListener("gmp-select", async (event) => {
                const place = event.placePrediction.toPlace();

                await place.fetchFields({
                    fields: ["displayName", "formattedAddress", "location", "viewport"]
                });
                // Fit the map to the place
                if (place.viewport) {
                    that.map.fitBounds(place.viewport);
                } else {
                    that.map.setCenter(place.location);
                    that.map.setZoom(18);
                }

                that.addMarker(place.location, null, place.formattedAddress);
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

        let icon = document.createElement('img');
        icon.src = PS_STORES_ICON;
        icon.style.width = '24px';
        icon.style.height = '24px';

        var marker = new google.maps.marker.AdvancedMarkerElement({
            position: latLng,
            map: that.map,
            content: icon,
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
                var content = '<div><h6>' + this.placeService.displayName + '</h6><p>' + address.formatted_address + '</p></div>';
                that.addInfoWindow(marker, content);
                if(cb && typeof cb === 'function') {
                    cb();
                }
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

            var latLng = marker.position;
            that.setFormVars({
                lat: latLng.lat,
                lng: latLng.lng,
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
        && $('#googleMapContainer').length
        && typeof google == 'object'
        && typeof google.maps == 'object'
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
                dataType: 'json',
                success: function(response) {
                    if (response.status) {
                        alert(success_delete_msg);
                        $('#grand_feature_div_' + ftr_id).remove();
                    } else {
                        alert(response.msg);
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
    toggleGoogleMapsFields();
    $('#WK_GOOGLE_ACTIVE_MAP_on').parent().on('click', function(e) {
        toggleGoogleMapsFields();
    });

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

    /* ----  AdminHotelFeaturePricesSettingsController Admin ---- */

    if ($('input[name="create_multiple"]:checked').val() == 1) {
        $('.room-type-name').hide();
        $('.room-type-name-tree').show();
        $('[name="submitAddhtl_room_type_feature_pricingAndStay"]').hide();
    } else {
        $('.room-type-name').show();
        $('[name="submitAddhtl_room_type_feature_pricingAndStay"]').show();
        $('.room-type-name-tree').hide();
    }

    $(document).on('change', 'input[name="create_multiple"]', function() {
        if ($('input[name="create_multiple"]:checked').val() == 1) {
            $('.room-type-name').hide();
            $('.room-type-name-tree').show();
            $('[name="submitAddhtl_room_type_feature_pricingAndStay"]').hide();
        } else {
            $('.room-type-name').show();
            $('[name="submitAddhtl_room_type_feature_pricingAndStay"]').show();
            $('.room-type-name-tree').hide();
        }
    });

    $(document).on('change', '.date_selection_type', function() {
        let panelIndex = $(this).closest('.advanced_price_rule').data('advanced_price_rule_index');
        if ($('#date_selection_type_'+panelIndex).val() == date_selection_types.specific.value) {
            $(".specific_date_type_"+panelIndex).show(200);
            $(".date_range_type_"+panelIndex).hide(200);
            $(".special_days_content_"+panelIndex).hide(200);
            $('.week_days_'+panelIndex).hide(200);
        } else if ($('#date_selection_type_'+panelIndex).val() == date_selection_types.range.value) {
            $(".specific_date_type_"+panelIndex).hide(200);
            $(".date_range_type_"+panelIndex).show(200);
            $(".special_days_content_"+panelIndex).show(200);
            if (parseInt($('[name="restriction['+panelIndex+'][is_special_days_exists]"]:checked').val())) {
                $('.week_days_'+panelIndex).show(200);
            }
        } else {
            $(".specific_date_type_"+panelIndex).hide(200);
            $(".date_range_type_"+panelIndex).show(200);
            $(".special_days_content_"+panelIndex).show(200);
            if (parseInt($('[name="restriction['+panelIndex+'][is_special_days_exists]"]:checked').val())) {
                $('.week_days_'+panelIndex).show(200);
            }
        }
    });


    $(document).on('change', '.is_special_days_exists', function() {
        let panelIndex = $(this).closest('.advanced_price_rule').data('advanced_price_rule_index');
        if (parseInt($('[name="restriction['+panelIndex+'][is_special_days_exists]"]:checked').val())) {
            $('.week_days_'+panelIndex).show(200);
        } else {
            $('.week_days_'+panelIndex).hide(200);
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

    const dateToday = $.datepicker.formatDate('yy-mm-dd',  new Date());
    const tomorrow = new Date();
    tomorrow.setDate(tomorrow.getDate() + 1);
    const dateTomorrow = $.datepicker.formatDate('yy-mm-dd', tomorrow);
    $(document).find('.advanced_price_rule').each(function () {
        udpateCollapseHeading($(this));
    });
    $(document).on('hide.bs.collapse', function(e) {
        if ($(e.target).hasClass('advanced_price_rule_body')) {
            let elem = $(e.target).closest('.advanced_price_rule');
            udpateCollapseHeading(elem);
            $(elem).find('.advance_price_rule_header_container').addClass('shown');
            $(elem).find('.advanced_price_rule_body .advanced_price_rule_body_actions').hide();
        }
    });

    function udpateCollapseHeading(elem) {
        let priceRuleHeadingText = '';
        let rowIndex = parseInt($(elem).data('advanced_price_rule_index'));
        let selecteDateType = $('#date_selection_type_'+rowIndex).val();
        if (selecteDateType == date_selection_types.range.value) {
            let dateFrom = $('#feature_plan_date_from_'+rowIndex).val();
            let dateTo = $('#feature_plan_date_to_'+rowIndex).val();
            priceRuleHeadingText = date_selection_types.range.title + ' ('+ dateFrom+ ' - '+ dateTo +')'
            if (parseInt($('[name="restriction['+rowIndex+'][is_special_days_exists]"]:checked').val())) {
                let special_days = [];
                $('[name="restriction['+rowIndex+'][special_days][]"]').each(function(){
                    if ($(this).prop('checked')) {
                        special_days.push($(this).parent().text().trim());
                    }
                });

                if (special_days.length != 0) {
                    priceRuleHeadingText += '<br/> <span class="special_days_heading"> ('
                    $(special_days).each(function(index, value) {
                        priceRuleHeadingText += value;
                        if (index != special_days.length-1) {
                            priceRuleHeadingText += ', ';
                        }
                    });
                    priceRuleHeadingText += ')</span>'
                }
            }
        } else if (selecteDateType == date_selection_types.specific.value) {
            priceRuleHeadingText = date_selection_types.specific.title;
            let date = $('#specific_date_'+rowIndex).val();
            priceRuleHeadingText += ' ('+ date +')'
        }

        $(elem).find('.advance_price_rule_header').html(priceRuleHeadingText);
    }

    $(document).on('show.bs.collapse', function(e) {
        if ($(e.target).hasClass('advanced_price_rule_body')) {
            $(e.target).closest('.advanced_price_rule').find('.advance_price_rule_header_container').removeClass('shown');
            $(e.target).closest('.advanced_price_rule').find('.advanced_price_rule_body .advanced_price_rule_body_actions').show();
        }
    });

    $(document).on('click', '#add_more_dates_button', function() {
        let dateSeletionOptions = $('<select>').addClass('form-control date_selection_type');
        $.each(date_selection_types, function(dateSelectionIndex, date_selection_type) {
            dateSeletionOptions.append($('<option>').attr('value', date_selection_type.value).text(date_selection_type.title))
        });

        let weekDaysOptions = $('<div>');
        $.each(week_days, function(weekDayIndex, weekDay) {
            weekDaysOptions.append($('<div>').addClass('day-wrap')
            .append($('<input>').attr({'type':'checkbox', 'value': weekDayIndex, 'name': 'special_days'}))
            .append($('<p>').text(weekDay)))
        });
        let panelIndex = parseInt($('.advanced_price_rule').last().data('advanced_price_rule_index'));
        if (isNaN(panelIndex)) {
            panelIndex = 0;
        } else {
            panelIndex++;
        }

        let panelElem = $('<div>').addClass('panel advanced_price_rule').attr('data-advanced_price_rule_index', panelIndex);
        let idElem = $('<input>').attr('type', 'hidden').attr('name', 'restriction['+panelIndex+'][id]')
        let headerElem = $('<div>').addClass('row advance_price_rule_header_container advance_price_rule_collapse').attr({'data-toggle':"collapse", 'data-target':"#advanced_price_rule_"+panelIndex})
            .append($('<div>').addClass('col-xs-9 advance_price_rule_header'))
            .append($('<div>').addClass('col-xs-3')
                .append($('<div>').addClass('col-xs-offset-7 col-xs-2')
                    .append($('<a>').addClass('btn btn-default remove_advanced_price_rule')
                        .append($('<span>').append($('<i>').addClass('icon-trash')))))
                .append($('<div>').addClass('col-xs-offset-1 col-xs-2')
                    .append($('<a>').addClass('btn btn-default')
                        .append($('<i>').addClass('icon-caret-down')))));

        let dateSelectionElem = $('<div>').addClass('form-group')
            .append($('<label>').addClass('control-label col-xs-4').attr('for', 'restriction['+panelIndex+'][date_selection_type]').text(' ' + dateSelectionTitle))
            .append($('<div>').addClass('col-xs-5')
                .append($(dateSeletionOptions).attr({'name': 'restriction['+panelIndex+'][date_selection_type]', 'id': 'date_selection_type_'+panelIndex})))
            .append($('<div>').addClass('col-xs-3 advanced_price_rule_body_actions')
                .append($('<div>').addClass('col-xs-offset-7 col-xs-2')
                    .append($('<a>').addClass('btn btn-default remove_advanced_price_rule')
                        .append($('<span>').append($('<i>').addClass('icon-trash')))))
                .append($('<div>').addClass('col-xs-offset-1 col-xs-2')
                    .append($('<a>').addClass('btn btn-default').attr({'data-toggle':"collapse", 'data-target':"#advanced_price_rule_"+panelIndex})
                        .append($('<i>').addClass('icon-caret-up')))));

        let specificDateElem = $('<div>').addClass('form-group specific_date_type_'+panelIndex).css('display', 'none')
            .append($('<label>').addClass('control-label col-xs-4 required').attr('for', 'restriction['+panelIndex+'][specific_date]').text(' ' + specificDateText))
            .append($('<div>').addClass('col-xs-5')
                .append($('<input>').addClass('specific_date form-control datepicker-input')
                    .attr({type:'text', id: 'specific_date_'+panelIndex, name: 'restriction['+panelIndex+'][specific_date]', value: dateToday, readonly: 'readonly'})));

        let dateFromElem = $('<div>').addClass('form-group date_range_type_'+panelIndex)
            .append($('<label>').addClass('control-label col-xs-4 required').attr('for', 'restriction['+panelIndex+'][date_from]').text(' ' + dateFromText))
            .append($('<div>').addClass('col-xs-5')
                .append($('<input>').addClass('feature_plan_date_from form-control  datepicker-input')
                    .attr({type:'text', id: 'feature_plan_date_from_'+panelIndex, name: 'restriction['+panelIndex+'][date_from]', value: dateToday, readonly: 'readonly'})));

        let dateToElem = $('<div>').addClass('form-group date_range_type_'+panelIndex)
            .append($('<label>').addClass('control-label col-xs-4 required').attr('for', 'restriction['+panelIndex+'][date_to]').text(' ' + dateToText))
            .append($('<div>').addClass('col-xs-5')
                .append($('<input>').addClass('feature_plan_date_to form-control  datepicker-input')
                    .attr({type:'text', id: 'feature_plan_date_to_'+panelIndex, name: 'restriction['+panelIndex+'][date_to]', value: dateTomorrow, readonly: 'readonly'})));

        let specialDaysElement = $('<div>').addClass('form-group special_days_content_'+panelIndex)
            .append($('<label>').addClass('control-label col-xs-4 required').attr('for', 'restriction['+panelIndex+'][is_special_days_exists]')
                .append($('<span>').addClass('label-tooltip').attr({'data-toggle': 'tooltip', 'data-html':'true', 'title': '', 'data-original-title': specialDaysTooltipText}).text(' '+specialDaysText)))
            .append($('<div>').addClass('col-xs-5')
                .append($('<span>').addClass('switch prestashop-switch fixed-width-lg')
                    .append($('<input>').attr({'type': 'radio', 'value': 1, 'name': 'restriction['+panelIndex+'][is_special_days_exists]', 'id': 'restriction['+panelIndex+'][is_special_days_exists_on]'}).addClass('is_special_days_exists'))
                    .append($('<label>').attr({'for': 'restriction['+panelIndex+'][is_special_days_exists_on]'}).text(yesText))
                    .append($('<input>').attr({'type': 'radio', 'value': 0, 'name': 'restriction['+panelIndex+'][is_special_days_exists]', 'id': 'restriction['+panelIndex+'][is_special_days_exists_off]', 'checked':'checked'}).addClass('is_special_days_exists'))
                    .append($('<label>').attr({'for': 'restriction['+panelIndex+'][is_special_days_exists_off]'}).text(noText))
                    .append($('<a>').addClass('slide-button btn'))))

        $(weekDaysOptions).find('input[type="checkbox"]').attr('name', 'restriction['+panelIndex+'][special_days][]');
        let specialDaysCheckBoxElem = $('<div>').addClass('form-group week_days week_days_'+panelIndex)
            .append($('<label>').addClass('control-label col-xs-4 required').attr('for', 'restriction['+panelIndex+'][special_days]').text(' ' +weekDaysText))
            .append($('<div>').addClass('col-xs-8 checkboxes-wrap').append($(weekDaysOptions).html()))

        let bodyElem = $('<div>').attr('id', 'advanced_price_rule_'+panelIndex).addClass('in advanced_price_rule_body')
            .append(dateSelectionElem)
            .append(specificDateElem)
            .append(dateFromElem)
            .append(dateToElem)
            .append(specialDaysElement)
            .append(specialDaysCheckBoxElem);

        panelElem.append(idElem).append(headerElem).append(bodyElem);
        $('#advanced_price_rule_group').append($(panelElem).prop('outerHTML'));
        $('#advanced_price_rule_group').find('.advanced_price_rule').last().find('.label-tooltip').tooltip();
        initDatePicker($('#advanced_price_rule_group').find('.advanced_price_rule').last())
    });

    $(document).on('click', '.remove_advanced_price_rule', function(){
        $(this).closest('.advanced_price_rule').remove();
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

    $('.advanced_price_rule').each(function(){
        initDatePicker($(this));
    });
    function initDatePicker(elem) {
        $(elem).find(".feature_plan_date_from").datepicker({
            showOtherMonths: true,
            dateFormat: 'yy-mm-dd',
            minDate: 0,
            onSelect: function(selectedDate) {
                let objDateToMin = $.datepicker.parseDate('yy-mm-dd', selectedDate);
                objDateToMin.setDate(objDateToMin.getDate());
                $(elem).find(".feature_plan_date_to").datepicker('option', 'minDate', objDateToMin);
            },
        });

        $(elem).find(".specific_date").datepicker({
            showOtherMonths: true,
            dateFormat: 'yy-mm-dd',
            minDate: 0,
        });

        $(elem).find(".feature_plan_date_to").datepicker({
            showOtherMonths: true,
            dateFormat: 'yy-mm-dd',
            beforeShow: function () {
                let dateFrom = $(elem).find(".feature_plan_date_from").val();
                let objDateToMin = null;
                if (typeof dateFrom != 'undefined' && dateFrom != '') {
                    objDateToMin = $.datepicker.parseDate('yy-mm-dd', dateFrom);
                } else {
                    objDateToMin = new Date();
                }

                objDateToMin.setDate(objDateToMin.getDate());
                $(elem).find(".feature_plan_date_to").datepicker('option', 'minDate', objDateToMin);
            },
            //for calender Css
            beforeShowDay: function (date) {
                return highlightDateBorder($("#feature_plan_date_to").val(), date);
            }
        });
    }

    function highlightDateBorder(elementVal, date)
    {
        if (elementVal) {
            let selectedDate = $.datepicker.formatDate('dd-mm-yy', date);
            if (selectedDate == elementVal) {
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

    $('input[name="enable_use_global_max_checkout_offset"]').on('change', function () {
        if (parseInt($(this).val())) {
            $('input[name="max_checkout_offset"]').closest('.form-group').hide(200);
        } else {
            $('input[name="max_checkout_offset"]').closest('.form-group').show(200);
        }
    });

    $('input[name="enable_use_global_min_booking_offset"]').on('change', function () {
        if (parseInt($(this).val())) {
            $('input[name="min_booking_offset"]').closest('.form-group').hide(200);
        } else {
            $('input[name="min_booking_offset"]').closest('.form-group').show(200);
        }
    });
});

function toggleGoogleMapsFields()
{
    if ($('#WK_GOOGLE_ACTIVE_MAP_on').attr('checked') == 'checked') {
        $('#conf_id_WK_MAP_HOTEL_ACTIVE_ONLY').parent().show();
        $('#conf_id_WK_DISPLAY_CONTACT_PAGE_GOOLGE_MAP').parent().show();
        $('#conf_id_WK_DISPLAY_PROPERTIES_PAGE_GOOGLE_MAP').parent().show();
    } else {
        $('#conf_id_WK_MAP_HOTEL_ACTIVE_ONLY').parent().hide();
        $('#conf_id_WK_DISPLAY_CONTACT_PAGE_GOOLGE_MAP').parent().hide();
        $('#conf_id_WK_DISPLAY_PROPERTIES_PAGE_GOOGLE_MAP').parent().hide();
    }
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
