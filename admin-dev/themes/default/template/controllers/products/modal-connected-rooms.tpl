{*
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

    #connectedRoomModal .modal-body {
        max-height: 600px;
        overflow-y: auto;
    }
</style>
<div class="modal-body">
    <input type="hidden" id="connected_room_main_id" name="main_room_id" value="{$main_room_id|intval}">
    {assign var="connected_room_types" value=[]}
    {if isset($htl_connected_rooms[$main_room_id].connected_room_types)}
        {assign var="connected_room_types" value=$htl_connected_rooms[$main_room_id].connected_room_types}
    {/if}
    <!-- connectedrooms list -->
    <div class="">
        <div class="">
            <div id="connected_rooms_list">
                <div id="connected_rooms_table_wrapper"
                    class="{if $connected_room_types|@count == 0}hide{/if}">
                    <table class="table table-bordered table-striped connected-rooms-table">
                        <thead>
                            <tr>
                                <th>{l s='Room Type'}</th>
                                <th>{l s='Connected Room'}</th>
                                <th class="text-center">
                                    {l s='Action'}
                                </th>
                            </tr>
                        </thead>
                        <tbody id="connected_rooms_tbody">
                            {foreach from=$connected_room_types item=typeGroup}
                                {foreach from=$typeGroup.connected_rooms item=room}
                                    <tr class="connected-room-row">
                                        <td>{$typeGroup.room_type_name|escape:'html':'UTF-8'}</td>
                                        <td>{$room.name|escape:'html':'UTF-8'}</td>
                                        <td class="text-center">
                                            <a href="javascript:void(0);"
                                                class="delete-connected-room btn btn-default"
                                                data-connected-id="{$room.id_connected_room|intval}"
                                                data-connected-room-id="{$room.id_room|intval}"
                                                title="{l s='Remove'}">
                                                <i class="icon-trash"></i>
                                            </a>
                                        </td>
                                    </tr>
                                {/foreach}
                            {/foreach}
                        </tbody>
                    </table>
                </div>
                <div class="list-empty {if $connected_room_types|@count > 0}hide{/if}"
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
                            {foreach from=$htl_not_connected_rooms key=typeId item=typeGroup}
                                <option value="{$typeGroup.id_room_type|intval}"
                                    {if $typeGroup.id_room_type == $current_roomtype}selected{/if}>
                                    {$typeGroup.name|escape:'html':'UTF-8'}
                                </option>
                            {/foreach}
                        </select>
                    </td>
                    <td>
                        <select class="form-control connect-room" name="connect_room">
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
