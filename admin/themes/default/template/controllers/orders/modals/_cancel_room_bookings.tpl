{*
* Since 2010 Webkul.
*
* NOTICE OF LICENSE
*
* All right is reserved,
* Please go through this link for complete license : https://store.webkul.com/license.html
*
* DISCLAIMER
*
* Do not edit or add to this file if you wish to upgrade this module to newer
* versions in the future. If you wish to customize this module for your
* needs please refer to https://store.webkul.com/customisation-guidelines/ for more information.
*
*  @author    Webkul IN <support@webkul.com>
*  @copyright Since 2010 Webkul IN
*  @license   https://store.webkul.com/license.html
*}

<div class="modal-body">
    {if $bookingOrderInfo|count > 0}
        <form id="order_discount_form" action="{$current_index}&amp;vieworder&amp;token={$smarty.get.token|escape:'html':'UTF-8'}&amp;id_order={$order->id|intval}" method="post">
            <div class="form-group">
                <div class="table-responsive">
                    <table class="table" id="customer_cart_details">
                        <thead>
                            <tr>
                                <th></th>
                                <th>{l s='Room No.'}</th>
                                <th>{l s='Room Type'}</th>
                                <th>{l s='Hotel Name'}</th>
                                <th>{l s='Duration'}</th>
                                <th>{l s='Total Price (Tax incl.)'}</th>
                            </tr>
                        </thead>
                        <tbody>
                            {foreach from=$bookingOrderInfo item=bookingInfo}
                                <tr>
                                    <td>
                                        <input type="checkbox" name="id_htl_booking[]" value="{$bookingInfo.id|escape:'html':'UTF-8'}"/>
                                    </td>
                                    <td><b>{$bookingInfo.room_num|escape:'html':'UTF-8'}</b></td>
                                    <td>{$bookingInfo.room_type_name|escape:'html':'UTF-8'}</td>
                                    <td>{$bookingInfo.hotel_name|escape:'html':'UTF-8'}</td>
                                    <td>{dateFormat date=$bookingInfo.date_from} - {dateFormat date=$bookingInfo.date_to}</span></td>
                                    <td>{convertPriceWithCurrency price=$bookingInfo.total_price_tax_incl currency=$currency->id}</td>
                                </tr>
                            {/foreach}
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="form-group">
                <label class="control-label">{l s='Reason to Cancel'}</label>
                <textarea rows="3" class="textarea-autosize cancellation_reason" name="cancellation_reason"></textarea>
            </div>

            <button style="display: none;" type="submit" name="initiateRefund" class="btn btn-primary" id="initiateRefund">
                {if $order->hasBeenPaid()}<i class="icon-undo"></i> {l s='Initiate Refund'}{else}{l s='Submit'}{/if}
            </button>

        </form>
    {else}
        <div class="list-empty">
            <div class="list-empty-msg">
                <i class="icon-warning-sign list-empty-icon"></i>
                {l s='No room bookings found.'}
            </div>
        </div>
    {/if}

    {if isset($loaderImg) && $loaderImg}
        <div class="loading_overlay">
            <img src='{$loaderImg}' class="loading-img"/>
        </div>
    {/if}
</div>
