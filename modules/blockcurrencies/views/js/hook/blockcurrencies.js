/**
* 2010-2022 Webkul.
*
* NOTICE OF LICENSE
*
* All right is reserved,
* Please go through LICENSE.txt file inside our module
*
* DISCLAIMER
*
* Do not edit or add to this file if you wish to upgrade this module to newer
* versions in the future. If you wish to customize this module for your
* needs please refer to CustomizationPolicy.txt file inside our module for more information.
*
* @author Webkul IN
* @copyright 2010-2022 Webkul IN
* @license LICENSE.txt
*/

$(document).ready(function() {
    $(document).on('click', '.btn-currency-selector-popup', function(e) {
        e.preventDefault();

        $.fancybox.open({
            href: '#currency-selector-popup',
            wrapCSS: 'fancybox-blockcurrencies',
            padding: 0,
            minHeight: 0,
        });
    });
});
