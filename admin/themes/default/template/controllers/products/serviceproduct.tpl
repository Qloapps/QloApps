{if isset($product->id)}
    <div id="product-configuration" class="panel product-tab">
		<input type="hidden" name="submitted_tabs[]" value="ServiceProduct" />
		<h3 class="tab"> <i class="icon-AdminAdmin"></i> {l s='Service Products'}</h3>

        {if (isset($associated_service_products) && $associated_service_products) || (isset($unassociated_service_products) && $unassociated_service_products)}
            <div class="from-group table-responsive-row clearfix">
                <table class="table hotel-roomtype-link-table">
                    <thead>
                        <tr class="nodrag nodrop">
                            <th class="text-center">
                                <input type="checkbox" class="bulk-service-products-status">
                            </th>
                            <th class="col-sm-1">
                                <span>{l s='ID'}</span>
                            </th>
                            <th class="col-sm-2">
                                <span>{l s='Name'}</span>
                            </th>
                            <th class="text-center">
                                <span>{l s='Auto Add to Cart'}</span>
                            </th>
                            <th>
                                <span>{l s='Position'}</span>
                            </th>
                            <th>
                                <span>{l s='Price'}</span>
                            </th>
                            <th>
                                <span>{l s='Tax'}</span>
                            </th>
                        </tr>
                    </thead>
                    <tbody>
                        {foreach from=$associated_service_products item=service_product}
                            <tr id='room_type_service_product_{$service_product.id_product|escape:'html':'UTF-8'}' position="{$service_product.association_info.position|escape:'html':'UTF-8'}" id_product='{$service_product.id_product|escape:'html':'UTF-8'}' id_element="{$product->id}" data-roomtype_url="{$link->getAdminLink('AdminProducts', true)|addslashes}">
                                {assign var=inputs_prefix value="service_product_`$service_product.id_product`_"}
                                <input type="hidden" name="available_service_products[]" value="{$service_product.id_product}">

                                <td class="text-center">
                                    <input type="checkbox" name="{$inputs_prefix}associated" class="is-associated" checked>
                                </td>
                                <td class="col-sm-1">
                                    {$service_product.id_product|escape:'html':'UTF-8'}
                                    <a target="blank" href="{$link->getAdminLink('AdminNormalProducts')|escape:'html':'UTF-8'}&amp;id_product={$service_product.id_product|escape:'html':'UTF-8'}&amp;updateproduct">
                                        <i class="icon-external-link-sign"></i>
                                    </a>
                                </td>
                                <td>{$service_product.name}</td>
                                <td class="text-center">
                                    <span {if $service_product.auto_add_to_cart}class="badge badge-success"{/if}>
                                        {if $service_product.auto_add_to_cart}{l s='Yes'}{else}{l s='No'}{/if}
                                    </span>
                                </td>
                                <td class="pointer dragHandle center positionImage">
                                    <div class="dragGroup">
                                        <div class="positions">
                                            {$service_product.association_info.position + 1}
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <div class="fixed-width-xl">
                                        <div class="input-group">
                                            <span class="input-group-addon">{$currency->prefix}{$currency->suffix}</span>
                                            <input type="text" name="{$inputs_prefix}price" value="{if isset($smarty.post["{$inputs_prefix}price"]) && $smarty.post["{$inputs_prefix}price"]}{$smarty.post["{$inputs_prefix}price"]}{elseif isset($service_product.association_info.custom_price) && $service_product.association_info.custom_price}{$service_product.association_info.custom_price}{/if}" data-id_product="{$service_product.id_product}">
                                        </div>
                                    </div>
                                    <div class="help-block">
                                        {l s='Default price: %s' sprintf={displayPrice price=$service_product.association_info.default_price currency=$currency->id}}
                                    </div>
                                </td>
                                <td>
                                    <div class="fixed-width-xl">
                                        <select class="service_product_id_tax_rules_group" name="{$inputs_prefix}id_tax_rules_group">
                                            <option value="0">{l s='No Tax'}</option>
                                            {foreach from=$tax_rules_groups item=tax_rules_group}
                                                <option value="{$tax_rules_group.id_tax_rules_group}" {if $service_product.association_info.id_tax_rules_group == $tax_rules_group.id_tax_rules_group}selected="selected"{/if} >
                                                    {$tax_rules_group['name']|htmlentitiesUTF8}
                                                </option>
                                            {/foreach}
                                        </select>
                                    </div>
                                    <div class="help-block">{l s='Default tax rule: %s' sprintf=$service_product.association_info.default_tax_rules_group_name}</div>
                                </td>
                            </tr>
                        {/foreach}

                        {foreach from=$unassociated_service_products item=service_product}
                            <tr class="nodrop nodrag">
                                {assign var=inputs_prefix value="service_product_`$service_product.id_product`_"}
                                <input type="hidden" name="available_service_products[]" value="{$service_product.id_product}">

                                <td class="text-center">
                                    <input type="checkbox" name="{$inputs_prefix}associated" class="is-associated" {if isset($smarty.post["{$inputs_prefix}associated"]) && in_array($smarty.post["{$inputs_prefix}associated"], array('on', 'true', '1'))}checked{/if}>
                                </td>
                                <td class="col-sm-1">{$service_product.id_product|escape:'html':'UTF-8'} <a target="blank" href="{$link->getAdminLink('AdminNormalProducts')|escape:'html':'UTF-8'}&amp;id_product={$service_product.id_product|escape:'html':'UTF-8'}&amp;updateproduct"><i class="icon-external-link-sign"></i></a></td>
                                <td>{$service_product.name}</td>
                                <td class="text-center"><span {if $service_product.auto_add_to_cart}class="badge badge-success"{/if}>{if $service_product.auto_add_to_cart}{l s='Yes'}{else}{l s='No'}{/if}</span></td>
                                <td>{l s='--'}</td>
                                <td>
                                    <div class="fixed-width-xl">
                                        <div class="input-group">
                                            <span class="input-group-addon">{$currency->prefix}{$currency->suffix}</span>
                                            <input type="text" name="{$inputs_prefix}price" data-id_product="{$service_product.id_product|escape:'html':'UTF-8'}" value="{if isset($smarty.post["{$inputs_prefix}price"]) && $smarty.post["{$inputs_prefix}price"]}{$smarty.post["{$inputs_prefix}price"]}{else}{$service_product.price}{/if}">
                                        </div>
                                    </div>
                                    <div class="help-block">
                                        {l s='Default price: %s' sprintf={displayPrice price=$service_product.price currency=$currency->id}}
                                    </div>
                                </td>
                                <td>
                                    <div class="fixed-width-xl">
                                        <select class="service_product_id_tax_rules_group" name="{$inputs_prefix}id_tax_rules_group">
                                            <option value="0">{l s='No Tax'}</option>
                                            {foreach from=$tax_rules_groups item=tax_rules_group}
                                                <option value="{$tax_rules_group.id_tax_rules_group}" {if isset($smarty.post["{$inputs_prefix}id_tax_rules_group"])}{if $tax_rules_group.id_tax_rules_group == $smarty.post["{$inputs_prefix}id_tax_rules_group"]}{/if}{elseif $tax_rules_group.id_tax_rules_group == $service_product.id_tax_rules_group}selected{/if}>
                                                    {$tax_rules_group['name']|htmlentitiesUTF8}
                                                </option>
                                            {/foreach}
                                        </select>
                                    </div>
                                    <div class="help-block">{l s='Default tax rule: %s' sprintf=$service_product.tax_rules_group_name}</div>
                                </td>
                            </tr>
                        {/foreach}
                    </tbody>
                </table>
            </div>
            <div class="panel-footer">
                <a href="{$link->getAdminLink('AdminProducts')|escape:'html':'UTF-8'}{if isset($smarty.request.page) && $smarty.request.page > 1}&amp;submitFilterproduct={$smarty.request.page|intval}{/if}" class="btn btn-default">
                    <i class="process-icon-cancel"></i>
                    {l s='Cancel'}
                </a>
                <button type="submit" name="submitAddproduct" class="btn btn-default pull-right" disabled="disabled">
                    <i class="process-icon-loading"></i>
                    {l s='Save'}
                </button>
                <button type="submit" name="submitAddproductAndStay" class="btn btn-default pull-right"  disabled="disabled">
                    <i class="process-icon-loading"></i>
                        {l s='Save and stay'}
                </button>
            </div>
        {else}
            <div class="alert alert-info">
                {l s='No services are attached with this room type'}
            </div>
        {/if}
    </div>
{/if}