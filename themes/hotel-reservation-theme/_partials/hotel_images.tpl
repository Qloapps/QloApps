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

{if is_array($hotel_images) && count($hotel_images)}
    {foreach from=$hotel_images item=hotel_image}
        <div class="col-sm-4 image-item">
            <a class="hotel-images-fancybox" href="{$hotel_image.link|escape:'html':'UTF-8'}">
                <img class="img img-responsive" src="{$hotel_image.link|escape:'html':'UTF-8'}">
            </a>
        </div>
    {/foreach}
{/if}
