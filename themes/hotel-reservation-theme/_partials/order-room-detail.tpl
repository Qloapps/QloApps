{*
* 2010-2024 Webkul.
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
* @copyright 2010-2024 Webkul IN
* @license LICENSE.txt
*}

<div class="booking-room-detail" data-id-product="{$data_v.id_product}" data-date-diff="{$rm_k}">
    <div class="row">
        <div class="col-xs-3 col-sm-2">
            <a href="{$link->getProductLink($data_v.id_product)|escape:'html':'UTF-8'}" title="{$data_v.name|escape:'html':'UTF-8'}" target="_blank">
                <img class="img img-responsive img-room-type" src="{$data_v.cover_img|escape:'html':'UTF-8'}" />
            </a>
        </div>
        <div class="col-xs-9 col-sm-10 info-wrap">
            <div class="row">
                <div class="col-xs-12">
                    <a href="{$link->getProductLink($data_v.id_product)|escape:'html':'UTF-8'}" title="{$data_v.name|escape:'html':'UTF-8'}" target="_blank" class="room-type-name">
                        <h3>{$data_v.name|escape:'html':'UTF-8'}</h3>
                    </a>

                    {if $rm_v['count_refunded'] > 0 || $rm_v['count_cancelled'] > 0}
                        <div class="num-refunded-rooms">
                            {if $rm_v['count_cancelled'] > 0}
                                <span class="badge badge-danger">
                                    {if $rm_v['count_cancelled'] > 1}
                                        {$rm_v['count_cancelled']} {l s='Rooms Cancelled'}
                                    {else}
                                        {$rm_v['count_cancelled']} {l s='Room Cancelled'}
                                    {/if}
                                </span>
                            {/if}
                            {if $rm_v['count_refunded'] > 0}
                                <span class="badge badge-danger">
                                    {if $rm_v['count_refunded'] > 1}
                                        {$rm_v['count_refunded']} {l s='Rooms Refunded'}
                                    {else}
                                        {$rm_v['count_refunded']} {l s='Room Refunded'}
                                    {/if}
                                </span>
                            {/if}
                        </div>
                    {/if}
                </div>
                <div class="col-xs-12">
                    <div class="description-list">
                        <dl class="">
                            <div class="row">
                                <div class="col-xs-12 col-md-6">
                                    <div class="row">
                                        <dt class="col-xs-5">{l s='Check-in'}</dt>
                                        <dd class="col-xs-7">{$rm_v.data_form|date_format:'D'}, {dateFormat date=$rm_v.data_form}</dd>
                                    </div>
                                </div>
                                <div class="col-xs-12 col-md-6">
                                    <div class="row">
                                        <dt class="col-xs-5">{l s='Check-out'}</dt>
                                        <dd class="col-xs-7">{$rm_v.data_to|date_format:'D'}, {dateFormat date=$rm_v.data_to}</dd>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-xs-12 col-md-6">
                                    <div class="row">
                                        <dt class="col-xs-5">{l s='Rooms'}</dt>
                                        <dd class="col-xs-7">{$rm_v.num_rm|string_format:'%02d'}</dd>
                                    </div>
                                </div>
                                <div class="col-xs-12 col-md-6">
                                    <div class="row">
                                        <dt class="col-xs-5">{l s='Guests'}</dt>
                                        <dd class="col-xs-7">
                                            {$rm_v.adults|string_format:'%02d'} {if $rm_v.adults > 1}{l s='Adults'}{else}{l s='Adult'}{/if}{if $rm_v.children}, {$rm_v.children|string_format:'%02d'} {if $rm_v.children > 1}{l s='Children'}{else}{l s='Child'}{/if}{/if}
                                        </dd>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-xs-12 col-md-6">
                                    <div class="row">
                                        <dt class="col-xs-5">{l s='Extra Services'}</dt>
                                        <dd class="col-xs-7">
                                            {if (isset($rm_v.extra_demands) && $rm_v.extra_demands) || isset($rm_v.additional_services) && $rm_v.additional_services}
                                                <a data-date_from="{$rm_v.data_form}" data-date_to="{$rm_v.data_to}" data-id_product="{$data_v.id_product}" data-id_order="{$order->id}" data-action="{$link->getPageLink({$page_name})}" class="btn-view-extra-services" href="#rooms_type_extra_services_form">
                                                {/if}
                                                {if $group_use_tax}
                                                    {displayWtPriceWithCurrency price=($rm_v.extra_demands_price_ti + $rm_v.additional_services_price_ti)  currency=$currency}
                                                {else}
                                                    {displayWtPriceWithCurrency price=($rm_v.extra_demands_price_te + $rm_v.additional_services_price_te) currency=$currency}
                                                {/if}
                                                {if (isset($rm_v.extra_demands) && $rm_v.extra_demands) || isset($rm_v.additional_services) && $rm_v.additional_services}
                                                </a>
                                            {/if}
                                        </dd>
                                    </div>
                                </div>
                                <div class="col-xs-12 col-md-6">
                                    <div class="row">
                                        <dt class="col-xs-5">{l s='Total Price'}</dt>
                                        <dd class="col-xs-7">
                                            {if $group_use_tax}
                                                {displayWtPriceWithCurrency price=($rm_v.amount_tax_incl + $rm_v.extra_demands_price_ti + $rm_v.additional_services_price_ti + $rm_v.additional_services_price_auto_add_ti) currency=$currency}
                                            {else}
                                                {displayWtPriceWithCurrency price=($rm_v.amount_tax_excl + $rm_v.extra_demands_price_te + $rm_v.additional_services_price_te + $rm_v.additional_services_price_auto_add_te) currency=$currency}
                                            {/if}
                                            {if (isset($rm_v.extra_demands) && $rm_v.extra_demands) || isset($rm_v.additional_services) && $rm_v.additional_services}
                                                <span class="order-price-info">
                                                    <img src="{$img_dir}icon/icon-info.svg" />
                                                </span>
                                                <div class="price-info-container" style="display: none;">
                                                    <div class="price-info-tooltip-cont">
                                                        <div class="list-row">
                                                            <div>
                                                                <p>{l s='Room(s) cost:'}</p>
                                                            </div>
                                                            <div class="text-right">
                                                                <p>
                                                                    {if $group_use_tax}
                                                                        {displayWtPriceWithCurrency price=($rm_v.amount_tax_incl + $rm_v.additional_services_price_auto_add_ti) currency=$currency}
                                                                    {else}
                                                                        {displayWtPriceWithCurrency price=($rm_v.amount_tax_excl + $rm_v.additional_services_price_auto_add_te) currency=$currency}
                                                                    {/if}
                                                                </p>
                                                            </div>
                                                        </div>
                                                        <div class="list-row">
                                                            <div>
                                                                <p>{l s='Service(s) cost:'}</p>
                                                            </div>
                                                            <div class="text-right">
                                                                <p>
                                                                    {if $group_use_tax}
                                                                        {displayWtPriceWithCurrency price=($rm_v.extra_demands_price_ti + $rm_v.additional_services_price_ti)  currency=$currency}
                                                                    {else}
                                                                        {displayWtPriceWithCurrency price=($rm_v.extra_demands_price_te + $rm_v.additional_services_price_te) currency=$currency}
                                                                    {/if}
                                                                </p>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            {/if}
                                        </dd>
                                    </div>
                                </div>
                            </div>
                        </dl>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
