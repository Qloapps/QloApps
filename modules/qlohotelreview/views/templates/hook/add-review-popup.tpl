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

<div id="qlohotelreview" style="display: none;">
    <div id="add-review-popup" class="card">
        <div class="card-header">
            <span class="hotel-name"></span>
        </div>
        <form action="{$action}" id="add-review-form" method="post" enctype="multipart/form-data">
            <div class="card-body">
                <input type="hidden" name="id_order" value="0"/>
                <input type="hidden" name="id_hotel" value="0"/>

                <div class="alert alert-danger" id="review-general-errors" style="display:none;"></div>
                <div class="form-group images-field">
                    <label>{l s='Images' mod='qlohotelreview'}</label>
                    <div class="inputs-wrap"></div>

                    <div class="image-input-btn">
                        <img class="img-camera-plus">
                    </div>

                    <div class="previews-wrap"></div>
                </div>

                <div class="form-group">
                    <label>
                        {l s='Tell us about your Overall experience' mod='qlohotelreview'}
                    </label>
                    <div class="stars-wrap">
                        <div class="raty" data-score-name="rating_overall" data-score="0"></div>
                    </div>
                </div>

                {if isset($categories) && count($categories)}
                    <div class="categories-wrap">
                        <div class="form-group">
                            <label>
                                {l s='Tell us more what went well?' mod='qlohotelreview'}
                            </label>
                        </div>
                        {foreach $categories as $category}
                            <div class="form-group">
                                <label class="label-category">
                                    {$category.name|escape:'html':'UTF-8'}
                                </label>
                                <div class="stars-wrap">
                                    <div class="raty" data-score-name="rating_categories[{$category.id_category}]" data-score="0"></div>
                                </div>
                            </div>
                        {/foreach}
                    </div>
                {/if}

                <div class="form-group">
                    <label>
                        {l s='Sum up your review' mod='qlohotelreview'}
                    </label>
                    <input class="form-control" type="text" name="subject" placeholder="{l s='Type here...' mod='qlohotelreview'}"/>
                    <p class="review-error subject"></p>
                </div>

                <div class="form-group">
                    <label>
                        {l s='Write in detail' mod='qlohotelreview'}
                    </label>
                    <textarea class="form-control description" type="text" rows="5" name="description" placeholder="{l s='Type here...' mod='qlohotelreview'}"></textarea>
                    <p class="review-error description"></p>
                </div>
            </div>

            <div class="card-footer clearfix review-actions-wrap">
                <div class="pull-right">
                    <button class="btn btn-secondary" id="btn-cancel-review">
                        {l s='Cancel' mod='qlohotelreview'}
                    </button>
                    <button class="btn btn-primary" id="btn-submit-review">
                        {l s='Make Review' mod='qlohotelreview'}
                    </button>
                </div>
            </div>
        </form>
    </div>

    <div id="popup-review-submit-success-no-approval" class="add-review-success-popup">
        <div class="card">
            <div class="text-center">
                <div><i class="icon icon-check-circle text-info"></i></div>
                <h3>{l s='Review added successfully.' mod='qlohotelreview'}</h3>
                <p>{l s='Your review has been added successfully.' mod='qlohotelreview'}</p>
            </div>
        </div>
    </div>

    <div id="popup-review-submit-success-with-approval" class="add-review-success-popup">
        <div class="card">
            <div class="text-center">
                <div><i class="icon icon-check-circle text-info"></i></div>
                <h3>{l s='Review submitted successfully.' mod='qlohotelreview'}</h3>
                <p>{l s='Your review has been submitted for approval.' mod='qlohotelreview'}</p>
            </div>
        </div>
    </div>
</div>
