{**
 * 2010-2022 Webkul.
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
 * @copyright 2010-2022 Webkul IN
 * @license LICENSE.txt
 *}

{if isset($order_slip_info.redeem_status) && $order_slip_info.redeem_status == OrderSlip::REDEEM_STATUS_GENERATED}
    <span class="label label-warning">{l s='Voucher generated'}</span>
{elseif isset($order_slip_info.redeem_status) && $order_slip_info.redeem_status == OrderSlip::REDEEM_STATUS_REFUNDED}
    <span class="label label-danger">{l s='Refunded'}</span>
{else}
    <a class="btn btn-default" href="{$link->getAdminLink('AdminSlip')|escape:'html':'UTF-8'}&action=generateVoucher&id_order_slip={$order_slip_info.id_order_slip|intval}">
        <i class="icon-tag"></i>
        {l s='Generate voucher'}
    </a>
{/if}
