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

{if isset($service_products_exists) && $service_products_exists}
    <ul class="nav nav-tabs product_description_tabs">
        {if !$PS_SERVICE_PRODUCT_CATEGORY_FILTER}
            <li class="active"><a href="#all_products" class="idTabHrefShort" data-toggle="tab">{l s='Services'}</a></li>
        {else}
            {foreach $service_products_by_category as $category}
                <li {if $category@iteration == 1}class="active"{/if}><a class="idTabHrefShort" href="#category_{$category['id_category']}" data-toggle="tab">{$category['name']}</a></li>
            {/foreach}
        {/if}
    </ul>
    <div class="card">
        <div class="row">
            <div class="col-sm-12 tab-content">
                {if $PS_SERVICE_PRODUCT_CATEGORY_FILTER}
                    {foreach $service_products_by_category as $service_product_category}
                        <div class="tab-pane {if $service_product_category@iteration == 1}active{/if}" id="category_{$service_product_category['id_category']}">
                            <ul class="product-list">
                                {include file="{$tpl_dir}_partials/service-products-list.tpl" service_products=$service_product_category['products'] group=$service_product_category['id_category'] init=true}
                            </ul>
                            {if RoomTypeServiceProduct::WK_NUM_RESULTS < $service_product_category['num_products']}
                                <div class="show_more_btn_container">
                                    <button class="btn btn-default get-service-products" data-id_category="{$service_product_category['id_category']}" data-page="2" data-num_total="{$service_product_category['num_products']}">{l s='Show More'}</button>
                                </div>
                            {/if}
                        </div>
                    {/foreach}
                {else}
                    <ul class="product-list">
                        {include file="{$tpl_dir}_partials/service-products-list.tpl" service_products=$service_products init=true}
                    </ul>
                    {if RoomTypeServiceProduct::WK_NUM_RESULTS < $num_total_service_products}
                        <div class="show_more_btn_container">
                            <button class="btn btn-default get-service-products" data-page="2" data-num_total="{$num_total_service_products}">{l s='Show More'}</button>
                        </div>
                    {/if}
                {/if}
            </div>
        </div>
    </div>
{/if}