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
    {capture name='connected_room_tooltip'}
        <div class="tooltip_cont">
            <div class="tip_header"><div class="tip_date">{l s='Connected Rooms'}</div></div>
            <div class="tip-body tip-body-grid">
                {foreach from=$htl_connected_rooms.connected_room_types item=group}
                    <div>
                        <div class="tip_element_head">{$group.room_type_name|escape:'html':'UTF-8'}</div>
                        <div class="tip_element_value">
                            {foreach from=$group.connected_rooms item=room name=connected_rooms}
                                {$room.name|escape:'html':'UTF-8'}{if !$smarty.foreach.connected_rooms.last}, {/if}
                            {/foreach}
                        </div>
                    </div>
                {/foreach}
            </div>
        </div>
    {/capture}
    {include file='helpers/tooltip.tpl' tooltip_content=$smarty.capture.connected_room_tooltip tooltip_icon_class='icon-random' tooltip_icon_style='color: #008abd;' }
{/if}
