$(document).ready(function()
{
	var ajax_check_var = '';
	$('.location_search_results_ul').hide();

    $("#check_in_time").datepicker(
    {
        showOtherMonths: true,
        dateFormat: 'dd M yy',
        minDate: 0,
        onClose: function( selectedDate ) 
        {
            var selectedDate = new Date(selectedDate);
            selectedDate.setDate(selectedDate.getDate() + 1);
            $( "#check_out_time" ).datepicker( "option", "minDate", selectedDate );
        },
    });

    $("#check_out_time").datepicker(
    {

        showOtherMonths: true,
        dateFormat: 'dd M yy',
        minDate: 0,
        onClose: function( selectedDate ) 
        {
            var selectedDate = new Date(selectedDate);
            selectedDate.setDate(selectedDate.getDate() - 1);
            $( "#check_in_time" ).datepicker( "option", "maxDate", selectedDate );
        }
    });

    function abortRunningAjax()
    {
    	if(ajax_check_var)
    	{
    		ajax_check_var.abort();
    	}
    }

    $('body').on('click', function(event)
    {
        if ($('.location_search_results_ul').is(':visible') && event.target.className != "search_result_li" && event.target.id != "hotel_location" )
            $('.location_search_results_ul').empty().hide();
    });

    $(document).on('keyup',"#hotel_location", function(event)
    {
        if (($('.location_search_results_ul').is(':visible')) && (event.which == 40 || event.which == 38))
        {
            $(this).blur();

            if (event.which == 40)
                $(".location_search_results_ul li:first").focus();
            else if (event.which == 38)
                $(".location_search_results_ul li:last").focus();
        }
        else
        {
            $('.location_search_results_ul').empty().hide();
        	
            if ($(this).val() != '')
        	{
    	    	abortRunningAjax();
    	    	ajax_check_var = $.ajax(
                {
    	    		url:autocomplete_search_url,
    	    		data:{to_search_data:$(this).val()},
    	    		method:'POST',
                    dataType:'json',
    	    		success:function(result)
    	    		{
                        if (result.status == 'success')
                        {
    	    			    $('.location_search_results_ul').html(result.data);
    	    			    $('.location_search_results_ul').show();
                        }
    	    		}
    	    	});
    	    }
        }
    });

	$(document).on('click', '.location_search_results_ul li', function(event)
    {
        $('#hotel_location').attr('value',$(this).html());
        $('#hotel_location').attr('city_cat_id',$(this).val());
        
        $('.location_search_results_ul').empty().hide();

        $.ajax({
			url:autocomplete_search_url,
    		data:{hotel_city_cat_id:$('#hotel_location').attr('city_cat_id')},
    		method:'POST',
            dataType:'json',
    		success:function(result)
    		{
                if (result.status == 'success')
                {
                    $('#hotel_cat_id').val('');
                    $('#hotel_cat_name').html('Select Hotel');
                    $('.hotel_dropdown_ul').empty();
                    $('.hotel_dropdown_ul').html(result.data);
                }
                else
                {
                    alert(no_results_found_cond);
                }
    		}
		});
	});	

	$(document).on('click', '.hotel_dropdown_ul li', function()
    {
    	var hotel_cat_id = $(this).attr('data-hotel-cat-id');
    	var hotel_name = $(this).html();

    	$('#hotel_cat_id').val(hotel_cat_id);
    	$('#hotel_cat_name').html(hotel_name);
    });

    $(".hotel_cat_id_btn").on("click", function()
    {
        if ($(this).hasClass("error_border"))
        {
            $(this).removeClass("error_border");
            $("#select_htl_error_p").empty();
        }
    });

    $("#check_in_time, #check_out_time").on("focus", function()
    {
        if ($(this).hasClass("error_border"))
        {
            $(this).removeClass("error_border");

            if ($(this).attr("name") == "check_in_time")
                $("#check_in_time_error_p").empty();
            else if ($(this).attr("name") == "check_out_time")
                $("#check_out_time_error_p").empty();
        }
    });

    $('#search_room_submit, #filter_search_btn').on('click', function(e)
    {
        var check_in_time = $("#check_in_time").val();
        var check_out_time = $("#check_out_time").val();
        var new_chk_in = $.datepicker.formatDate('yy-mm-dd', new Date(check_in_time));
        var new_chk_out = $.datepicker.formatDate('yy-mm-dd', new Date(check_out_time));
        var error = false;
    	if ($('#hotel_cat_id').val() == '')
    	{
            $(".hotel_cat_id_btn").addClass("error_border");
            $('#select_htl_error_p').text(hotel_name_cond);
            error = true;
    	}
    	if (new_chk_in == '')
    	{
            $("#check_in_time").addClass("error_border");
            $('#check_in_time_error_p').text(check_in_time_cond);
    		error = true;
    	}
        else if (new_chk_in < $.datepicker.formatDate('yy-mm-dd', new Date()))
        {
            $("#check_in_time").addClass("error_border");
            $('#check_in_time_error_p').text(less_checkin_date);
            error = true;
        }
    	if (new_chk_out == '')
    	{
            $("#check_out_time").addClass("error_border");
            $('#check_out_time_error_p').text(check_out_time_cond);
    		error = true;
    	}
        else if (new_chk_out < new_chk_in)
        {
            $("#check_out_time").addClass("error_border");
            $('#check_out_time_error_p').text(more_checkout_date);
            error = true;
        }
        if (error)
            return false;
        else
            return true;
    });

    $(document).on('keydown','body', function (e) 
    {
        if((e.which == 40 || e.which == 38) && $('.location_search_results_ul li.search_result_li').is(':focus'))
        {
            e.preventDefault();
            return false;
        }
    });

    $('body').on('keyup', '.search_result_li', function(event)
    {
        var ul_len = $('.location_search_results_ul li').length;
        if (event.which == 40 || event.which == 38) 
        {
            $(this).blur();
            $(this).closest('ul').scrollTop($(this).index() * $(this).outerHeight());
            if (event.which == 40)
            {
                if ($(this).index() != (ul_len-1))
                    $(this).next('li.search_result_li').focus();
                else
                    $(".location_search_results_ul li:first").focus();
            }
            else if (event.which == 38)
            {
                if ($(this).index())
                    $(this).prev('li.search_result_li').focus();
                else
                    $(".location_search_results_ul li:last").focus();
            }
        }
        else if (event.which == 13)
        {
            $(this).click();
        }
    });
});
