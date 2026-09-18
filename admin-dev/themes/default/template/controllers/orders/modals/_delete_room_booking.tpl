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
    <input type="hidden" name="id_product" value="{$id_product|intval}">
    <input type="hidden" name="id_order_detail" value="{$id_order_detail|intval}">
    <input type="hidden" name="id_hotel" value="{$id_hotel|intval}">
    <input type="hidden" name="id_room" value="{$id_room|intval}">
    <input type="hidden" name="id_htl_booking" value="{$id_htl_booking|intval}">
    <input type="hidden" name="id_order" value="{$id_order|intval}">
    <input type="hidden" name="date_from" value="{$date_from|escape:'html':'UTF-8'}">
    <input type="hidden" name="date_to" value="{$date_to|escape:'html':'UTF-8'}">
    <div class="form-group">
        <div class="form-group" id="remarkGroup">
            <label class="control-label required" for="room_remark">
                {l s='Remark'}
            </label>
            <textarea id="room_remark" class="form-control" name="message"
                placeholder="{l s='Enter remark for room deletion'}" maxlength="250"></textarea>
        </div>
    </div>
</div>
<div class="modal-footer">
    <button type="button" class="btn btn-default btn-close" data-dismiss="modal">{l s='Close'}</button>
    <button type="button" id="submitRoomDelete" class="btn btn-danger">
        <i class="icon icon-trash"></i>&nbsp;{l s="Delete Room"}
    </button>
</div>
<script>
    $(document).ready(function() {
        $('#room_remark').on('input', function() {
            if ($(this).val().trim() !== '') {
                $('#remarkGroup').removeClass('has-error');
            }
        });
    });
</script>
