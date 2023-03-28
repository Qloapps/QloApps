{if isset($product->id)}
    <div id="product-configuration" class="panel product-tab">
		<input type="hidden" name="submitted_tabs[]" value="ServiceProduct" />
		<h3 class="tab"> <i class="icon-AdminAdmin"></i> {l s='Service Products'}</h3>

        {if isset($service_products) && $service_products}
            <div class="from-group table-responsive-row clearfix">
                <table class="table tableDnD hotel-roomtype-link-table">
                    <thead>
                        <tr class="nodrag nodrop">
                            <th class="col-sm-1">
                                <span>{l s='Id Product'}</span>
                            </th>
                            <th class="col-sm-3">
                                <span>{l s='name'}</span>
                            </th>
                            <th class="">
                                <span>{l s='Category'}</span>
                            </th>
                            <th>
                                <span>{l s='Position'}</span>
                            </th>
                            <th class="">
                                <span>{l s='Price'}</span>
                            </th>
                            <th class="">
                                <span>{l s='Tax'}</span>
                            </th>
                            <th class="text-right">
                                <span>{l s='Action'}</span>
                            </th>
                        </tr>
                    </thead>
                    <tbody>
						{foreach from=$service_products key=key item=service_product}
                            <tr id='room_type_service_product_{$service_product.id_room_type_service_product|escape:'html':'UTF-8'}' position='{$service_product.position|escape:'html':'UTF-8'}' id_product='{$service_product.id_product|escape:'html':'UTF-8'}' id_element="{$product->id}" data-roomtype_url="{$link->getAdminLink('AdminProducts', true)|addslashes}">
                                <td class="col-sm-1">{$service_product.id_product|escape:'html':'UTF-8'} <a target="blank" href="{$link->getAdminLink('AdminNormalProducts')|escape:'html':'UTF-8'}&amp;id_product={$service_product.id_product|escape:'html':'UTF-8'}&amp;updateproduct"><i class="icon-external-link-sign"></i></a></td>
                                <td class="">{$service_product.name}</td>
                                <td class="">{$service_product.category}</td>
                                <td class="pointer dragHandle center positionImage">
                                    <div class="dragGroup">
                                        <div class="positions">
                                            {$service_product.position + 1}
                                        </div>
                                    </div>
                                </td>
                                <td class="">
                                    <span class="field-view service-product-price-text">{if isset($service_product.custom_price) && $service_product.custom_price}{displayPrice price=$service_product.custom_price currency=$id_currency}{else}{displayPrice price=$service_product.default_price currency=$id_currency}{/if}</span>
                                    <div class="field-edit" style="display:none">
                                        <div class="input-group">
                                            <input type="text" value="{if isset($service_product.custom_price) && $service_product.custom_price}{$service_product.custom_price|escape:'html':'UTF-8'}{else}{$service_product.default_price|escape:'html':'UTF-8'}{/if}" class="service-product-price" data-id_product="{$service_product.id_product|escape:'html':'UTF-8'}">
                                            <span class="input-group-addon">{$currency->prefix}{$currency->suffix}</span>
                                        </div>
                                    </div>
                                    <div class="help-block">{l s='Default price: %s' sprintf={displayPrice price=$service_product.default_price currency=$id_currency}}
                                </td>
                                <td>
                                    <span class="field-view service_product_tax_text">{if isset($service_product.tax_rules_group_name) && $service_product.tax_rules_group_name}{$service_product.tax_rules_group_name}{else}{$service_product.default_tax_rules_group_name}{/if}</span>
                                    <div class="field-edit" style="display:none">
                                        <select class="service_product_id_tax_rules_group"{if $tax_exclude_taxe_option}disabled="disabled"{/if}>
                                            <option value="0">{l s='No Tax'}</option>
                                            {foreach from=$tax_rules_groups item=tax_rules_group}
                                                <option value="{$tax_rules_group.id_tax_rules_group}" {if $service_product.id_tax_rules_group == $tax_rules_group.id_tax_rules_group}selected="selected"{/if} >
                                                    {$tax_rules_group['name']|htmlentitiesUTF8}
                                                </option>
                                            {/foreach}
                                        </select>
                                    </div>
                                    <div class="help-block">{l s='Default tax rule: %s' sprintf=$service_product.default_tax_rules_group_name}
                                </td>
                                <td class="text-right">
                                    <a href="#" class="btn btn-default button-edit-price field-view"><i class="icon-pencil"></i></a>
                                    <span class="field-edit" style="display:none">
                                        <a href="#" class="btn btn-default btn-save" data-roomtype_url="{$link->getAdminLink('AdminProducts', true)|addslashes}" data-id_product="{$service_product.id_product|escape:'html':'UTF-8'}" data-id_room_type_service_product_price="{$service_product.id_room_type_service_product_price|escape:'html':'UTF-8'}"><i class="icon-save"></i> {l s='save'}</a>
                                        <a href="#" class="btn btn-default btn-cancel"><i class="icon-times"></i></a>
                                    </span>
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