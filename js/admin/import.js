/*
* 2007-2017 PrestaShop
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

$(document).ready(function(){

	$('#saveImportMatchs').unbind('click').click(function(){

	var newImportMatchs = $('#newImportMatchs').attr('value');
	if (newImportMatchs == '')
		jAlert(errorEmpty);
	else
	{
		var matchFields = '';
		$('.type_value').each( function () {
			matchFields += '&'+$(this).attr('id')+'='+$(this).attr('value');
		});
		$.ajax({
	       type: 'POST',
	       url: 'index.php',
	       async: false,
	       cache: false,
	       dataType : "json",
	       data: 'ajax=1&action=saveImportMatchs&tab=AdminImport&token=' + token + '&skip=' + $('input[name=skip]').attr('value') + '&newImportMatchs=' + newImportMatchs + matchFields,
	       success: function(jsonData)
	       {
				$('#valueImportMatchs').append('<option id="'+jsonData.id+'" value="'+matchFields+'" selected="selected">'+newImportMatchs+'</option>');
				$('#selectDivImportMatchs').fadeIn('slow');
	       },
	      error: function(XMLHttpRequest, textStatus, errorThrown)
	       {
	       		jAlert('TECHNICAL ERROR Details: ' + html_escape(XMLHttpRequest.responseText));
	       }
	   });

	}
	});

	$('#loadImportMatchs').unbind('click').click(function(){

		var idToLoad = $('select#valueImportMatchs option:selected').attr('id');
		$.ajax({
		       type: 'POST',
		       url: 'index.php',
		       async: false,
		       cache: false,
		       dataType : "json",
		       data: 'ajax=1&action=loadImportMatchs&tab=AdminImport&token=' + token + '&idImportMatchs=' + idToLoad,
		       success: function(jsonData)
		       {
					var matchs = jsonData.matchs.split('|')
					$('input[name=skip]').val(jsonData.skip);
					for (i=0;i<matchs.length;i++)
						$('#type_value\\['+i+'\\]').val(matchs[i]).attr('selected',true);
		       },
		      error: function(XMLHttpRequest, textStatus, errorThrown)
		       {
		       		jAlert('TECHNICAL ERROR Details: ' + html_escape(XMLHttpRequest.responseText));

		       }
		   });
	});

	$('#deleteImportMatchs').unbind('click').click(function(){

		var idToDelete = $('select#valueImportMatchs option:selected').attr('id');
		$.ajax({
		       type: 'POST',
		       url: 'index.php',
		       async: false,
		       cache: false,
		       dataType : "json",
		       data: 'ajax=1&action=deleteImportMatchs&tab=AdminImport&token=' + token + '&idImportMatchs=' + idToDelete ,
		       success: function(jsonData)
		       {
					$('select#valueImportMatchs option[id=\''+idToDelete+'\']').remove();
					if ($('select#valueImportMatchs option').length == 0)
						$('#selectDivImportMatchs').fadeOut();
		       },
		      error: function(XMLHttpRequest, textStatus, errorThrown)
		       {
		       		jAlert('TECHNICAL ERROR Details: ' + html_escape(XMLHttpRequest.responseText));
		       }
		   });

	});
});

function validateImportation(mandatory)
{
    var type_value = [];
	var seted_value = [];
	var elem;
	var col = 'unknow';

	toggle(getE('error_duplicate_type'), false);
	toggle(getE('required_column'), false);
    for (i = 0; elem = getE('type_value['+i+']'); i++)
    {
		if (seted_value[elem.options[elem.selectedIndex].value])
		{
			scroll(0,0);
			toggle(getE('error_duplicate_type'), true);
			return false;
		}
		else if (elem.options[elem.selectedIndex].value != 'no')
			seted_value[elem.options[elem.selectedIndex].value] = true;
	}
	for (needed in mandatory)
		if (!seted_value[mandatory[needed]])
		{
			scroll(0,0);
			toggle(getE('required_column'), true);
			getE('missing_column').innerHTML = mandatory[needed];
			elem = getE('type_value[0]');
			for (i = 0; i < elem.length; ++i)
			{
				if (elem.options[i].value == mandatory[needed])
				{
					getE('missing_column').innerHTML = elem.options[i].innerHTML;
					break ;
				}
			}
			return false
		}
}

function html_escape(str) {
    return String(str)
	    .replace(/&/g, '&amp;')
	    .replace(/"/g, '&quot;')
	    .replace(/'/g, '&#39;')
	    .replace(/</g, '&lt;')
	    .replace(/>/g, '&gt;');
}
