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
*  @copyright  2007-2017 PrestaShop SA
*  @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*/

$(document).ready(function()
{
	// Check rewrite engine availability
	$.ajax({
		url: 'sandbox/anything.php',
		success: function(value) {
			$('#rewrite_engine').val(1);
		}
	});

	// Check database configuration
	$('#btTestDB').click(function()
	{
		$("#dbResultCheck")
			.removeClass('errorBlock')
			.removeClass('okBlock')
			.addClass('waitBlock')
			.html('&nbsp;')
			.slideDown('slow');
		$.ajax({
			url: 'index.php',
			data: {
                'checkDb': 'true',
                'dbServer': $('#dbServer').val(),
                'dbName': $('#dbName').val(),
                'dbLogin': $('#dbLogin').val(),
                'dbPassword': $('#dbPassword').val(),
                'dbEngine': $('#dbEngine').val(),
                'db_prefix': $('#db_prefix').val(),
                'clear': $('#db_clear').prop('checked') ? '1' : '0'
            },
			dataType: 'json',
			cache: false,
			success: function(json)
			{
				$("#dbResultCheck")
					.addClass((json.success) ? 'okBlock' : 'errorBlock')
					.removeClass('waitBlock')
					.removeClass((json.success) ? 'errorBlock' : 'okBlock')
					.html(json.message)
			},
            error: function(xhr)
            {
            	var re = /<([a-z]+)(.*?>.*?<\/\1>|.*?\/>)/img;
            	var str = xhr.responseText;
            	var m;

            	while ((m = re.exec(str)) != null) {
				    if (m.index === re.lastIndex) {
				        re.lastIndex++;
				    }
				    if (m)
				    	var html = true;
				}

                $("#dbResultCheck")
                    .addClass('errorBlock')
					.removeClass('waitBlock')
                    .removeClass('okBlock')
                    .html('An error occurred:<br /><br />' + (html ? 'Can you please reload the page' : xhr.responseText))
            }
		});
	});
});

function bindCreateDB()
{
	// Attempt to create the database
	$('#btCreateDB').click(function()
	{
		$("#dbResultCheck").slideUp('fast');
		$.ajax({
			url: 'index.php',
			data: {
                'createDb': 'true',
                'dbServer': $('#dbServer').val(),
                'dbName': $('#dbName').val(),
                'dbLogin': $('#dbLogin').val(),
                'dbPassword': $('#dbPassword').val()
            },
			dataType: 'json',
			cache: false,
			success: function(json)
			{
				$("#dbResultCheck")
					.addClass((json.success) ? 'okBlock' : 'errorBlock')
					.removeClass((json.success) ? 'errorBlock' : 'okBlock')
					.html(json.message)
					.slideDown('slow');
			},
            error: function(xhr)
            {
                $("#dbResultCheck")
                    .addClass('errorBlock')
                    .removeClass('okBlock')
                    .html('An error occurred:<br /><br />' + xhr.responseText)
                    .slideDown('slow');
            }
		});
	});
}
