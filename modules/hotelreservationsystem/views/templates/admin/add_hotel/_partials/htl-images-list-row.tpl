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

<tr class="{if $image.cover == 1}cover-image-tr{/if}">
    <td class="text-center">{$image.id|escape:'html':'UTF-8'}</td>
    <td class="text-center">
        <a class="htl-img-preview" href="{$image.image_link|escape:'html':'UTF-8'}">
            <img class="img-thumbnail" width="100" src="{$image.image_link_small|escape:'html':'UTF-8'}"/>
        </a>
    </td>
    <td class="text-center {if $image.cover == 1}cover-image-td{/if}">
        <a href="#" class="{if $image.cover == 1}text-success{else}text-danger{/if} changer-cover-image" data-id-hotel="{$hotel_info.id|escape:'html':'UTF-8'}" data-is-cover="{if $image.cover == 1}1{else}0{/if}" data-id-image="{$image.id|escape:'html':'UTF-8'}">
            {if $image.cover == 1}
                <i class="icon-check"></i>
            {else}
                <i class="icon-times"></i>
            {/if}
        </a>
    </td>
    <td class="text-center">
        <button type="button" class="btn btn-default delete-hotel-image" data-id-hotel="{$hotel_info.id|escape:'html':'UTF-8'}" data-is-cover="{if $image.cover == 1}1{else}0{/if}" data-id-image="{$image.id|escape:'html':'UTF-8'}"><i class="icon-trash"></i></button>
    </td>
</tr>