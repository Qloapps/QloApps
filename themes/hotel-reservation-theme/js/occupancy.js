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


$(document).ready(function(){
    $(document).on('click', '.booking_occupancy_wrapper .remove-room-link', function(e) {
        e.preventDefault();

		booking_occupancy_inner = $(this).closest('.booking_occupancy_inner');
        var booking_occupancy_wrapper = $(this).closest('.booking_occupancy_wrapper');
        $(this).closest('.occupancy_info_block').hide('fast', function(){
            $(this).remove();
            $(booking_occupancy_inner).find('.room_num_wrapper').each(function(key, val) {
                $(this).text(room_txt + ' - '+ (key+1) );
            });
            var countRooms = parseInt($(booking_occupancy_wrapper).find('.occupancy_info_block').length);
            if (countRooms < $(booking_occupancy_wrapper).find('.max_avail_type_qty').val()) {
                $(booking_occupancy_wrapper).find('.add_new_occupancy_btn').removeClass('disabled');
            }
            setRoomTypeGuestOccupancy($(booking_occupancy_inner).closest('.booking_occupancy_wrapper'));
        });
    });

	$(document).on('click', '.booking_occupancy_wrapper .occupancy_quantity_up', function(e) {
        e.preventDefault();
		// set input field value
		let max_guests_in_room = $(this).closest(".booking_occupancy_wrapper").find('.max_guests').val();
        let element = $(this).closest('.occupancy_count_block').find('.num_occupancy');
		let elementVal = parseInt(element.val());

		let current_room_occupancy = 0;
		$(this).closest('.occupancy_info_block').find('.num_occupancy').each(function(){
			current_room_occupancy += parseInt($(this).val());
		});
		let max_allowed_for_current = (max_guests_in_room - current_room_occupancy) + elementVal;

        let childElement = $(this).closest('.occupancy_count_block').find('.num_children').length;
        if (childElement) {
			let max_child_in_room;
			if ($(this).closest(".booking_occupancy_wrapper").find('.max_children').val()) {
				max_child_in_room = $(this).closest(".booking_occupancy_wrapper").find('.max_children').val();
			} else {
				max_child_in_room = window.max_child_in_room;
			}
            if (elementVal < max_child_in_room && elementVal < max_allowed_for_current) {
                element.val(elementVal + 1);
                $(this).closest('.occupancy_info_block').find('.children_age_info_block').show();

                let roomBlockIndex = parseInt($(this).closest('.occupancy_info_block').attr('occ_block_index'));
                var childAgeSelectWrapper = $('<div>');
                var childAgeSelect = $('<select>', {
                    class: 'guest_child_age custom-select small room_occupancies',
                    name: 'occupancy[' + roomBlockIndex + '][child_ages][]'
                });

                childAgeSelect.append($('<option>', { value: -1, text: select_age_txt }), $('<option>', { value: 0, text: under_1_age }));
                for (let age = 1; age < max_child_age; age++) {
                    childAgeSelect.append(
                        $('<option>', {
                            value: age,
                            text: age
                        })
                    );
                }

                childAgeSelectWrapper.append(childAgeSelect);
                $(this).closest('.occupancy_info_block').find('.children_ages').append(childAgeSelectWrapper);
                // set input field value
                $(this).closest('.occupancy_count_block').find('.occupancy_count > span').text(elementVal + 1);
            } else {
                if (elementVal >= max_child_in_room) {
                    if (elementVal == 0) {
                        showOccupancyError(no_children_allowed_txt, $(this).closest(".occupancy_info_block"));
                    } else {
                        showOccupancyError(max_children_txt, $(this).closest(".occupancy_info_block"));
                    }
                } else {
                    showOccupancyError(max_occupancy_reached_txt, $(this).closest(".occupancy_info_block"));
                }
            }
        } else {
			let max_adults_in_room;
			if ($(this).closest(".booking_occupancy_wrapper").find('.max_adults').val()) {
				max_adults_in_room = $(this).closest(".booking_occupancy_wrapper").find('.max_adults').val();
			}
			if (elementVal < max_adults_in_room && elementVal < max_allowed_for_current) {
				element.val(elementVal + 1);
				$(this).closest('.occupancy_count_block').find('.occupancy_count > span').text(elementVal + 1);
            } else {
                if (elementVal >= max_adults_in_room) {
                    showOccupancyError(max_adults_txt, $(this).closest(".occupancy_info_block"));
                } else {
                    showOccupancyError(max_occupancy_reached_txt, $(this).closest(".occupancy_info_block"));
                }
			}
        }
        setRoomTypeGuestOccupancy($(this).closest('.booking_occupancy_wrapper'));
    });

    var errorMsgTime;
    $('.occupancy-input-errors').parent().hide();
    function showOccupancyError(msg, occupancy_info_block)
    {
        var errorMsgBlock = $(occupancy_info_block).find('.occupancy-input-errors')
        $(errorMsgBlock).html(msg).parent().show('fast');
        clearTimeout(errorMsgTime);
        errorMsgTime = setTimeout(function() {
            $(errorMsgBlock).parent().hide('fast');
        }, 1000);

    }

	$(document).on('click', '.booking_occupancy_wrapper .occupancy_quantity_down', function(e) {
        e.preventDefault();

        // set input field value
        var element = $(this).closest('.occupancy_count_block').find('.num_occupancy');
        var elementVal = parseInt(element.val()) - 1;
        var childElement = $(this).closest('.occupancy_count_block').find('.num_children').length;

        if (childElement) {
            if (elementVal < 0) {
                elementVal = 0;
            } else {
                $(this).closest('.occupancy_info_block').find('.children_ages select').last().closest('div').remove();
                if (elementVal <= 0) {
                    $(this).closest('.occupancy_info_block').find('.children_age_info_block').hide();
                }
            }
        } else {
            if (elementVal == 0) {
                elementVal = 1;
            }
        }

        element.val(elementVal);
        // set input field value
        $(this).closest('.occupancy_count_block').find('.occupancy_count > span').text(elementVal);

        setRoomTypeGuestOccupancy($(this).closest('.booking_occupancy_wrapper'));
    });

	$(document).on('click', '.booking_guest_occupancy', function(e) {
        $(this).parent().find('.booking_occupancy_wrapper').toggle();
    });

    $(document).on('click', '.booking_occupancy_wrapper .submit_occupancy_btn', function(e) {
        e.preventDefault();
        if ($('.booking_occupancy_wrapper:visible').length) {
            return validateBookingOccupancies(e);
        }
    });

	$(document).on('click', function(e) {
        if ($('.booking_occupancy_wrapper:visible').length) {
            if (!($(e.target).closest(".booking_occupancy_wrapper").length
                || $(e.target).closest(".booking_guest_occupancy").length
                || $(e.target).closest(".remove-room-link").length
            )) {
                return validateBookingOccupancies(e);
			}
        }
    });

    function validateBookingOccupancies(e) {
        var occupancy_wrapper = $('.booking_occupancy_wrapper:visible');
        $(occupancy_wrapper).find(".occupancy_info_block").addClass('selected');
        setRoomTypeGuestOccupancy(occupancy_wrapper);
        let hasErrors = 0;
        let adults = $(occupancy_wrapper).find(".num_adults").map(function(){return $(this).val();}).get();
        let children = $(occupancy_wrapper).find(".num_children").map(function(){return $(this).val();}).get();
        let child_ages = $(occupancy_wrapper).find(".guest_child_age").map(function(){return $(this).val();}).get();

        // start validating above values
        if (!adults.length || (adults.length != children.length)) {
            hasErrors = 1;
            showErrorMessage(invalid_occupancy_txt);
        } else {
            $(occupancy_wrapper).find('.occupancy_count').removeClass('border-danger');

            // validate values of adults and children
            adults.forEach(function (item, index) {
                if (isNaN(item) || parseInt(item) < 1) {
                    hasErrors = 1;
                    $(occupancy_wrapper).find(".num_adults").eq(index).closest('.occupancy_count_block').find('.occupancy_count').addClass('border-danger');
                }
                if (isNaN(children[index])) {
                    hasErrors = 1;
                    $(occupancy_wrapper).find(".num_children").eq(index).closest('.occupancy_count_block').find('.occupancy_count').addClass('border-danger');
                }
            });

            // validate values of selected child ages
            $(occupancy_wrapper).find('.guest_child_age').removeClass('border-danger');
            child_ages.forEach(function (age, index) {
                age = parseInt(age);
                if (isNaN(age) || (age < 0) || (age >= parseInt(max_child_age))) {
                    hasErrors = 1;
                    $(occupancy_wrapper).find(".guest_child_age").eq(index).addClass('border-danger');
                }
            });
        }
        if (hasErrors == 0) {
            if (!($(e.target).closest(".ajax_add_to_cart_button").length
                || $(e.target).closest(".exclusive.book_now_submit").length
            )) {
                $(occupancy_wrapper).css({'display' : 'none'});
                $(occupancy_wrapper).siblings(".booking_guest_occupancy").removeClass('border-danger');
                $(document).trigger( "QloApps:updateRoomOccupancy", [occupancy_wrapper]);
            }
        } else {
            $(occupancy_wrapper).siblings(".booking_guest_occupancy").addClass('border-danger');

            return false;
        }

        return true;
    }

	$(document).on('click', '.booking_occupancy_wrapper .add_new_occupancy_btn', function(e) {
        e.preventDefault();
        var booking_occupancy_wrapper = $(this).closest('.booking_occupancy_wrapper');
        var roomBlockIndex = parseInt($(booking_occupancy_wrapper).find(".occupancy_info_block").last().attr('occ_block_index'));
        roomBlockIndex += 1;

        var countRooms = parseInt($(booking_occupancy_wrapper).find('.occupancy_info_block').length);
        countRooms += 1
        if ($(booking_occupancy_wrapper).find('.max_avail_type_qty').val() > 0
			&& countRooms <= $(booking_occupancy_wrapper).find('.max_avail_type_qty').val()
		) {
            var occupancy_adults_block = $('<div>', { class: 'form-group occupancy_count_block col-sm-5 col-6' }).append(
                $('<label>').text(adults_txt),
                $('<div>', { class: 'clearfix d-flex' }).append(
                    $('<input>', {
                        type: 'hidden',
                        class: 'num_occupancy num_adults room_occupancies',
                        name: 'occupancy[' + roomBlockIndex + '][adults]',
                        value: 1
                    }),
                    $('<div>', {
                        class: 'occupancy_count border p-small rounded float-left mr-1 max-width-7'
                    }).append($('<span>', {class : 'min-width-3 d-block text-center'}).text('1')),
                    $('<div>', { class: 'qty_direction d-flex flex-column gap-1' }).append(
                        $('<a>', { href: '#', class: 'p-0 border rounded occupancy_quantity_up' }).append($('<i>', { class: 'icon-plus' })),
                        $('<a>', { href: '#', class: 'p-0 border rounded occupancy_quantity_down' }).append($('<i>', { class: 'icon-minus' }))
                    )
                )
            );

            var occupancy_children_block = $('<div>', { class: 'form-group occupancy_count_block col-sm-7 col-6' }).append(
                $('<label>').text(children_txt),
                $('<div>', { class: 'clearfix d-flex' }).append(
                    $('<input>', {
                        type: 'hidden',
                        class: 'num_occupancy num_children room_occupancies',
                        name: 'occupancy[' + roomBlockIndex + '][children]',
                        value: 0
                    }),
                    $('<div>', {
                        class: 'occupancy_count border p-small rounded float-left mr-1 max-width-7'
                    }).append($('<span>', {class: 'min-width-3 d-block text-center'}).text('0')),
                    $('<div>', { class: 'qty_direction d-flex flex-column gap-1' }).append(
                        $('<a>', { href: '#', class: 'p-0 border rounded occupancy_quantity_up' }).append($('<i>', { class: 'icon-plus' })),
                        $('<a>', { href: '#', class: 'p-0 border rounded occupancy_quantity_down' }).append($('<i>', { class: 'icon-minus' }))
                    )
                ),
                $('<div>', {
                    class: 'label-desc-txt help_block',
                    text: '(' + below_txt + ' ' + max_child_age + ' ' + years_txt + ')'
                })
            );

            var occupancy_row = $('<div>', { class: 'row' }).append(occupancy_adults_block, occupancy_children_block);

            var occupancy_children_age_block = $('<div>', { class: 'row' }).append(
                $('<div>', { class: 'form-group children_age_info_block col-sm-12' }).append(
                    $('<label>', {class: 'help_block'}).text(all_children_txt),
                    $('<div>', { class: 'children_ages d-grid gap-1 justify-content-between' })
                ).css('display', 'none')
            );

            var occupancy_info_block = $('<div>', {
                class: 'occupancy_info_block',
                occ_block_index: roomBlockIndex
            }).append(occupancy_row, occupancy_children_age_block);

            var occupancy_header = $('<div>', { class: 'occupancy_info_head font-weight-bold mb-2' }).append(
                $('<span>', {
                    class: 'room_num_wrapper',
                    text: room_txt + ' - ' + countRooms
                }),
                $('<a>', {
                    href: '#',
                    class: 'remove-room-link float-right text-danger text-decoration-none',
                    text: remove_txt
                })
            );

            var occupancy_block = $('<div>', { class: 'occupancy_info_block' })
                .append(occupancy_header, occupancy_info_block, $('<hr>', { class: 'occupancy-info-separator ml-n3 mr-n3' }));

            $(booking_occupancy_wrapper).find('.booking_occupancy_inner').append(occupancy_block).find('[occ_block_index="'+roomBlockIndex+'"]').show('fast');

            // scroll to the latest added room
            // var objDiv = document.getElementById("booking_occupancy_wrapper");
            // objDiv.scrollTop = objDiv.scrollHeight;
			$(booking_occupancy_wrapper).animate({ scrollTop: $(booking_occupancy_wrapper).prop('scrollHeight') }, "slow");
            if (countRooms >= $(booking_occupancy_wrapper).find('.max_avail_type_qty').val()) {
                $(this).addClass('disabled');
            }
        }

        setRoomTypeGuestOccupancy(booking_occupancy_wrapper);
    });

	// The button to increment the product value
	$(document).on('click', '.rm_quantity_up', function(e){
		e.preventDefault();

		var element = $(this).closest('.rm_qty_cont').find('.quantity_wanted');
		var elementVal = parseInt(element.val()) + 1;
		let quantityAvailableT = $(this).closest('.rm_qty_cont').find(".max_avail_type_qty").val();
		if (isNaN(elementVal) || elementVal > quantityAvailableT) {
			elementVal = quantityAvailableT;
		}
		element.val(elementVal);
		$(this).closest('.rm_qty_cont').find('.qty_count > span').text(elementVal);
		$(document).trigger( "QloApps:updateRoomQuantity", [element]);
	});

	// The button to decrement the product value
	$(document).on('click', '.rm_quantity_down', function(e){
		e.preventDefault();
		var element = $(this).closest('.rm_qty_cont').find('.quantity_wanted');
		var elementVal = parseInt(element.val()) - 1;
		if (isNaN(elementVal) || elementVal < 1) {
			elementVal = 1;
		}

		element.val(elementVal);
		$(this).closest('.rm_qty_cont').find('.qty_count > span').text(elementVal);
		$(document).trigger( "QloApps:updateRoomQuantity", [element]);
	});

	$(document).on('hover', '.add_new_occupancy_btn', function(e) {
		if ($(this).hasClass('disabled')) {
			$(this).attr('title', $(this).attr('data-title-unavailable'));
		} else {
			$(this).attr('title', $(this).attr('data-title-available'));
		}
	});
});


function setRoomTypeGuestOccupancy(booking_occupancy_wrapper)
{
    var adults = 0;
    var children = 0;
	var rooms = $(booking_occupancy_wrapper).find('.occupancy_info_block').length;

	$(booking_occupancy_wrapper).find(".num_adults" ).each(function(key, val) {
        adults += parseInt($(this).val());
    });
    $(booking_occupancy_wrapper).find(".num_children" ).each(function(key, val) {
        children += parseInt($(this).val());
    });
	guestButtonVal = getRoomTypeGuestOccupancyFormated(adults, children, rooms);
	$(booking_occupancy_wrapper).siblings('.booking_guest_occupancy').find('span').text(guestButtonVal);
}

function getRoomTypeGuestOccupancyFormated(adults, children, rooms)
{
	var guestButtonVal = parseInt(adults) + ' ';
    if (parseInt(adults) > 1) {
        guestButtonVal += adults_txt;
    } else {
        guestButtonVal += adult_txt;
    }
    if (parseInt(children) > 0) {
        if (parseInt(children) > 1) {
            guestButtonVal += ', ' + parseInt(children) + ' ' + children_txt;
        } else {
            guestButtonVal += ', ' + parseInt(children) + ' ' + child_txt;
        }
    }
    if (parseInt(rooms) > 1) {
        guestButtonVal += ', ' + parseInt(rooms) + ' ' + rooms_txt;
    } else {
        guestButtonVal += ', ' + parseInt(rooms) + ' ' + room_txt;
    }

	return guestButtonVal;
}


function resetOccupancyField(booking_occupancy_wrapper)
{
	// $(booking_occupancy_wrapper).siblings('.booking_guest_occupancy').find('span').text(select_occupancy_txt);
	// $(booking_occupancy_wrapper).find('.booking_occupancy_inner > div').each(function(index, element){
	// 	let num_adults = $(booking_occupancy_wrapper).find('.base_adult').val();
	// 	if (index == 0) {
	// 		$(this).removeClass('selected');
	// 		$(this).find('.num_adults').val(num_adults).siblings('.occupancy_count').find('span').text(num_adults);
	// 		$(this).find('.num_children').val(0).siblings('.occupancy_count').find('span').text(0);
	// 		$(this).find('.children_ages > div').remove();
	// 	} else {
	// 		$(element).remove();
	// 	}
	// });
}
