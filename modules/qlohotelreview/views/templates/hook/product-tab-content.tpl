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

{block name='hotel_reviews'}
    <div id="hotel-reviews" class="tab-pane card {if isset($language_is_rtl) && $language_is_rtl} rtl {/if}">
        {if is_array($reviews) && count($reviews)}
            {block name='review_summary'}
                {include file='./_partials/review-summary.tpl'}
            {/block}
            {block name='media_list'}
                {include file='./_partials/media-list.tpl'}
            {/block}
            {block name='list_actions'}
                {include file='./_partials/list-actions.tpl'}
            {/block}
            {block name='review_list'}
                {include file='./_partials/review-list.tpl'}
            {/block}
        {else}
            {l s='No reviews.' mod='qlohotelreview'}
        {/if}
    </div>
{/block}
