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

<div class="modal-body">
    <form action="{$current_index}&amp;vieworder&amp;token={$smarty.get.token|escape:'html':'UTF-8'}&amp;id_order={$order->id|intval}" method="post" class="room_status_info_form">
        <div class="form-group">
            <label class="control-label">{l s='Status'}</label>
            <select name="booking_order_status" class="form-control booking_order_status margin-bottom-5">
                {foreach from=$hotel_order_status item=state}
                    <option value="{$state['id_status']|intval}">{$state.name|escape}</option>
                {/foreach}
            </select>
        </div>
        <div class="form-group" style="display:none;">
            <label class="control-label">{l s='Date'}</label>
            <div class="input-group">
                <input type="text" name="status_date" class="room_status_date wk-input-date" value="" />
                <div class="input-group-addon">
                    <i class="icon-calendar-o"></i>
                </div>
            </div>

            <input type="hidden" id="room_status_id_hotel_booking_detail" name="id_hotel_booking_detail" />
            <input type="hidden" id="room_status_date_from" name="date_from" />
            <input type="hidden" id="room_status_date_to" name="date_to" />
            <input type="hidden" id="room_status_id_room" name="id_room" />
            <input type="hidden" id="room_status_id_order" name="id_order" />
        </div>

        <button class="btn btn-primary" type="submit" name="submitbookingOrderStatus" style="display:none" id="submitbookingOrderStatus"></button>
    </form>

    {if isset($loaderImg) && $loaderImg}
        <div class="loading_overlay">
            <img src='{$loaderImg}' class="loading-img"/>
        </div>
    {/if}
</div>
