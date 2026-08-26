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
{extends file="helpers/options/options.tpl"}

{block name="input"}
    {if $field['type'] == 'html' && $category == 'standard_product' && $key == 'PS_STANDARD_PRODUCT_ORDER_ADDRESS'}
        <div class="col-lg-9">
            <div class="alert alert-info"> {l s='Please set all the required fields for the custom address below'}</div>
        </div>
        <div>
            {if isset($countries) && $countries}
                <div id="conf_service_id_country" class="form-group">
                    <label class="control-label required col-lg-3">{l s='Country'}</label>
                    <div class="col-lg-9">
                        <select class="form-control fixed-width-xxl " name="service_id_country" id="service_id_country">
                            {foreach from=$countries item=country}
                                <option value="{$country['id_country']}" {if isset($custom_address_details['id_country']) && $country['id_country'] == $custom_address_details['id_country']}selected{/if}>{$country['name']}</option>
                            {/foreach}
                        </select>
                    </div>
                </div>
                <div id="conf_service_id_state" class="form-group">
                    <label class="control-label required col-lg-3">{l s='State'}</label>
                    <div class="col-lg-9">
                        <select class="form-control fixed-width-xxl" name="service_id_state" id="service_id_state">
                        {if isset($custom_address_details['id_state'])}<option value="{$custom_address_details['id_state']}" selected></option>{/if}
                        </select>
                    </div>
                </div>
            {/if}
            <div id="conf_service_city" class="form-group">
                <label class="control-label required col-lg-3">{l s='City'}</label>
                <div class="col-lg-9">
                    <input class="form-control fixed-width-xxl" type="text" name="service_city" id="service_city" {if isset($custom_address_details['city'])}value="{$custom_address_details['city']}"{/if}/>
                </div>
            </div>
            <div id="conf_service_postcode" class="form-group">
                <label class="control-label col-lg-3">{l s='Zip/postal code'}</label>
                <div class="col-lg-9">
                    <input class="form-control fixed-width-xxl" type="text" name="service_postcode" id="service_postcode" {if isset($custom_address_details['postcode'])}value="{$custom_address_details['postcode']}"{/if}/>
                </div>
            </div>
        </div>
    {else}
        {$smarty.block.parent}
    {/if}
{/block}

{block name="footer"}
    {$smarty.block.parent}
    {if $category == 'standard_product'}
        <script type="text/javascript">
            function ajaxGetStates(id_state_selected) {
                $.ajax({
                    url: "index.php",
                    cache: false,
                    data: "ajax=1&tab=AdminStates&token={getAdminToken tab='AdminStates'}&action=states&id_country="+$('#service_id_country').val() + "&id_state=" + $('#service_id_state').val(),
                    success: function(html)
                    {
                        if (html == 'false') {
                            $("#conf_service_id_state").fadeOut();
                            $('#service_id_state option[value=0]').attr("selected", "selected");
                        } else {
                            $("#service_id_state").html(html);
                            $("#conf_service_id_state").fadeIn();
                            $('#service_id_state option[value=' + id_state_selected + ']').attr("selected", "selected");
                        }
                    }
                });
            }

            $(document).ready(function(){
                {if isset($custom_address_details['id_state'])}
                    if ($('#service_id_country') && $('#service_id_state')) {
                        ajaxGetStates({$custom_address_details['id_state']});
                        $('#service_id_country').change(function() {
                            ajaxGetStates();
                        });
                    }
                {/if}
            });
        </script>
    {/if}
{/block}


{block name="after"}
<script type="text/javascript">
    changeCMSActivationAuthorization();
    changeOverbookingOrderAction();
    changeStandardProductAddressType();
</script>
{/block}
