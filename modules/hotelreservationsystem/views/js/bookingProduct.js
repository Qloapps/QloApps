/*
 * 2010-2016 Webkul.
 *
 * NOTICE OF LICENSE
 *
 * All right is reserved,
 * Please go through this link for complete license : https://store.webkul.com/license.html
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this module to newer
 * versions in the future. If you wish to customize this module for your
 * needs please refer to https://store.webkul.com/customisation-guidelines/ for more information.
 *
 *  @author    Webkul IN <support@webkul.com>
 *  @copyright 2010-2016 Webkul IN
 *  @license   https://store.webkul.com/license.html
 */

$(document).ready(function() {
  $(document).on("click", '.submitBookingProduct', function(e) {
    //get all checked category value in a input hidden type name 'product_category'
    var rawCheckedID = [];
    $('.jstree-clicked').each(function() {
        var rawIsChecked = $(this).parent('.jstree-node').attr('id');
        rawCheckedID.push(rawIsChecked);
    });
        
    $('#product_category').val(rawCheckedID.join(","));

    var checkbox_length = $('#product_category').val();
    if (checkbox_length == 0) {
        alert('choose category');
        return false;
    }
  });

  // select which type of booking is
  $('#booking_type').on('change', function() {
    if ($(this).val() == 1) {
      $('.booking_price_period').text(day_text);
    } else if ($(this).val() == 2) {
      $('.booking_price_period').text(slot_text);
    }
  });

  //date range row append
  $(document).on('click', '#add_more_date_ranges', function() {
    var date_ranges_length = $('.booking_date_ranges').length;
    html = '<div class="single_date_range_slots_container" date_range_slot_num="'+date_ranges_length+'">';
      html += '<div  class="form-group table-responsive-row col-sm-6">';
        html += '<table class="table">';
          html += '<thead>';
            html += '<tr>';
              html += '<th class="center">';
                html += '<span>'+'Date From'+'</span>';
              html += '</th>';
              html += '<th class="center">';
                html += '<span>'+'Date To'+'</span>';
              html += '</th>';
            html += '</tr>';
          html += '</thead>';
          html += '<tbody>';
            html += '<tr>';
              html += '<td class="center">';
                html += '<div class="input-group">';
                  html += '<input autocomplete="off" class="form-control sloting_date_from" type="text" name="sloting_date_from[]" value="" readonly>';
                  html += '<span class="input-group-addon">';
                    html += '<i class="icon-calendar"></i>';
                  html += '</span>';
                html += '</div>';
              html += '</td>';

              html += '<td class="center">';
                html += '<div class="input-group">';
                  html += '<input autocomplete="off" class="form-control sloting_date_to" type="text" name="sloting_date_to[]" value="" readonly>';
                  html += '<span class="input-group-addon">';
                    html += '<i class="icon-calendar"></i>';
                  html += '</span>';
                html += '</div>';
              html += '</td>';
            html += '</tr>';
          html += '</tbody>';
        html += '</table>';
      html += '</div>';
      html += '<div  class="form-group table-responsive-row col-sm-6 time_slots_prices_table_div">  ';
        html += '<table class="table time_slots_prices_table">';
          html += '<thead>';
            html += '<tr>';
              html += '<th class="center">';
                html += '<span>'+'Slot Time From'+'</span>';
              html += '</th>';
              html += '<th class="center">';
                html += '<span>'+'Slot Time To'+'</span>';
              html += '</th>';
              html += '<th class="center">';
                html += '<span>'+'Price'+'</span>';
              html += '</th>';
            html += '</tr>';
          html += '</thead>';
          html += '<tbody>';
            html += '<tr>';
              html += '<td class="center">';
                html += '<div class="input-group">';
                  html += '<input id="booking_time_from" autocomplete="off" class="booking_time_from" type="text" name="booking_time_from'+date_ranges_length+'[]" value="" readonly>';
                  html += '<span class="input-group-addon">';
                    html += '<i class="icon-calendar"></i>';
                  html += '</span>';
                html += '</div>';
              html += '</td>';
              html += '<td class="center">';
                html += '<div class="input-group">';
                  html += '<input autocomplete="off" class="form-control booking_time_to" type="text" name="booking_time_to'+date_ranges_length+'[]" value="" readonly>';
                  html += '<span class="input-group-addon">';
                    html += '<i class="icon-calendar"></i>';
                  html += '</span>';
                html += '</div>';
              html += '</td>';
              html += '<td class="center">';
                html += '<div class="input-group">';
                  html += '<input type="text" name="slot_range_price'+date_ranges_length+'[]" value="'+Math.round($('#product_price').val())+'">';
                  html += '<span class="input-group-addon">'+defaultCurrencySign+'</span>';
                html += '</div>';
              html += '</td>';
              html += '<td class="center">';
                html += '<a href="#" class="remove_date_ranges btn btn-default"><i class="icon-trash"></i></a>';
              html += '</td>';
            html += '</tr>';
          html += '</tbody>';
        html += '</table>';
        html += '<div class="form-group">';
          html += '<div class="col-lg-12">';
            html += '<button class="add_more_time_slot_price" class="btn btn-default" type="button" data-size="s" data-style="expand-right">';
              html += '<i class="icon-calendar-empty"></i>'+'&nbsp;Add More Slots';
            html += '</button>';
          html += '</div>';
        html += '</div>';
      html += '</div>   ';
    html += '</div>';
    $('.time_slots_prices_content').append(html);
  });

  //time slots row append
  $(document).on('click', '.add_more_time_slot_price', function() {
    var date_ranges_length = $(this).closest('.single_date_range_slots_container').attr('date_range_slot_num');
    html = '<tr>';
      html += '<td class="center">';
        html += '<div class="input-group">';
          html += '<input autocomplete="off" class="form-control booking_time_from" type="text" name="booking_time_from'+date_ranges_length+'[]" readonly>';
          html += '<span class="input-group-addon">';
            html += '<i class="icon-calendar"></i>';
          html += '</span>';
        html += '</div>';
      html += '</td>';
      html += '<td class="center">';
        html += '<div class="input-group">';
          html += '<input autocomplete="off" class="form-control booking_time_to" type="text" name="booking_time_to'+date_ranges_length+'[]" readonly>';
          html += '<span class="input-group-addon">';
            html += '<i class="icon-calendar"></i>';
          html += '</span>';
        html += '</div>';
      html += '</td>';
      html += '<td class="center">';
        html += '<div class="input-group">';
          html += '<input type="text" class="form-control" name="slot_range_price'+date_ranges_length+'[]" value="'+Math.round($('#product_price').val())+'">';
          html += '<span class="input-group-addon">'+defaultCurrencySign+'</span>';
        html += '</div>';
      html += '</td>';
      html += '<td class="center">';
        html += '<a href="#" class="remove_time_slot btn btn-default"><i class="icon-trash"></i></a>';
      html += '</td>';
    html += '</tr>';

    $(this).closest('.time_slots_prices_table_div').find('.time_slots_prices_table').append(html);
  });

  //To remove a row created with add new time slots buttons
  $(document).on('click','.remove_time_slot',function(e) {
    e.preventDefault();
    $(this).closest('tr').remove();
  });

  //To remove a row created with add new date ranges buttons
  $(document).on('click','.remove_date_ranges',function(e) {
    e.preventDefault();
    $(this).closest('.single_date_range_slots_container').remove();
  });

  //date picker for date ranges
  $(document).on("focus", ".sloting_date_from, .sloting_date_to", function () {
    $(".sloting_date_from").datepicker({
      showOtherMonths: true,
      dateFormat: 'dd-mm-yy',
      minDate: 0,
      //for calender Css
      // onSelect: function(selectedDate) {
      //   var date_format = selectedDate.split("-");
      //   var selectedDate = new Date($.datepicker.formatDate('yy-mm-dd', new Date(date_format[2], date_format[1] - 1, date_format[0])));
      //   selectedDate.setDate(selectedDate.getDate() + 1);
      //   $(".sloting_date_to").datepicker("option", "minDate", selectedDate);
      // },
    });
    $(".sloting_date_to").datepicker({
      showOtherMonths: true,
      dateFormat: 'dd-mm-yy',
      minDate: 0,
      /*onSelect: function(selectedDate) {
        var date_format = selectedDate.split("-");
        var selectedDate = new Date($.datepicker.formatDate('yy-mm-dd', new Date(date_format[2], date_format[1] - 1, date_format[0])));
        selectedDate.setDate(selectedDate.getDate() - 1);
        $(".sloting_date_from").datepicker("option", "maxDate", selectedDate);
      },*/
    });
  });

  //time picker for time slots
  $(document).on("focus", ".booking_time_from, .booking_time_to", function () {
    $(".booking_time_from, .booking_time_to").timepicker({
      pickDate: false,
      datepicker: false,
      format: 'H:i'
    });
  });

  /* ----  AdminHotelFeaturePricesSettingsController Admin ---- */

    $('#date_selection_type').on('change', function() {
        if ($('#date_selection_type').val() == 2) {
            $(".specific_date_type").show();
            $(".date_range_type").hide();
            $(".special_days_content").hide();
        } else if ($('#date_selection_type').val() == 1) {
            $(".specific_date_type").hide();
            $(".date_range_type").show();
            $(".special_days_content").show();
        } else {
            $(".specific_date_type").hide();
            $(".date_range_type").show();
            $(".special_days_content").show();
        }
    });


    $(".is_special_days_exists").on ('click', function() {
        if ($(this).is(':checked')) {
            $('.week_days').show();
        } else {
            $('.week_days').hide();
        }
    });

    $('#price_impact_type').on('change', function() {
        if ($('#price_impact_type').val() == 2) {
            $(".payment_type_icon").text(defaultcurrency_sign);
        } else if ($('#price_impact_type').val() == 1) {
            $(".payment_type_icon").text('%');
        } else {
            $(".payment_type_icon").text(defaultcurrency_sign);
        }
    });

    var ajax_pre_check_var = '';
    $('.booking_product_search_results_ul').hide();

    function abortRunningAjax() {
        if (ajax_pre_check_var) {
            ajax_pre_check_var.abort();
        }
    }

    $(document).on('keyup', "#booking_product_name", function(event) {
      if (($('.booking_product_search_results_ul').is(':visible')) && (event.which == 40 || event.which == 38)) {
          $(this).blur();
          if (event.which == 40)
              $(".booking_product_search_results_ul li:first").focus();
          else if (event.which == 38)
              $(".booking_product_search_results_ul li:last").focus();
      } else {
        $('.booking_product_search_results_ul').empty().hide();

        if ($(this).val() != '') {
          abortRunningAjax();
          ajax_pre_check_var = $.ajax({
              url: autocomplete_room_search_url,
              data: {
                  room_type_name : $(this).val(),
                  action : 'SearchBookingProductByName',
                  ajax : true,
              },
              method: 'POST',
              dataType: 'JSON',
              success: function(data) {
                var html = '';
                if (data.status != 'failed') {
                  $.each(data, function(key, booking_product) {
                    html += '<li data-id_product="'+booking_product.id_product+'">'+booking_product.name+'</li>';
                  });
                  $('.booking_product_search_results_ul').html(html);
                  $('.booking_product_search_results_ul').show();
                    $('.error-block').hide();
                } else {
                  $('.error-block').show();
                }
              }
          });
        }
      }
    });

    $(document).on('click', '.booking_product_search_results_ul li', function(event) {
        $('#booking_product_name').attr('value', $(this).html());
        $('#product_id').val($(this).data('id_product'));

        $('.booking_product_search_results_ul').empty().hide();
    });

});
