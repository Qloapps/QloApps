/**
* 2010-2020 Webkul.
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
*  @copyright 2010-2020 Webkul IN
*  @license   https://store.webkul.com/license.html
*/

$(document).ready(function() {
    function close_accordion_section() {
        $('.accordion .accordion-section-title').removeClass('active');
        $('.accordion .accordion-section-content').slideUp(300).removeClass('open');
    }

    $(document).on('click', '.accordion-section-title', function(e) {
        // Grab current anchor value
        var currentAttrValue = $(this).attr('href');

        if ($(e.target).is('.active')) {
            close_accordion_section();
            $(this).find('i').removeClass('icon-angle-down');
            $(this).find('i').addClass('icon-angle-left');
        } else {
            close_accordion_section();
            // Add active class to section title
            $(this).addClass('active');
            $(this).find('i').removeClass('icon-angle-left');
            $(this).find('i').addClass('icon-angle-down');
            // Open up the hidden content panel
            $('.accordion ' + currentAttrValue).slideDown(300).addClass('open');
        }
        e.preventDefault();
    });

    $(document).on('click', '.selected_demand', function(e) {
        e.stopPropagation();
    });
});