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
                {
                    $(".hotel_state_lbl, .hotel_state_dv").hide();
                    $(".country_import_note").show();
                }
                else
                {
                    $(".hotel_state_lbl, .hotel_state_dv").show();
                    $(".country_import_note").hide();
                }
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
        if (confirm(confirm_delete_msg))
        {
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
        }
    });

    $('.add_feature_to_list').on('click', function(){
        if ($('.child_ftr').val() != '')
        {
            $("#chld_ftr_err_p").text('');
            var html = '<div class="child-feature-div"><div class="col-sm-3"></div><div class="col-sm-7 add_chld_ftr_div">'+$('.child_ftr').val()+'<a style="color: #555;" href="#" class="pull-right remove-chld-ftr"><i class="icon-trash"></i></a></div><input class="chld_ftr_arr" name="child_featurs[]" type="hidden" value="'+$('.child_ftr').val()+'"></div>'; 
            $('.added_feature').append(html);
            $('.child_ftr').val('');
        }
        else
        {
            $("#chld_ftr_err_p").text(chld_ftr_text_err);
        }
    });

    $(".admin_submit_feature").on('click', function(e){
        var err = 0;
        $(".error_text").text('');
        if ($('.parent_ftr').val() == '')
        {
            $("#prnt_ftr_err_p").text(prnt_ftr_err);
            err = 1;
        }
        if ($('.position').val() !='' && !$.isNumeric($('.position').val()))
        {
            $("#pos_err_p").text(pos_numeric_err);
            err = 1;
        }
        if ($('.chld_ftr_arr').length < 1)
        {
            $("#chld_ftr_err_p").text(chld_ftr_err);
            err = 1;
        }
        if (err)
        {
            return false;
        }
    });

    $('body').on('click', '.remove-chld-ftr', function(){
        $(this).parents('.child-feature-div').remove();
    });

    $('#basicModal_addNewFeature').on('hidden.bs.modal', function (e) {
        $('.parent_ftr').val('');
        $('.parent_ftr_id').val('');
        $('.position').val('');
        $('.added_feature').empty();
        $(".error_text").text('');
    }); 

    $('.edit_feature').on('click', function(e)
    {
        e.preventDefault();
        $('.added_feature').empty();
        var feature = $(this).attr('data-feature');
        var dat = JSON.parse(feature);
        $('.parent_ftr').val(dat.name);
        $('.parent_ftr_id').val(dat.id);
        $('.position').val(dat.position);
        $.each(dat.children,function(key, value){
            var html = '<div class="child-feature-div"><div class="col-sm-3"></div><div class="edit_chld_ftr_div col-sm-7">'+value.name+'<a href="#" class="pull-right remove-chld-ftr"><i class="icon-trash"></i></a></div><input class="chld_ftr_arr" name="child_featurs[]" type="hidden" value="'+value.id+'"></div>'; 
            $('.added_feature').append(html);
        });
        $('#basicModal_addNewFeature').modal('show');
    });

    /* ---- Book Now page Admin ---- */
    if (typeof(booking_calendar_data) != 'undefined')
    {
        var calendar_data = JSON.parse(booking_calendar_data);
        $(".hotel_date").datepicker(
        {
            defaultDate: new Date(),
            dateFormat: 'dd M yy',
            minDate: 0,
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
                        msg = 'Total Available Rooms: '+value.stats.num_avail+'&#013;Total Rooms In cart : '+value.stats.num_cart+'&#013;Total Booked Rooms: '+value.stats.num_booked+'&#013;Total Unvailable Rooms : '+value.stats.num_part_avai;
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
        //$("td."+check_css_condition_var).eq(count-1).css('border-radius','0 50% 50% 0');
    }
    else
    {
        $(".hotel_date").datepicker(
        {
            dateFormat: 'dd M yy',
        });
    }

    $("#from_date, #to_date").datepicker(
    {
        dateFormat: 'dd M yy',
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
    /*For swaping rooms in the modal*/
    $("#realloc_allocated_rooms").on('click', function(e){
        $(".error_text").text('');
        if ($('#realloc_avail_rooms').val() == 0)
        {
            $("#realloc_sel_rm_err_p").text(slct_rm_err);
            return false;
        }
    });
    $("#swap_allocated_rooms").on('click', function(e){
        $(".error_text").text('');
        if ($('#swap_avail_rooms').val() == 0)
        {
            $("#swap_sel_rm_err_p").text(slct_rm_err);
            return false;
        }
    });

    $('#mySwappigModal').on('hidden.bs.modal', function (e)
    {
        $(".modal_date_from").val('');
        $(".modal_date_to").val('');
        $(".modal_id_room").val('');
        $(".modal_curr_room_num").val('');
        $(".cust_name").text('');
        $(".cust_email").text('');
        $(".swp_rm_opts").remove();
        $(".realloc_rm_opts").remove();
    });

    $('#mySwappigModal').on('shown.bs.modal', function (e)
    {
        $(".modal_date_from").val(e.relatedTarget.dataset.date_from);
        $(".modal_date_to").val(e.relatedTarget.dataset.date_to);
        $(".modal_id_room").val(e.relatedTarget.dataset.id_room);
        $(".modal_curr_room_num").val(e.relatedTarget.dataset.room_num);
        $(".cust_name").text(e.relatedTarget.dataset.cust_name);
        $(".cust_email").text(e.relatedTarget.dataset.cust_email);
        html = '';
        var json_arr_rm_swp = JSON.parse(e.relatedTarget.dataset.avail_rm_swap);
        $.each(json_arr_rm_swp, function(key,val)
        {
            html += '<option class="swp_rm_opts" value="'+val.id_room+'" >'+val.room_num+'</option>';
        });
        if (html != '')
            $("#swap_avail_rooms").append(html);

        html = '';
        var json_arr_rm_realloc = JSON.parse(e.relatedTarget.dataset.avail_rm_realloc);
        $.each(json_arr_rm_realloc, function(key,val)
        {
            html += '<option class="realloc_rm_opts" value="'+val.id_room+'" >'+val.room_num+'</option>';
        });
        if (html != '')
            $("#realloc_avail_rooms").append(html);
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
        var search_date_from    = $("#search_date_from").val();
        var search_date_to      = $("#search_date_to").val();

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
                search_date_from: search_date_from,
                search_date_to: search_date_to,
                opt: 1,
            },
            success: function (result)
            {
                if (result) 
                {
                    if (result.rms_in_cart)
                    {
                        $(".cart_booking_btn").removeAttr('disabled');
                    }

                    btn.removeClass('btn-primary').removeClass('avai_add_cart').addClass('btn-danger').addClass('avai_delete_cart_data').html(remove);
                    
                    btn.attr('data-id-cart', result.id_cart);
                    btn.attr('data-id-cart-book-data', result.id_cart_book_data);
                    html  = "<tr>";
                    html += "<td>"+result.room_num+"</td>";
                    html += "<td>"+result.room_type+"</td>";
                    html += "<td>"+result.date_from+" To "+result.date_to+"</td>";
                    html += "<td>"+currency_prefix+result.amount+currency_suffix+"</td>";
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
        var search_date_from    = $("#search_date_from").val();
        var search_date_to      = $("#search_date_to").val();

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
                search_date_from: search_date_from,
                search_date_to: search_date_to,
                opt: 1,
            },
            success: function (result)
            {
                if (result) 
                {
                    if (result.rms_in_cart)
                    {
                        $(".cart_booking_btn").removeAttr('disabled');
                    }
                    
                    btn.removeClass('btn-primary').removeClass('par_add_cart').addClass('btn-danger').addClass('part_delete_cart_data').html(remove);

                    btn.attr('data-id-cart', result.id_cart);
                    btn.attr('data-id-cart-book-data', result.id_cart_book_data);

                    html  = "<tr>";
                    html += "<td>"+result.room_num+"</td>";
                    html += "<td>"+result.room_type+"</td>";
                    html += "<td>"+result.date_from+" To "+result.date_to+"</td>";
                    html += "<td>"+currency_prefix+result.amount+currency_suffix+"</td>";
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
            async: false,
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
                    if (!(result.rms_in_cart))
                    {
                        $(".cart_booking_btn").attr('disabled','true');
                    }

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
        var search_date_from    = $("#search_date_from").val();
        var search_date_to      = $("#search_date_to").val();

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
                search_date_from: search_date_from,
                search_date_to: search_date_to,
                id_hotel: id_hotel,
                opt: 0,
            },
            success: function (result)
            {
                if (result)
                {
                    if (!(result.rms_in_cart))
                    {
                        $(".cart_booking_btn").attr('disabled','true');
                    }

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
    
    /* ----  HotelOrderRefundRulesController Admin ---- */

    $('#refund_payment_type').on('change', function(){
        if ($('#refund_payment_type').val() == 2)
        {
            $(".payment_type_icon").text(defaultcurrency_sign);
        }
        else if ($('#refund_payment_type').val() == 1)
        {
            $(".payment_type_icon").text('%');
        }
        else
        {
            $(".payment_type_icon").text(defaultcurrency_sign);
        }
    });

    //js for HotelOrderRefundRequestController
    $('#id_order_cancellation_stage').on('change', function(){
        if ($('#id_order_cancellation_stage').val() == 3)
        {
            $(".cancellation_charge_div").show();
        }
        else
        {
            $(".cancellation_charge_div").hide();
        }
    });

    /* ----  HotelOrderRefundRulesController Admin ---- */


    /* ----  HotelConfigurationSettingController Admin ---- */
    
    if ($('#WK_SHOW_MSG_ON_BO_on').prop('checked') === true)
    {
        $("#conf_id_WK_BO_MESSAGE").show();
    }
    else
    {
        $("#conf_id_WK_BO_MESSAGE").hide();
    }

    $('#WK_SHOW_MSG_ON_BO_on').click(function(e) 
    {
        $("#conf_id_WK_BO_MESSAGE").show();
    });

    $('#WK_SHOW_MSG_ON_BO_off').click(function(e) 
    {   
        $("#conf_id_WK_BO_MESSAGE").hide();
    });

    /* ----  HotelConfigurationSettingController Admin ---- */
});