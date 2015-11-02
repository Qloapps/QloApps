/**
* 2007-2015 PrestaShop
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
*  @author    PrestaShop SA <contact@prestashop.com>
*  @copyright 2007-2015 PrestaShop SA
*  @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*/

$( document ).ready(function() {
	$('#onboarding-progress-bar').popover('show');
	$('#onboarding-launch').click(function() {
		$('.gwd-page-container').addClass('gwd-gen-oxd9');
		onBoarding.sleep(5400, function() {
			$('.onboarding .onboarding-steps').hide('normal', function() {
				validateStep(7);
				$('.onboarding .final').show('nomal');
				$('.quick-start-button').hide();
			});
		});
	});

	$('.onboarding-steps a').click(function() {
		$('.onboarding-steps').animate({top: '-560px'}, 300);
	});

	$('<div class="alert alert-onboarding"></div>').html($('#onboarding-starter')).detach().prependTo('#content');
	$('#onboarding-starter').removeClass('hide');

	$('.close-button, .continue_editing').click(function() {
		toggleOnboardingModal();
	});


	$('#final-button').click(function() {
		$('.final').animate({top: '-560px'}, 300);
		$('.overlay').hide();
	});
	
	$('#onboarding-close').click(function() {
		$('.alert-onboarding').remove();
		$.ajax(onboarding_ajax_url+"&remove=1");
	});

	$('.skip').click(function() {
		validateStep(7);
		$('.overlay, .alert-onboarding').hide();
	});

	if (typeof display_onboarding_modal !== 'undefined' && display_onboarding_modal)
		toggleOnboardingModal();
});

var onBoarding = {
	sleep : function (time, callback)
	{
		setTimeout(function() {
			callback();
		} , time);
	}
} ;

function toggleOnboardingModal()
{
	if ($('.onboarding-steps').css('top') === '-560px')
		$('.onboarding-steps').animate({top: '-3px'}, 300);
	else
		$('.onboarding-steps').animate({top: '-560px'}, 300);

	$('.onboarding').toggleClass('minimized');
}

function validateStep(current_step, href)
{
	$.ajax({
		type: 'POST',
		headers: { "cache-control": "no-cache" },
		url: onboarding_ajax_url,
		async: true,
		cache: false,
		data: 'controller=AdminOnboarding&ajax=true&action=validate_step&current_step='+current_step,
		success: function(result)
		{
			if (href)
				window.location.href = href;
		}
	});
}

function share_twitter_click(message)
{
	window.open('https://twitter.com/intent/tweet?button_hashtag=PrestaShop&text=' + message, 'sharertwt', 'toolbar=0,status=0,width=640,height=445');
	location.reload();
}

function share_facebook_click()
{
	window.open('http://www.facebook.com/sharer.php?u=http://www.prestashop.com/', 'sharerfacebook', 'toolbar=0,status=0,width=660,height=445');
	location.reload();
}

function share_google_click()
{
	window.open('https://plus.google.com/share?url=http://www.prestashop.com/', 'sharergplus', 'toolbar=0,status=0,width=660,height=445');
	location.reload();
}

function share_linkedin_click()
{
	window.open('https://www.linkedin.com/shareArticle?title=PrestaShop&url=http://www.prestashop.com/download', 'sharerlinkedin', 'toolbar=0,status=0,width=600,height=450');
	location.reload();
}
