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