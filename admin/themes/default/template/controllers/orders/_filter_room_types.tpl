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

{if is_array($hotel_room_types) && count($hotel_room_types)}
    <option value="" selected="selected">-</option>
    {foreach from=$hotel_room_types item=hotel_room_type}
        <option value="{$hotel_room_type.id_product}">{$hotel_room_type.room_type}, {$hotel_name}</option>
    {/foreach}
{/if}
