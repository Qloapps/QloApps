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

<ul class="panel nav nav-pills">
    <li {if isset($smarty.get.controller) && $smarty.get.controller == 'AdminHotelReviewHotelReview'}class="active"{/if}>
        <a href="{$link->getAdminLink('AdminHotelReviewHotelReview')|escape:'htmlall':'UTF-8'}">
            <i class="icon-comments-o"></i>
            {l s='Reviews' mod='qlohotelreview'}
        </a>
    </li>
    <li {if isset($smarty.get.controller) && $smarty.get.controller == 'AdminHotelReviewCategory'}class="active"{/if}>
        <a href="{$link->getAdminLink('AdminHotelReviewCategory')|escape:'htmlall':'UTF-8'}">
            <i class="icon-cogs"></i>
            {l s='Configuration' mod='qlohotelreview'}
        </a>
    </li>
</ul>