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

function initMap() {
    const hotelLocation = {
        lat: Number(hotel_location.latitude),
        lng: Number(hotel_location.longitude),
    };

    const map = new google.maps.Map($($('#search-results-wrap .map-wrap')).get(0), {
        zoom: 10,
        center: hotelLocation,
        disableDefaultUI: true,
        fullscreenControl: true,
    });

    const marker = new google.maps.Marker({
        position: hotelLocation,
        map: map,
        title: hotel_name,
    });

    marker.addListener('click', function() {
        let query = '';
        if (hotel_location.map_input_text != '') {
            query = hotel_location.map_input_text;
        } else {
            query = hotel_location.latitude + ',' + hotel_location.longitude;
        }

        window.open('https://www.google.com/maps/search/?api=1&query='+encodeURIComponent(query), '_blank');
    });
}

$(document).ready(function() {
    if (typeof google == 'object' && typeof hotel_location == 'object') {
        initMap();
    }
});
