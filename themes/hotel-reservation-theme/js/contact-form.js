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
//global variables
if (typeof $.uniform.defaults !== 'undefined')
{
	if (typeof contact_fileDefaultHtml !== 'undefined')
		$.uniform.defaults.fileDefaultHtml = contact_fileDefaultHtml;
	if (typeof contact_fileButtonHtml !== 'undefined')
		$.uniform.defaults.fileButtonHtml = contact_fileButtonHtml;
}

$(document).ready(function() {
    //By Webkul JS
    $(document).on('click', '.contact_type_ul li', function() {
        $('#contact_type').html($(this).html());
        $('#id_contact').val($(this).attr('value'));
    });

    $(document).on('change', 'select[name=id_contact]', function() {
        $('.desc_contact').hide();
        $('#desc_contact' + parseInt($(this).val())).show();
    });

    $(document).on('change', 'select[name=id_order]', function() {
        showProductSelect($(this).attr('value'));
    });

    showProductSelect($('select[name=id_order]').attr('value'));

    //By Webkul JS for Map locations of the hotels on google Map
    if (hotelLocationArray) {
        initMap();
    }
});

function showProductSelect(id_order) {
    $('.product_select').hide().prop('disabled', 'disabled').parent('.selector').hide();
    $('.product_select').parents('.form-group').find('label').hide();
    if ($('#' + id_order + '_order_products').length > 0) {
        $('#' + id_order + '_order_products').removeProp('disabled').show().parent('.selector').removeClass('disabled').show();
        $('.product_select').parents('.form-group').show().find('label').show();
    }
}


function initMap() {
    var map;
    var bounds = new google.maps.LatLngBounds();
    hotelLocationArray = JSON.parse(hotelLocationArray);

    // Display a map on the page
    map = new google.maps.Map(document.getElementById("map"));
    google.maps.event.trigger(map, 'resize');

    map.setTilt(45);

    // Display multiple markers on a map
    var infoWindow = new google.maps.InfoWindow();
    var marker;
    var i;

    $.each(hotelLocationArray, function(i, location) {
        var position = new google.maps.LatLng(location.latitude, location.longitude);
        bounds.extend(position);
        marker = new google.maps.Marker({
            position: position,
            map: map,
            title: location.hotel_name,
            animation: google.maps.Animation.DROP,
        });

        // Allow each marker to have an info window
        google.maps.event.addListener(marker, 'click', (function(marker, i) {
            return function() {
                var directionsLink = 'https://www.google.com/maps/dir/?api=1&destination='+
                location.latitude+','+location.longitude;
                var content = '<div><strong>'+location.hotel_name+'</strong></div>'+
                location.map_formated_address+
                '<div class="view-link"><a class="gm-btn-get-directions" href="'+
                directionsLink+'" target="_blank" tabindex="-1"><span>'+contact_map_get_dirs+'</span></a></div>';
                infoWindow.setContent(content);
                infoWindow.open(map, marker);
            }
        })(marker, i));

        // Automatically center the map fitting all markers on the screen
        map.fitBounds(bounds);
    });

    // Override our map zoom level once our fitBounds function runs (Make sure it only runs once)
    var boundsListener = google.maps.event.addListener((map), 'bounds_changed', function(event) {
        // this.setZoom(8);
        google.maps.event.removeListener(boundsListener);
    });
}
