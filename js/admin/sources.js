/**
* NOTICE OF LICENSE
*
* This source file is subject to the Open Software License version 3.0
* that is bundled with this package in the file LICENSE.md
* It is also available through the world-wide-web at this URL:
* https://opensource.org/license/osl-3-0-php
*
* @author Webkul IN
* @copyright Since 2010 Webkul
* @license https://opensource.org/license/osl-3-0-php Open Software License version 3.0
*/

$(document).on('click', '.merge-source-btn', function (e) {
    e.preventDefault();

    var $btn = $(this);

    $('.loading_overlay').show();
    $.ajax({
        type: 'POST',
        url: $btn.data('ajax-url'),
        dataType: 'JSON',
        cache: false,
        data: {
            ajax: true,
            action: 'InitMergeSourceModal',
            id_source: $btn.data('id-source'),
            id_source_type: $btn.data('id-source-type')
        },
        success: function (result) {
            if (!result.hasError && result.modalHtml) {
                $('#merge_source_modal').remove();
                $('#footer').next('.bootstrap').append(result.modalHtml);
                $('#merge_source_modal').modal('show');
            } else {
                showErrorMessage(result.error);
            }
        },
        complete: function () {
            $('.loading_overlay').hide();
        }
    });
});
