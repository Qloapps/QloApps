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

<option value=""></option>
{if isset($hotels_info) && is_array($hotels_info)}
    {foreach from=$hotels_info item=hotel_info}
        <option class="search_result_li" data-id-hotel="{$hotel_info.id_hotel}" data-hotel-cat-id="{$hotel_info.id_category}" data-max_order_date="{$hotel_info.max_order_date}" data-min_booking_offset="{$hotel_info.min_booking_offset}" tabindex="-1">
            {$hotel_info.hotel_name}
        </option>
    {/foreach}
{/if}
