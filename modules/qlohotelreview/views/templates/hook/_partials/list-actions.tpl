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

<div class="review-list-actions">
    <div class="row">
        <div class="col-xs-12 text-right">
            <div class="">
                <div class="dropdown review-sort-by">
                    <label class="sort-by">{l s='SORT BY' mod='qlohotelreview'}</label>
                    <button class="btn btn-default dropdown-toggle"
                        type="button"
                        data-toggle="dropdown"
                        data-value="{QhrHotelReview::QHR_SORT_BY_TIME_NEW}">
                        <span>{l s='Time (Newest First)' mod='qlohotelreview'}</span>
                        <span class="caret"></span>
                    </button>
                    <ul class="dropdown-menu text-left">
                        <li><a class="sort-by-option"
                            data-id-hotel="{$id_hotel}"
                            data-value="{QhrHotelReview::QHR_SORT_BY_RELEVENCE}" href="#">{l s='Relevance' mod='qlohotelreview'}
                        </a></li>
                        <li><a class="sort-by-option"
                            data-id-hotel="{$id_hotel}"
                            data-value="{QhrHotelReview::QHR_SORT_BY_TIME_NEW}" href="#">{l s='Time (Newest First)' mod='qlohotelreview'}
                        </a></li>
                        <li><a class="sort-by-option"
                            data-id-hotel="{$id_hotel}"
                            data-value="{QhrHotelReview::QHR_SORT_BY_TIME_OLD}" href="#">{l s='Time (Oldest First)' mod='qlohotelreview'}
                        </a></li>
                        <li><a class="sort-by-option"
                            data-id-hotel="{$id_hotel}"
                            data-value="{QhrHotelReview::QHR_SORT_BY_RATING_HIGH}" href="#">{l s='Rating (Highest First)' mod='qlohotelreview'}
                        </a></li>
                        <li><a class="sort-by-option"
                            data-id-hotel="{$id_hotel}"
                            data-value="{QhrHotelReview::QHR_SORT_BY_RATING_LOW}" href="#">{l s='Rating (Lowest First)' mod='qlohotelreview'}
                        </a></li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>