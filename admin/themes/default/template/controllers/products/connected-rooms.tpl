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
{if isset($htl_connected_rooms) && $htl_connected_rooms|@count > 0}
    <span class="connected-room-popup-wrapper">
        <i class="icon-random connected-room-icon"></i>
        <div class="connected-room-popup">
            <div class="popup-title">{l s='Connected Rooms'}</div>
            <div class="popup-content">
                {foreach from=$htl_connected_rooms key=conn_type item=rooms_by_type}
                    <div class="connected-room-type"> {$conn_type|escape:'html':'UTF-8'}</div>
                    <ul class="connected-room-list">
                        {foreach from=$rooms_by_type item=room}
                            <li>
                                {$room.connected_room_num|escape:'html':'UTF-8'}
                            </li>
                        {/foreach}
                    </ul>
                {/foreach}
            </div>
        </div>
    </span>
{/if}
<style>
    .connected-room-popup-wrapper {
        position: relative;
        display: inline-block;
        cursor: pointer;
        color: #333;
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    }
    .connected-room-icon {
        color: #008abd;
    }
    .connected-room-popup {
        display: none;
        position: absolute;
        top: 130%;
        left: 50%;
        background: #ffffff;
        padding: 12px 16px;
        border-radius: 6px;
        box-shadow: 0 8px 20px rgba(0, 0, 0, 0.15);
        font-size: 14px;
        min-width: 200px;
        z-index: 1000;
    }
    .connected-room-popup-wrapper:hover .connected-room-popup {
        display: block;
    }
    .popup-title {
        font-weight: 600;
        font-size: 16px;
        border-bottom: 1px solid rgba(0, 0, 0, 0.15);
        padding-bottom: 4px;
        margin-bottom: 4px;
    }
    .connected-room-type {
        font-weight: bold;
        margin-bottom: 2px;
    }
    .connected-room-list {
        padding-left: 15px;
    }
    .connected-room-list li {
        list-style: none;
        margin: 0px;
    }
</style>