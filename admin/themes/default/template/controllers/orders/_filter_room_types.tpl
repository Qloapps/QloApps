{**
 * 2010-2024 Webkul.
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
 * @copyright 2010-2024 Webkul IN
 * @license LICENSE.txt
 *}

{if is_array($room_types_info) && count($room_types_info)}
    <option value="" selected="selected">-</option>
    {foreach from=$room_types_info item=room_type}
        <option value="{$room_type.id_product}">{$room_type.room_type}, {$room_type.hotel_name}</option>
    {/foreach}
{/if}
