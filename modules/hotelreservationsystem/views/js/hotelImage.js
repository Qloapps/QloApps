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
    $('.htl-img-preview').fancybox({
        width: 'auto',
        height: 'auto',
        autoSize : false,
        maxWidth: 700,
        'hideOnContentClick': false,
    });

    $("#hotel_images").on("change", function(event) {
        const files = Array.from(event.target.files);
        uploadSelectedImages(files);
    });

    function uploadSelectedImages(files)
    {
        if (files.length == 0) return; // all done
        let file = files[0];
        files.splice(0, 1);
        createImageUploadRequest(file).then((formData) => {
            uploadHotelImages(formData).then(() => {
                uploadSelectedImages(files)
            });
        })
    }

    function createImageUploadRequest(file)
    {
        return new Promise((resolve, reject) => {
            if (typeof file != 'undefined') {
                if (file.size > maxSizeAllowed) {
                    reject(filesizeError + '[' + file.name +  ']');
                } else {
                    var formData = new FormData();
                    var idHotel = $("#id-hotel").val();
                    formData.append('hotel_image', file);
                    formData.append('id_hotel', idHotel);
                    formData.append('ajax', true);
                    formData.append('action', 'uploadHotelImages');
                    resolve(formData);
                }
            }
        });
    }

    function uploadHotelImages(formData) {
        return new Promise((resolve, reject) => {
            $.ajax({
                type:'POST',
                dataType:'JSON',
                url: adminHotelCtrlUrl,
                data: formData,
                cache:false,
                contentType: false,
                processData: false,
                success:function(response){
                    if (response.success) {
                        $('.list-empty-tr').remove();
                        $("#hotel-image-table tbody").append(response.data.image_row);
                        showSuccessMessage(imgUploadSuccessMsg);
                        resolve(imgUploadSuccessMsg);
                    } else {
                        if (typeof response.errors != 'undefined') {
                            showErrorMessage(response.errors);
                            reject(response.errors);
                        } else {
                            showErrorMessage(imgUploadErrorMsg);
                            reject(imgUploadErrorMsg);
                        }
                    }
                },
                error: function(data) {
                    showErrorMessage(imgUploadErrorMsg);
                    reject(imgUploadErrorMsg);
                }
            });
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