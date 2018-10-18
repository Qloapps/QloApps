/**
* 2010-2018 Webkul.
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
*  @copyright 2010-2018 Webkul IN
*  @license   https://store.webkul.com/license.html
*/

$(document).ready(function () {
    $('.htl-img-preview').fancybox({
        width: 'auto',
        height: 'auto',
        autoSize : false,
        maxWidth: 700,
        'hideOnContentClick': false,
    });

    $("#hotel_images").on("change", function(event) {
        files = event.target.files;
        for (var i = 0; i < files.length; i++) {
            var formData = new FormData();
            var file = files[i];

            formData.append('hotel_image', file);
            uploadHotelImages(formData);
        }
    });

    function uploadHotelImages(formData) {
        var idHotel = $("#id-hotel").val();
        formData.append('id_hotel', idHotel);
        formData.append('ajax', true);
        formData.append('action', 'uploadHotelImages');

        $.ajax({
            type:'POST',
            url: adminHotelCtrlUrl,
            data: formData,
            cache:false,
            contentType: false,
            processData: false,
            success:function(image){
                image = JSON.parse(image);
                if (!image.hasError) {
                    $('.list-empty-tr').remove();
                    var html = '';
                    html += '<tr class="';
                        if(image.cover) {
                            html += 'cover-image-tr';
                        }
                    html += '">';
                        html += '<td class="text-center">'+image.id_image+'</td>';
                        html += '<td class="text-center">';
                            html += '<a class="htl-img-preview" href="'+image.image_url+'">';
                                html += '<img class="img-thumbnail" width="100" src="'+image.image_url+'"/>';
                            html += '</a>';
                        html += '</td>';
                        html += '<td class="text-center ';
                            if(image.cover) {
                                html += 'cover-image-td';
                            }
                        html += '">';
                            html += '<a href="#" class="';
                                if(image.cover) {
                                    html += 'text-success';
                                } else {
                                    html += 'text-danger';
                                }
                            html += ' changer-cover-image" data-id-hotel="'+idHotel+'" data-is-cover="';
                                if(image.cover) {
                                    html += '1';
                                } else {
                                    html += '0';
                                }
                            html += '" data-id-image="'+image.id_image+'">';
                                if(image.cover) {
                                    html += '<i class="icon-check"></i>';
                                } else {
                                    html += '<i class="icon-times"></i>';
                                }
                            html += '</a>';
                        html += '</td>';
                        html += '<td class="text-center">';
                            html += '<button type="button" class="btn btn-default delete-hotel-image" data-id-hotel="'+idHotel+'" data-is-cover="';
                                if(image.cover) {
                                    html += '1';
                                } else {
                                    html += '0';
                                }
                            html += '" data-id-image="'+image.id_image+'"><i class="icon-trash"></i></button>';
                        html += '</td>';
                    html += '</tr>';
                    $("#hotel-image-table tbody").append(html);

                    showSuccessMessage(imgUploadSuccessMsg);
                } else {
                    if (typeof image.message != 'undefined') {
                        showErrorMessage(image.message);
                    } else {
                        showErrorMessage(imgUploadErrorMsg);
                    }
                }
            },
            error: function(data) {
                showErrorMessage(imgUploadErrorMsg);
            }
        });
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
                data: {
                    ajax: true,
                    action: 'changeCoverImage',
                    id_hotel: idHotel,
                    id_image: idImage,
                },
                success: function(result) {
                    if (result) {
                        // remover cover image identifier from old cover image
                        var oldCoverImageTr = $("#hotel-image-table tbody tr.cover-image-tr");
                        oldCoverImageTr.removeClass("cover-image-tr").find("td.cover-image-td").removeClass("cover-image-td").find("a.changer-cover-image").removeClass("text-success").addClass("text-danger").attr("data-is-cover", "0").find("i.icon-check").removeClass("icon-check").addClass("icon-times");
                        oldCoverImageTr.find("td button.delete-hotel-image").attr("data-is-cover", "0");

                        // Add classes in new covre image elements
                        triggerElement.removeClass("text-danger").addClass("text-success").find("i.icon-times").removeClass("icon-times").addClass("icon-check");
                        triggerElement.parent().addClass("cover-image-td").parent().addClass("cover-image-tr").find("td button.delete-hotel-image").attr("data-is-cover", "1");

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

    $('body').on('click', '.delete-hotel-image', function(e){
        e.preventDefault();
        var idHotel = $(this).attr('data-id-hotel');
        var idImage = $(this).attr('data-id-image');
        var isCover = $(this).attr('data-is-cover');
        var triggerElement = $(this);

        if (parseInt(idHotel) && parseInt(idImage)) {
            $.ajax({
                type:'POST',
                url: adminHotelCtrlUrl,
                data: {
                    ajax: true,
                    action: 'deleteHotelImage',
                    id_hotel: idHotel,
                    id_image: idImage,
                },
                success: function(result) {
                    if (result) {
                        if (parseInt(isCover)) {
                            location.reload();
                        } else {
                            var currentPosition = parseInt(triggerElement.parents('tr').find('td.image-position').text());
                            var nextAllSiblings = triggerElement.parents('tr').nextAll();
                            triggerElement.parents('tr').fadeOut().remove();

                            // Correct Images Positions
                            $(nextAllSiblings).each(function(index, element) {
                                $(this).find('td.image-position').text(parseInt(currentPosition + parseInt(index)));
                            });
                            showSuccessMessage(deleteImgSuccessMsg);
                        }
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