/**
* Since 2010 Webkul.
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
*  @copyright Since 2010 Webkul IN
*  @license   https://store.webkul.com/license.html
*/


$(document).ready(function() {
    // For processing room reallocation and swapping
    $(document).on('click', '.room_reallocate_swap', function(e) {
        e.preventDefault();
        RoomReallocationModal.show($(this));
    });

    $(document).on('hidden.bs.modal', '#room-reallocation-modal', function(){
        location.reload();
    });

    /*For reallocating rooms in the modal*/
    $(document).on('click', '#realloc_allocated_rooms', function(e){
        if (RoomReallocationModal.reallocate() == false) {
            return false;
        }
    });
    /*For swaping rooms in the modal*/
    $(document).on('click', '#swap_allocated_rooms', function(){
        if (RoomReallocationModal.swap() == false) {
            return false;
        }
    });

    // change room type for reallocation
    $(document).on("change", "#realloc_avail_room_type", function(e) {
        e.preventDefault();
        RoomReallocationModal.changeRoomType($(this));
    });
});

// Modal object to handle room reallocation processes
const RoomReallocationModal = {
    show: function(roomObj) {
        var id_order = roomObj.data('id_order');
        $(".loading_overlay").show();
        $.ajax({
            type: 'POST',
            headers: {
                "cache-control": "no-cache"
            },
            url: rooms_reallocation_url,
            dataType: 'JSON',
            cache: false,
            data: 'ajax=true&id_order='+id_order+'&action=InitRoomReallocationModal',
            success: function(result) {
                if (result.hasError == 0 && result.modalHtml) {
                    $('.bootstrap').append(result.modalHtml);

                    $(".modal_id_htl_booking").val(roomObj.data('id_htl_booking'));
                    $("input.modal_curr_room_num").val(roomObj.data('room_num'));
                    $("span.modal_curr_room_num").text(roomObj.data('room_num') + ', ' + roomObj.data('room_type_name'));
                    $(".cust_name").text(roomObj.data('cust_name'));
                    $(".cust_email").text(roomObj.data('cust_email'));

                    // reset price difference fields
                    $("#reallocation_price_diff").val(0);
                    $("#reallocation_price_diff_block").hide();
                    $(".realloc_roomtype_change_message").hide();

                    // For Rooms Swapping
                    var json_arr_rm_swp = roomObj.data('avail_rm_swap');
                    if (roomObj.data('avail_rm_swap') != 'false' && json_arr_rm_swp.length != 0) {
                        html = '<select class="form-control" name="swap_avail_rooms" id="swap_avail_rooms">';
                            $.each(json_arr_rm_swp, function(key,val) {
                                html += '<option class="swp_rm_opts" value="'+val.id_hotel_booking+'" >'+val.room_num+'</option>';
                            });
                        html += '</select>';
                        $(".swap_avail_rooms_container").empty().append(html);
                    } else {
                        $(".swap_avail_rooms_container").empty().text(no_swap_rm_avail_txt).addClass('text-danger');
                        $("#swap_room_tab .modal-footer").hide();
                    }

                    // For Rooms Reallocation
                    var json_arr_realloc_room_types = roomObj.data('avail_realloc_room_types');
                    if (roomObj.data('avail_realloc_room_types') != 'false' && json_arr_realloc_room_types.length != 0) {
                        var idCurrentRoomType = roomObj.data('id_room_type');
                        var roomsTypesHtml = '<select data-id_htl_booking="' + roomObj.data('id_htl_booking') + '" class="form-control" name="realloc_avail_room_type" id="realloc_avail_room_type">';
                            $.each(json_arr_realloc_room_types, function(key, room_type) {
                                roomsTypesHtml += "<option rooms_available='" + JSON.stringify(room_type.rooms) + "' class='realloc_rm_type_opts' value='" + room_type.id_product + "'";
                                if (idCurrentRoomType == room_type.id_product) {
                                    roomsTypesHtml += ' selected="selected"';
                                }
                                roomsTypesHtml += '>' + room_type.room_type_name + '</option>';
                            });
                            roomsTypesHtml += '</select>';

                            $(".realloc_avail_room_type_container").empty().append(roomsTypesHtml);

                        // if rooms are available for reallocation for the current room type then only select that room type
                        if (typeof json_arr_realloc_room_types[idCurrentRoomType] !==  'undefined') {
                            $('#realloc_avail_room_type option[value='+idCurrentRoomType+']').prop('selected', true);
                            setRoomsForReallocation(json_arr_realloc_room_types[idCurrentRoomType]['rooms']);

                        // if rooms are not available for reallocation for the current room type
                        // then select first room type in available room types and also price difference data
                        } else if (idFirstRoomType = Object.keys(json_arr_realloc_room_types)[0]) {
                            $('#realloc_avail_room_type option[value='+idFirstRoomType+']').prop('selected', true);
                            RoomReallocationModal.changeRoomType($("#realloc_avail_room_type"));
                        }
                    } else {
                        $(".realloc_avail_rooms_container").empty().text(no_realloc_rm_avail_txt).addClass('text-danger');
                        $(".realloc_avail_room_type_container").empty().text(no_realloc_rm_type_avail_txt).addClass('text-danger');
                        $("#reallocate_room_tab .modal-footer").hide();
                    }

                    $('#room-reallocation-modal').modal('show');
                } else {
                    showErrorMessage(txtSomeErr);
                }
            },
            complete: function() {
                $(".loading_overlay").hide();
            }
        });
    },
    swap: function() {
        $(".error_text").text('');
        var room_to_swap = $('#swap_avail_rooms').val();
        if (typeof room_to_swap == 'undefined' || room_to_swap == 0) {
            $("#swap_sel_rm_err_p").text(slct_rm_err);
            return false;
        }

        return true;
    },
    reallocate: function(reallocateBtnObj) {
        $(".error_text").text('');
        var room_to_reallocate = $('#realloc_avail_rooms').val();
        var room_type_to_reallocate = $('#realloc_avail_room_type').val();

        if (typeof room_type_to_reallocate == 'undefined' || room_type_to_reallocate == 0) {
            $("#realloc_sel_rm_type_err_p").text(slct_rm_type_err);
            return false;
        }

        if (typeof room_to_reallocate == 'undefined' || room_to_reallocate == 0) {
            $("#realloc_sel_rm_err_p").text(slct_rm_err);
            return false;
        }

        return true;
    },
    changeRoomType: function(roomTypeObj) {
        $(".loading_overlay").show();
        var idHotelBooking = roomTypeObj.data('id_htl_booking');
        $("#reallocation_price_diff").val(0);
        $("#reallocation_price_diff_block").hide();
        if (parseInt(idHotelBooking) > 0) {
            var optionSelected = roomTypeObj.find('option:selected');
            var roomsAvailable = JSON.parse(optionSelected.attr('rooms_available'));

            // set the rooms of the selceted room type
            setRoomsForReallocation(roomsAvailable);

            // send an ajax for fetching if price has changes in the new room type seleceted
            $.ajax({
                type: 'POST',
                headers: {
                    "cache-control": "no-cache"
                },
                url: rooms_reallocation_url,
                dataType: 'JSON',
                cache: false,
                data: {
                    id_htl_booking: idHotelBooking,
                    id_new_room_type: roomTypeObj.val(),
                    action: 'changeRoomTypeToReallocate',
                    ajax: true
                },
                success: function(result) {
                    if (result.success == 1) {
                        // has room type changed for reallocation
                        if (result.has_room_type_change == 1) {
                            $(".realloc_roomtype_change_message").show();
                            // has room type price changed for reallocation
                            if (result.has_price_changes == 1) {
                                $("#reallocation_price_diff").val(result.price_diff);
                                $("#reallocation_price_diff_block").show();
                            }
                        } else {
                            $(".realloc_roomtype_change_message").hide();
                        }
                        $('#room_type_change_info').empty();
                        if (result.is_changes_present == 1) {
                        }
                    } else if (typeof(result.error) != 'undefinded' && result.error) {
                        showErrorMessage(result.error);
                    } else {
                        showErrorMessage(txtSomeErr);
                    }
                },
                error: function(XMLHttpRequest, textStatus, errorThrown) {
                    showErrorMessage(txtSomeErr);
                },
                complete: function() {
                    $(".loading_overlay").hide();
                }
            });
        } else {
            $(".loading_overlay").hide();
            showErrorMessage(txtSomeErr);
            return false;
        }
    },
    close: function() {
        $('#room-reallocation-modal').modal('hide');
    },
    submit: function() {
    }
};

function setRoomsForReallocation(roomsAvailable)
{
    if (typeof(roomsAvailable) != 'undefined' && roomsAvailable.length) {
        var roomsHtml = '<select class="form-control" name="realloc_avail_rooms" id="realloc_avail_rooms">';
            roomsHtml += '<option class="realloc_rm_opts" value="0">---- ' + select_room_txt + ' ----</option>';
            $.each(roomsAvailable, function(key, roomInfo) {
                roomsHtml += '<option class="realloc_rm_opts" value="' + roomInfo.id_room + '">' + roomInfo.room_num + '</option>';
            });
        roomsHtml += '</select>';

        $(".realloc_avail_rooms_container").empty().append(roomsHtml);
    } else {
        $(".realloc_avail_rooms_container").empty().text(no_realloc_rm_avail_txt);
    }
}