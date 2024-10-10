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

{capture name=path}{l s='Shipping' mod='cashondelivery'}{/capture}

<h1 class="page-heading">{l s='Order summary' mod='cashondelivery'}</h1>

{assign var='current_step' value='payment'}
{include file="$tpl_dir./order-steps.tpl"}

<form action="{$link->getModuleLink('cashondelivery', 'validation', [], true)|escape:'html'}" method="post">
	<div class="box">
        <input type="hidden" name="confirm" value="1" />
        <h3 class="page-subheading">{l s='Cash on delivery (COD) payment' mod='cashondelivery'}</h3>
        <p>
            - {l s='You have chosen the Cash on Delivery method.' mod='cashondelivery'}
            <br/>
            - {l s='The total amount of your order is' mod='cashondelivery'}
            <span id="amount_{$currencies.0.id_currency}" class="price">{convertPrice price=$total}</span>
            {if $use_taxes == 1}
                {l s='(tax incl.)' mod='cashondelivery'}
            {/if}
        </p>
        <p>
            <b>{l s='Please confirm your order by clicking \'I confirm my order\'.' mod='cashondelivery'}.</b>
        </p>        
    </div>
    <p class="cart_navigation" id="cart_navigation">
        <a href="{$link->getPageLink('order', true)}?step=3" class="button-exclusive btn btn-default"><i class="icon-chevron-left"></i>{l s='Other payment methods' mod='cashondelivery'}</a>
        <button type="submit" class="button btn btn-default button-medium"><span>{l s='I confirm my order' mod='cashondelivery'}</span></button>
    </p>
</form>
