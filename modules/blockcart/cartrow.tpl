{assign var='productId' value=$product.id_product}
{assign var='productAttributeId' value=$product.id_product_attribute}
<dt data-id="cart_block_product_{$product.id_product|intval}_{if $product.id_product_attribute}{$product.id_product_attribute|intval}{else}0{/if}_{if $product.id_address_delivery}{$product.id_address_delivery|intval}{else}0{/if}_{if isset($hotel_wise_data.id_hotel) && $hotel_wise_data.id_hotel}{$hotel_wise_data.id_hotel}{else}0{/if}" class="{if $smarty.foreach.myLoop.first}first_item{elseif $smarty.foreach.myLoop.last}last_item{else}item{/if}">
    <a class="cart-images" href="{$link->getProductLink($product.id_product, $product.link_rewrite, $product.category)|escape:'html':'UTF-8'}" title="{$product.name|escape:'html':'UTF-8'}"><img src="{$link->getImageLink($product.link_rewrite, $product.id_image, 'cart_default')}" alt="{$product.name|escape:'html':'UTF-8'}" /></a>
    <div class="cart-info">
        {block name='blockcart_shopping_cart_product_name'}
            <div class="product-name">
            {* <!-- quantity changed for number of rooms -->
                <!-- <span class="quantity-formated"><span class="quantity">{$cart_booking_data[$data_k]['total_num_rooms']}</span>&nbsp;x&nbsp;</span> --> *}
                <a class="cart_block_product_name" href="{$link->getProductLink($product, $product.link_rewrite, $product.category, null, null, $product.id_shop, $product.id_product_attribute)|escape:'html':'UTF-8'}" title="{$product.name|escape:'html':'UTF-8'}">{$product.name|truncate:30:'...':true|escape:'html':'UTF-8'}</a>
            </div>
        {/block}
        {block name='blockcart_shopping_cart_hotel_name'}
            {if $product.selling_preference_type == Product::SELLING_PREFERENCE_HOTEL_STANDALONE || $product.selling_preference_type == Product::SELLING_PREFERENCE_HOTEL_STANDALONE_AND_WITH_ROOM_TYPE}
                <div class="hotel-name">
                    {$hotel_wise_data.hotel_name|escape:'html':'UTF-8'}
                </div>
            {/if}
        {/block}
        {if isset($product.attributes_small)}
            <div class="product-atributes">
                <a href="{$link->getProductLink($product, $product.link_rewrite, $product.category, null, null, $product.id_shop, $product.id_product_attribute)|escape:'html':'UTF-8'}" title="{l s='Product detail' mod='blockcart'}">{$product.attributes_small}</a>
            </div>
        {/if}
        {block name='blockcart_shopping_cart_product_total_price'}
            <div class="cart-info-sec rm_product_info_{$product.id_product}">
                <span class="product_info_label">{l s='Price' mod='blockcart'}:</span>
                <span class="price product_info_data">
                    {if !isset($product.is_gift) || !$product.is_gift}
                        {if $product.booking_product}
                            {displayWtPrice p="`$product.bookingData.total_room_type_amount`"}
                        {else if $product.selling_preference_type == Product::SELLING_PREFERENCE_HOTEL_STANDALONE || $product.selling_preference_type == Product::SELLING_PREFERENCE_HOTEL_STANDALONE_AND_WITH_ROOM_TYPE}
                            {if $priceDisplay == $smarty.const.PS_TAX_EXC}{displayWtPrice p="`$hotel_wise_data.total_price_tax_excl`"}{else}{displayWtPrice p="`$hotel_wise_data.total_price_tax_incl`"}{/if}
                        {else}
                            {if $priceDisplay == $smarty.const.PS_TAX_EXC}{displayWtPrice p="`$product.total_price_tax_excl`"}{else}{displayWtPrice p="`$product.total_price_tax_incl`"}{/if}
                        {/if}
                        <div id="hookDisplayProductPriceBlock-price">
                            {block name='displayProductPriceBlock'}
                                {hook h="displayProductPriceBlock" product=$product type="price" from="blockcart"}
                            {/block}
                        </div>
                    {else}
                        {l s='Free!' mod='blockcart'}
                    {/if}
                </span>
            </div>
        {/block}

        {block name='blockcart_shopping_cart_product_quantity'}
            <div class="cart-info-sec rm_product_info_{$product.id_product}">
                {if $product.allow_multiple_quantity || $product.booking_product}<span class="product_info_label">{l s='Total Qty.' mod='blockcart'}:</span>{/if}
                <span class="quantity-formated">
                    {if $product.booking_product}
                        <span class="quantity product_info_data">{$product.bookingData['total_num_rooms']}</span>
                    {elseif $product.allow_multiple_quantity}
                        {if $product.selling_preference_type == Product::SELLING_PREFERENCE_HOTEL_STANDALONE || $product.selling_preference_type == Product::SELLING_PREFERENCE_HOTEL_STANDALONE_AND_WITH_ROOM_TYPE}
                            <span class="quantity product_info_data">{$hotel_wise_data.total_qty}</span>
                        {elseif $product.selling_preference_type == Product::SELLING_PREFERENCE_STANDALONE}
                            <span class="quantity product_info_data">{$product.cart_quantity}</span>
                        {/if}
                    {/if}
                </span>
            </div>
        {/block}
    </div>
    <span class="remove_link">
        {if !isset($customizedDatas.$productId.$productAttributeId) && (!isset($product.is_gift) || !$product.is_gift)}
            <a class="ajax_cart_block_remove_link" href="{$link->getPageLink('cart', true, NULL, "delete=1&id_product={$product.id_product|intval}&ipa={$product.id_product_attribute|intval}&id_address_delivery={$product.id_address_delivery|intval}&token={$static_token}")|escape:'html':'UTF-8'}{if !empty($hotel_wise_data.id_hotel)}&id_hotel={$hotel_wise_data.id_hotel|escape:'html':'UTF-8'}{/if}" rel="nofollow" title="{l s='remove this product from my cart' mod='blockcart'}">&nbsp;</a>
        {/if}
    </span>
    <div style="clear:both"></div>
    {if $product.booking_product}
        {block name='blockcart_shopping_cart_dates'}
            <div id="booking_dates_container_{$product.id_product}" class="cart_prod_cont">
                <div class="table-responsive">
                    <table class="table">
                        <tbody>
                            <tr>
                                <th>{l s='Duration' mod='blockcart'}</th>
                                <th>{l s='Qty.' mod='blockcart'}</th>
                                <th>{l s='Price' mod='blockcart'}</th>
                                <th>&nbsp;<!-- {l s='Remove' mod='blockcart'} --></th>
                            </tr>
                            {foreach from=$product.bookingData['date_diff'] key=data_k1 item=data_v}
                                <tr class="rooms_remove_container">
                                    {assign var="is_full_date" value=($show_full_date && ($data_v['data_form']|date_format:'%D' == $data_v['data_to']|date_format:'%D'))}
                                    <td>
                                        {dateFormat date=$data_v['data_form'] full=$is_full_date}&nbsp;-&nbsp;{dateFormat date=$data_v['data_to'] full=$is_full_date}
                                    </td>
                                    <td class="num_rooms_in_date">{$data_v['num_rm']}</td>
                                    <td>{convertPrice price=($data_v['amount'] + $data_v['demand_price'])}</td>
                                    <td><a class="remove_rooms_from_cart_link" href="#" rm_price="{$data_v['amount']}" id_product="{$product.id_product|intval}" date_from="{$data_v['data_form']}" date_to="{$data_v['data_to']}" num_rooms="{$data_v['num_rm']}" title="{l s='remove this room from my cart' mod='blockcart'}"></a></td>
                                </tr>
                            {/foreach}
                        </tbody>
                    </table>
                </div>
            </div>
        {/block}
    {else if $product.hasOptions}
        {block name='blockcart_shopping_cart_options'}
            {if $product.selling_preference_type == Product::SELLING_PREFERENCE_HOTEL_STANDALONE || $product.selling_preference_type == Product::SELLING_PREFERENCE_HOTEL_STANDALONE_AND_WITH_ROOM_TYPE}
                {assign var='options' value=$hotel_wise_data.options}
            {else if $product.selling_preference_type == Product::SELLING_PREFERENCE_STANDALONE}
                {assign var='options' value=$product.options}
            {/if}
            <div class="table-responsive cart_prod_cont">
                <table class="table">
                    <tbody>
                        <tr>
                            <th>{l s='Variant' mod='blockcart'}</th>
                            {if $product.allow_multiple_quantity}
                                <th>{l s='Qty.' mod='blockcart'}</th>
                            {/if}
                            <th>{l s='Price' mod='blockcart'}</th>
                            <th>&nbsp;</th>
                        </tr>
                        {foreach from=$options item=data_v}
                            <tr class="product_option_row">
                                <td>{$data_v.option_name}</td>
                                {if $product.allow_multiple_quantity}
                                    <td>{$data_v['quantity']}</td>
                                {/if}
                                <td>{convertPrice price=($data_v['total_price_tax_excl'])}</td>
                                <td class="text-right"><a class="ajax_remove_product_option" href="#" id_product="{$product.id_product|intval}" id_hotel="{$data_v.id_hotel|intval}" id_product_option="{$data_v.id_product_option|intval}" title="{l s='remove this product from my cart' mod='blockcart'}">&nbsp;</a></td>
                            </tr>
                        {/foreach}
                    </tbody>
                </table>
            </div>
        {/block}
    {/if}
</dt>
{if isset($product.attributes_small)}
    <dd data-id="cart_block_combination_of_{$product.id_product|intval}{if $product.id_product_attribute}_{$product.id_product_attribute|intval}{/if}_{$product.id_address_delivery|intval}" class="{if $smarty.foreach.myLoop.first}first_item{elseif $smarty.foreach.myLoop.last}last_item{else}item{/if}">
{/if}
<!-- Customizable datas -->
{if isset($customizedDatas.$productId.$productAttributeId[$product.id_address_delivery])}
    {if !isset($product.attributes_small)}
        <dd data-id="cart_block_combination_of_{$product.id_product|intval}_{if $product.id_product_attribute}{$product.id_product_attribute|intval}{else}0{/if}_{if $product.id_address_delivery}{$product.id_address_delivery|intval}{else}0{/if}" class="{if $smarty.foreach.myLoop.first}first_item{elseif $smarty.foreach.myLoop.last}last_item{else}item{/if}">
    {/if}
    <ul class="cart_block_customizations" data-id="customization_{$productId}_{$productAttributeId}">
        {foreach from=$customizedDatas.$productId.$productAttributeId[$product.id_address_delivery] key='id_customization' item='customization' name='customizations'}
            <li name="customization">
                <div data-id="deleteCustomizableProduct_{$id_customization|intval}_{$product.id_product|intval}_{$product.id_product_attribute|intval}_{$product.id_address_delivery|intval}" class="deleteCustomizableProduct">
                    <a class="ajax_cart_block_remove_link" href="{$link->getPageLink('cart', true, NULL, "delete=1&id_product={$product.id_product|intval}&ipa={$product.id_product_attribute|intval}&id_customization={$id_customization|intval}&token={$static_token}")|escape:'html':'UTF-8'}" rel="nofollow">&nbsp;</a>
                </div>
                {if isset($customization.datas.$CUSTOMIZE_TEXTFIELD.0)}
                    {$customization.datas.$CUSTOMIZE_TEXTFIELD.0.value|replace:"<br />":" "|truncate:28:'...'|escape:'html':'UTF-8'}
                {else}
                    {l s='Customization #%d:' sprintf=$id_customization|intval mod='blockcart'}
                {/if}
            </li>
        {/foreach}
    </ul>
    {if !isset($product.attributes_small)}</dd>{/if}
{/if}
{if isset($product.attributes_small)}</dd>{/if}