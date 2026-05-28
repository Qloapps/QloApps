/**
 * QloHotelReports — Stats module JS
 *
 * Preserves report_group and report params when the calendar date-picker
 * form is submitted, so the active tab is not lost after a date range change.
 */
$(document).ready(function () {
    function getParam(name) {
        var match = window.location.search.match(new RegExp('[?&]' + name + '=([^&]*)'));
        return match ? match[1] : null;
    }

    var reportGroup = getParam('report_group') || 'bookings';
    var report      = getParam('report');

    $('form[action*="AdminStats"]').each(function () {
        var $form = $(this);
        if (!$form.find('input[name="report_group"]').length) {
            $form.append($('<input>').attr({ type: 'hidden', name: 'report_group', value: reportGroup }));
        }
        if (report && !$form.find('input[name="report"]').length) {
            $form.append($('<input>').attr({ type: 'hidden', name: 'report', value: report }));
        }
    });
});
