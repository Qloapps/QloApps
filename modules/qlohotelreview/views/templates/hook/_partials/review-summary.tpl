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

<div class="review-summary">
    <div class="row">
        <div class="col-sm-3">
            <div class="score-wrap">
                <h4 class="title-primary">{l s='Ratings Summary' mod='qlohotelreview'}</h4>
                <div>
                    <span class="score">{$summary.average|string_format:'%.1f'}</span>
                    <span class="max">/5</span>
                </div>
                <div class="stars-wrap">
                    <span class="raty readonly" data-score="{$summary.average}"></span>
                    <p class="tool-text">
                        {l s='Based on ' mod='qlohotelreview'} {$summary.total_reviews}
                        {if $summary.total_reviews > 1} {l s='reviews' mod='qlohotelreview'} {else} {l s='review' mod='qlohotelreview'} {/if}
                    </p>
                </div>
            </div>
        </div>
        <div class="col-sm-9">
            {if is_array($summary.categories) && count($summary.categories)}
                <div class="category-wrap">
                    <h4 class="title-primary">{l s='Categories' mod='qlohotelreview'}</h4>
                    <div class="clearfix">
                        <div class="row">
                            {foreach from=$summary.categories item=category}
                                <div class="col-xs-2 col-sm-3 col-md-2 class-list-item-wrap">
                                    <div class="score-circle-wrap">
                                        <div class="score-circle" data-value="{$category.average / 5|string_format:'%.1f'}" data-color="{$category.color}"></div>
                                    </div>
                                    <div class="title">{$category.average|string_format:'%.1f'}</div>
                                    <p class="name">{$category.name}</p>
                                </div>
                            {/foreach}
                        </div>
                    </div>
                </div>
            {/if}
        </div>
    </div>
</div>