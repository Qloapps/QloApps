$(document).ready( function () {
	gamificationTasks();
});

function gamificationTasks()
{
	$('#gamification_notif').remove();
	$('#notifs_icon_wrapper').append('<div id="gamification_notif" class="notifs"></div>');
	$.ajax({
		type: 'POST',
		url: admin_gamification_ajax_url,
		dataType: 'json',
		data: {
			controller : 'AdminGamification',
			action : 'gamificationTasks',
			ajax : true,
			id_tab : current_id_tab,
			ids_ps_advice : ids_ps_advice,
		},
		success: function(jsonData)
		{
			for (var i in jsonData.advices_to_display.advices)
			{
				ok = false;
				selector = jsonData.advices_to_display.advices[i].selector.split(',');
				for (var j in selector)
				{
					if (!ok)
					{
						if (jsonData.advices_to_display.advices[i].location == 'after')
							$(selector[j]).after(jsonData.advices_to_display.advices[i].html);
						else
							$(selector[j]).before(jsonData.advices_to_display.advices[i].html);
						
						if ($(selector[j]).length)
							ok = true;
					}
				}
			}

			initHeaderNotification(jsonData.header_notification);
			
			$('.gamification_fancybox').fancybox();
		}
	});
}

function initHeaderNotification(html)
{
	$('#gamification_notif').remove();
	$('#notifs_icon_wrapper').append(html);
	$('#gamification_notif').click(function () {
		if ($('#gamification_notif_wrapper').css('display') == 'block')
		{
			$('#gamification_notif_wrapper').hide();
		}
		else
		{
			disabledGamificationNotification();
			$('.notifs_wrapper').hide();
			$('#gamification_notif_number_wrapper').hide();
			$('#gamification_notif_wrapper').show();
			if (typeof(admintab_gamification) != "undefined")
			{
				$('#gamification_progressbar').progressbar({
					change: function() {
				        if (current_level_percent)
				        	$( ".gamification_progress-label" ).html( gamification_level+' '+current_level+' : '+$('#gamification_progressbar').progressbar( "value" ) + "%" );
				        else
				        	$( ".gamification_progress-label" ).html('');
				      },
		     	});
				$('#gamification_progressbar').progressbar("value", current_level_percent );
			}
		}
	});
	if ($('.dropdown-toggle').length)
		$('.dropdown-toggle').dropdown();
}

function disabledGamificationNotification()
{
	$.ajax({
		type: 'POST',
		url: admin_gamification_ajax_url,
		data: {
			controller : 'AdminGamification',
			action : 'disableNotification',
			ajax : true
		},
		success: function(jsonData)
		{
			$('#gamification_notif_number_wrapper').hide();
		}
	});
}

function initBubbleDescription()
{
	$('.badge_square').each( function () {
		if ($(this).children('.gamification_badges_description').text().length)
		{
			$(this).CreateBubblePopup({
				position : 'top',
				openingDelay:0,
				alwaysVisible: false,
				align	 : 'center',
				innerHtml: $(this).children('.gamification_badges_description').text(),
				innerHtmlStyle: { color:'#000',  'text-align':'center' },
				themeName: 'black',
				themePath: '../modules/gamification/views/jquerybubblepopup-themes'		 
			});
		}
	});
}


function filterBadge(type)
{
	group = '.'+$('#group_select_'+type+' option:selected').val();
	status = '.'+$('#status_select_'+type+' option:selected').val();
	level = '.'+$('#level_select_'+type+' option:selected').val();

	if (group == '.undefined')
		group = '';
	if (status == '.undefined')
		status = '';
	if (level == '.undefined')
		level = '';
	
	$('#list_'+type).isotope({filter: '.badge_square'+group+status+level, animationEngine : 'css'});
	
	if (!$('#list_'+type+' li').not('.isotope-hidden').length)
		$('#no_badge_'+type).fadeIn();
	else
		$('#no_badge_'+type).fadeOut();
}



