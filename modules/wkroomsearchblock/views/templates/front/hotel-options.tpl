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

<option value=""></option>
{if isset($hotels_info) && is_array($hotels_info)}
    {foreach from=$hotels_info item=hotel_info}
        <option class="search_result_li" data-id-hotel="{$hotel_info.id_hotel}" data-hotel-cat-id="{$hotel_info.id_category}" data-max_order_date="{$hotel_info.max_order_date}" data-preparation_time="{$hotel_info.preparation_time}" tabindex="-1">
            {$hotel_info.hotel_name}
        </option>
    {/foreach}
{/if}
