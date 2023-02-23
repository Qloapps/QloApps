{**
* 2010-2023 Webkul.
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
* @copyright 2010-2023 Webkul IN
* @license LICENSE.txt
*}

<div class="rm_qty_cont clearfix">
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