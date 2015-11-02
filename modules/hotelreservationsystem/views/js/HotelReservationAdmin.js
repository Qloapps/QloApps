$(document).ready(function()
{
    //For Add Hotels
    var i=1;
    $(".hotel-other-img").on("click", function(e){
        e.preventDefault();
        createOtherImage();
    });

    function createOtherImage()
    {
        var newdiv = document.createElement('div');
        newdiv.setAttribute("id", "childDiv" + i);
        newdiv.setAttribute("class", "htlChildDivClass");
        newdiv.innerHTML = "<div class='col-md-8'><input type='file' id='images"+i+"' name='images[]'/></div><a class='htl_more_img_remove btn btn-default button button-small'><span>"+image_remove+"</span></a>";
        var ni = document.getElementById('htl_other_images');
        ni.appendChild(newdiv);
        i++;
    }

    // Other image div remove event
    $(document).on("click", ".htl_more_img_remove", function(){
        $(this).parent(".htlChildDivClass").remove();
    });

    $('#hotel_country').on('change',function(){
        $('#hotel_state').empty();
        $.ajax({
            data:{
                id_country:$('#hotel_country').val(),
                ajax:true,
                action:'StateByCountryId'
            },
            method:'POST',
            dataType:'json',
            url:statebycountryurl,
            success:function(data)
            {
                var html = "";
                if (data)
                {
                    $.each(data,function(index, value){
                        html += "<option value="+value.id+">"+value.name+"</option>";
                    });
                }
                $('#hotel_state').append(html);
                if(html == '')
                    $(".country_import_note").show();
                else
                    $(".country_import_note").hide();
            },
            error: function(XMLHttpRequest, textStatus, errorThrown)
            {
                alert(textStatus);
            }
        });
    });
    
    $("#check_in_time").timepicker({
        pickDate:false,
        datepicker:false,
        format:'H:i'
    });

    $("#check_out_time").timepicker({
        pickDate:false,
        datepicker:false,
        format:'H:i'
    });

    // For hotel Features
    function close_accordion_section() 
    {
        $('.accordion .accordion-section-title').removeClass('active');
        $('.accordion .accordion-section-content').slideUp(300).removeClass('open');
    }
 
    $('.accordion-section-title').click(function(e) {
        // Grab current anchor value
        var currentAttrValue = $(this).attr('href');    
 
        if($(e.target).is('.active')) 
        {
            $(this).find('span').removeClass('icon-minus');
            $(this).find('span').addClass('icon-plus');
            close_accordion_section();
        }
        else 
        {
            close_accordion_section();
            // Add active class to section title
            $(this).addClass('active');
            $('.accordion-section-title').find('span').removeClass('icon-minus');
            $('.accordion-section-title').find('span').addClass('icon-plus');
            $(this).find('span').addClass('icon-minus');
            // Open up the hidden content panel
            $('.accordion ' + currentAttrValue).slideDown(300).addClass('open'); 
        }
        e.preventDefault();
    });

    $(".dlt-feature").on('click',function(e){
        e.preventDefault();
        var ftr_id = $(this).attr('data-feature-id');
        $.ajax({
            url:delete_url,
            data:
            {
                feature_id:ftr_id,
                ajax:true,
                action:'deleteFeature',
            },
            method:'POST',
            success:function(data)
            {
                if (data == 'success')
                {
                    alert(success_delete_msg);
                    $('#grand_feature_div_'+ftr_id).remove();
                }
                else
                {
                    alert(error_delete_msg);
                }
            },
            error:function(XMLHttpRequest, textStatus, errorThrown)
            {
                alert(textStatus);
            }
        });
    });

    $('.add_feature_to_list').on('click', function(){
        if ($('.child_ftr').val() != '')
        {
            var html = '<div class="child-feature-div"><div class="col-sm-3"></div><div class="col-sm-7 add_chld_ftr_div">'+$('.child_ftr').val()+'<a style="color: #555;" href="#" class="pull-right remove-chld-ftr"><i class="icon-trash"></i></a></div><input name="child_featurs[]" type="hidden" value="'+$('.child_ftr').val()+'"></div>'; 
            $('.added_feature').append(html);
        }
    });

    $('.more_feature_to_list').on('click', function(){
        if ($('.edit_child_ftr').val() != '')
        {
            var html = '<div class="child-feature-div"><div class="col-sm-3"></div><div class="edit_chld_ftr_div col-sm-7" >'+$('.edit_child_ftr').val()+'<a href="#" class="pull-right ed-remove-chld-ftr"><i class="icon-trash"></i></a></div><input name="child_featurs[]" type="hidden" value="'+$('.edit_child_ftr').val()+'"></div>'; 
            $('.added_more_feature').append(html);
        }
    });

    $('body').on('click', '.remove-chld-ftr', function(){
        $(this).parents('.child-feature-div').remove();
    });

    $('body').on('click', '.ed-remove-chld-ftr', function(){
        $(this).parents('.child-feature-div').remove();
    });

    $('.edit_feature').on('click', function(e)
    {
        e.preventDefault();
        $('.added_more_feature').empty();
        var feature = $(this).attr('data-feature');
        var dat = JSON.parse(feature);
        $('.parent_ftr').val(dat.name);
        $('.parent_ftr_id').val(dat.id);
        $('.position').val(dat.position);
        $.each(dat.children,function(key, value){
            var html = '<div class="child-feature-div"><div class="col-sm-3"></div><div class="edit_chld_ftr_div col-sm-7">'+value.name+'<a href="#" class="pull-right ed-remove-chld-ftr"><i class="icon-trash"></i></a></div><input name="child_featurs[]" type="hidden" value="'+value.id+'"></div>'; 
            $('.added_more_feature').append(html);
        });
        $('#show_edit_feature').modal('show');
    });

    /* ---- Book Now page Admin ---- */
    if (typeof(booking_calendar_data) != 'undefined')
    {
        var calendar_data = JSON.parse(booking_calendar_data);
        $(".hotel_date").datepicker(
        {
            defaultDate: new Date(),
            dateFormat: 'yy-mm-dd',
            onChangeMonthYear: function(year, month)
            {
                if (check_calender_var)
                    $.ajax({
                        url: rooms_booking_url,
                        data: {
                            ajax:true,
                            action:'getDataOnMonthChange',
                            month:month,
                            year:year,
                        },
                        method:'POST',
                        async: false,
                        success: function (result)
                        {
                            calendar_data = JSON.parse(result);
                        },
                        error: function(XMLHttpRequest, textStatus, errorThrown)
                        {
                            alert(textStatus);
                        }
                    });
            },
            beforeShowDay: function(date)
            {
                var currentMonth = date.getMonth() + 1;
                var currentDate = date.getDate();
                if (currentMonth < 10)
                {
                    currentMonth = '0' + currentMonth;
                }
                if (currentDate < 10)
                {
                    currentDate = '0' + currentDate;
                }

                dmy = date.getFullYear() + "-" + currentMonth + "-" + currentDate;
                var flag = 0;

                $.each(calendar_data, function(key, value)
                {
                    if (key === dmy)
                    {
                        msg = 'Total Available : '+value.stats.num_avail+'&#013;Total Unvailable : '+value.stats.num_unavail+'&#013;Total Booked : '+value.stats.num_booked;
                        flag = 1;
                        return 1;
                    }
                });
                if (flag)
                {
                    return [true, check_css_condition_var, msg];
                }
                else
                    return [true];
            }
        });
        
        var count = $("."+check_css_condition_var).length;
        //$("td."+check_css_condition_var).eq(0).css('border-radius','50% 0 0 50%');
        $("td."+check_css_condition_var).eq(count-1).css('border-radius','0 50% 50% 0');
    }
    else
    {
        $(".hotel_date").datepicker(
        {
            dateFormat: 'yy-mm-dd'
        });
    }

    $("#from_date, #to_date").datepicker(
    {
        dateFormat: 'yy-mm-dd'
    });

    $("#hotel_id").on('change',function()
    {
        var hotel_id = $(this).val();
        if (!isNaN(hotel_id))
        {
            if (hotel_id > 0) 
            {
                $.ajax({
                    url: rooms_booking_url,
                    type: 'POST',
                    dataType: 'json',
                    data: {
                        ajax:true,
                        action:'getRoomType',
                        hotel_id: hotel_id,
                    },
                    success: function (result)
                    {
                        $("#hotel_id option[value='0']").remove(); // to remove Select hotel option

                        $('#room_type').empty();
                        if (result)
                        {
                            html = "<option value='0'>"+opt_select_all+"</option>"; 
                            $.each(result, function(key, value)
                            {
                                html += "<option value='"+value.id_product+"'>"+value.room_type+"</option>";
                            });
                            $('#room_type').append(html);
                        }
                        else
                        {
                            html = "<option value='-1'>"+slt_another_htl+"</option>";
                            $('#room_type').append(html);
                        }
                    }
                });
            }
        }
    });

    // In RoomBookingController
    //
    // $('.avai_comment, .par_comment').hide();

    // $('.avai_bk_type').on('change', function()
    // {
    //     var id_room = $(this).attr('data-id-room');
    //     var booking_type = $(this).val();

    //     if (booking_type == 1)
    //     {
    //         $('#comment_'+id_room).hide().val('');
    //     }
    //     else if (booking_type == 2)
    //         $('#comment_'+id_room).show();
    // });

    // $('.par_bk_type').on('change', function()
    // {
    //     var id_room = $(this).attr('data-id-room');
    //     var sub_key = $(this).attr('data-sub-key');
    //     var booking_type = $(this).val();

    //     if (booking_type == 1)
    //     {
    //         $('#comment_'+id_room+'_'+sub_key).hide().val('');
    //     }
    //     else if (booking_type == 2)
    //     {
    //         $('#comment_'+id_room+'_'+sub_key).show();
    //     }
    // });

    $('body').on('click', '.avai_add_cart', function()
    {
        var search_id_prod = $("#search_id_prod").val();
        var id_prod = $(this).attr('data-id-product');
        var id_room = $(this).attr('data-id-room');
        var id_hotel = $(this).attr('data-id-hotel');
        var date_from = $(this).attr('data-date-from');
        var date_to = $(this).attr('data-date-to');
        var booking_type = $("input[name='bk_type_"+id_room+"']:checked").val();
        var comment = $("#comment_"+id_room).val();
        var btn = $(this);

        $.ajax({
            url: rooms_booking_url,
            type: 'POST',
            dataType: 'json',
            data: {
                ajax:true,
                action:'addDataToCart',
                id_prod: id_prod,
                id_room: id_room,
                id_hotel: id_hotel,
                date_from: date_from,
                date_to: date_to,
                booking_type: booking_type,
                comment: comment,
                search_id_prod: search_id_prod,
                opt: 1,
            },
            success: function (result)
            {
                if (result) 
                {
                    btn.removeClass('btn-primary').removeClass('avai_add_cart').addClass('btn-danger').addClass('avai_delete_cart_data').html(remove);
                    
                    btn.attr('data-id-cart', result.id_cart);
                    btn.attr('data-id-cart-book-data', result.id_cart_book_data);
                    html  = "<tr>";
                    html += "<td>"+result.room_num+"</td>";
                    html += "<td>"+result.room_type+"</td>";
                    html += "<td>"+result.date_from+" To "+result.date_to+"</td>";
                    html += "<td>"+currency_prefix+result.amount*result.qty+currency_suffix+"</td>";
                    html += "<td><button class='btn btn-default ajax_cart_delete_data' data-id-product='"+id_prod+"' data-id-hotel='"+id_hotel+"' data-id-cart='"+result.id_cart+"' data-id-cart-book-data='"+result.id_cart_book_data+"' data-date-from='"+date_from+"' data-date-to='"+date_to+"'><i class='icon-trash'></i></button></td>";
                    html += "</tr>";

                    $('.cart_tbody').append(html);

                    $('#cart_total_amt').html(currency_prefix+result.total_amount+currency_suffix);
                    // $('#cart_record').html(result.rms_in_cart);

                    // For Stats
                    $('#cart_record').html(result.booking_stats.stats.num_cart);
                    $("#num_avail").html(result.booking_stats.stats.num_avail);
                    $('#cart_stats').html(result.booking_stats.stats.num_cart);
                }
            }
        });
    });

    $('body').on('click', '.par_add_cart', function()
    {
        var search_id_prod = $("#search_id_prod").val();
        var id_prod = $(this).attr('data-id-product');
        var id_room = $(this).attr('data-id-room');
        var id_hotel = $(this).attr('data-id-hotel');
        var date_from = $(this).attr('data-date-from');
        var date_to = $(this).attr('data-date-to');
        
        var sub_key = $(this).attr('data-sub-key');
        var booking_type = $("input[name='bk_type_"+id_room+"_"+sub_key+"']:checked").val();
        var comment = $("#comment_"+id_room+"_"+sub_key).val();
        var btn = $(this);

        $.ajax({
            url: rooms_booking_url,
            type: 'POST',
            dataType: 'json',
            data: {
                ajax:true,
                action:'addDataToCart',
                id_prod: id_prod,
                id_room: id_room,
                id_hotel: id_hotel,
                date_from: date_from,
                date_to: date_to,
                booking_type: booking_type,
                comment: comment,
                search_id_prod: search_id_prod,
                opt: 1,
            },
            success: function (result)
            {
                if (result) 
                {
                    btn.removeClass('btn-primary').removeClass('par_add_cart').addClass('btn-danger').addClass('part_delete_cart_data').html(remove);

                    btn.attr('data-id-cart', result.id_cart);
                    btn.attr('data-id-cart-book-data', result.id_cart_book_data);

                    html  = "<tr>";
                    html += "<td>"+result.room_num+"</td>";
                    html += "<td>"+result.room_type+"</td>";
                    html += "<td>"+result.date_from+" To "+result.date_to+"</td>";
                    html += "<td>"+currency_prefix+result.amount*result.qty+currency_suffix+"</td>";
                    html += "<td><button class='btn btn-default ajax_cart_delete_data' data-id-product='"+id_prod+"' data-id-hotel='"+id_hotel+"' data-id-cart='"+result.id_cart+"' data-id-cart-book-data='"+result.id_cart_book_data+"' data-date-from='"+date_from+"' data-date-to='"+date_to+"'><i class='icon-trash'></i></button></td>";
                    html += "</tr>";

                    $('.cart_tbody').append(html);

                    $('#cart_total_amt').html(currency_prefix+result.total_amount+currency_suffix);
                    // $('#cart_record').html(result.rms_in_cart);
                    
                    // For Stats
                    $('#cart_record').html(result.booking_stats.stats.num_cart);
                    $('#cart_stats').html(result.booking_stats.stats.num_cart);
                    $("#num_part").html(result.booking_stats.stats.num_part_avai);
                }
            }
        });
    });

    $('body').on('click','.ajax_cart_delete_data',function()
    {
        //for booking_data
        var search_id_prod      = $("#search_id_prod").val();
        var search_date_from    = $("#search_date_from").val();
        var search_date_to      = $("#search_date_to").val();
        

        var ajax_delete         = 1; 
        var id_product          = $(this).attr('data-id-product');
        var id_cart             = $(this).attr('data-id-cart');
        var id_cart_book_data   = $(this).attr('data-id-cart-book-data');
        var date_from           = $(this).attr('data-date-from');
        var date_to             = $(this).attr('data-date-to');
        var id_hotel            = $(this).attr('data-id-hotel');
        var btn                 = $(this);

        $.ajax({
            url: rooms_booking_url,
            type: 'POST',
            dataType: 'json',
            data: 
            {
                ajax:true,
                action:'addDataToCart',
                id_prod: id_product,
                id_cart: id_cart,
                id_cart_book_data: id_cart_book_data,
                date_from: date_from,
                date_to: date_to,
                id_hotel: id_hotel,
                search_id_prod: search_id_prod,
                search_date_from: search_date_from,
                search_date_to: search_date_to,
                ajax_delete: ajax_delete,
                opt: 0,
            },
            success: function (result)
            {
                if (result)
                {
                    btn.parent().parent().remove();
                    $('#cart_total_amt').html(currency_prefix+result.total_amount+currency_suffix);
                    // $('#cart_record').html(result.rms_in_cart);

                    // For Stats
                    $('#cart_record').html(result.booking_data.stats.num_cart);
                    $('#cart_stats').html(result.booking_data.stats.num_cart);
                    $("#num_avail").html(result.booking_data.stats.num_avail);
                    $("#num_part").html(result.booking_data.stats.num_part_avai);
                    
                    var panel_btn = $(".tab-pane tr td button[data-id-cart-book-data='"+id_cart_book_data+"']");

                    panel_btn.attr('data-id-cart', '');
                    panel_btn.attr('data-id-cart-book-data', '');
                    
                    if (panel_btn.hasClass('avai_delete_cart_data')) 
                        panel_btn.removeClass('avai_delete_cart_data').addClass('avai_add_cart');
                    else if (panel_btn.hasClass('part_delete_cart_data'))
                         panel_btn.removeClass('part_delete_cart_data').addClass('par_add_cart');

                    panel_btn.removeClass('btn-danger').addClass('btn-primary').html(add_to_cart);

                    $("#htl_rooms_list").empty().append(result.room_tpl);
                }
            }
        });
    });

    $('body').on('click','.avai_delete_cart_data, .part_delete_cart_data',function()
    {
        var search_id_prod      = $("#search_id_prod").val();
        var id_product          = $(this).attr('data-id-product');
        var id_cart             = $(this).attr('data-id-cart');
        var id_cart_book_data   = $(this).attr('data-id-cart-book-data');
        var date_from           = $(this).attr('data-date-from');
        var date_to             = $(this).attr('data-date-to');
        var id_hotel            = $(this).attr('data-id-hotel');
        var btn                 = $(this);

        $.ajax({
            url: rooms_booking_url,
            type: 'POST',
            dataType: 'json',
            data: 
            {
                ajax:true,
                action:'addDataToCart',
                id_prod: id_product,
                id_cart: id_cart,
                id_cart_book_data: id_cart_book_data,
                date_from: date_from,
                date_to: date_to,
                search_id_prod: search_id_prod,
                id_hotel: id_hotel,
                opt: 0,
            },
            success: function (result)
            {
                if (result)
                {
                    $(".cart_tbody tr td button[data-id-cart-book-data='"+id_cart_book_data+"']").parent().parent().remove();
                    $('#cart_total_amt').html(currency_prefix+result.total_amount+currency_suffix);
                    // $('#cart_record').html(result.rms_in_cart);
                    
                    //For Stats
                    $('#cart_record').html(result.booking_stats.stats.num_cart);
                    $('#cart_stats').html(result.booking_stats.stats.num_cart);
                    $("#num_avail").html(result.booking_stats.stats.num_avail);
                    $("#num_part").html(result.booking_stats.stats.num_part_avai);

                    btn.attr('data-id-cart', '');
                    btn.attr('data-id-cart-book-data', '');

                    if (btn.hasClass('avai_delete_cart_data')) 
                        btn.removeClass('avai_delete_cart_data').addClass('avai_add_cart');
                    else if (btn.hasClass('part_delete_cart_data'))
                         btn.removeClass('part_delete_cart_data').addClass('par_add_cart');

                    btn.removeClass('btn-danger').addClass('btn-primary').html(add_to_cart);
                }
            }
        });
    });

    $('#search_hotel_list').on('click', function(e)
    {
        if ($('#from_date').val() == '')
        {
            alert(from_date_cond);
            return false;
        }
        else if ($('#to_date').val() == '')
        {
            alert(to_date_cond);
            return false;
        }
        else if ($('#hotel-id').val() == '')
        {
            alert(hotel_name_cond);
            return false;
        }
        else if ($('#num-rooms').val() == '')
        {
            alert(num_rooms_cond);
            return false;
        }
    });
    
    // var frm_date = new Date($('#from_date').val());
    // var date_to = new Date($('#to_date').val());
    // data = JSON.parse(calender_info);

    // while (frm_date.getDate() <= date_to.getDate())
    // {
    //     if (data.info['available'].length >= $('#num-rooms').val())
    //  {
    //      $('.ui-datepicker-calendar').find("td[data-month="+frm_date.getMonth()+"]").find("a:contains("+frm_date.getDate()+")").parent().css('background-color','green');
    //      var dd = frm_date.getDate()+1;
    //         var mm = frm_date.getMonth()+1;
    //         var yyyy = frm_date.getFullYear();
    //         if (mm<10)
    //         {
    //          frm_dt = yyyy+'-0'+mm+'-'+dd;
    //         }
    //         else
    //         {
    //          frm_dt = yyyy+'-'+mm+'-'+dd;
    //         }
    //      frm_date = new Date(frm_dt);
    //  }
    //  else
    //  {

    //      $('.ui-datepicker-calendar').find("td[data-month="+frm_date.getMonth()+"]").find("a:contains("+frm_date.getDate()+")").parent().css('background-color','gold');
    //      var dd = frm_date.getDate()+1;
    //         var mm = frm_date.getMonth()+1;
    //         var yyyy = frm_date.getFullYear();
    //         if (mm<10)
    //         {
    //          frm_dt = yyyy+'-0'+mm+'-'+dd;
    //         }
    //         else
    //         {
    //          frm_dt = yyyy+'-'+mm+'-'+dd;
    //         }
    //      frm_date = new Date(frm_dt);
    //  }   
    // }

    // $('.date_range_search').html($('#from_date').val()+' to '+$('#to_date').val());

    /* ---- Book Now page Admin ---- */
    
    
});