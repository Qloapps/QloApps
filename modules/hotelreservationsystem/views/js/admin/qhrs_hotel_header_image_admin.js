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

    $('#qlo-media-type').on('change', function () {
        var type = parseInt($(this).val(), 10);
        if (type === qloHmMediaTypeImage) {
            $('#qlo-video-settings').hide();
            $('#qlo-image-settings, #qlo-image-panel, #qlo-slider-info').show();
            closeImgForm();
        } else {
            closeImgForm();
            $('#qlo-image-settings, #qlo-image-panel, #qlo-slider-info').hide();
            $('#qlo-video-settings').show();
        }
    });

    $('#qlo-header-media-form').on('submit', function (e) {
        var newType = parseInt($('#qlo-media-type').val(), 10);
        if (newType === qloHmMediaTypeVideo && qloHmMediaType === qloHmMediaTypeImage) {
            var imageCount = $('#qlo-image-tbody .qlo-img-row').length;
            if (imageCount > 1 && $('#qlo-confirm-delete-images').val() !== '1') {
                e.preventDefault();
                $('#qlo-confirm-switch-video-modal').modal('show');
                return false;
            }
        }
    });

    $('#qlo-confirm-switch-video-btn').on('click', function () {
        $('#qlo-confirm-switch-video-modal').modal('hide');
        $('#qlo-confirm-delete-images').val('1');
        $('#qlo-header-media-form')[0].submit();
    });

    var pendingBulkDeleteForm   = null;
    var pendingBulkDeleteAction = null;
    var editImagePreviewUrl     = null;

    function revokeEditImagePreview() {
        if (editImagePreviewUrl) {
            URL.revokeObjectURL(editImagePreviewUrl);
            editImagePreviewUrl = null;
        }
    }

    $(document).on('click', '.qlo-bulk-delete-images-trigger', function (e) {
        e.preventDefault();
        pendingBulkDeleteForm   = $(this).closest('form').get(0);
        pendingBulkDeleteAction = $(this).data('action');
        $('#qlo-confirm-bulk-delete-modal').modal('show');
    });

    $('#qlo-confirm-bulk-delete-btn').on('click', function () {
        $('#qlo-confirm-bulk-delete-modal').modal('hide');
        if (pendingBulkDeleteForm && pendingBulkDeleteAction) {
            sendBulkAction(pendingBulkDeleteForm, pendingBulkDeleteAction);
        }
    });

    $(document).on('change', 'input[name="QLO_HEADER_SLIDER_AUTO_PLAY"]', function () {
        if ($(this).val() === '1') {
            $('#qlo-slide-interval-group, #qlo-slide-anim-group').show(200);
        } else {
            $('#qlo-slide-interval-group, #qlo-slide-anim-group').hide(200);
        }
    });

    hideOtherLanguage(qloHmDefaultLangId);

    function updateImgFileNameHm(files) {
        var names = [];
        for (var i = 0; files && i < files.length; i++) {
            names.push(files[i].name);
        }
        $('#qlo-img-form-file-name').val(names.join(', '));
    }

    $('#qlo-img-form-file-selectbutton, #qlo-img-form-file-name').on('click', function () {
        $('#qlo-img-form-file').trigger('click');
    });

    $('#qlo-img-form-file-name').on('dragenter dragover', function (e) {
        e.stopPropagation();
        e.preventDefault();
    });

    $('#qlo-img-form-file-name').on('drop', function (e) {
        e.preventDefault();
        var files = e.originalEvent.dataTransfer.files;
        document.getElementById('qlo-img-form-file').files = files;
        updateImgFileNameHm(files);
    });

    $('#qlo-img-form-file').on('change', function () {
        updateImgFileNameHm(this.files);
    });

    $('#qlo-img-edit-file-selectbutton, #qlo-img-edit-file-name').on('click', function () {
        $('#qlo-img-edit-file').trigger('click');
    });

    $('#qlo-img-edit-file').on('change', function () {
        var file = this.files && this.files[0] ? this.files[0] : null;
        $('#qlo-img-edit-file-name').val(file ? file.name : '');

        revokeEditImagePreview();
        if (file) {
            editImagePreviewUrl = URL.createObjectURL(file);
            $('#qlo-img-edit-thumb').attr('src', editImagePreviewUrl);
            $('#qlo-img-edit-preview-group').show();
        }
    });

    $(document).on('change', '#qlo-source-type', function () {
        if ($(this).val() === 'url') {
            $('#qlo-vid-file-wrap').hide();
            $('#qlo-vid-url-wrap').show();
            $('#qlo-current-video-wrap').hide();
        } else {
            $('#qlo-vid-file-wrap').show();
            $('#qlo-vid-url-wrap').hide();
            if ($('#qlo-current-video-wrap').length) {
                $('#qlo-current-video-wrap').show();
            }
        }
    });

    $(document).on('change', '#qlo-video-file-input', function () {
        var file  = this.files && this.files[0] ? this.files[0] : null;
        var $hint = $('.qlo-vid-filename');
        if (file && typeof qloHmMaxVideoUpload !== 'undefined' && qloHmMaxVideoUpload > 0 && file.size > qloHmMaxVideoUpload) {
            this.value = '';
            $hint.hide();
            showErrorMessage(qloHmI18n.fileTooLarge);
            return;
        }
        if (file) {
            $hint.text(file.name).show();
        } else {
            $hint.hide();
        }
    });

    $(document).on('click', '#qlo-vid-file-add-btn', function () {
        $('#qlo-video-file-input').trigger('click');
    });

    var videoUrlMimeMap = { mp4: 'video/mp4', webm: 'video/webm', ogg: 'video/ogg' };

    $(document).on('input change', '#qlo-video-url-input', function () {
        var url = $.trim($(this).val());
        var $wrap   = $('#qlo-video-url-preview-wrap');
        var $player = $('#qlo-video-url-preview-player')[0];
        var $source = $('#qlo-video-url-preview-source');

        if (!/^https?:\/\//i.test(url)) {
            $wrap.hide();
            return;
        }

        var ext  = (url.split('?')[0].split('.').pop() || '').toLowerCase();
        $source.attr('src', url);
        $source.attr('type', videoUrlMimeMap[ext] || 'video/mp4');
        if ($player) { $player.load(); }
        $wrap.show();
    });

    $('#qlo-add-image-btn').on('click', function () {
        openImgForm('add');
    });

    $(document).on('click', '.qlo-edit-img', function () {
        var $row          = $(this).closest('tr.qlo-img-row');
        var id            = parseInt($row.data('id'), 10);
        var tagLines      = $row.data('tag-lines') || {};
        var isActive      = $row.find('.js-toggle-active').hasClass('action-enabled');
        var showHotelName = $row.find('.js-toggle-hotel-name').hasClass('action-enabled');
        var thumbSrc      = $row.attr('data-thumb-src') || '';
        var tlColor       = $row.attr('data-tag-line-color') || '#ffffff';
        var tlFontSize    = parseInt($row.attr('data-tag-line-font-size') || 16, 10);
        var tlFontWeight  = $row.attr('data-tag-line-font-weight') || '400';
        openImgForm('edit', id, tagLines, isActive, thumbSrc, tlColor, tlFontSize, tlFontWeight, showHotelName);
    });

    function updateBulkUpdateTriggerState() {
        $('#qlo-bulk-update-trigger').attr('disabled', !$('.qlo-img-checkbox:checked').length);
    }

    $(document).on('change', '.qlo-img-checkbox', updateBulkUpdateTriggerState);
    $('.qlo-select-all-images, .qlo-unselect-all-images').on('click', updateBulkUpdateTriggerState);

    $('#qlo-bulk-update-modal').on('show.bs.modal', function (e) {
        if ($(e.relatedTarget).attr('disabled')) {
            return false;
        }
        $('#qlo-bulk-active').val('');
        $('#qlo-bulk-hotelname').val('');
        $('#qlo_bulk_update_tagline_off').prop('checked', true);
        $('#qlo-bulk-tagline-fields-wrap').hide();
        $('.qlo-bulk-tagline-field').val('');
        $('#qlo-bulk-tl-color').val('').trigger('keyup');
        $('#qlo-bulk-tl-font-size').val(0);
        $('#qlo-bulk-tl-font-weight').val('');
        hideOtherLanguage(qloHmDefaultLangId);
    });

    $(document).on('change', 'input[name="qlo_bulk_update_tagline"]', function () {
        if ($(this).val() === '1') {
            $('#qlo-bulk-tagline-fields-wrap').show(200);
        } else {
            $('#qlo-bulk-tagline-fields-wrap').hide(200);
        }
    });

    $('#qlo-bulk-update-apply').on('click', function () {
        var ids = $('.qlo-img-checkbox:checked').map(function () { return $(this).val(); }).get();
        var updateTagline = $('input[name="qlo_bulk_update_tagline"]:checked').val() === '1';
        var postData = {
            ajax: 1, action: 'bulkUpdateImages', token: qloHmToken,
            id_header_image: ids,
            active: $('#qlo-bulk-active').val(),
            show_hotel_chain_name: $('#qlo-bulk-hotelname').val(),
            update_tagline: updateTagline ? 1 : 0,
            tag_line_color: $.trim($('#qlo-bulk-tl-color').val()),
            tag_line_font_size: $('#qlo-bulk-tl-font-size').val(),
            tag_line_font_weight: $('#qlo-bulk-tl-font-weight').val()
        };
        if (updateTagline) {
            $('.qlo-bulk-tagline-field').each(function () {
                postData['tag_line_' + $(this).data('lang')] = $.trim($(this).val());
            });
        }

        var $btn = $(this).prop('disabled', true);
        $('#page-loader').show();
        $.ajax({
            url:  qloHmCurrentIndex,
            type: 'POST',
            data: postData,
            success: function (raw) {
                $btn.prop('disabled', false);
                var data = safeParseJsonHm(raw);
                if (data && data.success) {
                    (data.data.rows || []).forEach(function (rowData) {
                        var $row = $('#qlo-image-tbody .qlo-img-row[data-id="' + rowData.id + '"]');
                        patchImageRow($row, rowData);
                    });
                    $('#qlo-bulk-update-modal').modal('hide');
                    showSuccessMessage(data.confirmations);
                } else {
                    showErrorMessage(data ? data.errors.join('<br>') : qloHmI18n.updateFailed);
                }
            },
            error: function () { $btn.prop('disabled', false); showErrorMessage(qloHmI18n.requestFailed); }
        }).always(function () { $('#page-loader').hide(); });
    });

    $('#qlo-img-form-upload-btn').on('click', function () {
        var fileInput = document.getElementById('qlo-img-form-file');
        var files     = fileInput ? Array.prototype.slice.call(fileInput.files) : [];
        if (!files.length) {
            showErrorMessage((typeof qloHmI18n !== 'undefined') ? qloHmI18n.noFileSelected : 'Please select at least one image file.');
            return;
        }
        uploadImagesWithTagLine(files);
    });

    $('#qlo-img-form-save-btn').on('click', function () {
        var id = parseInt($.trim($('#qlo-img-form-id').val()), 10);
        if (id) {
            saveEditedImage(id);
        }
    });

    $(document).on('click', '.qlo-img-row .js-toggle-active', function (e) {
        e.preventDefault();
        var $link  = $(this);
        var $row   = $link.closest('tr.qlo-img-row');
        var id     = parseInt($row.data('id'), 10);
        var active = $link.hasClass('action-enabled') ? 0 : 1;
        $('#page-loader').show();
        $.ajax({
            url:  qloHmCurrentIndex,
            type: 'POST',
            data: { ajax: 1, action: 'toggle_image_active', id_header_image: id, active: active, token: qloHmToken },
            success: function (raw) {
                var data = safeParseJsonHm(raw);
                if (data && data.success) {
                    $link.toggleClass('action-enabled action-disabled');
                    $link.find('i').toggleClass('hidden');
                    showSuccessMessage(data.confirmations);
                } else {
                    showErrorMessage(data ? data.errors.join('<br>') : qloHmI18n.updateFailed);
                }
            },
            error: function () { showErrorMessage(qloHmI18n.requestFailed); }
        }).always(function () { $('#page-loader').hide(); });
    });

    $(document).on('click', '.qlo-img-row .js-toggle-hotel-name', function (e) {
        e.preventDefault();
        var $link          = $(this);
        var $row           = $link.closest('tr.qlo-img-row');
        var id             = parseInt($row.data('id'), 10);
        var showHotelName  = $link.hasClass('action-enabled') ? 0 : 1;
        $('#page-loader').show();
        $.ajax({
            url:  qloHmCurrentIndex,
            type: 'POST',
            data: { ajax: 1, action: 'toggle_image_hotel_name', id_header_image: id, show_hotel_chain_name: showHotelName, token: qloHmToken },
            success: function (raw) {
                var data = safeParseJsonHm(raw);
                if (data && data.success) {
                    $link.toggleClass('action-enabled action-disabled');
                    $link.find('i').toggleClass('hidden');
                    showSuccessMessage(data.confirmations);
                } else {
                    showErrorMessage(data ? data.errors.join('<br>') : qloHmI18n.updateFailed);
                }
            },
            error: function () { showErrorMessage(qloHmI18n.requestFailed); }
        }).always(function () { $('#page-loader').hide(); });
    });

    $(document).on('click', '.qlo-delete-img', function () {
        var id = parseInt($(this).data('id'), 10);
        $('#page-loader').show();
        $.ajax({
            url:  qloHmCurrentIndex,
            type: 'POST',
            data: { ajax: 1, action: 'deleteMedia', id_header_image: id, token: qloHmToken },
            success: function (raw) {
                var data = safeParseJsonHm(raw);
                if (data && data.success) {
                    var $row = $('#qlo-image-tbody .qlo-img-row[data-id="' + id + '"]');
                    if (parseInt($('#qlo-img-form-id').val(), 10) === id) {
                        closeImgForm();
                    }
                    $row.fadeOut(250, function () {
                        $row.remove();
                        var count = $('#qlo-image-tbody .qlo-img-row').length;
                        $('#qlo-image-count').text(count);
                        if (!count) {
                            $('#qlo-no-images').show();
                        }
                        updateBulkActionsVisibility();
                    });
                    showSuccessMessage(data.confirmations || qloHmI18n.deleteFailed);
                } else {
                    showErrorMessage(data ? data.errors.join('<br>') : qloHmI18n.deleteFailed);
                }
            },
            error: function () { showErrorMessage(qloHmI18n.requestFailed); }
        }).always(function () { $('#page-loader').hide(); });
    });

    if ($('#qlo-image-table').length) {
        var _hmDragOriginalOrder = null;
        $('#qlo-image-table').tableDnD({
            dragHandle:  'dragHandle',
            onDragClass: 'myDragClass',
            onDragStart: function (table) {
                _hmDragOriginalOrder = [];
                $(table).find('tbody tr.qlo-img-row').each(function () {
                    _hmDragOriginalOrder.push(parseInt($(this).data('id'), 10));
                });
            },
            onDrop: function (table) {
                var ids = [];
                $(table).find('tbody tr.qlo-img-row').each(function (i) {
                    var id = parseInt($(this).data('id'), 10);
                    ids.push(id);
                    $(this).find('.positions').text(i + 1);
                });
                if (_hmDragOriginalOrder && JSON.stringify(ids) === JSON.stringify(_hmDragOriginalOrder)) {
                    return;
                }
                $('#page-loader').show();
                $.post(qloHmCurrentIndex, {
                    ajax:      1,
                    action:    'saveImagePositions',
                    image_ids: ids,
                    token:     qloHmToken
                }, function (raw) {
                    var data = safeParseJsonHm(raw);
                    if (data && data.success) {
                        showSuccessMessage(data.confirmations);
                    }
                }).always(function () { $('#page-loader').hide(); });
            }
        });
    }

    function openImgForm(mode, id, tagLines, isActive, imgUrl, tlColor, tlFontSize, tlFontWeight, showHotelName) {
        $('#qlo-img-form-id').val(id || '');
        $('#qlo-form-upload-progress').hide();

        if (mode === 'edit') {
            $('#qlo-img-modal-title-add').hide();
            $('#qlo-img-modal-title-edit').show();
            $('#qlo-img-add-footer').hide();
            $('#qlo-img-edit-footer').show();
            $('#qlo-img-form-file-group').hide();
            $('#qlo-img-form-add-active-group').hide();
            $('#qlo-img-form-add-hotelname-group').hide();
            $('#qlo-img-form-edit-group').show();
            $('#qlo-img-edit-file-group').show();
            var editFileInput = document.getElementById('qlo-img-edit-file');
            if (editFileInput) {
                editFileInput.value = '';
            }
            $('#qlo-img-edit-file-name').val('');
            revokeEditImagePreview();
            $('#qlo-img-form-edit-hotelname-group').show();
            if (isActive) {
                $('#qlo_img_active_edit_on').prop('checked', true);
            } else {
                $('#qlo_img_active_edit_off').prop('checked', true);
            }
            if (showHotelName) {
                $('#qlo_img_hotelname_edit_on').prop('checked', true);
            } else {
                $('#qlo_img_hotelname_edit_off').prop('checked', true);
            }
            tagLines = tagLines || {};
            $('.qlo-form-tagline-field').each(function () {
                var langId = $(this).data('lang');
                $(this).val(tagLines[langId] || '');
            });
            if (imgUrl) {
                $('#qlo-img-edit-thumb').attr('src', imgUrl);
                $('#qlo-img-edit-preview-group').show();
            } else {
                $('#qlo-img-edit-preview-group').hide();
            }
            $('#qlo-img-tl-color').val(tlColor || '#ffffff');
            $('#qlo-img-tl-font-size').val(tlFontSize || 16);
            var fw = tlFontWeight || '400';
            $('#qlo-img-tl-font-weight').val(fw);
        } else {
            $('#qlo-img-modal-title-edit').hide();
            $('#qlo-img-modal-title-add').show();
            $('#qlo-img-edit-footer').hide();
            $('#qlo-img-add-footer').show();
            $('#qlo-img-form-file-group').show();
            $('#qlo-img-form-add-active-group').show();
            $('#qlo-img-form-add-hotelname-group').show();
            $('#qlo-img-form-edit-group').hide();
            $('#qlo-img-form-edit-hotelname-group').hide();
            $('#qlo-img-edit-preview-group').hide();
            $('#qlo-img-edit-file-group').hide();
            var fileInput = document.getElementById('qlo-img-form-file');
            if (fileInput) {
                fileInput.value = '';
            }
            $('#qlo-img-form-file-name').val('');
            $('#qlo_img_active_add_on').prop('checked', true);
            $('#qlo_img_hotelname_add_on').prop('checked', true);
            $('.qlo-form-tagline-field').val('');
            $('#qlo-img-tl-color').val('#ffffff');
            $('#qlo-img-tl-font-size').val(16);
            $('#qlo-img-tl-font-weight').val('400');
        }

        $('#qlo-img-tl-color').trigger('keyup');

        hideOtherLanguage(id_language);

        $('#qlo-img-form-modal').modal('show');
    }

    function closeImgForm() {
        $('#qlo-img-form-modal').modal('hide');
        revokeEditImagePreview();
    }

    function getFormTagLines() {
        var tagLines = {};
        $('.qlo-form-tagline-field').each(function () {
            tagLines[$(this).data('lang')] = $.trim($(this).val());
        });
        return tagLines;
    }

    function saveEditedImage(id) {
        var tagLines      = getFormTagLines();
        var active        = parseInt($('input[name="qlo_img_active_edit"]:checked').val() || 0, 10);
        var showHotelName = parseInt($('input[name="qlo_img_hotelname_edit"]:checked').val() || 0, 10);
        var tlColor      = $('#qlo-img-tl-color').val() || '#ffffff';
        var tlFontSize   = parseInt($('#qlo-img-tl-font-size').val() || 16, 10);
        var tlFontWeight = $('#qlo-img-tl-font-weight').val() || '400';
        var editFileInput = document.getElementById('qlo-img-edit-file');
        var newFile      = editFileInput && editFileInput.files && editFileInput.files[0] ? editFileInput.files[0] : null;

        if (newFile && typeof qloHmMaxUpload !== 'undefined' && qloHmMaxUpload > 0 && newFile.size > qloHmMaxUpload) {
            showErrorMessage(qloHmI18n.fileTooLarge);
            return;
        }

        var fd = new FormData();
        fd.append('ajax', '1');
        fd.append('action', 'edit_image');
        fd.append('id_header_image', id);
        fd.append('active', active);
        fd.append('show_hotel_chain_name', showHotelName);
        fd.append('token', qloHmToken);
        fd.append('tag_line_color', tlColor);
        fd.append('tag_line_font_size', tlFontSize);
        fd.append('tag_line_font_weight', tlFontWeight);
        $.each(tagLines, function (langId, val) {
            fd.append('tag_line_' + langId, val);
        });
        if (newFile) {
            fd.append('header_image_file', newFile);
        }

        $('#page-loader').show();
        $.ajax({
            url:         qloHmCurrentIndex,
            type:        'POST',
            data:        fd,
            processData: false,
            contentType: false,
            success: function (raw) {
                var resp = safeParseJsonHm(raw);
                if (resp && resp.success) {
                    var $row = $('#qlo-image-tbody .qlo-img-row[data-id="' + id + '"]');
                    patchImageRow($row, $.extend({ id: id }, resp));
                    closeImgForm();
                    showSuccessMessage(resp.confirmations || qloHmI18n.imageUpdatedSuccess);
                } else {
                    showErrorMessage(resp ? resp.errors.join('<br>') : qloHmI18n.updateFailed);
                }
            },
            error: function () { showErrorMessage(qloHmI18n.requestFailed); }
        }).always(function () { $('#page-loader').hide(); });
    }

    function uploadImagesWithTagLine(files) {
        var index         = 0;
        var tagLines      = getFormTagLines();
        var active        = parseInt($('input[name="qlo_img_active_add"]:checked').val() || 1, 10);
        var showHotelName = parseInt($('input[name="qlo_img_hotelname_add"]:checked').val() || 1, 10);
        var tlColor      = $('#qlo-img-tl-color').val() || '#ffffff';
        var tlFontSize   = parseInt($('#qlo-img-tl-font-size').val() || 16, 10);
        var tlFontWeight = $('#qlo-img-tl-font-weight').val() || '400';
        $('#qlo-form-upload-progress').show();
        $('#qlo-img-form-upload-btn').prop('disabled', true);
        $('#page-loader').show();

        function next() {
            if (index >= files.length) {
                $('#qlo-form-upload-progress').hide();
                $('#qlo-img-form-upload-btn').prop('disabled', false);
                $('#page-loader').hide();
                closeImgForm();
                return;
            }
            var file = files[index++];
            if (typeof qloHmMaxUpload !== 'undefined' && qloHmMaxUpload > 0 && file.size > qloHmMaxUpload) {
                showErrorMessage(qloHmI18n.fileTooLarge);
                next();
                return;
            }
            var fd = new FormData();
            fd.append('header_image_file', file);
            fd.append('ajax',   '1');
            fd.append('action', 'uploadImage');
            fd.append('token',  qloHmToken);
            fd.append('active', active);
            fd.append('show_hotel_chain_name', showHotelName);
            fd.append('tag_line_color',       tlColor);
            fd.append('tag_line_font_size',   tlFontSize);
            fd.append('tag_line_font_weight', tlFontWeight);
            $.each(tagLines, function (langId, val) {
                fd.append('tag_line_' + langId, val);
            });

            $.ajax({
                url:         qloHmCurrentIndex,
                type:        'POST',
                data:        fd,
                processData: false,
                contentType: false,
                success: function (raw) {
                    var resp = safeParseJsonHm(raw);
                    if (resp && resp.success) {
                        appendImageRow(resp.data.image_row);
                        showSuccessMessage(qloHmI18n.imageUploadedSuccess);
                    } else {
                        showErrorMessage(file.name + ': ' + (resp ? resp.errors.join(', ') : qloHmI18n.uploadFailed));
                    }
                    next();
                },
                error: function () {
                    showErrorMessage(file.name + ': ' + qloHmI18n.requestFailed);
                    next();
                }
            });
        }
        next();
    }

    function appendImageRow(rowHtml) {
        $('#qlo-image-tbody').append(rowHtml);
        $('#qlo-image-table').tableDnDUpdate();
        $('#qlo-no-images').hide();
        $('#qlo-image-count').text(parseInt($('#qlo-image-count').text(), 10) + 1);
        updateBulkActionsVisibility();
    }

    function updateBulkActionsVisibility() {
        var count = $('#qlo-image-tbody .qlo-img-row').length;
        $('#qlo-bulk-actions-row').toggle(count > 1);
        updateBulkUpdateTriggerState();
    }

    function patchImageRow($row, data) {
        var id = parseInt(data.id, 10);
        if (data.thumb) {
            var $thumbCell = $row.find('.qlo-img-thumb-cell');
            if (!$thumbCell.length) {
                $thumbCell = $row.find('td').eq(1);
            }
            $thumbCell.html(data.thumb);
        }
        if (data.thumb_src) {
            $row.attr('data-thumb-src', data.thumb_src);
        }
        var updatedTagLines = safeParseJsonHm(data.tag_lines_json) || {};
        $row.attr('data-tag-lines', data.tag_lines_json);
        $row.data('tag-lines', updatedTagLines);
        $row.attr('data-tag-line-color', data.tag_line_color || '#ffffff');
        $row.attr('data-tag-line-font-size', data.tag_line_font_size || 16);
        $row.attr('data-tag-line-font-weight', data.tag_line_font_weight || '400');
        var display = data.tag_line || '';
        $row.find('.qlo-img-tagline-cell').text(
            display.length > 50 ? display.substring(0, 50) + '...' : (display || '—')
        );
        var nowActive = parseInt(data.active, 10);
        var $toggle = $row.find('.js-toggle-active');
        $toggle.toggleClass('action-enabled', !!nowActive)
               .toggleClass('action-disabled', !nowActive);
        $toggle.find('i.icon-check').toggleClass('hidden', !nowActive);
        $toggle.find('i.icon-remove').toggleClass('hidden', !!nowActive);
        $toggle.attr('href', $toggle.attr('href').replace(/id_header_image=\d+/, 'id_header_image=' + id)
                                                .replace(/active=\d+/, 'active=' + (nowActive ? 0 : 1)));

        if (data.show_hotel_chain_name !== undefined) {
            var nowShowHotelName = parseInt(data.show_hotel_chain_name, 10);
            var $hnToggle = $row.find('.js-toggle-hotel-name');
            $hnToggle.toggleClass('action-enabled', !!nowShowHotelName)
                     .toggleClass('action-disabled', !nowShowHotelName);
            $hnToggle.find('i.icon-check').toggleClass('hidden', !nowShowHotelName);
            $hnToggle.find('i.icon-remove').toggleClass('hidden', !!nowShowHotelName);
            $hnToggle.attr('href', $hnToggle.attr('href').replace(/id_header_image=\d+/, 'id_header_image=' + id)
                                                        .replace(/show_hotel_chain_name=\d+/, 'show_hotel_chain_name=' + (nowShowHotelName ? 0 : 1)));
        }
    }

    function safeParseJsonHm(raw) {
        try { return JSON.parse(raw); } catch (_e) { return null; }
    }
});
