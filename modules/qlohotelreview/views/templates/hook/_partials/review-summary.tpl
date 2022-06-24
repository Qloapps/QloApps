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

<div class="review-summary">
    <div class="row">
        <div class="col-xs-3">
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
        <div class="col-xs-9">
            {if is_array($summary.categories) && count($summary.categories)}
                <div class="category-wrap">
                    <h4 class="title-primary">{l s='Categories' mod='qlohotelreview'}</h4>
                    <div class="clearfix">
                        <div class="row">
                            {foreach from=$summary.categories item=category}
                                <div class="col-xs-2 class-list-item-wrap">
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