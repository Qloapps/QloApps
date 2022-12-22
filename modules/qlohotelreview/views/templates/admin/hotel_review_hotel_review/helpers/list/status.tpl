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

{if $status == QhrHotelReview::QHR_STATUS_PENDING}
    <span class="badge badge-warning">{l s='Pending' mod='qlohotelreview'}</span>
{elseif $status == QhrHotelReview::QHR_STATUS_DISAPPROVED}
    <span class="badge badge-danger">{l s='Disapproved' mod='qlohotelreview'}</span>
{elseif $status == QhrHotelReview::QHR_STATUS_APPROVED}
    <span class="badge badge-success">{l s='Approved' mod='qlohotelreview'}</span>
{/if}
