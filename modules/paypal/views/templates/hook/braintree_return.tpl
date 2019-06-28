{*
* 2007-2018 PrestaShop
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
*  @author    PrestaShop SA <contact@prestashop.com>
*  @copyright 2007-2018 PrestaShop SA
*  @license	http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*}
<div id="braintree_return">
    <p>{l s='Your order on' mod='paypal'} <span class="paypal-bold">{$shop_name|escape:'htmlall':'UTF-8'}</span> {l s='is complete.' mod='paypal'}
        <br /><br />
        {l s='You have chosen pay by cart.' mod='paypal'}
        <br /><br /><span class="paypal-bold">{l s='Your order will be sent very soon.' mod='paypal'}</span>
        <br /><br />{l s='For any questions or for further information, please contact our' mod='paypal'}
        <a href="{$link->getPageLink('contact', true)|escape:'htmlall':'UTF-8'}" data-ajax="false" target="_blank">{l s='customer support' mod='paypal'}</a>.
    </p>
</div>
<br/>