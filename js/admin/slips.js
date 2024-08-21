/*
* Since 2010 Webkul.
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
*  @copyright Since 2010 Webkul IN
*  @license   https://store.webkul.com/license.html
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

    $('.change_status').on('click', function (e) {
        e.preventDefault();
        var id_order_slip = $.trim($(this).closest('tr').find('td[data-key="id_order_slip"]').text());
        processupdate(id_order_slip, $(this).attr('href'))
        return false;
    });

    function processupdate(id_order_slip, link)
    {
        confirmDelete(id_order_slip).then((toUpdate) => {
            if (toUpdate) {
                window.location = link;
            }
        });

    }

    function confirmDelete(id_order_slip)
    {
        return new Promise((resolve) => {
            $.ajax({
                type: 'POST',
                url: admin_order_slip_tab_link,
                dataType: 'JSON',
                cache: false,
                data: {
                    ajax: true,
                    action: 'initSlipStatusModal',
                    id_order_slip: id_order_slip
                },
                success: function(result) {
                    if (result.success && result.modalHtml) {
                        $('#moduleConfirmUpdate').remove();
                        $('#footer').next('.bootstrap').append(result.modalHtml);
                        $('#moduleConfirmUpdate').modal('show');
                        $('#moduleConfirmUpdate .process_update').click(() => {
                            resolve(true);
                        });
                        $('#moduleConfirmUpdate .btn-close').click(() => {
                            resolve(false);
                        });
                    } else {
                        resolve(true);
                    }
                },
                complete: function() {
                    $(".loading_overlay").hide();
                }
            });
        });
    }
});