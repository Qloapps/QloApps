{*
* 2007-2017 PrestaShop
*
* NOTICE OF LICENSE
*
* This source file is subject to the Academic Free License (AFL 3.0)
* that is bundled with this package in the file LICENSE.txt.
* It is also available through the world-wide-web at this URL:
* http://opensource.org/licenses/afl-3.0.php
* If you did not receive a copy of the license and are unable to
* obtain it through the world-wide-web, please send an email
* to license@prestashop.com so we can send you a copy immediately.
*
* DISCLAIMER
*
* Do not edit or add to this file if you wish to upgrade PrestaShop to newer
* versions in the future. If you wish to customize PrestaShop for your
* needs please refer to http://www.prestashop.com for more information.
*
*  @author PrestaShop SA <contact@prestashop.com>
*  @copyright  2007-2017 PrestaShop SA
*  @license    http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*}

<div class="row cat_cont">
    <div class="col-sm-12">
        <div class="row margin-lr-0 catSortBlock">
            <div class="col-sm-2 sortBlockHeading">
                <p>{l s='Sort By:'}</p>
            </div>
            <div class="col-sm-3">
                <div class="filter_dw_cont">
                    <button class="btn btn-default dropdown-toggle" type="button" id="gst_rating" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        <span class="pull-left sort_btn_span" data-sort-by="0" data-sort-value="0" data-sort-for="{l s='Rating'}">{l s='Rating'}</span>
                        <span class="caret pull-right margin-top-7"></span>
                    </button>
                    <ul class="dropdown-menu" aria-labelledby="gst_rating">
                        <li><a href="#" class="sort_result" data-sort-by="1" data-value="1">{l s='Rating Ascending'}</a></li>
                        <li><a href="#" class="sort_result" data-sort-by="1" data-value="2">{l s='Rating Descending'}</a></li>
                    </ul>
                </div>
            </div>
            <div class="col-sm-3">
                <div class="filter_dw_cont">
                    <button class="btn btn-default dropdown-toggle" type="button" id="price_ftr" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        <span class="pull-left sort_btn_span" data-sort-by="0" data-sort-value="0" data-sort-for="{l s='Price'}">{l s='Price'}</span>
                        <span class="caret pull-right margin-top-7"></span>
                    </button>
                    <ul class="dropdown-menu" aria-labelledby="price_ftr">
                        <li><a href="#" class="sort_result" data-sort-by="2" data-value="1">{l s='Price : Lowest First'}</a></li>
                        <li><a href="#" class="sort_result" data-sort-by="2" data-value="2">{l s='Price : Highest first '}</a></li>
                    </ul>
                </div>
            </div>
        </div>
        <input type="hidden" id="max_order_date" name="max_order_date" value="{$max_order_date}">
        <div class="row margin-lr-0" id="category_data_cont">
            {include file="./_partials/room_type_list.tpl"}
        </div>

    </div>
</div>
{strip}
    {addJsDef product_controller_url=$link->getPageLink('product')}
    {addJsDef feat_img_dir=$feat_img_dir}
    {addJsDef ratting_img=$ratting_img}
    {addJsDef currency_prefix = $currency->prefix}
    {addJsDef currency_suffix = $currency->suffix}
    {addJsDef max_order_date = $max_order_date}
{/strip}