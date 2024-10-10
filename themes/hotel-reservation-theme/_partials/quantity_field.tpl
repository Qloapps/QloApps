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

<div class="rm_qty_cont form-group clearfix">
    <input type="hidden" class="text-center form-control quantity_wanted" min="1" name="qty" value="{if isset($quantity) && $quantity}{$quantity|escape:'html':'UTF-8'}{else}1{/if}">
    <input type="hidden" class="max_avail_type_qty" value="{if isset($total_available_rooms)}	{$total_available_rooms|escape:'html':'UTF-8'}{/if}">
    <div class="qty_count pull-left">
        <span>{if isset($quantity) && $quantity}{$quantity|escape:'html':'UTF-8'}{else}1{/if}</span>
    </div>
    <div class="qty_direction pull-left">
        <a href="#" data-field-qty="qty" class="btn btn-default quantity_up rm_quantity_up">
            <span><i class="icon-plus"></i></span>
        </a>
        <a href="#" data-field-qty="qty" class="btn btn-default quantity_down rm_quantity_down">
            <span><i class="icon-minus"></i></span>
        </a>
    </div>
</div>
