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

var fees_is_hide = false;

$(document).ready(function() {
	bind_inputs();
	initCarrierWizard();
	if (parseInt($('input[name="is_free"]:checked').val()))
		is_freeClick($('input[name="is_free"]:checked'));
	displayRangeType();

	$('#attachement_fileselectbutton').click(function(e) {
		$('#carrier_logo_input').trigger('click');
	});

	$('#attachement_filename').click(function(e) {
		$('#carrier_logo_input').trigger('click');
	});

	$('#carrier_logo_input').change(function(e) {
		var name  = '';
		if ($(this)[0].files !== undefined)
		{
			var files = $(this)[0].files;

			$.each(files, function(index, value) {
				name += value.name+', ';
			});

			$('#attachement_filename').val(name.slice(0, -2));
		}
		else // Internet Explorer 9 Compatibility
		{
			name = $(this).val().split(/[\\/]/);
			$('#attachement_filename').val(name[name.length-1]);
		}
	});

	$('#carrier_logo_remove').click(function(e) {
		$('#attachement_filename').val('');
	});

	if ($('#is_free_on').prop('checked') === true)
	{
		$('#shipping_handling_off').prop('checked', true).prop('disabled', true);
		$('#shipping_handling_on').prop('disabled', true).prop('checked', false);
	}

	$('#is_free_on').click(function(e) {
		$('#shipping_handling_off').prop('checked', true).prop('disabled', true);
		$('#shipping_handling_on').prop('disabled', true).prop('checked', false);
	});

	$('#is_free_off').click(function(e) {
		if ($('#shipping_handling_off').prop('disabled') === true)
		{
			$('#shipping_handling_off').prop('disabled', false).prop('checked', false);
			$('#shipping_handling_on').prop('disabled', false).prop('checked', true);
		}
	});
});

function initCarrierWizard()
{
	$("#carrier_wizard").smartWizard({
		'labelNext' : labelNext,
		'labelPrevious' : labelPrevious,
		'labelFinish' : labelFinish,
		'fixHeight' : 1,
		'onShowStep' : onShowStepCallback,
		'onLeaveStep' : onLeaveStepCallback,
		'onFinish' : onFinishCallback,
		'transitionEffect' : 'slideleft',
		'enableAllSteps' : enableAllSteps,
		'keyNavigation' : false
	});
	displayRangeType();
}

function displayRangeType()
{
	if ($('input[name="shipping_method"]:checked').val() == 1)
	{
		string = string_weight;
		$('.weight_unit').show();
		$('.price_unit').hide();
	}
	else
	{
		string = string_price;
		$('.price_unit').show();
		$('.weight_unit').hide();
	}
	is_freeClick($('input[name="is_free"]:checked'));
	$('.range_type').html(string);
}

function onShowStepCallback()
{
	$('.anchor li a').each(function () {
		$(this).closest('li').addClass($(this).attr('class'));
	});
	$('#carrier_logo_block').prependTo($('div.content').filter(function() { return $(this).css('display') != 'none' }).find('.defaultForm').find('fieldset'));
	resizeWizard();
}

function onFinishCallback(obj, context)
{
	$('.wizard_error').remove();
	$.ajax({
		type:"POST",
		url : validate_url,
		async: false,
		dataType: 'json',
		data : $('#carrier_wizard .stepContainer .content form').serialize() + '&action=finish_step&ajax=1&step_number='+context.fromStep,
		success : function(data) {
			if (data.has_error)
			{
				displayError(data.errors, context.fromStep);
				resizeWizard();
			}
			else
				window.location.href = carrierlist_url;
		},
		error: function(XMLHttpRequest, textStatus, errorThrown) {
			jAlert("TECHNICAL ERROR: \n\nDetails:\nError thrown: " + XMLHttpRequest + "\n" + 'Text status: ' + textStatus);
		}
	});
}

function onLeaveStepCallback(obj, context)
{
	if (context.toStep == nbr_steps)
		displaySummary();

	return validateSteps(context.fromStep, context.toStep); // return false to stay on step and true to continue navigation
}

function displaySummary()
{
    var id_default_lang = typeof default_language !== 'undefined' ? default_language : 1,
        id_lang = id_default_lang;

    // Try to find current employee language
    if (typeof languages !== 'undefined' && typeof iso_user !== 'undefined')
        for (var i=0; i<languages.length; i++)
            if (languages[i]['iso_code'] == iso_user)
            {
                id_lang = languages[i]['id_lang'];
                break;
            }

    // used as buffer - you must not replace directly in the translation vars
    var tmp,
        delay_text = $('#delay_' + id_lang).val();

    // Assign text in default language if empty
    if (!delay_text)
        delay_text = $('#delay_' + id_default_lang).val();

	// Carrier name
	$('#summary_name').text($('#name').val());

	// Delay and pricing
	$('#summary_meta_informations').html(summary_translation_meta_informations
		.replace('@s1', '<strong></strong>')
		.replace('@s2', '<strong></strong>')
	);
	if ($('#is_free_on').attr('checked'))
		$('#summary_meta_informations').find('strong:eq(0)').text(summary_translation_free);
	else
		$('#summary_meta_informations').find('strong:eq(0)').text(summary_translation_paid);
	$('#summary_meta_informations').find('strong:eq(1)').text(delay_text);

	// Tax and calculation mode for the shipping cost
	$('#summary_shipping_cost').html(summary_translation_shipping_cost
		.replace('@s1', '<strong></strong>')
		.replace('@s2', '<strong></strong>')
	);

	if ($('#billing_price').attr('checked'))
		$('#summary_shipping_cost').find('strong:eq(0)').text(summary_translation_price);
	else if ($('#billing_weight').attr('checked'))
		$('#summary_shipping_cost').find('strong:eq(0)').text(summary_translation_weight);
	else
		$('#summary_shipping_cost').find('strong:eq(0)').text(summary_translation_undefined);

	$('#summary_shipping_cost').find('strong:eq(1)').text($('#id_tax_rules_group option:selected').text());
	

	// Weight or price ranges

	if ($('input[name="shipping_method"]:checked').val() == 1)
		unit = PS_WEIGHT_UNIT;
	else
		unit = currency_sign;

	var range_inf = summary_translation_undefined;
	var range_sup = summary_translation_undefined;

	$('tr.range_inf td input').each(function()
	{
		if (!isNaN(parseFloat($(this).val())) && (range_inf == summary_translation_undefined || parseFloat(range_inf) > parseFloat($(this).val())))
			range_inf = $(this).val();
	});

	$('tr.range_sup td input').each(function(){

		if (!isNaN(parseFloat($(this).val())) && (range_sup == summary_translation_undefined || parseFloat(range_sup) < parseFloat($(this).val())))
			range_sup = $(this).val();
	});
	
	tmp = summary_translation_range+' '+summary_translation_range_limit;
	$('#summary_range').html(tmp
		.replace('@s1', '<strong></strong>')
		.replace('@s2', '<strong></strong>')
		.replace('@s3', '<strong></strong>')
	);

	$('#summary_range').find('strong:eq(0)').text(range_inf +' '+ unit);
	$('#summary_range').find('strong:eq(1)').text(range_sup +' '+ unit);
	$('#summary_range').find('strong:eq(2)').text($('#range_behavior option:selected').text().toLowerCase());

	if ($('#is_free_on').attr('checked'))
		$('span.is_free').hide();

	// Delivery zones
	$('#summary_zones').html('');
	$('.input_zone').each(function(){
		if ($(this).attr('checked'))
			$('#summary_zones').html($('#summary_zones').html() + '<li><strong>' + $(this).closest('tr').find('label').text() + '</strong></li>');
	});

	// Group restrictions
	$('#summary_groups').html('');
	$('input[name$="groupBox[]"]').each(function(){
		if ($(this).attr('checked'))
			$('#summary_groups').html($('#summary_groups').html() + '<li><strong>' + $(this).closest('tr').find('td:eq(2)').text() + '</strong></li>');
	});

	// shop restrictions
	$('#summary_shops').html('');
	$('.input_shop').each(function(){
		if ($(this).attr('checked'))
			$('#summary_shops').html($('#summary_shops').html() + '<li><strong>' + $(this).closest().text() + '</strong></li>');
	});
}

function validateSteps(fromStep, toStep)
{
	var is_ok = true;
	if ((multistore_enable && fromStep == 3) || (!multistore_enable && fromStep == 2))
	{
		if (toStep > fromStep && !$('#is_free_on').attr('checked'))
		{
			is_ok = false;
			$('.input_zone').each(function () {
				if ($(this).prop('checked'))
					is_ok = true;
			});

			if (!is_ok)
			{
				displayError([select_at_least_one_zone], fromStep);
				return;
			}
		}

		if (toStep > fromStep && !$('#is_free_on').attr('checked') && !validateRange(2))
			is_ok = false;
	}

	$('.wizard_error').remove();

	if (is_ok && isOverlapping())
		is_ok = false;

	if (is_ok)
	{
		form = $('#carrier_wizard #step-'+fromStep+' form');
		$.ajax({
			type:"POST",
			url : validate_url,
			async: false,
			dataType: 'json',
			data : form.serialize()+'&step_number='+fromStep+'&action=validate_step&ajax=1',
			success : function(datas)
			{
				if (datas.has_error)
				{
					is_ok = false;
					$('div.input-group input').focus(function () {
						$(this).closest('div.input-group').removeClass('has-error');
					});
					displayError(datas.errors, fromStep);
					resizeWizard();
				}
			},
			error: function(XMLHttpRequest, textStatus, errorThrown) {
				jAlert("TECHNICAL ERROR: \n\nDetails:\nError thrown: " + XMLHttpRequest + "\n" + 'Text status: ' + textStatus);
			}
		});
	}
	return is_ok;
}

function displayError(errors, step_number)
{
	$('#carrier_wizard .actionBar a.btn').removeClass('disabled');
	$('.wizard_error').remove();
	str_error = '<div class="error wizard_error" style="display:none"><ul>';
	for (var error in errors)
	{
		$('#carrier_wizard .actionBar a.btn').addClass('disabled');
		$('input[name="'+error+'"]').closest('div.input-group').addClass('has-error');
		str_error += '<li>'+errors[error]+'</li>';
	}
	$('#step-'+step_number).prepend(str_error+'</ul></div>');
	$('.wizard_error').fadeIn('fast');
	bind_inputs();
}

function resizeWizard()
{
	resizeInterval = setInterval(function (){$("#carrier_wizard").smartWizard('fixHeight'); clearInterval(resizeInterval)}, 100);
}

function bind_inputs()
{
	$('input').focus(function () {
		$(this).closest('div.input-group').removeClass('has-error');
		$('#carrier_wizard .actionBar a.btn').not('.buttonFinish').removeClass('disabled');
		$('.wizard_error').fadeOut('fast', function () { $(this).remove()});
	});

	$('tr.delete_range td button').off('click').on('click', function () {
		if (confirm(delete_range_confirm))
		{
			index = $(this).closest('td').index();
			$('tr.range_sup td:eq('+index+'), tr.range_inf td:eq('+index+'), tr.fees_all td:eq('+index+'), tr.delete_range td:eq('+index+')').remove();
			$('tr.fees').each(function () {
				$(this).find('td:eq('+index+')').remove();
			});
			rebuildTabindex();
		}
		return false;
	});

	$('tr.fees td input:checkbox').off('change').on('change', function ()
	{
		if($(this).is(':checked'))
		{
			$(this).closest('tr').find('td').each(function () {
				index = $(this).index();
				if ($('tr.fees_all td:eq('+index+')').hasClass('validated'))
				{
					enableGlobalFees(index);
					$(this).find('div.input-group input:text').removeAttr('disabled');
				}
				else
					disabledGlobalFees(index);
			});
		}
		else
			$(this).closest('tr').find('td').find('div.input-group input:text').attr('disabled', 'disabled');

		return false;
	});

	$('tr.range_sup td input:text, tr.range_inf td input:text').focus(function () {
		$(this).closest('div.input-group').removeClass('has-error');
	});

	$('tr.range_sup td input:text, tr.range_inf td input:text').keypress(function (evn) {
		index = $(this).closest('td').index();
		if (evn.keyCode == 13)
		{
			if (validateRange(index))
				enableRange(index);
			else
				disableRange(index);
			return false;
		}
	});

	$('tr.fees_all td input:text').keypress(function (evn) {
		index = $(this).parent('td').index();
		if (evn.keyCode == 13)
			return false;
	});

	$('tr.range_sup td input:text, tr.range_inf td input:text').typeWatch({
		captureLength: 0,
		highlight: false,
		wait: 1000,
		callback: function() {
			index = $(this.el).closest('td').index();
			range_sup = $('tr.range_sup td:eq('+index+')').find('div.input-group input:text').val().trim();
			range_inf = $('tr.range_inf td:eq('+index+')').find('div.input-group input:text').val().trim();
			if (range_sup != '' && range_inf != '')
			{
				if (validateRange(index))
					enableRange(index);
				else
					disableRange(index);
			}
		}
	});

	$(document.body).off('change', 'tr.fees_all td input').on('change', 'tr.fees_all td input', function() {
	   index = $(this).closest('td').index();
		val = $(this).val();
		$(this).val('');
		$('tr.fees').each(function () {
			$(this).find('td:eq('+index+') input:text:enabled').val(val);
		});

		return false;
	});

	$('input[name="is_free"]').off('click').on('click', function() {
		is_freeClick(this);
	});

	$('input[name="shipping_method"]').off('click').on('click', function() {
		$.ajax({
			type:"POST",
			url : validate_url,
			async: false,
			dataType: 'html',
			data : 'id_carrier='+parseInt($('#id_carrier').val())+'&shipping_method='+parseInt($(this).val())+'&action=changeRanges&ajax=1',
			success : function(data) {
				$('#zone_ranges').replaceWith(data);
				displayRangeType();
				bind_inputs();
			},
			error: function(XMLHttpRequest, textStatus, errorThrown) {
				jAlert("TECHNICAL ERROR: \n\nDetails:\nError thrown: " + XMLHttpRequest + "\n" + 'Text status: ' + textStatus);
			}
		});
	});

	$('#zones_table td input[type=text]').off('change').on('change', function () {
		checkAllFieldIsNumeric();
	});
}

function is_freeClick(elt)
{
	var is_free = $(elt);
	if (parseInt(is_free.val()))
		hideFees();
	else if (fees_is_hide)
		showFees();
}

function hideFees()
{
	$('tr.range_inf td, tr.range_sup td, tr.fees_all td, tr.fees td').each(function () {
		if ($(this).index() >= 2)
		{
			$(this).find('input:text, button').val('').attr('disabled', 'disabled').css('background-color', '#999999').css('border-color', '#999999');
			$(this).css('background-color', '#999999');
		}
	});
	fees_is_hide = true;
}

function showFees()
{
	$('tr.range_inf td, tr.range_sup td, tr.fees_all td, tr.fees td').each(function () {
		if ($(this).index() >= 2)
		{
			//enable only if zone is active
			tr = $(this).closest('tr');
			validate = $('tr.fees_all td:eq('+$(this).index()+')').hasClass('validated');
			if ($(tr).index() > 2 && $(tr).find('td:eq(1) input').attr('checked') && validate || !$(tr).hasClass('range_sup') || !$(tr).hasClass('range_inf'))
				$(this).find('div.input-group input:text').removeAttr('disabled');
			$(this).find('input:text, button').css('background-color', '').css('border-color', '');
			$(this).find('button').css('background-color', '').css('border-color', '').removeAttr('disabled');
			$(this).css('background-color', '');
		}
	});
}

function validateRange(index)
{
	$('#carrier_wizard .actionBar a.btn').removeClass('disabled');
	$('.wizard_error').remove();
	//reset error css
	$('tr.range_sup td input:text').closest('div.input-group').removeClass('has-error');
	$('tr.range_inf td input:text').closest('div.input-group').removeClass('has-error');

	var is_valid = true;
	range_sup = parseFloat($('tr.range_sup td:eq('+index+')').find('div.input-group input:text').val().trim());
	range_inf = parseFloat($('tr.range_inf td:eq('+index+')').find('div.input-group input:text').val().trim());

	if (isNaN(range_sup) || range_sup.length === 0)
	{
		$('tr.range_sup td:eq('+index+')').find('div.input-group input:text').closest('div.input-group').addClass('has-error');
		is_valid = false;
		displayError([invalid_range], $("#carrier_wizard").smartWizard('currentStep'));
	}
	else if (is_valid && (isNaN(range_inf) || range_inf.length === 0))
	{
		$('tr.range_inf td:eq('+index+')').find('div.input-group input:text').closest('div.input-group').addClass('has-error');
		is_valid = false;
		displayError([invalid_range], $("#carrier_wizard").smartWizard('currentStep'));
	}
	else if (is_valid && range_inf >= range_sup)
	{
		$('tr.range_sup td:eq('+index+')').find('div.input-group input:text').closest('div.input-group').addClass('has-error');
		$('tr.range_inf td:eq('+index+')').find('div.input-group input:text').closest('div.input-group').addClass('has-error');
		is_valid = false;
		displayError([invalid_range], $("#carrier_wizard").smartWizard('currentStep'));
	}
	else if (is_valid && index > 2) //check range only if it's not the first range
	{
		$('tr.range_sup td').not('.range_type, .range_sign, tr.range_sup td:last').each(function ()
		{
			if ($('tr.fees_all td:eq('+index+')').hasClass('validated'))
			{
				is_valid = false;
				curent_index = $(this).index();

				current_sup = $(this).find('div.input-group input').val();
				current_inf = $('tr.range_inf td:eq('+curent_index+') input').val();

				if ($('tr.range_inf td:eq('+curent_index+1+') input').length)
					next_inf = $('tr.range_inf td:eq('+curent_index+1+') input').val();
				else
					next_inf = false;

				//check if range already exist
				//check if ranges is overlapping
				if ((range_sup != current_sup && range_inf != current_inf) && ((range_sup > current_sup || range_sup <= current_inf) && (range_inf < current_inf || range_inf >= current_sup)))
					is_valid = true;
			}

		});

		if (!is_valid)
		{
			$('tr.range_sup td:eq('+index+')').find('div.input-group input:text').closest('div.input-group').addClass('has-error');
			$('tr.range_inf td:eq('+index+')').find('div.input-group input:text').closest('div.input-group').addClass('has-error');
			displayError([range_is_overlapping], $("#carrier_wizard").smartWizard('currentStep'));
		}
		else
			isOverlapping();
	}
	return is_valid;
}

function enableZone(index)
{
	$('tr.fees').each(function () {
		if ($(this).find('td:eq(1)').find('input[type=checkbox]:checked').length)
			$(this).find('td:eq('+index+')').find('div.input-group input').removeAttr('disabled');
	});
}

function disableZone(index)
{
	$('tr.fees').each(function () {
		$(this).find('td:eq('+index+')').find('div.input-group input').attr('disabled', 'disabled');
	});
}

function enableRange(index)
{
	$('tr.fees').each(function () {
		//only enable fees for enabled zones
		if ($(this).find('td').find('input:checkbox').attr('checked') == 'checked')
			enableZone(index);
	});
	$('tr.fees_all td:eq('+index+')').addClass('validated').removeClass('not_validated');

	//if ($('.zone input[type=checkbox]:checked').length)
		enableGlobalFees(index);
	bind_inputs();
}

function enableGlobalFees(index)
{
	$('span.fees_all').show();
	$('tr.fees_all td:eq('+index+')').find('div.input-group input').show().removeAttr('disabled');
	$('tr.fees_all td:eq('+index+')').find('div.input-group .currency_sign').show();
}

function disabledGlobalFees(index)
{
	$('span.fees_all').hide();
	$('tr.fees_all td:eq('+index+')').find('div.input-group input').hide().attr('disabled', 'disabled');
	$('tr.fees_all td:eq('+index+')').find('div.input-group .currency_sign').hide();
}


function disableRange(index)
{
	$('tr.fees').each(function () {
		//only enable fees for enabled zones
		if ($(this).find('td').find('input:checkbox').attr('checked') == 'checked')
			disableZone(index);
	});
	$('tr.fees_all td:eq('+index+')').find('div.input-group input').attr('disabled', 'disabled');
	$('tr.fees_all td:eq('+index+')').removeClass('validated').addClass('not_validated');
}

function add_new_range()
{
	if (!$('tr.fees_all td:last').hasClass('validated'))
	{
		alert(need_to_validate);
		return false;
	}

	last_sup_val = $('tr.range_sup td:last input').val();
	//add new rand sup input
	$('tr.range_sup td:last').after('<td class="range_data"><div class="input-group fixed-width-md"><span class="input-group-addon weight_unit" style="display: none;">'+PS_WEIGHT_UNIT+'</span><span class="input-group-addon price_unit" style="display: none;">'+currency_sign+'</span><input class="form-control" name="range_sup[]" type="text" autocomplete="off" /></div></td>');
	//add new rand inf input
	$('tr.range_inf td:last').after('<td class="border_bottom"><div class="input-group fixed-width-md"><span class="input-group-addon weight_unit" style="display: none;">'+PS_WEIGHT_UNIT+'</span><span class="input-group-addon price_unit" style="display: none;">'+currency_sign+'</span><input class="form-control" name="range_inf[]" type="text" value="'+last_sup_val+'" autocomplete="off" /></div></td>');
	$('tr.fees_all td:last').after('<td class="border_top border_bottom"><div class="input-group fixed-width-md"><span class="input-group-addon currency_sign" style="display:none" >'+currency_sign+'</span><input class="form-control" style="display:none" type="text" /></div></td>');

	$('tr.fees').each(function () {
		$(this).find('td:last').after('<td><div class="input-group fixed-width-md"><span class="input-group-addon currency_sign">'+currency_sign+'</span><input class="form-control" disabled="disabled" name="fees['+$(this).data('zoneid')+'][]" type="text" /></div></td>');
	});
	$('tr.delete_range td:last').after('<td><button class="btn btn-default">'+labelDelete+'</button</td>');

	bind_inputs();
	rebuildTabindex();
	displayRangeType();
	resizeWizard();
	return false;
}

function delete_new_range()
{
	if ($('#new_range_form_placeholder').find('td').length = 1)
		return false;
}

function checkAllFieldIsNumeric()
{
	$('#carrier_wizard .actionBar a.btn').removeClass('disabled');
	$('#zones_table td input[type=text]').each(function () {
		if (!$.isNumeric($(this).val()) && $(this).val() != '')
			$(this).closest('div.input-group').addClass('has-error');
	});
}

function rebuildTabindex()
{
	i = 1;
	$('#zones_table tr').each(function ()
	{
		j = i;
		$(this).find('td').each(function ()
		{
			j = zones_nbr + j;
			if ($(this).index() >= 2 && $(this).find('div.input-group input'))
				$(this).find('div.input-group input').attr('tabindex', j);
		});
		i++;
	});
}

function repositionRange(current_index, new_index)
{
	$('tr.range_sup, tr.range_inf, tr.fees_all, tr.fees, tr.delete_range ').each(function () {
		$(this).find('td:eq('+current_index+')').each(function () {
			$(this).closest('tr').find('td:eq('+new_index+')').after(this.outerHTML);
			$(this).remove();
		});
	});
}

function checkRangeContinuity(reordering)
{
	reordering = typeof reordering !== 'undefined' ? reordering : false;
	res = true;

	$('tr.range_sup td').not('.range_type, .range_sign').each(function ()
	{
		index = $(this).index();
		if (index > 2)
		{
			range_sup = parseFloat($('tr.range_sup td:eq('+index+')').find('div.input-group input:text').val().trim());
			range_inf = parseFloat($('tr.range_inf td:eq('+index+')').find('div.input-group input:text').val().trim());
			prev_index = index-1;
			prev_range_sup = parseFloat($('tr.range_sup td:eq('+prev_index+')').find('div.input-group input:text').val().trim());
			prev_range_inf = parseFloat($('tr.range_inf td:eq('+prev_index+')').find('div.input-group input:text').val().trim());

			if (range_inf < prev_range_inf || range_sup < prev_range_sup)
			{
				res = false;
				if (reordering)
				{
					new_position = getCorrectRangePosistion(range_inf, range_sup);
					if (new_position)
						repositionRange(index, new_position);
				}
			}
		}
	});
	if (res)
		$('.ranges_not_follow').fadeOut();
	else
		$('.ranges_not_follow').fadeIn();
	resizeWizard();
}

function getCorrectRangePosistion(current_inf, current_sup)
{
	new_position = false;
	$('tr.range_sup td').not('.range_type, .range_sign').each(function ()
	{
		index = $(this).index();
		range_sup = parseFloat($('tr.range_sup td:eq('+index+')').find('div.input-group input:text').val().trim());
		next_range_inf = 0
		if ($('tr.range_inf td:eq('+index+1+')').length)
			next_range_inf = parseFloat($('tr.range_inf td:eq('+index+1+')').find('div.input-group input:text').val().trim());
		if (current_inf >= range_sup && current_sup < next_range_inf)
			new_position = index;
	});
	return new_position;
}

function isOverlapping()
{
	var is_valid = false;
	$('#carrier_wizard .actionBar a.btn').removeClass('disabled');
	$('tr.range_sup td').not('.range_type, .range_sign').each( function ()
	{
		index = $(this).index();
		current_inf = parseFloat($('.range_inf td:eq('+index+') input').val());
		current_sup = parseFloat($('.range_sup td:eq('+index+') input').val());

		$('tr.range_sup td').not('.range_type, .range_sign').each( function ()
		{
			testing_index = $(this).index();

			if (testing_index != index) //do not test himself
			{
				testing_inf = parseFloat($('.range_inf td:eq('+testing_index+') input').val());
				testing_sup = parseFloat($('.range_sup td:eq('+testing_index+') input').val());

				if ((current_inf >= testing_inf && current_inf < testing_sup) || (current_sup > testing_inf && current_sup < testing_sup))
				{
					$('tr.range_sup td:eq('+testing_index+') div.input-group, tr.range_inf td:eq('+testing_index+') div.input-group').addClass('has-error');
					displayError([overlapping_range], $("#carrier_wizard").smartWizard('currentStep'));
					is_valid = true;
				}
			}
		});
	});
	return is_valid;
}

function checkAllZones(elt)
{
	if($(elt).is(':checked'))
	{
		$('.input_zone').attr('checked', 'checked');
		$('.fees div.input-group input:text').each(function () {
			index = $(this).closest('td').index();
			enableGlobalFees(index);
			if ($('tr.fees_all td:eq('+index+')').hasClass('validated'))
			{
				$(this).removeAttr('disabled');
				$('.fees_all td:eq('+index+') div.input-group input:text').removeAttr('disabled');
			}
		});
	}
	else
	{
		$('.input_zone').removeAttr('checked');
		$('.fees div.input-group input:text, .fees_all div.input-group input:text').attr('disabled', 'disabled');
	}

}
