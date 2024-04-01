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
    <form  action="{$current_index}&amp;viewOrder&amp;id_order={$order->id}{if isset($smarty.get.token)}&amp;token={$smarty.get.token|escape:'html':'UTF-8'}{/if}" method="post">
        <div class="form-group">
            <label class="control-label">{l s='Note Detail'}</label>
            <input type="hidden" name="id_order_invoice" id="id_order_invoice" value="" />
            <textarea name="note" id="editNote" class="edit-note textarea-autosize"></textarea>
        </div>
        <button class="btn btn-default" type="submit" name="submitEditNote" style="display:none" id="submitEditNote"></button>
    </form>
</div>
