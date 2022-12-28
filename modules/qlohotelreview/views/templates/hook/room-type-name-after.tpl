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

<div>
    <span class="raty readonly" data-score="{$avg_rating}"></span>
    <span class="num_reviews">{$num_reviews} {if $num_reviews|intval > 1}{l s='Review(s)' mod='qlohotelreview'}{else}{l s='Review' mod='qlohotelreview'}{/if}</span>
</div>
