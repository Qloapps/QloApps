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

<div class="review-list">
    {foreach $reviews as $review}
        {include file='./review.tpl' review=$review}
    {/foreach}
</div>

{if isset($show_load_more_btn) && $show_load_more_btn}
    <div class="text-left">
        <a href="#"
            class="btn btn-primary btn-primary-review"
            id="btn-load-more-reviews"
            data-id-hotel="{$id_hotel}"
            data-next-page="2">
            <span>{l s='LOAD MORE' mod='qlohotelreview'}</span>
        </a>
    </div>
{/if}