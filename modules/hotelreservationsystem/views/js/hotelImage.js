/**
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

$(document).ready(function () {
    function initHotelImagePreview()
    {
        $('.htl-img-preview').fancybox({
            width: 'auto',
            height: 'auto',
            autoSize : false,
            maxWidth: 700,
            'hideOnContentClick': false,
        });
    }

    initHotelImagePreview();

    function openModalWithLoader(modalId)
    {
        $('#page-loader').show();
        $(modalId).modal('show');
        setTimeout(function () {
            $('#page-loader').hide();
        }, 0);
    }

    function updateBadge(delta)
    {
        var $badge = $('#hotel-images-heading .badge');
        $badge.text(Math.max(0, parseInt($badge.text(), 10) + delta));
    }

    function getCheckedImageIds()
    {
        return $('.hotel-image-checkbox:checked').map(function () {
            return $(this).val();
        }).get();
    }

    function showEmptyRowIfNoImagesLeft()
    {
        if (!$('#hotel-image-table tbody tr[data-id-image]').length) {
            $('.list-empty-tr').show();
        }
    }

    $('#open-add-hotel-images-modal').on('click', function () {
        var fileInput = document.getElementById('hotel-images-file-input');
        if (fileInput) {
            fileInput.value = '';
        }
        renderHotelImagesFileList(null);
        openModalWithLoader('#addHotelImagesModal');
    });

    $('#hotel-images-file-add-btn').on('click', function () {
        $('#hotel-images-file-input').trigger('click');
    });

    $(document).on('change', '#hotel-images-file-input', function () {
        renderHotelImagesFileList(this.files);
    });

    $(document).on('click', '.hotel-images-remove-file', function () {
        var removeIdx = parseInt($(this).data('index'), 10);
        var fileInput = document.getElementById('hotel-images-file-input');
        if (!fileInput || !fileInput.files) {
            return;
        }
        try {
            var dt = new DataTransfer();
            for (var i = 0; i < fileInput.files.length; i++) {
                if (i !== removeIdx) {
                    dt.items.add(fileInput.files[i]);
                }
            }
            fileInput.files = dt.files;
        } catch (e) {
            // ponytail: DataTransfer unsupported in this browser, list just won't reflect the removal
        }
        renderHotelImagesFileList(fileInput.files);
    });

    function renderHotelImagesFileList(files)
    {
        var $list = $('#hotel-images-files-list').empty();
        if (!files || !files.length) {
            $list.hide();
            return;
        }
        $list.show();
        for (var i = 0; i < files.length; i++) {
            (function (idx, file) {
                var size = file.size < 1048576
                    ? (file.size / 1024).toFixed(1) + ' KB'
                    : (file.size / 1048576).toFixed(1) + ' MB';
                $list.append(
                    $('<li class="hotel-images-file-row"/>')
                        .append($('<span/>').text(file.name + ' (' + size + ')'))
                        .append(
                            $('<button type="button" class="btn btn-default btn-xs hotel-images-remove-file"/>')
                                .attr('data-index', idx)
                                .html('<i class="icon-trash"></i>')
                        )
                );
            })(i, files[i]);
        }
    }

    $('#hotel-images-upload-btn').on('click', function () {
        var fileInput = document.getElementById('hotel-images-file-input');
        var files = fileInput ? Array.prototype.slice.call(fileInput.files) : [];
        if (!files.length) {
            showErrorMessage(imgSelectErrorMsg);
            return;
        }
        uploadHotelImagesSequentially(files);
    });

    function uploadHotelImagesSequentially(files)
    {
        var index = 0;
        var idHotel = $('#id-hotel').val();
        var idHtlImageCategory = $('#id_htl_image_category').val();
        var $btn = $('#hotel-images-upload-btn').prop('disabled', true);

        $('#page-loader').show();

        function next() {
            if (index >= files.length) {
                $btn.prop('disabled', false);
                $('#page-loader').hide();
                $('#addHotelImagesModal').modal('hide');
                return;
            }

            var file = files[index++];
            var formData = new FormData();
            formData.append('hotel_image', file);
            formData.append('ajax', true);
            formData.append('action', 'UploadHotelImage');
            formData.append('id_hotel', idHotel);
            formData.append('id_htl_image_category', idHtlImageCategory);

            $.ajax({
                type: 'POST',
                url: adminHotelCtrlUrl,
                dataType: 'json',
                data: formData,
                processData: false,
                contentType: false,
                success: function (resp) {
                    if (resp && resp.success) {
                        $('.list-empty-tr').hide();
                        $('#hotel-image-table tbody').append(resp.image_row);
                        updateBadge(1);
                        initHotelImagePreview();
                        showSuccessMessage(imgUploadSuccessMsg);
                    } else {
                        showErrorMessage(file.name + ': ' + ((resp && resp.errors && resp.errors.join(', ')) || imgUploadErrorMsg));
                    }
                    next();
                },
                error: function () {
                    showErrorMessage(file.name + ': ' + imgUploadErrorMsg);
                    next();
                }
            });
        }
        next();
    }

    $('body').on('click', '.changer-cover-image', function(e){
        e.preventDefault();
        var idHotel = $(this).attr('data-id-hotel');
        var idImage = $(this).attr('data-id-image');
        var isCover = $(this).attr('data-is-cover');
        var triggerElement = $(this);
        if (isCover == 0) {
            $.ajax({
                type:'POST',
                url: adminHotelCtrlUrl,
                dataType: 'json',
                data: {
                    ajax: true,
                    action: 'changeCoverImage',
                    id_hotel: idHotel,
                    id_image: idImage,
                },
                success: function(result) {
                    if (result.status) {
                        // remover cover image identifier from old cover image
                        var oldCoverImageTr = $("#hotel-image-table tbody tr.cover-image-tr");
                        oldCoverImageTr.removeClass("cover-image-tr").find("td.cover-image-td").removeClass("cover-image-td").find("a.changer-cover-image").removeClass("text-success").addClass("text-danger").attr("data-is-cover", "0").find("i.icon-check").removeClass("icon-check").addClass("icon-times");
                        oldCoverImageTr.find("td button.delete-hotel-image").attr("data-is-cover", "0");
                        oldCoverImageTr.find("td button.edit-hotel-image").attr("data-is-cover", "0");

                        // Add classes in new covre image elements
                        triggerElement.removeClass("text-danger").addClass("text-success").attr("data-is-cover", "1").find("i.icon-times").removeClass("icon-times").addClass("icon-check");
                        triggerElement.parent().addClass("cover-image-td").parent().addClass("cover-image-tr").find("td button.delete-hotel-image").attr("data-is-cover", "1");
                        triggerElement.parent().parent().find("td button.edit-hotel-image").attr("data-is-cover", "1");

                        showSuccessMessage(coverImgSuccessMsg);
                    } else {
    					showErrorMessage(coverImgErrorMsg);
                    }
                },
                error: function(data){
   					showErrorMessage(coverImgErrorMsg);
                }
            });
        }
    });

    $('body').on('click', '.edit-hotel-image', function () {
        var idImage = $(this).data('id-image');
        var idCategory = parseInt($(this).data('id-htl-image-category'), 10) || 0;
        var isCover = parseInt($(this).attr('data-is-cover'), 10) === 1;

        var $categorySelect = $('#edit_id_htl_image_category');

        $('#edit_hotel_image_id').val(idImage);
        $categorySelect.val($categorySelect.find('option[value="' + idCategory + '"]').length ? idCategory : 0);
        $('#edit_hotel_image_cover_on').prop('checked', isCover);
        $('#edit_hotel_image_cover_off').prop('checked', !isCover);

        openModalWithLoader('#editHotelImageModal');
    });

    $('#save-edit-hotel-image-btn').on('click', function () {
        var idImage = $('#edit_hotel_image_id').val();
        var idHotel = $('#id-hotel').val();
        var $btn = $(this).prop('disabled', true);

        $('#page-loader').show();

        $.ajax({
            type: 'POST',
            url: adminHotelCtrlUrl,
            dataType: 'json',
            data: {
                ajax: true,
                action: 'EditHotelImage',
                id_hotel: idHotel,
                id_image: idImage,
                id_htl_image_category: $('#edit_id_htl_image_category').val(),
                cover: $('input[name="edit_hotel_image_cover"]:checked').val()
            },
            success: function (resp) {
                if (resp && resp.status) {
                    $('#hotel-image-table tr[data-id-image="' + idImage + '"]').replaceWith(resp.image_row);
                    if (resp.old_cover_row) {
                        $('#hotel-image-table tr[data-id-image="' + resp.old_cover_id + '"]').replaceWith(resp.old_cover_row);
                    }
                    initHotelImagePreview();
                    showSuccessMessage(imgUpdateSuccessMsg);
                    $('#editHotelImageModal').modal('hide');
                } else {
                    showErrorMessage((resp && resp.errors && resp.errors.join('<br>')) || imgUploadErrorMsg);
                }
            },
            error: function () {
                showErrorMessage(imgUploadErrorMsg);
            }
        }).always(function () {
            $btn.prop('disabled', false);
            $('#page-loader').hide();
        });
    });

    $('#hotel-image-select-all').on('click', function (e) {
        e.preventDefault();
        $('.hotel-image-checkbox').prop('checked', true);
    });

    $('#hotel-image-unselect-all').on('click', function (e) {
        e.preventDefault();
        $('.hotel-image-checkbox').prop('checked', false);
    });

    $('#hotel-image-bulk-update-category').on('click', function (e) {
        e.preventDefault();
        if (!$('.hotel-image-checkbox:checked').length) {
            showErrorMessage(bulkUpdateSelectErrorMsg);
            return;
        }

        $('#bulk_id_htl_image_category').val(0);
        openModalWithLoader('#bulkUpdateHotelImageCategoryModal');
    });

    var pendingBulkDeleteImageIds = null;

    $('#hotel-image-bulk-delete').on('click', function (e) {
        e.preventDefault();
        var imageIds = getCheckedImageIds();
        if (!imageIds.length) {
            showErrorMessage(bulkDeleteSelectErrorMsg);
            return;
        }
        pendingBulkDeleteImageIds = imageIds;
        $('#confirmDeleteHotelImageModal').modal('show');
    });

    $('#save-bulk-hotel-image-category-btn').on('click', function () {
        var idHotel = $('#id-hotel').val();
        var idHtlImageCategory = $('#bulk_id_htl_image_category').val();
        var imageIds = getCheckedImageIds();
        var $btn = $(this).prop('disabled', true);

        $('#page-loader').show();

        $.ajax({
            type: 'POST',
            url: adminHotelCtrlUrl,
            dataType: 'json',
            data: {
                ajax: true,
                action: 'BulkUpdateHotelImageCategory',
                id_hotel: idHotel,
                id_htl_image_category: idHtlImageCategory,
                image_ids: imageIds
            },
            success: function (resp) {
                if (resp && resp.status) {
                    $.each(resp.image_ids, function (i, idImage) {
                        var $row = $('#hotel-image-table tr[data-id-image="' + idImage + '"]');
                        $row.find('.hotel-image-category-cell').text(resp.category_name || '—');
                        $row.find('.edit-hotel-image').attr('data-id-htl-image-category', idHtlImageCategory);
                    });
                    $('.hotel-image-checkbox').prop('checked', false);
                    showSuccessMessage(imgUpdateSuccessMsg);
                    $('#bulkUpdateHotelImageCategoryModal').modal('hide');
                } else {
                    showErrorMessage((resp && resp.errors && resp.errors.join('<br>')) || imgUploadErrorMsg);
                }
            },
            error: function () {
                showErrorMessage(imgUploadErrorMsg);
            }
        }).always(function () {
            $btn.prop('disabled', false);
            $('#page-loader').hide();
        });
    });

    var pendingDeleteHotelImage = null;

    $('body').on('click', '.delete-hotel-image', function (e) {
        e.preventDefault();
        pendingDeleteHotelImage = $(this);
        pendingBulkDeleteImageIds = null;
        $('#confirmDeleteHotelImageModal').modal('show');
    });

    $('#confirm-delete-hotel-image-btn').on('click', function () {
        $('#confirmDeleteHotelImageModal').modal('hide');

        if (pendingBulkDeleteImageIds && pendingBulkDeleteImageIds.length) {
            var bulkImageIds = pendingBulkDeleteImageIds;
            var bulkIdHotel = $('#id-hotel').val();
            pendingBulkDeleteImageIds = null;

            $('#page-loader').show();
            $.ajax({
                type: 'POST',
                url: adminHotelCtrlUrl,
                dataType: 'json',
                data: {
                    ajax: true,
                    action: 'BulkDeleteHotelImage',
                    id_hotel: bulkIdHotel,
                    image_ids: bulkImageIds
                },
                success: function (result) {
                    if (result.status) {
                        $.each(result.deleted_ids, function (i, idImage) {
                            $('#hotel-image-table tr[data-id-image="' + idImage + '"]').fadeOut().remove();
                        });
                        if (result.new_cover_row) {
                            $('#hotel-image-table tr[data-id-image="' + result.new_cover_id + '"]').replaceWith(result.new_cover_row);
                            initHotelImagePreview();
                        }
                        showEmptyRowIfNoImagesLeft();
                        updateBadge(-result.deleted_ids.length);
                        $('.hotel-image-checkbox').prop('checked', false);
                        showSuccessMessage(deleteImgSuccessMsg);
                    } else {
                        showErrorMessage((result.errors && result.errors.join('<br>')) || deleteImgErrorMsg);
                    }
                },
                error: function () {
                    showErrorMessage(deleteImgErrorMsg);
                }
            }).always(function () {
                $('#page-loader').hide();
            });
            return;
        }

        var triggerElement = pendingDeleteHotelImage;
        if (!triggerElement) {
            return;
        }

        var idHotel = triggerElement.attr('data-id-hotel');
        var idImage = triggerElement.attr('data-id-image');

        if (parseInt(idHotel) && parseInt(idImage)) {
            $.ajax({
                type:'POST',
                url: adminHotelCtrlUrl,
                dataType: 'json',
                data: {
                    ajax: true,
                    action: 'deleteHotelImage',
                    id_hotel: idHotel,
                    id_image: idImage,
                },
                success: function(result) {
                    if (result.status) {
                        triggerElement.parents('tr').fadeOut().remove();

                        if (result.new_cover_row) {
                            $('#hotel-image-table tr[data-id-image="' + result.new_cover_id + '"]').replaceWith(result.new_cover_row);
                            initHotelImagePreview();
                        }

                        showEmptyRowIfNoImagesLeft();
                        updateBadge(-1);

                        showSuccessMessage(deleteImgSuccessMsg);
                    } else {
                        showErrorMessage(deleteImgErrorMsg);
                    }
                },
                error: function(data){
                    showErrorMessage(deleteImgErrorMsg);
                }
            });
        } else {
            showErrorMessage(deleteImgErrorMsg);
        }
    });
});
