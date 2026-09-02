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

{assign var="isCoverImage" value=$image.cover == 1}
<tr class="{if $isCoverImage}cover-image-tr{/if}" data-id-image="{$image.id|escape:'html':'UTF-8'}">
    <td class="row-selector text-center"><input type="checkbox" class="noborder hotel-image-checkbox" value="{$image.id|escape:'html':'UTF-8'}"></td>
    <td class="text-center">{$image.id|escape:'html':'UTF-8'}</td>
    <td class="text-center">
        <a class="htl-img-preview" href="{$image.image_link|escape:'html':'UTF-8'}">
            <img class="img-thumbnail" width="100" src="{$image.image_link_small|escape:'html':'UTF-8'}"/>
        </a>
    </td>
    <td class="text-center hotel-image-category-cell">
        {if isset($image.category_name) && $image.category_name}
            {$image.category_name|escape:'html':'UTF-8'}
        {else}
            &mdash;
        {/if}
    </td>
    <td class="text-center {if $isCoverImage}cover-image-td{/if}">
        <a href="#" class="{if $isCoverImage}text-success{else}text-danger{/if} changer-cover-image" data-id-hotel="{$hotel_info.id|escape:'html':'UTF-8'}" data-is-cover="{if $isCoverImage}1{else}0{/if}" data-id-image="{$image.id|escape:'html':'UTF-8'}">
            {if $isCoverImage}
                <i class="icon-check"></i>
            {else}
                <i class="icon-times"></i>
            {/if}
        </a>
    </td>
    <td class="text-center">
        <button type="button" class="btn btn-default edit-hotel-image" data-id-hotel="{$hotel_info.id|escape:'html':'UTF-8'}" data-id-image="{$image.id|escape:'html':'UTF-8'}" data-id-htl-image-category="{if isset($image.id_htl_image_category)}{$image.id_htl_image_category|intval}{else}0{/if}" data-is-cover="{if $isCoverImage}1{else}0{/if}"><i class="icon-pencil"></i></button>
        <button type="button" class="btn btn-default delete-hotel-image" data-id-hotel="{$hotel_info.id|escape:'html':'UTF-8'}" data-is-cover="{if $isCoverImage}1{else}0{/if}" data-id-image="{$image.id|escape:'html':'UTF-8'}"><i class="icon-trash"></i></button>
    </td>
</tr>
