{**
 * 2010-2023 Webkul.
 *
 * NOTICE OF LICENSE
 *
 * All right is reserved,
 * Please go through LICENSE.txt file inside our module
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this module to newer
 * versions in the future. If you wish to customize this module for your
 * needs please refer to CustomizationPolicy.txt file inside our module for more information.
 *
 * @author Webkul IN
 * @copyright 2010-2023 Webkul IN
 * @license LICENSE.txt
 *}

{if is_array($hotel_rooms_info) && count($hotel_rooms_info)}
    <option value="" selected="selected">-</option>
    {foreach from=$hotel_rooms_info item=hotel_room}
        <option value="{$hotel_room.id_product}">{$hotel_room.room_num}, {$hotel_room.room_type_name}, {$hotel_room.hotel_name}</option>
    {/foreach}
{/if}
