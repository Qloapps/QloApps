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

{foreach $service_products as $service_product}
    {if !($service_product@first && isset($init) && $init == true)}
        <hr>
    {/if}
    {block name='service_products_list_row'}
        {include file="{$tpl_dir}_partials/service-products-list-row.tpl" service_product=$service_product product=$product}
    {/block}
{/foreach}
