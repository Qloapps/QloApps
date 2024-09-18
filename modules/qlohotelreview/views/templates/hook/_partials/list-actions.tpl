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

<div class="review-list-actions">
    <div class="row">
        <div class="col-xs-12 text-right">
            <div class="">
                <div class="dropdown review-sort-by">
                    <label class="sort-by">{l s='SORT BY' mod='qlohotelreview'}</label>
                    <div class="review-sort-by-container">
                        <button class="btn btn-default dropdown-toggle" type="button" data-toggle="dropdown" data-value="{QhrHotelReview::QHR_SORT_BY_TIME_NEW}">
                            <span>{l s='Newest First' mod='qlohotelreview'}</span>
                            <span class="caret"></span>
                        </button>
                        <ul class="dropdown-menu text-left">
                            <li><a class="sort-by-option" data-id-hotel="{$id_hotel}" data-value="{QhrHotelReview::QHR_SORT_BY_RELEVANCE}" href="#">{l s='Most Relevant' mod='qlohotelreview'}</a></li>
                            <li><a class="sort-by-option" data-id-hotel="{$id_hotel}" data-value="{QhrHotelReview::QHR_SORT_BY_TIME_NEW}" href="#">{l s='Newest First' mod='qlohotelreview'}</a></li>
                            <li><a class="sort-by-option" data-id-hotel="{$id_hotel}" data-value="{QhrHotelReview::QHR_SORT_BY_TIME_OLD}" href="#">{l s='Oldest First' mod='qlohotelreview'}</a></li>
                            <li><a class="sort-by-option" data-id-hotel="{$id_hotel}" data-value="{QhrHotelReview::QHR_SORT_BY_RATING_HIGH}" href="#">{l s='Positive First' mod='qlohotelreview'}</a></li>
                            <li><a class="sort-by-option" data-id-hotel="{$id_hotel}" data-value="{QhrHotelReview::QHR_SORT_BY_RATING_LOW}" href="#">{l s='Negative First' mod='qlohotelreview'}</a></li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>