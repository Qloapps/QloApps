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
                <th class="text-center">&nbsp;</th>
                <th class="text-center">{l s='Image Id' mod='hotelreservationsystem'}</th>
                <th class="text-center">{l s='Image' mod='hotelreservationsystem'}</th>
                <th class="text-center">{l s='Category' mod='hotelreservationsystem'}</th>
                <th class="text-center">{l s='Cover' mod='hotelreservationsystem'}</th>
                <th class="text-center">{l s='Action' mod='hotelreservationsystem'}</th>
            </tr>
        </thead>
        <tbody>
            {if isset($hotelImages) && $hotelImages}
                {foreach from=$hotelImages item=image name=hotelImage}
                    {include file="./htl-images-list-row.tpl"}
                {/foreach}
            {/if}
            <tr class="list-empty-tr" {if isset($hotelImages) && $hotelImages}style="display:none;"{/if}>
                <td class="list-empty" colspan="6">
                    <div class="list-empty-msg">
                        <i class="icon-warning-sign list-empty-icon"></i>
                        {l s='No Image Found' mod='hotelreservationsystem'}
                    </div>
                </td>
            </tr>
        </tbody>
    </table>
</div>
<div class="btn-group bulk-actions dropup">
    <button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown">
        {l s='Bulk actions' mod='hotelreservationsystem'} <span class="caret"></span>
    </button>
    <ul class="dropdown-menu">
        <li>
            <a href="#" id="hotel-image-select-all">
                <i class="icon-check-sign"></i>&nbsp;{l s='Select all' mod='hotelreservationsystem'}
            </a>
        </li>
        <li>
            <a href="#" id="hotel-image-unselect-all">
                <i class="icon-check-empty"></i>&nbsp;{l s='Unselect all' mod='hotelreservationsystem'}
            </a>
        </li>
        <li class="divider"></li>
        <li>
            <a href="#" id="hotel-image-bulk-update-category">
                <i class="icon-edit"></i>&nbsp;{l s='Update selection' mod='hotelreservationsystem'}
            </a>
        </li>
        <li>
            <a href="#" id="hotel-image-bulk-delete">
                <i class="icon-trash"></i>&nbsp;{l s='Delete selected' mod='hotelreservationsystem'}
            </a>
        </li>
    </ul>
</div>
