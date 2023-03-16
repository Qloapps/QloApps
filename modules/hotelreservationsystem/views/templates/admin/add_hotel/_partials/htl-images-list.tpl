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

<div class="table-responsive">
    <table class="table" id="hotel-image-table">
        <thead>
            <tr>
                <th class="text-center">{l s='Image Id' mod='hotelreservationsystem'}</th>
                <th class="text-center">{l s='Image' mod='hotelreservationsystem'}</th>
                <th class="text-center">{l s='Cover' mod='hotelreservationsystem'}</th>
                <th class="text-center">{l s='Action' mod='hotelreservationsystem'}</th>
            </tr>
        </thead>
        <tbody>
            {if isset($hotelImages) && $hotelImages}
                {foreach from=$hotelImages item=image name=hotelImage}
                    {include file="./htl-images-list-row.tpl"}
                {/foreach}
            {else}
                <tr class="list-empty-tr">
                    <td class="list-empty" colspan="5">
                        <div class="list-empty-msg">
                            <i class="icon-warning-sign list-empty-icon"></i>
                            {l s='No Image Found' mod='hotelreservationsystem'}
                        </div>
                    </td>
                </tr>
            {/if}
        </tbody>
    </table>
</div>