/**
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Academic Free License (AFL 3.0)
 * that is bundled with this package in the file LICENSE.md
 * It is also available through the world-wide-web at this URL:
 * https://opensource.org/licenses/afl-3.0.php
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
 * @license https://opensource.org/licenses/afl-3.0.php Academic Free License 3.0
 */

$(document).ready(function() {
    $('.ics_date_to_icon').on('click', function() {
        $("#ics_date_to").focus();
    });
    $('.ics_date_from_icon').on('click', function() {
        $("#ics_date_from").focus();
    });

    $("#ics_date_to").datepicker({
        dateFormat: 'dd-mm-yy',
        dayNamesMin: [ "Sun", "Mon", "Tue", "Wed", "Thu", "Fri", "Sat"],
        beforeShowDay: function (date) {
            return highlightSelectedDateRange(date, $("#ics_date_from").val(), $("#ics_date_to").val());
        },
        beforeShow: function (input, instance) {
            var date_to = $('#ics_date_from').val();
            if (typeof date_to != 'undefined' && date_to != '') {
                var date_format = date_to.split("-");
                var selectedDate = new Date($.datepicker.formatDate('yy-mm-dd', new Date(date_format[2], date_format[1] - 1, date_format[0])));
                selectedDate.setDate(selectedDate.getDate()+1);
                $("#ics_date_to").datepicker("option", "minDate", selectedDate);
            } else {
                var date_format = new Date();
                var selectedDate = new Date($.datepicker.formatDate('yy-mm-dd', new Date()));
                selectedDate.setDate(selectedDate.getDate()+1);
                $("#ics_date_to").datepicker("option", "minDate", selectedDate);
            }
        }
    });

    $("#ics_date_from").datepicker({
        dateFormat: 'dd-mm-yy',
        dayNamesMin: [ "Sun", "Mon", "Tue", "Wed", "Thu", "Fri", "Sat"],
        beforeShowDay: function (date) {
            return highlightSelectedDateRange(date, $("#ics_date_from").val(), $("#ics_date_to").val());
        },
        onClose: function() {
            var checkOut = $("#ics_date_to").val();
            var date = $("#ics_date_from").val();
            var dateFormat = date.split("-");
            var selectedDate = new Date(
                $.datepicker.formatDate('yy-mm-dd', new Date(dateFormat[2], dateFormat[1] - 1, dateFormat[0]))
            );
            selectedDate.setDate(selectedDate.getDate() + 1);
            $("#ics_date_to").datepicker("option", "minDate", selectedDate);

            /* open datepicker of chechout date only if
            checkout date is empty or checkin selected is equal or more than check out date */
            if (checkOut == '') {
                $("#ics_date_to").datepicker( "show" );
            } else {
                // Lets make the date in the required format
                selectedDate.setDate(selectedDate.getDate() - 1);
                var currentDate = selectedDate.getDate();
                var currentMonth = selectedDate.getMonth() + 1;
                if (currentMonth < 10) {
                    currentMonth = '0' + currentMonth;
                }
                if (currentDate < 10) {
                    currentDate = '0' + currentDate;
                }

                dmy = selectedDate.getFullYear() + "-" + currentMonth + "-" + currentDate;
                checkOut = checkOut.split("-");
                checkOut = (checkOut[2]) + '-' + (checkOut[1]) + '-' + (checkOut[0]);

                if (checkOut <= dmy) {
                    $("#ics_date_to").datepicker('show');
                }
            }
        }
    });

    var ajaxVar = ''

    $('#submitIcsExport').click(function(e) {
        e.preventDefault();
        var $element = $(this);
        $('#ics_export_form .form-control').removeClass('error-border');

        var form = $('#ics_export_form');
        form.find('.field-error').html('').hide();

        abortRunningAjax(ajaxVar);

        ajaxVar = $.ajax({
            url: ctrlLink,
            data: form.serialize()+'&ajax=true&action=validateIcsForm',
            method: 'POST',
            dataType: 'JSON',
            success: function(result) {
                if (result.status == 1) {
                    $('#ics_export_form').submit();
                } else {
                    if (typeof result.errors !== 'undefined') {
                        $.each(result.errors, function(key, value) {
                            $('#'+key).addClass('error-border');
                            $('#'+key).closest('.form-group').find('.field-error').html('* '+value).show();
                        });
                    }

                    if (typeof result.errors.ics_no_bookings !== 'undefined' && result.errors.ics_no_bookings) {
                        showErrorMessage(result.errors.ics_no_bookings);
                    }
                }
            }
        });
    });
});


function abortRunningAjax(ajaxVar) {
    if (ajaxVar) {
        ajaxVar.abort();
    }
}
// highlight dates of the selected date range
function highlightSelectedDateRange(date, checkIn, checkOut)
{
    if (checkIn || checkOut) {
        // Lets make the date in the required format
        var currentDate = date.getDate();
        var currentMonth = date.getMonth()+1;
        if (currentMonth < 10) {
            currentMonth = '0' + currentMonth;
        }
        if (currentDate < 10) {
            currentDate = '0' + currentDate;
        }
        dmy = date.getFullYear() + "-" + currentMonth + "-" + currentDate;

        if (checkIn) {
            checkIn = checkIn.split("-");
            checkIn = (checkIn[2]) + '-' + (checkIn[1]) + '-' + (checkIn[0]);
        }
        if (checkOut) {
            checkOut = checkOut.split("-");
            checkOut = (checkOut[2]) + '-' + (checkOut[1]) + '-' + (checkOut[0]);
        }

        if (dmy == checkIn || dmy == checkOut) {
            return [true, 'selectedCheckedDate', ''];
        } else if ((checkIn && checkOut) && (dmy >= checkIn && dmy <= checkOut)) {
            return [true, 'in-select-date-range', ''];
        }
    }

    return [true, ''];
}