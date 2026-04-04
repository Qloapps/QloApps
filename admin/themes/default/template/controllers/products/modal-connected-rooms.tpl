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
    <div class="modal-dialog modal-md">
        <div class="modal-content">
            <!-- header -->
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title">
                    <i class="icon-random"></i>
                    {l s='Manage Connected Rooms'} -
                    <strong id="connected_room_title"></strong>
                </h4>
            </div>
            <form id="connected_rooms_form">
                <div class="modal-body">
                    <input type="hidden" id="connected_room_main_id" name="main_room_id" value="{$main_room_id|intval}">
                    <div class="panel panel-default">
                        <div class="panel-heading">
                            <i class="icon-plus-circle"></i>
                            {l s='Add Connection'}
                        </div>
                        <div class="panel-body">
                            <div class="row">
                                <!-- room type -->
                                <div class="form-group col-sm-6">
                                    <label class="control-label">
                                        {l s='Room Type'}
                                    </label>
                                    <select class="form-control" id="connect_room_type" name="connect_room_type">
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
                                </div>
                                <!-- rooms -->
                                <div class="form-group col-sm-6">
                                    <label class="control-label">
                                        {l s='Room'}
                                    </label>
                                    <select class="form-control" id="connect_room" name="connect_room">
                                        {foreach from=$htl_not_connected_rooms item=room}
                                                <option value="{$room.id|intval}" class="room-option" data-type="{$room.id_product|intval}">
                                                    {$room.room_num|escape:'html':'UTF-8'}
                                                </option>
                                        {/foreach}
                                    </select>
                                </div>
                                <!-- add -->
                                <div class="col-sm-12 text-right">
                                    <button type="button" id="save_connected_room" class="btn btn-primary">
                                        <i class="icon-plus"></i> {l s='Add'}
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- connectedrooms list -->
                    <div class="panel panel-default">
                        <div class="panel-heading">
                            <i class="icon-link"></i>
                            {l s='Connected Rooms'}
                        </div>
                        <div class="panel-body">
                            <div id="connected_rooms_list">
                                {if isset($htl_connected_rooms) && $htl_connected_rooms|count > 0}
                                    <table class="table table-bordered table-striped">
                                        <thead>
                                            <tr>
                                                <th>{l s='Room'}</th>
                                                <th>{l s='Room Type'}</th>
                                                <th width="60" class="text-center">
                                                    {l s='Action'}
                                                </th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            {foreach from=$htl_connected_rooms item=hotelRooms}
                                                {foreach from=$hotelRooms key=roomType item=rooms}
                                                    {foreach from=$rooms item=row}
                                                        <tr>
                                                            <td>{$row.connected_room_num|escape:'html':'UTF-8'}</td>
                                                            <td>{$row.connected_room_type|escape:'html':'UTF-8'}</td>
                                                            <td class="text-center">
                                                                <a href="javascript:void(0);" class="delete-connected-room text-danger"
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
                                        </tbody>

                                    </table>
                                {else}
                                    <p class="text-muted text-center">
                                        {l s='No connected rooms.'}
                                    </p>
                                {/if}
                            </div>
                        </div>
                    </div>
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
