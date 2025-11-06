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

$(document).ready(function() {
    $(document).on('click', 'ul.pagination li a',  function(e){
        e.preventDefault();
        $('#pagination').val($(this).data('pagination'));
        $('form#our-properties-list').submit();
    });
});

function initMap() {
    var map;
    var bounds = new google.maps.LatLngBounds();
    hotelLocationArray = JSON.parse(hotelLocationArray);

    // Display a map on the page
    map = new google.maps.Map(document.getElementById("map"), {mapId: PS_MAP_ID});
    google.maps.event.trigger(map, 'resize');

    map.setTilt(45);

    // Display multiple markers on a map
    var infoWindow = new google.maps.InfoWindow();
    var marker;
    var i;

    $.each(hotelLocationArray, function(i, location) {
        var position = new google.maps.LatLng(location.latitude, location.longitude);
        bounds.extend(position);
        let icon = document.createElement('img');
        icon.src = PS_STORES_ICON;
        icon.style.width = '24px';
        icon.style.height = '24px';

        marker = new google.maps.marker.AdvancedMarkerElement({
            map: map,
            position: position,
            title: location.hotel_name,
            content: icon,
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
