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
    {assign var=connectedRoomsCount value=0}
    {foreach from=$htl_connected_rooms item=rooms_by_type}
        {assign var=connectedRoomsCount value=$connectedRoomsCount+$rooms_by_type|@count}
    {/foreach}
    <span class="qlo-tooltip-wrapper connected-room-popup-wrapper">
        <i class="icon-random connected-room-icon"></i>
        <div class="qlo-tooltip connected-room-popup{if $htl_connected_rooms|@count == 1} single-col{/if}">
            <div class="qlo_tooltip_content">
                <div class="qlo_tooltip_cont">
                    <div class="qlo_header">
                        <div class="qlo_date">{l s='Connected Rooms'}</div>
                    </div>
                    <div class="qlo_body grid{if $htl_connected_rooms|@count == 1} single-col{/if}">
                        {foreach from=$htl_connected_rooms key=conn_type item=rooms_by_type}
                            <div class="qlo_element">
                                <div class="qlo_element_heading">{$conn_type|escape:'html':'UTF-8'}</div>
                                <div class="qlo_element_value">
                                    <ul class="connected-room-list qlo_tooltip_list">
                                        {foreach from=$rooms_by_type item=room}
                                            <li>{$room.connected_room_num|escape:'html':'UTF-8'}</li>
                                        {/foreach}
                                    </ul>
                                </div>
                            </div>
                        {/foreach}
                    </div>
                </div>
            </div>
        </div>
</span>
{/if}
<style>
    .connected-room-icon {
        color: #008abd;
    }
</style>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        var wrappers = document.querySelectorAll('.qlo-tooltip-wrapper');
        wrappers.forEach(function(wrapper) {
            wrapper.addEventListener('mouseenter', function() {
                var popup = wrapper.querySelector('.connected-room-popup');
                if (!popup) {
                    return;
                }
                popup.classList.remove('align-right', 'align-left');
                var rect = popup.getBoundingClientRect();
                if (rect.right > window.innerWidth) {
                    popup.classList.add('align-right');
                } else if (rect.left < 0) {
                    popup.classList.add('align-left');
                }
            });
        });
    });
</script>
