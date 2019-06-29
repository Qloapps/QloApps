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
$(document).ready(function(){
	map = new google.maps.Map(document.getElementById('map'), {
		center: new google.maps.LatLng(defaultLat, defaultLong),
		zoom: 10,
		mapTypeId: 'roadmap',
		mapTypeControlOptions: {style: google.maps.MapTypeControlStyle.DROPDOWN_MENU}
	});
	infoWindow = new google.maps.InfoWindow();

	locationSelect = document.getElementById('locationSelect');
		locationSelect.onchange = function() {
		var markerNum = locationSelect.options[locationSelect.selectedIndex].value;
		if (markerNum !== 'none')
		google.maps.event.trigger(markers[markerNum], 'click');
	};

	$('#addressInput').keypress(function(e) {
		code = e.keyCode ? e.keyCode : e.which;
		if(code.toString() == 13)
			searchLocations();
	});

	$(document).on('click', 'input[name=location]', function(e){
		e.preventDefault();
		$(this).val('');
	});

	$(document).on('click', 'button[name=search_locations]', function(e){
		e.preventDefault();
		searchLocations();
	});

	initMarkers();
});

function initMarkers()
{
	searchUrl += '?ajax=1&all=1';
	downloadUrl(searchUrl, function(data) {
		var xml = parseXml(data.trim());
		var markerNodes = xml.documentElement.getElementsByTagName('marker');
		var bounds = new google.maps.LatLngBounds();
		for (var i = 0; i < markerNodes.length; i++)
		{
			var name = markerNodes[i].getAttribute('name');
			var address = markerNodes[i].getAttribute('address');
			var addressNoHtml = markerNodes[i].getAttribute('addressNoHtml');
			var other = markerNodes[i].getAttribute('other');
			var id_store = markerNodes[i].getAttribute('id_store');
			var has_store_picture = markerNodes[i].getAttribute('has_store_picture');
			var latlng = new google.maps.LatLng(
			parseFloat(markerNodes[i].getAttribute('lat')),
			parseFloat(markerNodes[i].getAttribute('lng')));
			createMarker(latlng, name, address, other, id_store, has_store_picture);
			bounds.extend(latlng);
		}
		map.fitBounds(bounds);
		var zoomOverride = map.getZoom();
        if(zoomOverride > 10)
        	zoomOverride = 10;
		map.setZoom(zoomOverride);
	});
}

function searchLocations()
{
	$('#stores_loader').show();
	var address = document.getElementById('addressInput').value;
	var geocoder = new google.maps.Geocoder();
	geocoder.geocode({address: address}, function(results, status) {
		if (status === google.maps.GeocoderStatus.OK)
			searchLocationsNear(results[0].geometry.location);
		else
		{
			if (!!$.prototype.fancybox && isCleanHtml(address))
			    $.fancybox.open([
			        {
			            type: 'inline',
			            autoScale: true,
			            minHeight: 30,
			            content: '<p class="fancybox-error">' + address + ' ' + translation_6 + '</p>'
			        }
			    ], {
			        padding: 0
			    });
			else
			    alert(address + ' ' + translation_6);
		}
		$('#stores_loader').hide();
	});
}

function clearLocations(n)
{
	infoWindow.close();
	for (var i = 0; i < markers.length; i++)
		markers[i].setMap(null);

	markers.length = 0;

	locationSelect.innerHTML = '';
	var option = document.createElement('option');
	option.value = 'none';
	if (!n)
		option.innerHTML = translation_1;
	else
	{
		if (n === 1)
			option.innerHTML = '1'+' '+translation_2;
		else
			option.innerHTML = n+' '+translation_3;
	}
	locationSelect.appendChild(option);

	if (!!$.prototype.uniform)
		$("select#locationSelect").uniform();

	$('#stores-table tr.node').remove();
}

function searchLocationsNear(center)
{
	var radius = document.getElementById('radiusSelect').value;
	var searchUrl = baseUri+'?controller=stores&ajax=1&latitude=' + center.lat() + '&longitude=' + center.lng() + '&radius=' + radius;
	downloadUrl(searchUrl, function(data) {
		var xml = parseXml(data.trim());
		var markerNodes = xml.documentElement.getElementsByTagName('marker');
		var bounds = new google.maps.LatLngBounds();

		clearLocations(markerNodes.length);
		$('table#stores-table').find('tbody tr').remove();
		for (var i = 0; i < markerNodes.length; i++)
		{
			var name = markerNodes[i].getAttribute('name');
			var address = markerNodes[i].getAttribute('address');
			var addressNoHtml = markerNodes[i].getAttribute('addressNoHtml');
			var other = markerNodes[i].getAttribute('other');
			var distance = parseFloat(markerNodes[i].getAttribute('distance'));
			var id_store = parseFloat(markerNodes[i].getAttribute('id_store'));
			var phone = markerNodes[i].getAttribute('phone');
			var has_store_picture = markerNodes[i].getAttribute('has_store_picture');
			var latlng = new google.maps.LatLng(
			parseFloat(markerNodes[i].getAttribute('lat')),
			parseFloat(markerNodes[i].getAttribute('lng')));

			createOption(name, distance, i);
			createMarker(latlng, name, address, other, id_store, has_store_picture);
			bounds.extend(latlng);
			address = address.replace(phone, '');

			$('table#stores-table').find('tbody').append('<tr ><td class="num">'+parseInt(i + 1)+'</td><td class="name">'+(has_store_picture == 1 ? '<img src="'+img_store_dir+parseInt(id_store)+'.jpg" alt="" />' : '')+'<span>'+name+'</span></td><td class="address">'+address+(phone !== '' ? ''+translation_4+' '+phone : '')+'</td><td class="distance">'+distance+' '+distance_unit+'</td></tr>');
			$('#stores-table').show();
		}

		if (markerNodes.length)
		{
			map.fitBounds(bounds);
			var listener = google.maps.event.addListener(map, "idle", function() {
				if (map.getZoom() > 13) map.setZoom(13);
				google.maps.event.removeListener(listener);
			});
		}
		locationSelect.style.visibility = 'visible';
		$(locationSelect).parent().parent().addClass('active').show();
		locationSelect.onchange = function() {
			var markerNum = locationSelect.options[locationSelect.selectedIndex].value;
			google.maps.event.trigger(markers[markerNum], 'click');
		};
	});
}

function createMarker(latlng, name, address, other, id_store, has_store_picture)
{
	var html = '<b>'+name+'</b><br/>'+address+(has_store_picture === 1 ? '<br /><br /><img src="'+img_store_dir+parseInt(id_store)+'.jpg" alt="" />' : '')+other+'<br /><a href="http://maps.google.com/maps?saddr=&daddr='+latlng+'" target="_blank">'+translation_5+'<\/a>';
	var image = new google.maps.MarkerImage(img_ps_dir+logo_store);
	var marker = '';

	if (hasStoreIcon)
		marker = new google.maps.Marker({ map: map, icon: image, position: latlng });
	else
		marker = new google.maps.Marker({ map: map, position: latlng });
	google.maps.event.addListener(marker, 'click', function() {
		infoWindow.setContent(html);
		infoWindow.open(map, marker);
	});
	markers.push(marker);
}

function createOption(name, distance, num)
{
	var option = document.createElement('option');
	option.value = num;
	option.innerHTML = name+' ('+distance.toFixed(1)+' '+distance_unit+')';
	locationSelect.appendChild(option);
}

function downloadUrl(url, callback)
{
	var request = window.ActiveXObject ?
	new ActiveXObject('Microsoft.XMLHTTP') :
	new XMLHttpRequest();

	request.onreadystatechange = function() {
		if (request.readyState === 4) {
			request.onreadystatechange = doNothing;
			callback(request.responseText, request.status);
		}
	};

	request.open('GET', url, true);
	request.send(null);
}

function parseXml(str)
{
	if (window.ActiveXObject)
	{
		var doc = new ActiveXObject('Microsoft.XMLDOM');
		doc.loadXML(str);
		return doc;
	}
	else if (window.DOMParser)
		return (new DOMParser()).parseFromString(str, 'text/xml');
}

function doNothing()
{
}
