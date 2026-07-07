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
            if (imageCount > 1) {
                if (!confirm(qloHmI18n.switchToVideoConfirm)) {
                    e.preventDefault();
                    return false;
                }
                $('#qlo-confirm-delete-images').val('1');
            }
        }
    });

    $(document).on('change', 'input[name="QLO_HEADER_SLIDER_AUTO_PLAY"]', function () {
        if ($(this).val() === '1') {
            $('#qlo-slide-interval-group, #qlo-slide-anim-group').show();
        } else {
            $('#qlo-slide-interval-group, #qlo-slide-anim-group').hide();
        }
    });

    $(document).on('change', '#qlo-img-form-file', function () {
        renderFileListHm(this.files);
    });

    $(document).on('click', '.qlo-remove-file', function () {
        var removeIdx = parseInt($(this).data('index'), 10);
        var fileInput = document.getElementById('qlo-img-form-file');
        if (!fileInput || !fileInput.files) { return; }
        try {
            var dt = new DataTransfer();
            for (var i = 0; i < fileInput.files.length; i++) {
                if (i !== removeIdx) { dt.items.add(fileInput.files[i]); }
            }
            fileInput.files = dt.files;
        } catch (_e) {
        }
        renderFileListHm(fileInput.files);
    });

    function renderFileListHm(files) {
        var $list = $('#qlo-img-files-list').empty();
        if (!files || !files.length) { $list.hide(); return; }
        $list.show();
        for (var i = 0; i < files.length; i++) {
            (function (idx, file) {
                var size = file.size < 1048576
                    ? (file.size / 1024).toFixed(1) + ' KB'
                    : (file.size / 1048576).toFixed(1) + ' MB';
                var $li = $(
                    '<li>' +
                        '<span class="qlo-file-name">' + escapeHtmlHm(file.name) + '</span>' +
                        '<span class="qlo-file-size text-muted">(' + size + ')</span>' +
                        '<button type="button" class="btn btn-default qlo-remove-file" data-index="' + idx + '" title="Remove">' +
                            '<i class="icon-trash"></i>' +
                        '</button>' +
                    '</li>'
                );
                $list.append($li);
            })(i, files[i]);
        }
    }

    hideOtherLanguage(qloHmDefaultLangId);

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

    $('#qlo-add-image-btn').on('click', function () {
        openImgForm('add');
    });

    $('#qlo-img-file-add-btn').on('click', function () {
        $('#qlo-img-form-file').trigger('click');
    });

    $(document).on('click', '.qlo-edit-img', function () {
        var $row         = $(this).closest('tr.qlo-img-row');
        var id           = parseInt($row.data('id'), 10);
        var tagLines     = $row.data('tag-lines') || {};
        var isActive     = $row.find('.list-action-enable').hasClass('action-enabled');
        var thumbSrc     = $row.find('img.qlo-img-thumb').attr('src') || '';
        var tlColor      = $row.attr('data-tag-line-color') || '#ffffff';
        var tlFontSize   = parseInt($row.attr('data-tag-line-font-size') || 16, 10);
        var tlFontWeight = $row.attr('data-tag-line-font-weight') || '400';
        openImgForm('edit', id, tagLines, isActive, thumbSrc, tlColor, tlFontSize, tlFontWeight);
    });

    $('#qlo-bulk-tagline-apply').on('click', function () {
        var tagLines = {};
        $('.qlo-bulk-tagline-field').each(function () {
            tagLines[$(this).data('lang')] = $.trim($(this).val());
        });
        var postData = { ajax: 1, action: 'bulkUpdateTagLines', token: qloHmToken };
        $.each(tagLines, function (langId, val) {
            postData['tag_line_' + langId] = val;
        });
        var $btn = $(this).prop('disabled', true);
        $.ajax({
            url:  qloHmCurrentIndex,
            type: 'POST',
            data: postData,
            success: function (raw) {
                $btn.prop('disabled', false);
                var data = safeParseJsonHm(raw);
                if (data && data.success) {
                    var updatedTagLines = safeParseJsonHm(data.tag_lines_json) || tagLines;
                    $('#qlo-image-tbody .qlo-img-row').each(function () {
                        $(this).attr('data-tag-lines', data.tag_lines_json);
                        $(this).data('tag-lines', updatedTagLines);
                        var display = data.tag_line || '';
                        $(this).find('.qlo-img-tagline-cell').text(
                            display.length > 50 ? display.substring(0, 50) + '...' : (display || '—')
                        );
                    });
                    showSuccessMessage(data.confirmations);
                } else {
                    showErrorMessage(data ? data.errors.join('<br>') : qloHmI18n.updateFailed);
                }
            },
            error: function () { $btn.prop('disabled', false); showErrorMessage(qloHmI18n.requestFailed); }
        });
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

    $(document).on('click', '.qlo-img-row .list-action-enable', function (e) {
        e.preventDefault();
        var $link  = $(this);
        var $row   = $link.closest('tr.qlo-img-row');
        var id     = parseInt($row.data('id'), 10);
        var active = $link.hasClass('action-enabled') ? 0 : 1;
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
        });
    });

    $(document).on('click', '.qlo-delete-img', function () {
        var id = parseInt($(this).data('id'), 10);
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
        });
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
                });
            }
        });
    }

    function openImgForm(mode, id, tagLines, isActive, imgUrl, tlColor, tlFontSize, tlFontWeight) {
        $('#qlo-img-form-id').val(id || '');
        $('#qlo-form-upload-progress').hide();

        if (mode === 'edit') {
            $('#qlo-img-modal-title-add').hide();
            $('#qlo-img-modal-title-edit').show();
            $('#qlo-img-add-footer').hide();
            $('#qlo-img-edit-footer').show();
            $('#qlo-img-form-file-group').hide();
            $('#qlo-img-files-list').hide().empty();
            $('#qlo-img-form-add-active-group').hide();
            $('#qlo-img-form-edit-group').show();
            if (isActive) {
                $('#qlo_img_active_edit_on').prop('checked', true);
            } else {
                $('#qlo_img_active_edit_off').prop('checked', true);
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
            $('#qlo-img-form-edit-group').hide();
            $('#qlo-img-edit-preview-group').hide();
            var fileInput = document.getElementById('qlo-img-form-file');
            if (fileInput) {
                fileInput.value = '';
            }
            $('#qlo-img-files-list').hide().empty();
            $('#qlo_img_active_add_on').prop('checked', true);
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
    }

    function getFormTagLines() {
        var tagLines = {};
        $('.qlo-form-tagline-field').each(function () {
            tagLines[$(this).data('lang')] = $.trim($(this).val());
        });
        return tagLines;
    }

    function saveEditedImage(id) {
        var tagLines     = getFormTagLines();
        var active       = parseInt($('input[name="qlo_img_active_edit"]:checked').val() || 0, 10);
        var tlColor      = $('#qlo-img-tl-color').val() || '#ffffff';
        var tlFontSize   = parseInt($('#qlo-img-tl-font-size').val() || 16, 10);
        var tlFontWeight = $('#qlo-img-tl-font-weight').val() || '400';
        var postData = {
            ajax: 1, action: 'edit_image', id_header_image: id, active: active, token: qloHmToken,
            tag_line_color: tlColor, tag_line_font_size: tlFontSize, tag_line_font_weight: tlFontWeight
        };
        $.each(tagLines, function (langId, val) {
            postData['tag_line_' + langId] = val;
        });

        $.ajax({
            url:  qloHmCurrentIndex,
            type: 'POST',
            data: postData,
            success: function (raw) {
                var resp = safeParseJsonHm(raw);
                if (resp && resp.success) {
                    var $row = $('#qlo-image-tbody .qlo-img-row[data-id="' + id + '"]');
                    var updatedTagLines = safeParseJsonHm(resp.tag_lines_json) || tagLines;
                    $row.attr('data-tag-lines', resp.tag_lines_json);
                    $row.data('tag-lines', updatedTagLines);
                    $row.attr('data-tag-line-color', resp.tag_line_color || '#ffffff');
                    $row.attr('data-tag-line-font-size', resp.tag_line_font_size || 16);
                    $row.attr('data-tag-line-font-weight', resp.tag_line_font_weight || '400');
                    var display = resp.tag_line || '';
                    $row.find('.qlo-img-tagline-cell').text(
                        display.length > 50 ? display.substring(0, 50) + '...' : (display || '—')
                    );
                    var nowActive = parseInt(resp.active, 10);
                    var $toggle = $row.find('.list-action-enable');
                    $toggle.toggleClass('action-enabled', !!nowActive)
                           .toggleClass('action-disabled', !nowActive);
                    $toggle.find('i.icon-check').toggleClass('hidden', !nowActive);
                    $toggle.find('i.icon-remove').toggleClass('hidden', !!nowActive);
                    $toggle.attr('href', $toggle.attr('href').replace(/id_header_image=\d+/, 'id_header_image=' + id)
                                                            .replace(/active=\d+/, 'active=' + (nowActive ? 0 : 1)));
                    closeImgForm();
                    showSuccessMessage(resp.confirmations || qloHmI18n.imageUpdatedSuccess);
                } else {
                    showErrorMessage(resp ? resp.errors.join('<br>') : qloHmI18n.updateFailed);
                }
            },
            error: function () { showErrorMessage(qloHmI18n.requestFailed); }
        });
    }

    function uploadImagesWithTagLine(files) {
        var index        = 0;
        var tagLines     = getFormTagLines();
        var active       = parseInt($('input[name="qlo_img_active_add"]:checked').val() || 1, 10);
        var tlColor      = $('#qlo-img-tl-color').val() || '#ffffff';
        var tlFontSize   = parseInt($('#qlo-img-tl-font-size').val() || 16, 10);
        var tlFontWeight = $('#qlo-img-tl-font-weight').val() || '400';
        $('#qlo-form-upload-progress').show();
        $('#qlo-img-form-upload-btn').prop('disabled', true);

        function next() {
            if (index >= files.length) {
                $('#qlo-form-upload-progress').hide();
                $('#qlo-img-form-upload-btn').prop('disabled', false);
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
                        var uploadedTagLines = safeParseJsonHm(resp.tag_lines_json) || tagLines;
                        appendImageRow(
                            resp.id, resp.imgUrl, resp.tag_line || '', uploadedTagLines, resp.tag_lines_json || '{}',
                            parseInt(resp.active, 10),
                            resp.tag_line_color || '#ffffff',
                            resp.tag_line_font_size || 16,
                            resp.tag_line_font_weight || '400'
                        );
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

    function appendImageRow(id, imgUrl, tagLineDisplay, tagLines, tagLinesJson, active, tlColor, tlFontSize, tlFontWeight) {
        id           = parseInt(id, 10);
        active       = active !== undefined ? !!active : true;
        tlColor      = tlColor || '#ffffff';
        tlFontSize   = tlFontSize || 16;
        tlFontWeight = tlFontWeight || '400';
        var display      = tagLineDisplay || '';
        var cellText     = display.length > 50 ? display.substring(0, 50) + '...' : (display || '—');
        var pos          = parseInt($('#qlo-image-count').text(), 10) + 1;
        var nextActive   = active ? 0 : 1;
        var toggleUrl    = qloHmCurrentIndex + '&ajax=1&action=toggle_image_active&id_header_image=' + id + '&active=' + nextActive + '&token=' + qloHmToken;
        var toggleClass  = active ? 'action-enabled' : 'action-disabled';
        var checkHidden  = active ? '' : ' hidden';
        var removeHidden = active ? ' hidden' : '';

        var $row = $(
            '<tr class="qlo-img-row" id="qlo_img_' + id + '" data-id="' + id + '"' +
                ' data-tag-lines="' + escapeAttrHm(tagLinesJson) + '"' +
                ' data-tag-line-color="' + escapeAttrHm(tlColor) + '"' +
                ' data-tag-line-font-size="' + parseInt(tlFontSize, 10) + '"' +
                ' data-tag-line-font-weight="' + escapeAttrHm(tlFontWeight) + '">' +
                '<td class="row-selector text-center"><input type="checkbox" name="htl_header_imageBox[]" class="noborder qlo-img-checkbox" value="' + id + '"></td>' +
                '<td><img src="' + escapeHtmlHm(imgUrl) + '" class="qlo-img-thumb img-thumbnail" alt=""></td>' +
                '<td class="qlo-img-tagline-cell">' + escapeHtmlHm(cellText) + '</td>' +
                '<td class="pointer dragHandle center positionImage" id="td_qlo_img_' + id + '">' +
                    '<div class="dragGroup"><div class="positions">' + pos + '</div></div>' +
                '</td>' +
                '<td class="center">' +
                    '<a class="list-action-enable ajax_table_link ' + toggleClass + '"' +
                        ' href="' + escapeAttrHm(toggleUrl) + '">' +
                        '<i class="icon-check' + checkHidden + '"></i>' +
                        '<i class="icon-remove' + removeHidden + '"></i>' +
                    '</a>' +
                '</td>' +
                '<td class="text-right">' +
                    '<div class="btn-group-action">' +
                        '<div class="btn-group pull-right">' +
                            '<button type="button" class="btn btn-default qlo-edit-img" data-id="' + id + '">' +
                                '<i class="icon-pencil"></i>&nbsp;' + qloHmI18n.editLabel +
                            '</button>' +
                            '<button class="btn btn-default dropdown-toggle" data-toggle="dropdown">' +
                                '<i class="icon-caret-down"></i>&nbsp;' +
                            '</button>' +
                            '<ul class="dropdown-menu">' +
                                '<li>' +
                                    '<a href="javascript:void(0);" class="qlo-delete-img" data-id="' + id + '">' +
                                        '<i class="icon-trash"></i>&nbsp;' + qloHmI18n.deleteImageLabel +
                                    '</a>' +
                                '</li>' +
                            '</ul>' +
                        '</div>' +
                    '</div>' +
                '</td>' +
            '</tr>'
        );
        $row.data('tag-lines', tagLines);

        $('#qlo-image-tbody').append($row);
        $('#qlo-image-table').tableDnDUpdate();
        $('#qlo-no-images').hide();
        $('#qlo-image-count').text(parseInt($('#qlo-image-count').text(), 10) + 1);
        updateBulkActionsVisibility();
    }

    function updateBulkActionsVisibility() {
        var count = $('#qlo-image-tbody .qlo-img-row').length;
        $('#qlo-bulk-actions-row').toggle(count > 1);
    }

    function safeParseJsonHm(raw) {
        try { return JSON.parse(raw); } catch (_e) { return null; }
    }

    function escapeHtmlHm(str) {
        return String(str)
            .replace(/&/g, '&amp;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;')
            .replace(/"/g, '&quot;');
    }

    function escapeAttrHm(str) {
        return String(str)
            .replace(/&/g, '&amp;')
            .replace(/"/g, '&quot;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;');
    }


});
