/*
*
* NOTICE OF LICENSE
*
* This source file is subject to the Open Software License (OSL 3.0)
* that is bundled with this package in the file LICENSE.txt.
* It is also available through the world-wide-web at this URL:
* http://opensource.org/licenses/osl-3.0.php
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
*  @copyright  2007-2015 PrestaShop SA
*  @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*/

$(document).ready(function()
{
	checkTimeZone($('#infosCountry'));
	// When a country is changed
	$('#infosCountry').change(function()
	{
		checkTimeZone(this);
	});
});

function checkTimeZone(elt)
{
	var iso = $(elt).val();

	// Get timezone by iso
	$.ajax({
		url: 'index.php',
		data: 'timezoneByIso=true&iso='+iso,
		dataType: 'json',
		cache: true,
		success: function(json) {
			if (json.success) {
				$('#infosTimezone').val(json.message).trigger("liszt:updated");
				if (in_array(iso, ['br','us','ca','ru','me','au','id']))
				{
					if ($('#infosTimezone:visible').length == 0 && $('#infosTimezone_chosen').length == 0)
					{
						$('#infosTimezone:hidden').show();
						$('#timezone_div').show();
						$('#infosTimezone').chosen();
					}
					$('#timezone_div').show();
				}
				else
					$('#timezone_div').hide();
			}
		}
	});
}

function in_array(needle, haystack) {
    var length = haystack.length;
    for (var i = 0; i < length; i++) {
        if (haystack[i] == needle)
			return true;
    }
    return false;
}
