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
{if !isset($addresses_style)}
    {$addresses_style.company = 'address_company'}
    {$addresses_style.firstname = 'address_name'}
    {$addresses_style.lastname = 'address_name'}
    {$addresses_style.address1 = 'address_address1'}
    {$addresses_style.address2 = 'address_address2'}
    {$addresses_style.city = 'address_city'}
    {$addresses_style.country = 'address_country'}
    {$addresses_style.phone = 'address_phone'}
    {$addresses_style.phone_mobile = 'address_phone_mobile'}
    {$addresses_style.alias = 'address_title'}
{/if}
{assign var='have_non_virtual_products' value=false}
{foreach $products as $product}
    {if $product.is_virtual == 0}
        {assign var='have_non_virtual_products' value=true}
        {break}
    {/if}
{/foreach}

{addJsDefL name=txtProduct}{l s='Product' js=1}{/addJsDefL}
{addJsDefL name=txtProducts}{l s='Products' js=1}{/addJsDefL}
{capture name=path}{l s='Your shopping cart'}{/capture}

{if $productNumber == 0}
<p class="alert alert-warning">{l s='Your shopping cart is empty.'}</p>
{elseif $PS_CATALOG_MODE}
<p class="alert alert-warning">{l s='This store has not accepted your new order.'}</p>
{else}
    <p id="emptyCartWarning" class="alert alert-warning unvisible">{l s='Your shopping cart is empty.'}</p>
    <h2>{l s='Payment Options'}</h2>
    <!-- HOOK_ADVANCED_PAYMENT -->
    <div id="HOOK_ADVANCED_PAYMENT">
        <div class="row">
        <!-- Should get a collection of "PaymentOption" object -->
        {assign var='adv_payment_empty' value=true}
        {foreach from=$HOOK_ADVANCED_PAYMENT item=pay_option key=key}
            {if $pay_option}
                {assign var='adv_payment_empty' value=false}
            {/if}
        {/foreach}
        {if $HOOK_ADVANCED_PAYMENT && !$adv_payment_empty}
            {foreach $HOOK_ADVANCED_PAYMENT as $advanced_payment_opt_list}
                {foreach $advanced_payment_opt_list as $paymentOption}
                    <div class="col-xs-12 col-md-6">
                        <p class="payment_module pointer-box">
                            <a class="payment_module_adv">
                                <img class="payment_option_logo" src="{$paymentOption->getLogo()}"/>
                                <span class="payment_option_cta">
                                    {$paymentOption->getCallToActionText()}
                                </span>
                                <span class="pull-right payment_option_selected">
                                    <i class="icon-check"></i>
                                </span>
                            </a>

                        </p>
                        <div class="payment_option_form">
                            {if $paymentOption->getForm()}
                                {$paymentOption->getForm()}
                            {else}
                                <form method="{if $paymentOption->getMethod()}{$paymentOption->getMethod()}{else}POST{/if}" action="{$paymentOption->getAction()}">
                                    {if $paymentOption->getInputs()}
                                        {foreach from=$paymentOption->getInputs() item=value key=name}
                                            <input type="hidden" name="{$name}" value="{$value}">
                                        {/foreach}
                                    {/if}
                                </form>
                            {/if}
                        </div>
                    </div>
                {/foreach}
            {/foreach}
        </div>
        {else}
        <div class="col-xs-12 col-md-12">
            <p class="alert alert-warning ">{l s='Unable to find any available payment option for your cart. Please contact us if the problem persists'}</p>
        </div>
        {/if}
    </div>
    <!-- end HOOK_ADVANCED_PAYMENT -->

    {if $opc}
        <!-- Carrier -->
        {include file="$tpl_dir./order-carrier-advanced.tpl"}
        <!-- END Carrier -->
    {/if}

    {if $is_logged AND !$is_guest}
        {include file="$tpl_dir./order-address-advanced.tpl"}
    {elseif $opc}
        <!-- Create account / Guest account / Login block -->
        {include file="$tpl_dir./order-opc-new-account-advanced.tpl"}
        <!-- END Create account / Guest account / Login block -->
    {/if}

    <!-- TNC -->
    {if $conditions AND $cms_id}
        {if $override_tos_display }
            {$override_tos_display}
        {else}
            <div class="row">
                <div class="col-xs-12 col-md-12">
                    <h2>{l s='Terms and Conditions'}</h2>
                    <div class="box">
                        <p class="checkbox">
                            <input type="checkbox" name="cgv" id="cgv" value="1" {if $checkedTOS}checked="checked"{/if} />
                            <label for="cgv">{l s='I agree to the terms of service and will adhere to them unconditionally.'}</label>
                            <a href="{$link_conditions|escape:'html':'UTF-8'}" class="iframe" rel="nofollow">{l s='(Read the Terms of Service)'}</a>
                        </p>
                    </div>
                </div>
            </div>
        {/if}
    {/if}
    <!-- end TNC -->

    {include file="$tpl_dir./shopping-cart-advanced.tpl"}
{/if}
