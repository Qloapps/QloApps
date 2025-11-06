/*
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
    $("#date_from").datepicker({
        showOtherMonths: true,
        dateFormat: 'yy-mm-dd',
        onClose: function() {
            let dateFrom = $('#date_from').val().trim();
            let dateTo = $('#date_to').val().trim();

            if (dateFrom >= dateTo) {
                let objDateToMin = $.datepicker.parseDate('yy-mm-dd', dateFrom);
                objDateToMin.setDate(objDateToMin.getDate());

                $('#date_to').datepicker('option', 'minDate', objDateToMin);
            }
        },
    });

    $("#date_to").datepicker({
        showOtherMonths: true,
        dateFormat: 'yy-mm-dd',
        beforeShow: function() {
            let dateFrom = $('#date_from').val().trim();

            if (typeof dateFrom != 'undefined' && dateFrom != '') {
                let objDateToMin = $.datepicker.parseDate('yy-mm-dd', dateFrom);
                objDateToMin.setDate(objDateToMin.getDate());

                $('#date_to').datepicker('option', 'minDate', objDateToMin);
            }
        },
    });
});