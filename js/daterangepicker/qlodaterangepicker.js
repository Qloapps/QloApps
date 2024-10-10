/**
* 2010-2022 Webkul.
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
*  @copyright 2010-2022 Webkul IN
*  @license   https://store.webkul.com/license.html
*/

(function($) {

    var dateRangePickerOrg = $.fn.dateRangePicker;
    $.fn.dateRangePicker = function(opt) {
        if(typeof opt === "object") {
            const custom_opt = {
                format: 'DD-MM-YYYY',
                showTopbar: false,
                autoClose: true,
                singleMonth: true,
                language: lang_iso,
                minDays: 2,
                startOfWeek: 'monday',
                hoveringTooltip: false,
                container: '#page',
                customArrowPrevSymbol: '<i class="icon icon-angle-left"></i>',
                customArrowNextSymbol: '<i class="icon icon-angle-right"></i>',
                getValue: function()
                {
                    return $(this).find('span').html();
                },
                setValue: function(s, s1, s2) {
                    if (s) {
                        $(this).find('span').html(s.replace('to', '&nbsp;<i class="icon icon-minus"></i>&nbsp;'));
                    } else {
                        $(this).find('span').html(
                            RangePickerCheckin + ' &nbsp;<i class="icon icon-minus"></i>&nbsp; ' + RangePickerCheckout
                        );
                    }
                }
            }
            $.each(opt, function(index) {
                delete custom_opt[index];
            });

            $.extend(true, opt, custom_opt);

        }

        var args = Array.prototype.slice.call(arguments,0);
        return dateRangePickerOrg.apply(this, args);
    }
})(jQuery);