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

{if is_array($hotel_rooms_info) && count($hotel_rooms_info)}
    <option value="" selected="selected">-</option>
    {foreach from=$hotel_rooms_info item=hotel_room}
        <option value="{$hotel_room.id}">{$hotel_room.room_num}, {$hotel_room.room_type_name}, {$hotel_room.hotel_name}</option>
    {/foreach}
{/if}
