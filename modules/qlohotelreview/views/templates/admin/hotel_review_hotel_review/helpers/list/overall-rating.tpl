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

{if $rating < 2}
    <span class="badge badge-danger">{$rating|escape:'html':'UTF-8'}</span>
{elseif $rating < 3.5}
    <span class="badge badge-warning">{$rating|escape:'html':'UTF-8'}</span>
{else}
    <span class="badge badge-success">{$rating|escape:'html':'UTF-8'}</span>
{/if}
