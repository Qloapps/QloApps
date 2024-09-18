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

function initMap() {
    const hotelLocation = {
        lat: Number(hotel_location.latitude),
        lng: Number(hotel_location.longitude),
    };

    const map = new google.maps.Map($('#search-results-wrap .map-wrap').get(0), {
        zoom: 10,
        center: hotelLocation,
        disableDefaultUI: true,
        fullscreenControl: true,
    });

    const marker = new google.maps.Marker({
        position: hotelLocation,
        map: map,
        title: hotel_name,
        icon: PS_STORES_ICON
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
    if (typeof hotel_location == 'object'
        && $('#search-results-wrap .map-wrap').length
        && typeof google == 'object'
        && typeof google.maps == 'object'
    ) {
        initMap();
    }
});
