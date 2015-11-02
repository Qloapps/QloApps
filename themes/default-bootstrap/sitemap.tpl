{*
* 2007-2015 PrestaShop
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
*  @copyright  2007-2015 PrestaShop SA
*  @license    http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*}

{capture name=path}{l s='Sitemap'}{/capture}

<h1 class="page-heading">
    {l s='Sitemap'}
</h1>
<div id="sitemap_content" class="row">
	<div class="col-xs-12 col-sm-6">
		<div class="sitemap_block box">
    		<h3 class="page-subheading">{l s='Our offers'}</h3>
    		<ul>
    			<li>
                    <a 
                    href="{$link->getPageLink('new-products')|escape:'html':'UTF-8'}" 
                    title="{l s='View a new product'}">
                        {l s='New products'}
                    </a>
                </li>
    			{if !$PS_CATALOG_MODE}
    			{if $PS_DISPLAY_BEST_SELLERS}
        			<li>
                        <a 
                        href="{$link->getPageLink('best-sales')|escape:'html':'UTF-8'}" 
                        title="{l s='View top-selling products'}">
                            {l s='Best sellers'}
                        </a>
                    </li>
                {/if}
        			<li>
                        <a 
                        href="{$link->getPageLink('prices-drop')|escape:'html':'UTF-8'}" 
                        title="{l s='View products with a price drop'}">
                            {l s='Price drop'}
                        </a>
                    </li>
    			{/if}
    			{if $display_manufacturer_link OR $PS_DISPLAY_SUPPLIERS}
                    <li>
                        <a 
                        href="{$link->getPageLink('manufacturer')|escape:'html':'UTF-8'}" 
                        title="{l s='View a list of manufacturers'}">
                            {l s='Manufacturers'}
                        </a>
                    </li>
                {/if}
    			{if $display_supplier_link OR $PS_DISPLAY_SUPPLIERS}
                    <li>
                        <a 
                        href="{$link->getPageLink('supplier')|escape:'html':'UTF-8'}" 
                        title="{l s='View a list of suppliers'}">
                            {l s='Suppliers'}
                        </a>
                    </li>
                {/if}
    		</ul>
	   </div>
    </div>
    <div class="col-xs-12 col-sm-6">
		<div class="sitemap_block box">
    		<h3 class="page-subheading">
                {l s='Your Account'}
            </h3>
    		<ul>
        		{if $is_logged}
        			<li>
                        <a 
                        href="{$link->getPageLink('my-account', true)|escape:'html':'UTF-8'}" 
                        rel="nofollow" 
                        title="{l s='Manage your customer account'}">
                            {l s='Your Account'}
                        </a>
                    </li>
        			<li>
                        <a 
                        href="{$link->getPageLink('identity', true)|escape:'html':'UTF-8'}" 
                        rel="nofollow" 
                        title="{l s='Manage your personal information'}">
                            {l s='Personal information'}
                        </a>
                    </li>
        			<li>
                        <a 
                        href="{$link->getPageLink('addresses', true)|escape:'html':'UTF-8'}" 
                        rel="nofollow" 
                        title="{l s='View a list of my addresses'}">
                            {l s='Addresses'}
                        </a>
                    </li>
        			{if $voucherAllowed}
                        <li>
                            <a 
                            href="{$link->getPageLink('discount', true)|escape:'html':'UTF-8'}" 
                            rel="nofollow" 
                            title="{l s='View a list of my discounts'}">
                                {l s='Discounts'}
                            </a>
                        </li>
                        {/if}
        			<li>
                        <a 
                        href="{$link->getPageLink('history', true)|escape:'html':'UTF-8'}" 
                        rel="nofollow"
                        title="{l s='View a list of my orders'}" >
                            {l s='Order history'}
                        </a>
                    </li>
        		{else}
        			<li>
                        <a 
                        href="{$link->getPageLink('authentication', true)|escape:'html':'UTF-8'}" 
                        rel="nofollow"
                        title="{l s='Authentication'}" >
                            {l s='Authentication'}
                        </a>
                    </li>
        			<li>
                        <a 
                        href="{$link->getPageLink('authentication', true)|escape:'html':'UTF-8'}"
                        rel="nofollow" 
                        title="{l s='Create new account'}" >
                            {l s='Create new account'}
                        </a>
                    </li>
        		{/if}
        		{if $is_logged}
        			<li>
                        <a 
                        href="{$link->getPageLink('index')}?mylogout" 
                        rel="nofollow"
                        title="{l s='Sign out'}" >
                            {l s='Sign out'}
                        </a>
                    </li>
        		{/if}
    		</ul>
    	</div>
    </div>
</div>
<div id="listpage_content" class="row">
	<div class="col-xs-12 col-sm-6">
		<div class="categTree box">
            <h3 class="page-subheading">{l s='Categories'}</h3>
            <div class="tree_top">
                <a href="{$base_dir_ssl}" title="{$categoriesTree.name|escape:'html':'UTF-8'}"></a>
            </div>
            <ul class="tree">
            {if isset($categoriesTree.children)}
                {foreach $categoriesTree.children as $child}
                    {if $child@last}
                        {include file="$tpl_dir./category-tree-branch.tpl" node=$child last='true'}
                    {else}
                        {include file="$tpl_dir./category-tree-branch.tpl" node=$child}
                    {/if}
                {/foreach}
            {/if}
            </ul>
        </div>
    </div>
    <div class="col-xs-12 col-sm-6">
		<div class="sitemap_block box">
            <h3 class="page-subheading">{l s='Pages'}</h3>
            <ul>
            	<li>
                    <a href="{$categoriescmsTree.link|escape:'html':'UTF-8'}" title="{$categoriescmsTree.name|escape:'html':'UTF-8'}">
                        {$categoriescmsTree.name|escape:'html':'UTF-8'}
                    </a>
                </li>
                {if isset($categoriescmsTree.children)}
                    {foreach $categoriescmsTree.children as $child}
                        {if (isset($child.children) && $child.children|@count > 0) || $child.cms|@count > 0}
                            {include file="$tpl_dir./category-cms-tree-branch.tpl" node=$child}
                        {/if}
                    {/foreach}
                {/if}
                {foreach from=$categoriescmsTree.cms item=cms name=cmsTree}
                    <li>
                        <a href="{$cms.link|escape:'html':'UTF-8'}" title="{$cms.meta_title|escape:'html':'UTF-8'}">
                            {$cms.meta_title|escape:'html':'UTF-8'}
                        </a>
                    </li>
                {/foreach}
                <li>
                    <a href="{$link->getPageLink('contact', true)|escape:'html':'UTF-8'}" title="{l s='Contact'}">
                        {l s='Contact'}
                    </a>
                </li>
                {if $display_store}
                    <li class="last">
                        <a href="{$link->getPageLink('stores')|escape:'html':'UTF-8'}" title="{l s='List of our stores'}">
                            {l s='Our stores'}
                        </a>
                    </li>
                {/if}
            </ul>
        </div>
    </div>
</div>
