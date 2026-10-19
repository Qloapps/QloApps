/* Preserve jQuery UI's tooltip widget under $.fn.uiTooltip before it may be
 * overwritten (e.g. by Bootstrap re-registering $.fn.tooltip). Shared by both
 * the back office and front office, since the load order differs between the
 * two but the aliasing logic itself doesn't depend on either context. */
(function ($) {
    'use strict';

    if ($ && $.fn && $.isFunction($.fn.tooltip) && !$.fn.tooltip.Constructor) {
        $.fn.uiTooltip = $.fn.tooltip;
    }
}(window.jQuery));
