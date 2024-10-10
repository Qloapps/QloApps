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
{capture name=path}<a href="{$link->getPageLink('my-account', true)|escape:'html'}" title="{l s='Manage my account' mod='mailalerts'}" rel="nofollow">{l s='My account' mod='mailalerts'}</a><span class="navigation-pipe">{$navigationPipe}</span><span class="navigation_page">{l s='My alerts' mod='mailalerts'}</span>{/capture}
<div id="mailalerts_block_account" class="block">
	<h1 class="page-heading">{l s='My alerts' mod='mailalerts'}</h1>
	{if $mailAlerts}
		<ul class="products-block">
			{foreach from=$mailAlerts item=mailAlert name=myLoop}
			<li class="clearfix{if $smarty.foreach.myLoop.last} last_item{/if}">
				<a class="products-block-image" href="{$link->getProductLink($mailAlert.id_product, null, null, null, null, $mailAlert.id_shop)}" title="{$mailAlert.name|escape:'html':'UTF-8'}"><img src="{$link->getImageLink($mailAlert.link_rewrite, $mailAlert.cover, 'small_default')|escape:'html'}" alt=""/></a>
                <div class="product-content">
                	<span class="remove">
                        <i class="icon-remove" rel="ajax_id_mailalert_{$mailAlert.id_product}_{$mailAlert.id_product_attribute}"></i>
                    </span>
                    <h5><a class="product-name" href="{$link->getProductLink($mailAlert.id_product, null, null, null, null, $mailAlert.id_shop)|escape:'html'}" title="{$mailAlert.name|escape:'html':'UTF-8'}">{$mailAlert.name|escape:'html':'UTF-8'}</a></h5>
                    <div class="product-description"><small>{$mailAlert.attributes_small|escape:'html':'UTF-8'}</small></div>
                </div>
			</li>
			{/foreach}
		</ul>
	{/if}
    <p id="mailalerts_block_account_warning" class="{if $mailAlerts}hidden{/if} alert alert-warning">{l s='No mail alerts yet.' mod='mailalerts'}</p>
	<ul class="footer_links clearfix">
		<li><a class="btn btn-default button button-small" href="{$link->getPageLink('my-account', true)|escape:'html'}" title="{l s='Back to Your Account' mod='mailalerts'}"><span><i class="icon-chevron-left"></i>{l s='Back to Your Account' mod='mailalerts'}</span></a></li>
	</ul>
</div>
{addJsDef mailalerts_url_remove=$link->getModuleLink('mailalerts', 'actions', ['process' => 'remove'])|escape:'quotes':'UTF-8'}