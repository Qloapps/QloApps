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