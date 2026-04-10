{**
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
*}
<div class="modal fade" id="connectedRoomModal" tabindex="-1" role="dialog">
    <style>
        #connectedRoomModal .connected-rooms-table {
            table-layout: fixed;
            width: 100%;
            border-radius: 3px;
        }

        #connectedRoomModal .modal-title .disable_dates_title {
            display: inline-block;
            font-weight: 600;
        }

        #connectedRoomModal .modal-title .connected_room_num {
            color: grey;
        }

        #connectedRoomModal .modal-title {
            line-height: 2;
        }

        #connectedRoomModal .modal-title .close {
            line-height: 2;
        }
    </style>
    <div class="modal-dialog modal-md">
        <div class="modal-content">
            <!-- header -->
            <div class="modal-header">
                <div class="modal-title">
                    <div class="row">
                        <div class="disable_dates_title">
                            <i class="icon-random"></i>&nbsp; {l s='Connected Rooms'} <span id="connected_room_title"
                                class="connected_room_num"></span>
                        </div>
                        <div class="pull-right">
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
            <form id="connected_rooms_form">
                <div class="modal-body">
                    <input type="hidden" id="connected_room_main_id" name="main_room_id" value="{$main_room_id|intval}">
                    <!-- connectedrooms list -->
                    <div class="">
                        <div class="">
                            <div id="connected_rooms_list">
                                <div id="connected_rooms_table_wrapper"
                                    class="{if !isset($htl_connected_rooms) || $htl_connected_rooms|count == 0}hide{/if}">
                                    <table class="table table-bordered table-striped connected-rooms-table">
                                        <colgroup>
                                            <col style="width: 45%;">
                                            <col style="width: 35%;">
                                            <col style="width: 120px;">
                                        </colgroup>
                                        <thead>
                                            <tr>
                                                <th>{l s='Room Type'}</th>
                                                <th>{l s='Room'}</th>
                                                <th width="120" class="text-center">
                                                    {l s='Action'}
                                                </th>
                                            </tr>
                                        </thead>
                                        <tbody id="connected_rooms_tbody">
                                            {if isset($htl_connected_rooms) && $htl_connected_rooms|count > 0}
                                                {foreach from=$htl_connected_rooms item=hotelRooms}
                                                    {foreach from=$hotelRooms key=roomType item=rooms}
                                                        {foreach from=$rooms item=row}
                                                            <tr class="connected-room-row">
                                                                <td>{$row.connected_room_type|escape:'html':'UTF-8'}</td>
                                                                <td>{$row.connected_room_num|escape:'html':'UTF-8'}</td>
                                                                <td class="text-center">
                                                                    <a href="javascript:void(0);"
                                                                        class="delete-connected-room btn btn-default"
                                                                        data-connected-id="{$row.id_connected_room|intval}"
                                                                        data-room-id="{$row.id_room_information|intval}"
                                                                        data-connected-room-id="{$row.id_room|intval}"
                                                                        data-hotel-id="{$row.id_hotel|intval}" title="{l s='Remove'}">
                                                                        <i class="icon-trash"></i>
                                                                    </a>
                                                                </td>
                                                            </tr>
                                                        {/foreach}
                                                    {/foreach}
                                                {/foreach}
                                            {/if}
                                        </tbody>
                                    </table>
                                </div>
                                <div class="list-empty {if isset($htl_connected_rooms) && $htl_connected_rooms|count > 0}hide{/if}"
                                    id="connected_rooms_empty_state">
                                    <div class="list-empty-msg">
                                        <i class="icon-warning-sign list-empty-icon"></i>
                                        {l s='No connected rooms.'}
                                    </div>
                                </div>
                            </div>
                            {if isset($htl_not_connected_rooms) && $htl_not_connected_rooms|count > 0}


                                <button type="button" id="add_connected_room_row" class="btn btn-default">
                                    <i class="icon-plus"></i> {l s='Add Connected Room'}
                                </button>


                            {/if}
                        </div>
                    </div>
                    {if isset($htl_not_connected_rooms) && $htl_not_connected_rooms|count > 0}
                        <table class="hide">
                            <tbody>
                                <tr id="connected_room_add_template" class="connected-room-add-row hide">
                                    <td>
                                        <select class="form-control connect-room-type" name="connect_room_type">
                                            {assign var=types value=[]}
                                            {foreach from=$htl_not_connected_rooms item=room}
                                                {if !in_array($room.id_product, $types)}
                                                    {append var=types value=$room.id_product}
                                                    <option value="{$room.id_product|intval}"
                                                        {if $room.id_product == $current_roomtype}selected{/if}>
                                                        {$room.room_type|escape:'html':'UTF-8'}
                                                    </option>
                                                {/if}
                                            {/foreach}
                                        </select>
                                    </td>
                                    <td>
                                        <select class="form-control connect-room" name="connect_room">
                                            {foreach from=$htl_not_connected_rooms item=room}
                                                <option value="{$room.id|intval}" class="room-option"
                                                    data-type="{$room.id_product|intval}">
                                                    {$room.room_num|escape:'html':'UTF-8'}
                                                </option>
                                            {/foreach}
                                        </select>
                                    </td>
                                    <td class="text-center">
                                        <button type="button" class="btn btn-default remove-connected-room-row"
                                            title="{l s='Remove'}">
                                            <i class="icon-trash"></i>
                                        </button>
                                        <button type="button" class="btn btn-primary save-connected-room">
                                            {l s='Add'}
                                        </button>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    {/if}
                </div>
                <!-- footer -->
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">
                        <i class="icon-remove"></i>
                        {l s='Close'}
                    </button>
                </div>
            </form>
        </div>
    </div>
    <input type="hidden" id="hotel_id" value="{$hotel_id|intval}">
</div>