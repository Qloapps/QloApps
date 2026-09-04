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
            $('#qlo-slide-interval-group').show(200);
        } else {
            $('#qlo-slide-interval-group').hide(200);
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
        var titles        = $row.data('titles') || {};
        var descriptions  = $row.data('descriptions') || {};
        var isActive      = $row.find('.js-toggle-active').hasClass('action-enabled');
        var thumbSrc      = $row.attr('data-thumb-src') || '';
        var descColor     = $row.attr('data-description-color') || '#ffffff';
        var descFontSize  = parseInt($row.attr('data-description-font-size') || 16, 10);
        var descFontWeight = $row.attr('data-description-font-weight') || '400';
        openImgForm('edit', id, titles, descriptions, isActive, thumbSrc, descColor, descFontSize, descFontWeight);
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
        $('#qlo_bulk_update_title_off').prop('checked', true);
        $('#qlo-bulk-title-fields-wrap').hide();
        $('.qlo-bulk-title-field').val('');
        $('#qlo_bulk_update_description_off').prop('checked', true);
        $('#qlo-bulk-description-fields-wrap').hide();
        $('.qlo-bulk-description-field').val('');
        $('#qlo-bulk-desc-color').val('').trigger('keyup');
        $('#qlo-bulk-desc-font-size').val(0);
        $('#qlo-bulk-desc-font-weight').val('');
        hideOtherLanguage(qloHmDefaultLangId);
    });

    $(document).on('change', 'input[name="qlo_bulk_update_title"]', function () {
        if ($(this).val() === '1') {
            $('#qlo-bulk-title-fields-wrap').show(200);
        } else {
            $('#qlo-bulk-title-fields-wrap').hide(200);
        }
    });

    $(document).on('change', 'input[name="qlo_bulk_update_description"]', function () {
        if ($(this).val() === '1') {
            $('#qlo-bulk-description-fields-wrap').show(200);
        } else {
            $('#qlo-bulk-description-fields-wrap').hide(200);
        }
    });

    $('#qlo-bulk-update-apply').on('click', function () {
        var ids = $('.qlo-img-checkbox:checked').map(function () { return $(this).val(); }).get();
        var updateTitle = $('input[name="qlo_bulk_update_title"]:checked').val() === '1';
        var updateDescription = $('input[name="qlo_bulk_update_description"]:checked').val() === '1';
        var postData = {
            ajax: 1, action: 'bulkUpdateImages', token: qloHmToken,
            id_header_image: ids,
            active: $('#qlo-bulk-active').val(),
            update_title: updateTitle ? 1 : 0,
            update_description: updateDescription ? 1 : 0,
            description_color: $.trim($('#qlo-bulk-desc-color').val()),
            description_font_size: $('#qlo-bulk-desc-font-size').val(),
            description_font_weight: $('#qlo-bulk-desc-font-weight').val()
        };
        if (updateTitle) {
            $('.qlo-bulk-title-field').each(function () {
                postData['title_' + $(this).data('lang')] = $.trim($(this).val());
            });
        }
        if (updateDescription) {
            $('.qlo-bulk-description-field').each(function () {
                postData['description_' + $(this).data('lang')] = $.trim($(this).val());
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
        uploadImagesWithDetails(files);
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

    function openImgForm(mode, id, titles, descriptions, isActive, imgUrl, descColor, descFontSize, descFontWeight) {
        $('#qlo-img-form-id').val(id || '');
        $('#qlo-form-upload-progress').hide();

        if (mode === 'edit') {
            $('#qlo-img-modal-title-add').hide();
            $('#qlo-img-modal-title-edit').show();
            $('#qlo-img-add-footer').hide();
            $('#qlo-img-edit-footer').show();
            $('#qlo-img-form-file-group').hide();
            $('#qlo-img-form-add-active-group').hide();
            $('#qlo-img-form-edit-group').show();
            $('#qlo-img-edit-file-group').show();
            var editFileInput = document.getElementById('qlo-img-edit-file');
            if (editFileInput) {
                editFileInput.value = '';
            }
            $('#qlo-img-edit-file-name').val('');
            revokeEditImagePreview();
            if (isActive) {
                $('#qlo_img_active_edit_on').prop('checked', true);
            } else {
                $('#qlo_img_active_edit_off').prop('checked', true);
            }
            titles = titles || {};
            descriptions = descriptions || {};
            $('.qlo-form-title-field').each(function () {
                var langId = $(this).data('lang');
                $(this).val(titles[langId] || '');
            });
            $('.qlo-form-description-field').each(function () {
                var langId = $(this).data('lang');
                $(this).val(descriptions[langId] || '');
            });
            if (imgUrl) {
                $('#qlo-img-edit-thumb').attr('src', imgUrl);
                $('#qlo-img-edit-preview-group').show();
            } else {
                $('#qlo-img-edit-preview-group').hide();
            }
            $('#qlo-img-desc-color').val(descColor || '#ffffff');
            $('#qlo-img-desc-font-size').val(descFontSize || 16);
            var fw = descFontWeight || '400';
            $('#qlo-img-desc-font-weight').val(fw);
        } else {
            $('#qlo-img-modal-title-edit').hide();
            $('#qlo-img-modal-title-add').show();
            $('#qlo-img-edit-footer').hide();
            $('#qlo-img-add-footer').show();
            $('#qlo-img-form-file-group').show();
            $('#qlo-img-form-add-active-group').show();
            $('#qlo-img-form-edit-group').hide();
            $('#qlo-img-edit-preview-group').hide();
            $('#qlo-img-edit-file-group').hide();
            var fileInput = document.getElementById('qlo-img-form-file');
            if (fileInput) {
                fileInput.value = '';
            }
            $('#qlo-img-form-file-name').val('');
            $('#qlo_img_active_add_on').prop('checked', true);
            $('.qlo-form-title-field').val('');
            $('.qlo-form-description-field').val('');
            $('#qlo-img-desc-color').val('#ffffff');
            $('#qlo-img-desc-font-size').val(16);
            $('#qlo-img-desc-font-weight').val('400');
        }

        $('#qlo-img-desc-color').trigger('keyup');

        hideOtherLanguage(id_language);

        $('#qlo-img-form-modal').modal('show');
    }

    function closeImgForm() {
        $('#qlo-img-form-modal').modal('hide');
        revokeEditImagePreview();
    }

    function getFormTitles() {
        var titles = {};
        $('.qlo-form-title-field').each(function () {
            titles[$(this).data('lang')] = $.trim($(this).val());
        });
        return titles;
    }

    function getFormDescriptions() {
        var descriptions = {};
        $('.qlo-form-description-field').each(function () {
            descriptions[$(this).data('lang')] = $.trim($(this).val());
        });
        return descriptions;
    }

    function saveEditedImage(id) {
        var titles          = getFormTitles();
        var descriptions    = getFormDescriptions();
        var active          = parseInt($('input[name="qlo_img_active_edit"]:checked').val() || 0, 10);
        var descColor       = $('#qlo-img-desc-color').val() || '#ffffff';
        var descFontSize    = parseInt($('#qlo-img-desc-font-size').val() || 16, 10);
        var descFontWeight  = $('#qlo-img-desc-font-weight').val() || '400';
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
        fd.append('token', qloHmToken);
        fd.append('description_color', descColor);
        fd.append('description_font_size', descFontSize);
        fd.append('description_font_weight', descFontWeight);
        $.each(titles, function (langId, val) {
            fd.append('title_' + langId, val);
        });
        $.each(descriptions, function (langId, val) {
            fd.append('description_' + langId, val);
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

    function uploadImagesWithDetails(files) {
        var index           = 0;
        var titles          = getFormTitles();
        var descriptions    = getFormDescriptions();
        var active          = parseInt($('input[name="qlo_img_active_add"]:checked').val() || 1, 10);
        var descColor       = $('#qlo-img-desc-color').val() || '#ffffff';
        var descFontSize    = parseInt($('#qlo-img-desc-font-size').val() || 16, 10);
        var descFontWeight  = $('#qlo-img-desc-font-weight').val() || '400';
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
            fd.append('description_color',       descColor);
            fd.append('description_font_size',   descFontSize);
            fd.append('description_font_weight', descFontWeight);
            $.each(titles, function (langId, val) {
                fd.append('title_' + langId, val);
            });
            $.each(descriptions, function (langId, val) {
                fd.append('description_' + langId, val);
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
        var updatedTitles = safeParseJsonHm(data.titles_json) || {};
        $row.attr('data-titles', data.titles_json);
        $row.data('titles', updatedTitles);
        var titleDisplay = data.title || '';
        $row.find('.qlo-img-title-cell').text(
            titleDisplay.length > 50 ? titleDisplay.substring(0, 50) + '...' : (titleDisplay || '—')
        );

        var updatedDescriptions = safeParseJsonHm(data.descriptions_json) || {};
        $row.attr('data-descriptions', data.descriptions_json);
        $row.data('descriptions', updatedDescriptions);
        $row.attr('data-description-color', data.description_color || '#ffffff');
        $row.attr('data-description-font-size', data.description_font_size || 16);
        $row.attr('data-description-font-weight', data.description_font_weight || '400');
        var descDisplay = data.description || '';
        $row.find('.qlo-img-description-cell').text(
            descDisplay.length > 50 ? descDisplay.substring(0, 50) + '...' : (descDisplay || '—')
        );

        var nowActive = parseInt(data.active, 10);
        var $toggle = $row.find('.js-toggle-active');
        $toggle.toggleClass('action-enabled', !!nowActive)
               .toggleClass('action-disabled', !nowActive);
        $toggle.find('i.icon-check').toggleClass('hidden', !nowActive);
        $toggle.find('i.icon-remove').toggleClass('hidden', !!nowActive);
        $toggle.attr('href', $toggle.attr('href').replace(/id_header_image=\d+/, 'id_header_image=' + id)
                                                .replace(/active=\d+/, 'active=' + (nowActive ? 0 : 1)));
    }

    function safeParseJsonHm(raw) {
        try { return JSON.parse(raw); } catch (_e) { return null; }
    }
});
