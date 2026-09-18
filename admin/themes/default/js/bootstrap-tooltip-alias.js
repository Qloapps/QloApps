/* Preserve Bootstrap 3's tooltip before jQuery UI overwrites $.fn.tooltip. */
(function ($) {
    'use strict';

    if ($ && $.fn && $.isFunction($.fn.tooltip) && $.fn.tooltip.Constructor) {
        $.fn.bootstrapTooltip = $.fn.tooltip;
    }
}(window.jQuery));
