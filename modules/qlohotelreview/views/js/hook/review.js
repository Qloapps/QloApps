/**
* 2010-2022 Webkul.
*
* NOTICE OF LICENSE
*
* All right is reserved,
* Please go through LICENSE.txt file inside our module
*
* DISCLAIMER
*
* Do not edit or add to this file if you wish to upgrade this module to newer
* versions in the future. If you wish to customize this module for your
* needs please refer to CustomizationPolicy.txt file inside our module for more information.
*
* @author Webkul IN
* @copyright 2010-2022 Webkul IN
* @license LICENSE.txt
*/

var QhrReviewForm = {
    init: function(idOrder, idHotel, hotelName) {
        $('#add-review-form').find('[name="id_order"]').val(idOrder);
        $('#add-review-form').find('[name="id_hotel"]').val(idHotel);
        $('#add-review-form').find('.hotel-name').html(hotelName);
        QhrReviewImages.init();
    },
    show: function() {
        $.fancybox.open({
            href: '#add-review-popup',
            wrapCSS: 'fancybox-add-review',
            padding: 20,
        });
    },
    close: function() {
        $.fancybox.close();
    },
    beforeSubmit: function() {
        QhrReviewForm.hideGeneralErrors();
        QhrReviewForm.disableSubmitButton();
    },
    submit: function() {
        QhrReviewForm.beforeSubmit();
        var formData = new FormData($('#add-review-form').get(0));
        formData.append('ajax', true);
        formData.append('token', qlo_hotel_review_js_vars.review_ajax_token);
        formData.append('action', 'AddReview');
        $.ajax({
            url: qlo_hotel_review_js_vars.review_ajax_link,
            data: formData,
            processData: false,
            contentType: false,
            type: 'POST',
            success: function(response) {
                var jsonResponse = JSON.parse(response);
                if (jsonResponse.status == true) {
                    QhrReviewForm.close();
                    QhrReviewForm.showSuccessMessage();
                    QhrReviewForm.removeBtn();
                    QhrReviewForm.reset();
                } else if (Object.keys(jsonResponse.errors.by_key).length) {
                    $.each(jsonResponse.errors.by_key, function(key, value) {
                        $('.review-error.' + key).html(value);
                    });
                } else {
                    QhrReviewForm.showGeneralErrors(jsonResponse.errors.general);
                }
            },
            complete: function() {
                QhrReviewForm.enableSubmitButton();
            },
        });
    },
    showGeneralErrors: function(errors) {
        $('#review-general-errors').stop().html(errors);
        $('#review-general-errors').show('slow');
    },
    hideGeneralErrors: function() {
        $('#review-general-errors').hide('slow', function() {
            $('#review-general-errors').html('');
        });
    },
    enableSubmitButton: function() {
        $('#btn-submit-review').removeClass('disabled');
    },
    disableSubmitButton: function() {
        $('#btn-submit-review').addClass('disabled');
    },
    showSuccessMessage: function() {
        var href = '';
        if (qlo_hotel_review_js_vars.admin_approval_enabled) {
            href = '#popup-review-submit-success-with-approval';
        } else {
            href = '#popup-review-submit-success-no-approval';
        }

        $.fancybox.open({
            href: href,
            wrapCSS: 'fancybox-add-review-success',
            padding: 20,
        });
    },
    reset: function() {
        $('#add-review-popup .previews-wrap').html('');
        $('#add-review-popup .inputs-wrap').html('');
        $('#add-review-popup [name="subject"]').val('');
        $('#add-review-popup [name="description"]').val('');
        $('#add-review-popup .review-error').html('');
        initRaty(qlo_hotel_review_js_vars.raty_img_path);
    },
    removeBtn: function() {
        var btnAddReview = $('#add-review-btn');
        $(btnAddReview).hide('slow', function() {
            $(btnAddReview).remove();
        });
    },
    remove: function() {
        var btnAddReview = $('#add-review-btn');
        var reviewPopup = $('#qlohotelreview');

        $.each([btnAddReview, reviewPopup], function(i, v) {
            $(v).hide('slow', function() {
                $(v).remove();
            });
        });
    },
}

var QhrReviewImages = {
    init: function() {
        QhrReviewImages.inputHtml = '<input type="file" accept="image/*" class="input-images hidden" name="images[]" multiple>';
    },
    getFilesCount: function() {
        var count = 0;
        $('.images-field input.input-images').each(function(i, input) {
            count += $(input).get(0).files.length;
        });
        return count;
    },
    addFileToPreview: function(file, iInput, iFile) {
        var reader = new FileReader();
        reader.onload = function (e) {
            $('.previews-wrap').append('<span><img data-i-input="'+iInput+'" data-i-file="'+iFile+'" src="'+e.target.result+'" class="img-preview"><i class="icon icon-times-circle-o"></i></span>');
        };
        reader.readAsDataURL(file);
    },
    removeFile: function(iInput, iFile) {
        var input = $('.images-field input.input-images').get(iInput);
        var files = $(input).get(0).files;
        var dataTransfer = new DataTransfer();
        $(files).each(function(iiFile, file) {
            if (iiFile != iFile) {
                dataTransfer.items.add(file);
            }
        });
        input.files = dataTransfer.files;
    },
    resetPreviews: function() {
        $('.previews-wrap').html('');
    },
    updatePreviews: function() {
        QhrReviewImages.resetPreviews();
        $('.images-field input.input-images').each(function(iInput, input) {
            $.each($(input).get(0).files, function(iFile, file) {
                QhrReviewImages.addFileToPreview(file, iInput, iFile);
            })
        });
    },
    cleanInputFields: function() {
        $('.images-field input.input-images').each(function(i, input) {
            if (!$(input).get(0).files.length) {
                $(input).remove();
            }
        });
    },
    addMore: function() {
        QhrReviewImages.cleanInputFields();
        $('.images-field').find('.inputs-wrap').append(QhrReviewImages.inputHtml);
        $('.images-field').find('.inputs-wrap input.input-images').last().click();
    },
    removeLastInput: function() {
        $('.images-field').find('.inputs-wrap input.input-images').last().remove(0);
    },
    hideAddImagesButton: function() {
        $('.image-input-btn').hide(200);
    },
    showAddImagesButton: function() {
        $('.image-input-btn').show(200);
    },
}

function initRaty(path) {
    $('.raty').html(''); // reset first to avoid star duplications
    $.extend($.raty, { path: path });
    $('.raty').raty({ score: 0, half: true, hints: null, noRatedMsg: '0' });
}

$(document).on('click', '#add-review-btn', function(e) {
    e.preventDefault();
    var idOrder = parseInt($(this).data('id-order'));
    var idHotel = parseInt($(this).data('id-hotel'));
    var hotelName = $(this).data('hotel-name');
    QhrReviewForm.init(idOrder, idHotel, hotelName);
    QhrReviewForm.show();
});

$(document).on('click', '#btn-submit-review', function(e) {
    e.preventDefault();
    QhrReviewForm.submit();
});

$(document).on('click', '#btn-cancel-review', function(e) {
    e.preventDefault();
    QhrReviewForm.close();
});

$(document).on('click', '.image-input-btn', function(e) {
    e.preventDefault();
    QhrReviewImages.addMore();
});

$(document).on('click', '.img-preview + i', function(e) {
    e.preventDefault();
    var img = $(this).siblings('img');
    var iInput = parseInt($(img).attr('data-i-input'));
    var iFile = parseInt($(img).attr('data-i-file'));
    QhrReviewImages.removeFile(iInput, iFile);
    $(this).closest('span').remove();
    QhrReviewImages.showAddImagesButton();
});

$(document).on('change', 'input.input-images', function(e) {
    e.preventDefault();
    if (!this.files.length) {
        $(this).remove();
        return;
    }

    var filesCount = QhrReviewImages.getFilesCount();
    if (filesCount > qlo_hotel_review_js_vars.num_images_max) {
        QhrReviewImages.removeLastInput();
        alert(qlo_hotel_review_js_vars.texts.num_files);
    } else {
        QhrReviewImages.updatePreviews();

        if (filesCount == qlo_hotel_review_js_vars.num_images_max) {
            QhrReviewImages.hideAddImagesButton();
        }
    }
});

$(document).ready(function () {
    if (typeof qlo_hotel_review_js_vars === 'object' && qlo_hotel_review_js_vars.id_order) {
        showOrder(1, qlo_hotel_review_js_vars.id_order, qlo_hotel_review_js_vars.link);
    }

    // init raty
    if (typeof qlo_hotel_review_js_vars === 'object' && qlo_hotel_review_js_vars.raty_img_path) {
        initRaty(qlo_hotel_review_js_vars.raty_img_path);
    }
});
