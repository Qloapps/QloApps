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

<div class="review">
    <div class="row">
        <div class="col-sm-8">
            <p class="heading-primary">{$review.subject|escape:'html':'UTF-8'}</p>
            <p class="heading-secondary">{$review.customer_name|escape:'html':'UTF-8'}</p>
        </div>
        <div class="col-sm-4 text-right">
            <div class="raty-wrap">
                <span class="raty readonly" data-score="{$review.rating}"></span>
            </div>
            <p class="date-add">{dateFormat date=$review.date_add full=0}</p>
        </div>
    </div>

    {if isset($review.images) && is_array($review.images) && count($review.images)}
        <div class="images-wrap">
            {foreach $review.images as $image}
                <div class="image-wrap">
                    <a href="{$image}">
                        <img class="img img-responsive" src="{$image}">
                    </a>
                </div>
            {/foreach}
        </div>
    {/if}
    <p class="description">{$review.description|escape:'html':'UTF-8'}</p>
    <div class="row">
        <div class="col-sm-6">
            {if $logged && !$review.response_helpful}
                <a href="#" class="btn-helpful" data-id-hotel-review="{$review.id_hotel_review}">
                    <span>{l s='Do you find this helpful?' mod='qlohotelreview'}</span>
                    <i class="icon icon-thumbs-o-up text-primary"></i>
                </a>
            {/if}
            <p class="helpful-count">
                {l s='%1$d people found it helpful.' sprintf=[$review.total_useful] mod='qlohotelreview'}</p>
        </div>
        {if $logged && !$review.response_report}
            <div class="col-sm-6 text-right">
                <a href="#" class="btn-report-abuse" data-id-hotel-review="{$review.id_hotel_review}">
                    <span>{l s='Report abuse' mod='qlohotelreview'}</span>
                </a>
            </div>
        {/if}
    </div>

    {if isset($review.message) && $review.message}
        <div class="reply-wrap">
            <div class="row">
                <div class="col-sm-offset-1 col-sm-11">
                    <div class="reply-box">
                        <div class="reply-header">
                            <p class="heading-primary">{$review.hotel_name|escape:'html':'UTF-8'}</p>
                            <p class="heading-secondary">{l s='has replied' mod='qlohotelreview'}</p>
                        </div>
                        <div class="reply-message">
                            <p>{$review.message|escape:'html':'UTF-8'}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    {/if}
</div>