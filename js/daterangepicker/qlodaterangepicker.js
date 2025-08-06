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
            let container = $(this).parent();
            container.css({ position: 'relative' });

            const custom_opt = {
                format: 'DD-MM-YYYY',
                showTopbar: false,
                autoClose: true,
                singleMonth: true,
                language: lang_iso,
                minDays: 2,
                startOfWeek: 'monday',
                hoveringTooltip: false,
                container: container,
                inline: true,
                customArrowPrevSymbol: '<i class="icon icon-angle-left"></i>',
                customArrowNextSymbol: '<i class="icon icon-angle-right"></i>',
                getValue: function() {
                    return $(this).find('span').html();
                },
                setValue: function(s) {
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

        const dateRangePickerInput = dateRangePickerOrg.apply(this, args);
        const calendarDom = $(dateRangePickerInput).data('dateRangePicker').getDatePicker();

        dateRangePickerInput.on('datepicker-open', function() {
            const positionClass = getPositionClass(dateRangePickerInput, calendarDom);
            $(calendarDom).removeClass('top bottom').addClass(positionClass);
            setPosition(dateRangePickerInput, calendarDom, positionClass)
        });

        return dateRangePickerInput;

        // helper function definitions
        function getPositionClass(dateRangePickerInput, calendarDom) {
            const inputElementHeight = dateRangePickerInput.outerHeight();
            const spaceTop = dateRangePickerInput.offset().top - $(window).scrollTop();
            const spaceBottom = $(window).height() - inputElementHeight - spaceTop;
            const maxHeightNeeded = $(calendarDom).get(0).scrollHeight;

            let positionClass = 'bottom';
            // determine position class
            if (spaceBottom < maxHeightNeeded && spaceTop > spaceBottom) {
                positionClass = 'top';
            }

            return positionClass;
        }

        function setPosition(dateRangePickerInput, calendarDom, positionClass) {
            const inputElementHeight = dateRangePickerInput.outerHeight();

            const css = {};
            if (positionClass == 'top') {
                css.top = 'unset';
                css.bottom = inputElementHeight;
            } else {
                css.bottom = 'unset';
                css.top = $(dateRangePickerInput).position().top + inputElementHeight;
            }

            $(calendarDom).css(css);
        }
    }
})(jQuery);
