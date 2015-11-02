$(document).ready( function () {
	gamificationTasks();
});

function gamificationTasks()
{
	if (typeof ids_ps_advice == 'undefined')
		ids_ps_advice = new Array();
	$('#gamification_notif').remove();
	$('#header_notifs_icon_wrapper').append('<div id="gamification_notif" class="notifs"></div>');
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
			if (jsonData.advices_to_display.advices.length)
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
				//display close button only for last version of the module
				$('.gamification_close').show();
	
				$('.gamification_close').on('click', function () {
					if (confirm(hide_advice))
						adviceCloseClick($(this).attr('id'));
					return false;
				});
			}
			
			if (typeof jsonData.advices_premium_to_display != 'undefined')
			{
				$('#hookDashboardZoneTwo section:eq(0)').after('<div id="premium_advice_container" class="row"></div>');
				for (var p in jsonData.advices_premium_to_display.advices)
					if (jsonData.advices_premium_to_display.advices[p] != null && typeof jsonData.advices_premium_to_display.advices[p].html != 'undefined')
						$('#premium_advice_container').append(jsonData.advices_premium_to_display.advices[p].html);
				
				$('.gamification_premium_close').on('click', function () {
					var $adviceContainer = $(this).parent();
					var $btn = $(this);
					$adviceContainer.find('.gamification-close-confirmation').removeClass('hide');
					$adviceContainer.find('button').on('click',function(e){
						e.preventDefault();
						if ($(this).data('advice') == 'cancel' ) {
							$adviceContainer.find('.gamification-close-confirmation').addClass('hide');
						}
						else if ($(this).data('advice') == 'delete' ) {
							adviceCloseClick($btn.attr('id'));
						}
					});
					return false;
				});
			}
			
			initHeaderNotification(jsonData.header_notification);
			
			$('.gamification_fancybox').fancybox();
			
			$(".preactivationLink").on('click', function(e) {
				e.preventDefault();
				preactivationLinkClick($(this).attr('rel'), $(this).attr('href'));
			});
			
			$('.gamification_badges_img').tooltip();
		}
	});
}

function initHeaderNotification(html)
{
	$('#gamification_notif').remove();
	$('#header_notifs_icon_wrapper').append(html);
	$('.gamification_notif').click(function () {
		if ($('#gamification_notif_wrapper').parent().css('display') == 'none')
		{
			disabledGamificationNotification();
			$('#gamification_notif_value').html(0);
			$('#gamification_notif_number_wrapper').hide();

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

	if (parseInt($('#gamification_notif_value').html()) == 0)
		$('#gamification_notif_number_wrapper').hide();
	
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
			$('#gamification_notif_value').html(0);
			$('#gamification_notif_number_wrapper').hide();
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


function preactivationLinkClick(module, href) {
	$.ajax({
		url : admin_gamification_ajax_url,
		data : {
			ajax : "1",
			controller : "AdminGamification",
			action : "savePreactivationRequest",
			module : module,
		},
		type: 'POST',
		success : function(jsonData){
			window.location.href = href;
		},
		error : function(jsonData){
			window.location.href = href;
		}
	});
}

function adviceCloseClick(id_advice) {
	$.ajax({
		url : admin_gamification_ajax_url,
		data : {
			ajax : "1",
			controller : "AdminGamification",
			action : "closeAdvice",
			id_advice : id_advice,
		},
		type: 'POST'
	});

	$('#wrap_id_advice_'+id_advice).fadeOut();
	$('#wrap_id_advice_'+id_advice).html('<img src="'+advice_hide_url+id_advice+'.png"/>');
}
