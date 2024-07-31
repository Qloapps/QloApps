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
<div class="modal-header">
    <button type="button" class="close" data-dismiss="modal"><i class="icon-remove-sign"></i></button>
    <h4 class="modal-title"><i class="icon icon-exclamation-triangle"></i>&nbsp;{l s='Confirm Delete'}</h4>
    <p>{l s='The following cart rules in your selection were created dynamically from refunds or credit slips. Please confirm if you also intend to delete these cart rules.'}</p>
</div>
<div class="modal-body">
    <div class="table-responsive">
        <table class="table">
            {foreach $cartRules as $cartRule}
                <tr>
                    <td>
                        <input type="checkbox" class="cart_rule_to_delete" value="{$cartRule['id_cart_rule']}" checked>
                    </td>
                    <td>
                        <a href="{$link->getAdminLink('AdminCartRules')}&updatecart_rule&id_cart_rule={$cartRule['id_cart_rule']}" target="_blank">#{$cartRule['id_cart_rule']}</a>
                    </td>
                    <td>
                        <b>{if $cartRule['generated_by'] == CartRule::GENERATED_BY_REFUND}
                            {l s='Generated against refund'}
                        {else if $cartRule['generated_by'] == CartRule::GENERATED_BY_ORDER_SLIP}
                            {l s='Generated against credit slip'}
                        {/if}</b>
                    </td>
                </tr>
            {/foreach}
        </table>
    </div>
</div>