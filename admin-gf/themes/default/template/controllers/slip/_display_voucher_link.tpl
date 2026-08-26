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

 {if $id_cart_rule}
    <a class="btn btn-link" href="{$link->getAdminLink('AdminCartRules')|escape:'html':'UTF-8'}&updatecart_rule&id_cart_rule={$id_cart_rule}" target="_blank">
        #{$id_cart_rule}
    </a>
{else}
    <a href="{$link->getAdminLink('AdminSlip')|escape:'html':'UTF-8'}&generateVoucher=1&id_order_slip={$row['id_order_slip']}" class="btn btn-default" title="{l s='Generate voucher for credit slip'}">
        <i class="icon-refresh"></i> {l s='Generate Voucher'}
    </a>
{/if}
