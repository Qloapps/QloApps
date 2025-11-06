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
