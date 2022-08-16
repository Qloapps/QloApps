{**
* 2010-2023 Webkul.
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
* @copyright 2010-2023 Webkul IN
* @license LICENSE.txt
*}

{foreach $service_products as $product}
    {if !($product@first && isset($init) && $init == true)}
        <hr>
    {/if}
    {include file="{$tpl_dir}_partials/service-products-list-row.tpl" product=$product}
{/foreach}