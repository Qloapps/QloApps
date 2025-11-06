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

{if isset($service_products_exists) && $service_products_exists}
    {block name='service_products_tabs'}
        <ul class="nav nav-tabs product_description_tabs">
            {if !$PS_SERVICE_PRODUCT_CATEGORY_FILTER}
                <li class="active"><a href="#all_products" class="idTabHrefShort" data-toggle="tab">{l s='Services'}</a></li>
            {else}
                {foreach $service_products_by_category as $category}
                    <li {if $category@iteration == 1}class="active"{/if}><a class="idTabHrefShort" href="#category_{$category['id_category']}" data-toggle="tab">{$category['name']}</a></li>
                {/foreach}
            {/if}
        </ul>
    {/block}
    {block name='service_products_tabs_content'}
        <div class="card">
            <div class="row">
                <div class="col-sm-12 tab-content">
                    {if $PS_SERVICE_PRODUCT_CATEGORY_FILTER}
                        {foreach $service_products_by_category as $service_product_category}
                            <div class="tab-pane {if $service_product_category@iteration == 1}active{/if}" id="category_{$service_product_category['id_category']}">
                                <ul class="product-list">
                                    {block name='service_products_list'}
                                        {include file="{$tpl_dir}_partials/service-products-list.tpl" service_products=$service_product_category['products'] group=$service_product_category['id_category'] init=true product=$product}
                                    {/block}
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
                            {block name='service_products_list'}
                                {include file="{$tpl_dir}_partials/service-products-list.tpl" service_products=$service_products group='all' init=true  product=$product}
                            {/block}
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
    {/block}
{/if}
