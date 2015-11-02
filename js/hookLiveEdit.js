var has_been_moved = false;
var modules_list = new Array();
var hooks_list = new Array();
var hookable_list = new Array();
var timer;
$(document).ready(function() {
	// do some place for submit button
	$('body').css('margin-top', '45px');
	// this is the result box, which will "pop up" the succeed or fail result
	$('#fancy').fancybox({
		autoDimensions: true,
		autoScale: true,
		width: 300,
		height: 300,
		padding: 0,
		hideOnOverlayClick: false,
		hideOnContentClick: false,
		showCloseButton: false
	});
	
	$('#live_edit_feedback_str').html('');
	// add liveToken in each link to navigate into the shop and keeping the liveedit mode
	$('a').each(function() {
		var href = this.href;
		var search = this.search;
		var hrefAdd = 'live_edit&liveToken=' + get('liveToken') + '&ad=' + get('ad') + '&id_shop=' + get('id_shop') + '&id_employee=' + get('id_employee');
		if (href != undefined && href != '#' && href.substr(0, baseDir.length) == baseDir)
		{
			if (search.length == 0)
				this.search = hrefAdd;
			else
				this.search += '&' + hrefAdd;
		}
	});
	// populate  
	getHookableList();
	$('.unregisterHook').unbind('click').click(function()
	{
		ids = $(this).attr('id').split('_');
		$(this).parent().parent().parent().fadeOut('slow', function() {
			$(this).parent().data('id', + ids[0]);
			$(this).remove();
		});
		return false;
	});
	$('#cancelMove').unbind('click').click(function()
	{
		$('#' + cancelMove + '').sortable('cancel');
		return false;
	});
	$('#saveLiveEdit').unbind('click').click(function()
	{
		saveModulePosition();
		return false;
	});
	$('#closeLiveEdit').unbind('click').click(function() 
	{
		if (!has_been_moved)
			closeLiveEdit();
		else
		{
			$("#live_edit_feedback_str").html('<div style="padding:10px;"><p style="margin-bottom:10px;">' + confirmClose + '</p><p style="height:1.6em;display:block"><a style="margin:auto;float:left" class="button" href="#" onclick="closeLiveEdit();">' + confirm + '</a><a style="margin:auto;float:right;" class="button" href="#" onclick="closeFancybox();">' + cancel + '</a></p></div>');
			$("#fancy").attr('href', '#live_edit_feedback');
			$("#fancy").trigger("click");
		}
		return false;
	});
	$('.add_module_live_edit').unbind('click').click(function()
	{
		$("#live_edit_feedback_str").html('<div style="text-align:center; padding: 30px;"><img src="' + baseDir + 'img/loadingAnimation.gif"></div>');
		$("#fancy").attr('href', '#live_edit_feedback');
		$("#fancy").trigger("click");
		var id = $(this).attr('id');
		getHookableModuleList(id.substr(4, id.length));
		return false;
	});
	$('.dndHook').each(function() {
		var id_hook = $(this).attr('id');
		var new_target_id = '';
		var old_target = '';
		var cancel = false;
		$('#' + id_hook + '').sortable({
			opacity: 0.5,
			cursor: 'move',
			connectWith: '.dndHook',
			receive: function(event, ui) {
				if (new_target_id == '') {
					new_target_id = event.target.id;
				}
				has_been_moved = true;
			},
			start: function(event, ui) {
				new_target_id = ui.item[0].parentNode.id;

			},
			stop: function(event, ui) {
				if (cancel)
				{
					$(this).sortable('cancel');
				}
				else
				{
					old_target = event.target.id;
					cancelMove = old_target;
					if (new_target_id == '')
						new_target_id = old_target;
	        		ids = $(ui.item[0]).attr('id').split('_');
					newHookId = $("input[value="+new_target_id+"]").attr('name').substr(10);
					newHookId = newHookId.substr(0, newHookId.length -1);
					new_id = ids[0] + "_" + newHookId + "_" + ids[2] + "_" + ids[3] + "_" + ids[4] + "_" + ids[5] ;
					$(ui.item[0]).attr('id', new_id);
				}
			},
			change: function(evartent, ui) {
				new_target_id = $(ui.placeholder).parent().attr('id');
				ids = ui.item[0].id.split('_');
				if ($.inArray(ids[5], hookable_list[new_target_id]) != -1) {
					cancel = false;
					ui.placeholder.css({
						visibility: 'visible',
						border: '1px solid #72CB67',
						background: '#DFFAD3'
					});
				}
				else
				{
					ui.placeholder.css({
						visibility: 'visible',
						border: '1px solid #EC9B9B',
						background: '#FAE2E3'
					});
					cancel = true;
				}
			}
		});
		$('#' + id_hook + '').disableSelection();
	});
});
// init hookable_list 
function getHookableList() {
	hooks_list = new Array();
	$("input[name^=hook_list]").each(function(e){
		hooks_list.push($(this).val());
	}); 

    $.ajax({
			type: 'POST',
			url: baseDir + ad + '/index.php',
			async: true,
			dataType: 'json',
			data: {
				action: 'getHookableList',
				tab: 'AdminModulesPositions',
				ajax:1,
				hooks_list: hooks_list,
				modules_list: modules_list,
				id_shop: get('id_shop'),
				token: get('liveToken')
			},
			success: function(jsonData) {
			if (jsonData.hasError) {
	        	var errors = '';
				for (error in jsonData.errors) //IE6 bug fix
				if (error != 'indexOf')
					errors += $('<div />').html(jsonData.errors[error]).text() + "\n";
				alert(errors);
        }
        else
        	hookable_list = jsonData;// create and fill input array
        },
        error: function(XMLHttpRequest, textStatus, errorThrown) {
            $('#live_edit_feedback_str').html('<div class="live_edit_feed_back_ko" style="text-align:center; padding-top: 33px;"><img src="' + baseDir + 'img/admin/error.png"><h3>TECHNICAL ERROR:</h3>' + loadFail + '<br><br><a style="margin:auto" class="button" href="#" onclick="closeFancybox();">' + close + '</a></div>');
            $("#fancy").attr('href', '#live_edit_feedback');
            $("#fancy").trigger("click");
        }
    });
}
function getHookableModuleList(hook) {
    $.ajax({
        type: 'GET',
        url: baseDir + ad + '/index.php',
        async: true,
        dataType: 'json',
        data: {
        	ajax:1,
			tab: 'AdminModulesPositions',
        	action:'getHookableModuleList',
        	hook: hook,
			id_shop: get('id_shop'),
			token: get('liveToken')
        },
        success: function(jsonData) {
            var select = '<select id="select_module">';
            for (var i = 0; i < jsonData.length; i++) {
                select += '<option value="' + jsonData[i].id + '">' + jsonData[i].name + '</option>';
            }
            select += '</select>';
            $("#live_edit_feedback_str").html('<div style="padding:10px">' + select + '<br><br><a style="margin:auto" class="button" href="#" >' + add + '</a><br><br><a style="margin:auto" class="button" href="#" onclick="closeFancybox();">' + cancel + '</a>');
        },
        error: function(XMLHttpRequest, textStatus, errorThrown) {
            alert("TECHNICAL ERROR: unable to unregister hook \n\nDetails:\nError thrown: " + XMLHttpRequest + "\n" + 'Text status: ' + textStatus);
        }
    });
}
function saveModulePosition()
{
    $("input.dynamic-input-save-position").remove();
    $("#live_edit_feedback_str").html('<div style="text-align:center; padding: 30px;"><img src="' + baseDir + 'img/loadingAnimation.gif"></div>');
    $("#fancy").attr('href', '#live_edit_feedback');
    $("#fancy").trigger("click");
    var str = '';
    for (var i = 0; i < hooks_list.length; i++)
	{
        str += '&' + hooks_list[i] + '=';

		if ($('#' + hooks_list[i] + ' > .dndModule').length)
		{
			$('#' + hooks_list[i] + ' > .dndModule').each(function(){
	
	            ids = $(this).attr('id').split('_');
	            $("#liveEdit-action-form").append('<input class="dynamic-input-save-position" type="hidden" name="hook[' + ids[1] + '][]" value="' + ids[3] + '" />');
	        });
		}
		else if (typeof($('#' + hooks_list[i]).data('id')) !== 'undefined' && !$('input[name="hook[' + i + ']"]').length)
	            	$("#liveEdit-action-form").append('<input class="dynamic-input-save-position" type="hidden" name="hook[' + parseInt($('#' + hooks_list[i]).data('id')) + '][0]" value="0" />');
    }
	$("#liveEdit-action-form").append('<input class="dynamic-input-save-position" type="hidden" name="saveHook" value="1" />');
	datas = $("#liveEdit-action-form").serializeArray();

    $.ajax({
        type: 'POST',
        url: baseDir + ad + "/index.php", 
        async: true,
        dataType: 'json',
        data: datas,
        success: function(jsonData){
        $('#live_edit_feedback_str').html('<div class="live_edit_feed_back_ok" style="text-align:center; padding-top: 33px;"><img src="' + baseDir + 'img/admin/ok2.png"><h3>' + saveOK + '</h3><a style="margin:auto" class="exclusive" href="#" onclick="closeFancybox();">' + close + '</a></div>');
            timer = setTimeout("hideFeedback()", 3000);
            has_been_moved = false;
        },
        error: function(XMLHttpRequest, textStatus, errorThrown) {
            $('#live_edit_feedback_str').html('<div class="live_edit_feed_back_ko" style="text-align:center; padding-top: 33px;"><img src="' + baseDir + 'img/admin/error.png"><h3>TECHNICAL ERROR:</h3>' + unableToSaveModulePosition + '<br><br><a style="margin:auto" class="button" href="#" onclick="closeFancybox();">' + close + '</a></div>');
        }
}
)

	return true;
}
function closeFancybox()
{
	clearTimeout(timer);
	$.fancybox.close();
	$('#live_edit_feedback_str').html('');
}
function closeLiveEdit()
{
	window.location.href = window.location.protocol+'//'+window.location.host+window.location.pathname;
}
function hideFeedback()
{
	$('#live_edit_feed_back').fadeOut('slow', function() {
		$.fancybox.close();
		$('#live_edit_feedback_str').html('');
	});
};

function get(name)
{
	var regexS = "[\\?&]" + name + "=([^&#]*)";
	var regex = new RegExp(regexS);
	var results = regex.exec(window.location.href);
	if (results == null) {
		return "";
	}
	else {
		return results[1];
	}
}
