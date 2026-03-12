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

{block name='hotel_review'}
    <div id="qlohotelreview" style="display: none;">
        <div id="add-review-popup" class="card qlo-account-card">
            <div class="card-header d-flex align-items-center justify-content-between">
                <span class="hotel-name h6 mb-0"></span>
                <button type="button" class="btn btn-link p-0 text-dark btn-cancel-review" aria-label="{l s='Close' mod='qlohotelreview'}">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            {block name='hotel_review_add_form'}
                <form action="{$action}" id="add-review-form" method="post" enctype="multipart/form-data">
                    <div class="card-body">
                        <input type="hidden" name="id_order" value="0"/>
                        <input type="hidden" name="id_hotel" value="0"/>

                        <div class="alert alert-danger" id="review-general-errors" style="display:none;"></div>
                        <div class="form-group images-field">
                            <label class="d-block mb-2">{l s='Images' mod='qlohotelreview'}</label>
                            <div class="inputs-wrap"></div>
                            <div class="border rounded bg-light p-3 text-center">
                                <button type="button" class="image-input-btn btn btn-light border d-flex flex-column align-items-center justify-content-center w-100">
                                    <span class="icon-camera h4 mb-2"></span>
                                    <span class="small text-muted">{l s='Drop files here to upload' mod='qlohotelreview'}</span>
                                </button>
                            </div>
                            <div class="previews-wrap d-flex flex-wrap mt-2"></div>
                        </div>

                        <div class="form-group mb-3">
                            <label>
                                {l s='Tell us about your Overall experience' mod='qlohotelreview'}
                            </label>
                            <div class="stars-wrap">
                                <div class="raty" data-score-name="rating_overall" data-score="0"></div>
                            </div>
                        </div>

                        {if isset($categories) && count($categories)}
                            <div class="categories-wrap">
                                <div class="form-group mb-2">
                                    <label>
                                        {l s='Tell us more what went well?' mod='qlohotelreview'}
                                    </label>
                                </div>
                                {foreach $categories as $category}
                                    <div class="form-group mb-2">
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

                        <div class="form-group mb-0">
                            <label>
                                {l s='Write in detail' mod='qlohotelreview'}
                            </label>
                            <textarea class="form-control description" type="text" rows="5" name="description" placeholder="{l s='Type here...' mod='qlohotelreview'}"></textarea>
                            <p class="review-error description"></p>
                        </div>
                    </div>

                    {block name='hotel_review_add_form_actions'}
                        <div class="card-footer d-flex justify-content-end review-actions-wrap">
                            <button class="btn btn-outline-secondary btn-cancel-review" id="btn-cancel-review">
                                {l s='Cancel' mod='qlohotelreview'}
                            </button>
                            <button class="btn btn-primary ml-2" id="btn-submit-review">
                                {l s='Make Review' mod='qlohotelreview'}
                            </button>
                        </div>
                    {/block}
                </form>
            {/block}
        </div>

        {block name='hotel_review_submit_content'}
            <div id="popup-review-submit-success-no-approval" class="add-review-success-popup">
                <div class="card qlo-account-card">
                    <div class="text-center">
                        <div><i class="icon icon-check-circle text-info"></i></div>
                        <h3>{l s='Review added successfully.' mod='qlohotelreview'}</h3>
                        <p>{l s='Your review has been added successfully.' mod='qlohotelreview'}</p>
                    </div>
                </div>
            </div>

            <div id="popup-review-submit-success-with-approval" class="add-review-success-popup">
                <div class="card qlo-account-card">
                    <div class="text-center">
                        <div><i class="icon icon-check-circle text-info"></i></div>
                        <h3>{l s='Review submitted successfully.' mod='qlohotelreview'}</h3>
                        <p>{l s='Your review has been submitted for approval.' mod='qlohotelreview'}</p>
                    </div>
                </div>
            </div>
        {/block}
    </div>
{/block}
