/**
* NOTICE OF LICENSE
*
* This source file is subject to the Open Software License version 3.0
* that is bundled with this package in the file LICENSE.md
* It is also available through the world-wide-web at this URL:
* https://opensource.org/license/osl-3-0-php
* If you did not receive a copy of the license and are unable to
* obtain it through the world-wide-web, please send an email
* to support@qloapps.com so we can send you a copy immediately.
*
* DISCLAIMER
*
* Do not edit or add to this file if you wish to upgrade this module to a newer
* versions in the future. If you wish to customize this module for your needs
* please refer to https://store.webkul.com/customisation-guidelines for more information.
*
* @author Webkul IN
* @copyright Since 2010 Webkul
* @license https://opensource.org/license/osl-3-0-php Open Software License version 3.0
*/

$(document).ready(function() {
    var trigger_ajax = '';
    $('#productName').on('keyup', function(event) {
        var suggestion_ul = $(this).siblings("ul.prod_suggest_ul");
        if (!((suggestion_ul.is(':visible')) && (event.which == 40 || event.which == 38))) {
            if (trigger_ajax)
                trigger_ajax.abort();

            suggestion_ul.empty().hide();
            suggestion_ul.siblings("input#id_product").val(null);

            if ($(this).val().trim().length) {
                var word = $(this).val();
                var d = new Date();
                var n = d.getTime();
                var id_hotel = parseInt($('#id_hotel').val());
                if (!isNaN(id_hotel)) {
                    trigger_ajax = $.ajax({
                        url: "ajax_products_list.php",
                        dataType: 'json',
                        data: {
                            q: word,
                            _: n,
                            booking_product: '1',
                            id_hotel: id_hotel
                        },
                        success: function(result) {
                            if (result) {
                                var html = '';
                                $.each(result, function(key, value) {
                                    html += '<li class="suggestion_li"><a class="suggestion_a text-capitalize" data-primary="' + value.id + '" data-secondary="' + value.name + '">'+value.name+' (#'+value.id+')</a></li>';
                                });
                                suggestion_ul.html(html).show();
                            } else {
                                suggestion_ul.empty().hide();
                                suggestion_ul.siblings("input#id_product").val(null);
                            }
                        }
                    });
                }
            }
        }
    });

    $('body').on('click', '.suggestion_a', function(e) {
        e.preventDefault();
        var data_primary = $(this).attr('data-primary');
        var data_secondary = $(this).attr('data-secondary');
        var suggestion_ul = $(this).parents("ul.prod_suggest_ul");

        suggestion_ul.siblings("input#id_product").val(data_primary);
        suggestion_ul.siblings("input#productName").val(data_secondary);
        suggestion_ul.empty().hide();
    });

    $('body').on('click', function(event){
        if ($('ul.prod_suggest_ul').is(':visible')) {
            $('ul.prod_suggest_ul').empty().hide();
        }
    });
    $(document).on('change', '#id_hotel', function () {
        // resetting the fields on hotel change
        $('#id_product').val('');
        $('#productName').val('');
    });
});