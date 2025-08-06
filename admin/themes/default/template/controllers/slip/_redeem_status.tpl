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

{if $redeem_status == OrderSlip::REDEEM_STATUS_REDEEMED}
    <span class="badge badge-danger">{l s='Redeemed'}</span>
{else}
    <span class="badge badge-success">{l s='Active'}</span>
{/if}
