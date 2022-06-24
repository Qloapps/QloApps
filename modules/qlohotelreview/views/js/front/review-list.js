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

var QhrReview = {
    ajaxUrl: qlo_hotel_review_js_vars.review_ajax_link + '?token=' + qlo_hotel_review_js_vars.review_ajax_token,
    btnLoadMore: '#btn-load-more-reviews',
    markHelpful: function(btnHelpful) {
        var idHotelReview = parseInt($(btnHelpful).data('id-hotel-review'));
        $.ajax({
            url: QhrReview.ajaxUrl + '&rand=' + new Date().getTime(),
            type: 'POST',
            data: {
                ajax: true,
                action: 'markReviewHelpful',
                id_hotel_review: idHotelReview,
            },
            dataType: 'JSON',
            headers: { 'cache-control': 'no-cache' },
            success: function(response) {
                if (response.status == 'ok') {
                    $(btnHelpful).fadeOut('slow', function() {
                        $(btnHelpful).remove();
                    })
                }
            }
        });
    },
    reportAbuse: function(btnReportAbuse) {
        var idHotelReview = parseInt($(btnReportAbuse).data('id-hotel-review'));
        $.ajax({
            url: QhrReview.ajaxUrl + '&rand=' + new Date().getTime(),
            type: 'POST',
            data: {
                ajax: true,
                action: 'reportAbuse',
                id_hotel_review: idHotelReview,
            },
            dataType: 'JSON',
            headers: { 'cache-control': 'no-cache' },
            success: function(response) {
                if (response.status == 'ok') {
                    $(btnReportAbuse).fadeOut('slow', function() {
                        $(btnReportAbuse).remove();
                    })
                }
            }
        });
    },
    sortBy: function(idHotel, sortBy) {
        var btnLoadMore = QhrReview.btnLoadMore;
        $.ajax({
            url: QhrReview.ajaxUrl + '&rand=' + new Date().getTime(),
            dataType: 'JSON',
            data: {
                ajax: true,
                action: 'sortBy',
                id_hotel: idHotel,
                sort_by: sortBy,
            },
            type: 'POST',
            headers: { 'cache-control': 'no-cache' },
            success: function(response) {
                if (response.status == 'ok' && response.message == 'HTML_OK') {
                    $('.review-list').animate({opacity: 0}, 250, 'linear', function() {
                        $('.review-list').html(response.html);
                        initRaty(qlo_hotel_review_js_vars.raty_img_path, '.review-list .raty');
                        $('.review-list').animate({opacity: 1}, 250, 'linear', function() {
                            if (response.has_next_page) {
                                $(btnLoadMore).attr('data-next-page', 2);
                                $(btnLoadMore).show(200);
                            } else {
                                $(btnLoadMore).hide(200);
                            }
                        });
                    })
                }
            }
        });
    },
    loadMore: function(idHotel, sortBy, page) {
        var btnLoadMore = QhrReview.btnLoadMore;
        $.ajax({
            url: QhrReview.ajaxUrl + '&rand=' + new Date().getTime(),
            dataType: 'JSON',
            data: {
                ajax: true,
                action: 'getReviews',
                id_hotel: idHotel,
                sort_by: sortBy,
                page: page,
            },
            type: 'POST',
            headers: { 'cache-control': 'no-cache' },
            success: function(response) {
                if (response.message == 'HTML_OK') {
                    $('.review-list').append(response.html);
                    initRaty(qlo_hotel_review_js_vars.raty_img_path, '.review-list .raty');

                    if (response.has_next_page) {
                        $(btnLoadMore).show(200);
                        $(btnLoadMore).attr('data-next-page', page + 1);
                    } else {
                        $(btnLoadMore).hide(200);
                    }
                }
            }
        });
    }
}

function initRaty(path, selector = '.raty') {
    $(selector).html(''); // reset first to avoid star duplications
    $.extend($.raty, { path: path });
    $(selector).raty({readOnly: true, hints: null, noRatedMsg: '0'});
}

function initCircleProgress() {
    $('.score-circle').each(function (i, v) {
        var value = $(v).data('value');
        var color = $(v).data('color');
        $(v).circleProgress({
            value: parseFloat(value),
            size: 120,
            thickness: '10',
            startAngle: -Math.PI / 2,
            fill: color,
            emptyFill: '#F2F2F2',
        });
    });
}

$(document).on('click', '.btn-helpful', function(e) {
    e.preventDefault();
    QhrReview.markHelpful(this);
});

$(document).on('click', '.btn-report-abuse', function(e) {
    e.preventDefault();
    QhrReview.reportAbuse(this);
});

$(document).on('click', '.btn-primary-review.view-all', function(e) {
    $('.media-list .media-item .img-fancybox').first().click();
});

$(document).on('click', '.img-fancybox', function(e) {
    var index = parseInt($(this).data('index'));
    if (qlo_hotel_review_js_vars.review_images.length) {
        $.fancybox.open(
            qlo_hotel_review_js_vars.review_images, {
                index: index,
            }
        );
    }
});

$(document).on('click', '.sort-by-option', function(e) {
    e.preventDefault();
    var idHotel = parseInt($(this).data('id-hotel'));
    var sortBy = parseInt($(this).data('value'));
    var optionText = $(this).text().trim();
    var currentValue = parseInt($(this).closest('.review-sort-by').find('button').attr('data-value'));
    if (sortBy != currentValue) {
        $(this).closest('.review-sort-by').find('button').attr('data-value', sortBy);
        $(this).closest('.review-sort-by').find('button span').first().text(optionText);
        QhrReview.sortBy(idHotel, sortBy);
    }
});

$(document).on('click', '#btn-load-more-reviews', function(e) {
    e.preventDefault();
    var btnLoadMore = $(this);
    var idHotel = parseInt($(btnLoadMore).data('id-hotel'));
    var sortBy = parseInt($('.review-sort-by').find('button').attr('data-value'));
    var page = parseInt($(btnLoadMore).attr('data-next-page'));
    QhrReview.loadMore(idHotel, sortBy, page);
});

$(document).ready(function () {
    if (typeof qlo_hotel_review_js_vars === 'object' && qlo_hotel_review_js_vars.id_order) {
        showOrder(1, qlo_hotel_review_js_vars.id_order, qlo_hotel_review_js_vars.link);
    }

    // init raty
    if (typeof qlo_hotel_review_js_vars === 'object' && qlo_hotel_review_js_vars.raty_img_path) {
        initRaty(qlo_hotel_review_js_vars.raty_img_path);
    }

    // init circle scores
    initCircleProgress();

    // init fancybox
    $('.review-images-fancybox').fancybox();
});
