/**
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License version 3.0
 * that is bundled with this package in the file LICENSE.md
 * It is also available through the world-wide-web at this URL:
 * https://opensource.org/license/osl-3.0-php
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
 * @license https://opensource.org/license/osl-3.0-php Open Software License version 3.0
 */

/* ---- AdminHotelAmenitiesController - Form ---- */
$(document).ready(function () {
    $('#htl_logo_type_select').on('change', function () {
        if ($(this).val() === 'icon') {
            $('#htl_logo_image_row').hide();
            $('#htl_logo_icon_row').show();
        } else {
            $('#htl_logo_icon_row').hide();
            $('#htl_logo_image_row').show();
        }
    });

    $('#logo_image-selectbutton').on('click', function () {
        $('#logo_image').trigger('click');
    });

    $('#logo_image-name').on('click', function () {
        $('#logo_image').trigger('click');
    });

    $('#logo_image').on('change', function () {
        var files = this.files;
        if (files && files.length > 0) {
            $('#logo_image-name').val(files[0].name);
        } else {
            var name = $(this).val().split(/[\\/]/);
            $('#logo_image-name').val(name[name.length - 1]);
        }
    });
});

/* ---- AdminHotelAmenitiesController - View ---- */
(function ($) {
    $(document).on('click', '.htl-delete-category', function (e) {
        e.preventDefault();
        if (!confirm(confirm_delete_msg)) { return; }
        var $btn = $(this);
        $.ajax({
            url: delete_url,
            type: 'POST',
            data: {
                ajax: 1,
                action: 'DeleteCategory',
                id_category: $btn.data('category-id'),
                token: htlAmenityToken
            },
            success: function (res) {
                var r = $.parseJSON(res);
                if (r.status) {
                    window.location = delete_url + '&conf=1';
                } else {
                    showErrorMessage(error_delete_msg);
                }
            }
        });
    });

    $(document).on('click', '.htl-delete-amenity', function (e) {
        e.preventDefault();
        if (!confirm(confirm_delete_msg)) { return; }
        var $btn = $(this);
        $.ajax({
            url: delete_url,
            type: 'POST',
            data: {
                ajax: 1,
                action: 'DeleteAmenityItem',
                id_amenity: $btn.data('amenity-id'),
                token: htlAmenityToken
            },
            success: function (res) {
                var r = $.parseJSON(res);
                if (r.status) {
                    window.location = delete_url + '&conf=1';
                } else {
                    showErrorMessage(error_delete_msg);
                }
            }
        });
    });
}(jQuery));
