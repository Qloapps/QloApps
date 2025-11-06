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
        mapId: PS_MAP_ID
    });

    let icon = document.createElement('img');
    icon.src = PS_STORES_ICON;
    icon.style.width = '24px';
    icon.style.height = '24px';

    const marker = new google.maps.marker.AdvancedMarkerElement({
        map: map,
        position: hotelLocation,
        title: location.hotel_name,
        content: icon,
    });

    marker.query = location.query || null;
    marker.latitude = hotelLocation.lat;
    marker.longitude = hotelLocation.lng;

    marker.addListener('click', function() {
        let query = '';
        if (this.query) {
            query = this.query;
        } else if (this.latitude && this.longitude) {
            query = `${this.latitude},${this.longitude}`;
        }

        if (query) {
            window.open(`https://www.google.com/maps/search/?api=1&query=${encodeURIComponent(query)}`, '_blank');
        }
    });
}

