/**
* 2010-2019 Webkul.
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
*  @copyright 2010-2019 Webkul IN
*  @license   https://store.webkul.com/license.html
*/

$(document).ready(function() {
    $("input[name='active_adv_option']").on('change', function () {
        if (parseInt($(this).val())) {
            $(".adv_options_dtl").removeClass('hidden');
        } else {
            $(".adv_options_dtl").addClass('hidden');
            $("#" + WK_CATALOG_ORDER_REWARD + "_advertise_theme_color_div").addClass('hidden');
        }
    });

    $('body').on('click', '.remove_adv_option', function(e) {
        e.preventDefault();
        $(this).closest('tr').remove();
    });

    $('#add_more_options_button').on('click',function() {
        var choosenLangId = $('#choosedLangId').val();
        var html = '<tr class="adv_option_data_values">';
        html += '<td class="center">';
            if (languages.length > 1) {
                html += '<div class="input-group">';
                    html += '<span class="input-group-addon">';
                        html += '<img class="all_lang_icon" data-lang-id="'+choosenLangId+'" src="'+img_dir_l+choosenLangId+'.jpg">';
                    html += '</span>';
                }
                $.each(languages, function(key, language) {
                    html += '<input type="text" name="option_name_'+language.id_lang+'[]" ';
                    html += 'class="form-control wk_text_field_all wk_text_field_'+language.id_lang+'" ';
                    html += 'maxlength="128"';
                    if (currentLang.id_lang != language.id_lang) {
                        html += ' style="display:none;"';
                    }
                    html += ' />';
                });
        if (languages.length > 1) {
            html += '</div>'
        }
        html += '</td>';
        html += '<td class="center">';
            html += '<div class="input-group">';
                html += '<span class="input-group-addon">'+defaultcurrencySign+'</span>';
                    html += '<input type="text" name="option_price[]"/>';
                html += '</div>';
            html += '</td>';
            html += '<td class="center">';
                html += '<a href="#" class="remove_adv_option btn btn-default"><i class="icon-trash"></i></a>';
            html += '</td>';
        html += '</tr>';
        $('.adv_option_table').append(html);
    });
});