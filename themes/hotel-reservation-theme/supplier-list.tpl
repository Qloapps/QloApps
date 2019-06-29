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

{capture name=path}{l s='Suppliers:'}{/capture}

<h1 class="page-heading product-listing">{l s='Suppliers:'}
	{strip}
		<span class="heading-counter">
			{if $nbSuppliers == 0}{l s='There are no suppliers.'}
			{else}
				{if $nbSuppliers == 1}
					{l s='There is %d supplier.' sprintf=$nbSuppliers}
				{else}
					{l s='There are %d suppliers.' sprintf=$nbSuppliers}
				{/if}
			{/if}
		</span>
	{/strip}
</h1>

{if isset($errors) AND $errors}
	{include file="$tpl_dir./errors.tpl"}
{else}

{if $nbSuppliers > 0}
	<div class="content_sortPagiBar">
        <div class="sortPagiBar clearfix">
			{if isset($supplier) && isset($supplier.nb_products) && $supplier.nb_products > 0}
				<ul class="display hidden-xs">
					<li class="display-title">
						{l s='View:'}
					</li>
					<li id="grid">
						<a rel="nofollow" href="#" title="{l s='Grid'}">
							<i class="icon-th-large"></i>{l s='Grid'}
						</a>
					</li>
					<li id="list">
						<a rel="nofollow" href="#" title="{l s='List'}">
							<i class="icon-th-list"></i>{l s='List'}
						</a>
					</li>
				</ul>
			{/if}
	        {include file="./nbr-product-page.tpl"}
        </div>
        <div class="top-pagination-content clearfix bottom-line">
            {include file="$tpl_dir./pagination.tpl" no_follow=1}
        </div>
    </div> <!-- .content_sortPagiBar -->

    {assign var='nbItemsPerLine' value=3}
    {assign var='nbItemsPerLineTablet' value=2}
    {assign var='nbLi' value=$suppliers_list|@count}
    {math equation="nbLi/nbItemsPerLine" nbLi=$nbLi nbItemsPerLine=$nbItemsPerLine assign=nbLines}
    {math equation="nbLi/nbItemsPerLineTablet" nbLi=$nbLi nbItemsPerLineTablet=$nbItemsPerLineTablet assign=nbLinesTablet}

	<ul id="suppliers_list" class="list row">
		{foreach from=$suppliers_list item=supplier name=supplier}
	    	{math equation="(total%perLine)" total=$smarty.foreach.supplier.total perLine=$nbItemsPerLine assign=totModulo}
	        {math equation="(total%perLineT)" total=$smarty.foreach.supplier.total perLineT=$nbItemsPerLineTablet assign=totModuloTablet}
	        {if $totModulo == 0}{assign var='totModulo' value=$nbItemsPerLine}{/if}
	        {if $totModuloTablet == 0}{assign var='totModuloTablet' value=$nbItemsPerLineTablet}{/if}
			<li class="{if $smarty.foreach.supplier.iteration%$nbItemsPerLine == 0} last-in-line{elseif $smarty.foreach.supplier.iteration%$nbItemsPerLine == 1} first-in-line{/if} {if $smarty.foreach.supplier.iteration > ($smarty.foreach.supplier.total - $totModulo)}last-line{/if} {if $smarty.foreach.supplier.iteration%$nbItemsPerLineTablet == 0}last-item-of-tablet-line{elseif $smarty.foreach.supplier.iteration%$nbItemsPerLineTablet == 1}first-item-of-tablet-line{/if} {if $smarty.foreach.supplier.iteration > ($smarty.foreach.supplier.total - $totModuloTablet)}last-tablet-line{/if}col-xs-12">
				<div class="mansup-container">
					<div class="row">
		            	<div class="left-side col-xs-12 col-sm-3">
							<!-- logo -->
							<div class="logo">
								{if isset($supplier.nb_products) && $supplier.nb_products > 0}
									<a href="{$link->getsupplierLink($supplier.id_supplier, $supplier.link_rewrite)|escape:'html':'UTF-8'}" title="{$supplier.name|escape:'html':'UTF-8'}">
								{/if}
								<img src="{$img_sup_dir}{$supplier.image|escape:'html':'UTF-8'}-medium_default.jpg" alt="" width="{$mediumSize.width}" height="{$mediumSize.height}" />
								{if isset($supplier.nb_products) && $supplier.nb_products > 0}
									</a>
								{/if}
							</div> <!-- .logo -->
						</div> <!-- .left-side -->

						<div class="middle-side col-xs-12 col-sm-5">
							<h3>
								{if isset($supplier.nb_products) && $supplier.nb_products > 0}
									<a class="product-name" href="{$link->getsupplierLink($supplier.id_supplier, $supplier.link_rewrite)|escape:'html':'UTF-8'}">
								{/if}
								{$supplier.name|truncate:60:'...'|escape:'html':'UTF-8'}
								{if isset($supplier.nb_products) && $supplier.nb_products > 0}
									</a>
								{/if}
							</h3>
							<div class="description">
								{$supplier.description|truncate:180:'...'}
							</div>
			            </div><!-- .middle-side -->

						<div class="right-side col-xs-12 col-sm-4">
			            	<div class="right-side-content">
			                    <p class="product-counter">
			                        {if isset($supplier.nb_products) && $supplier.nb_products > 0}
			                            <a href="{$link->getsupplierLink($supplier.id_supplier, $supplier.link_rewrite)|escape:'html':'UTF-8'}">
			                        {/if}
			                        {if isset($supplier.nb_products) && $supplier.nb_products == 1}{l s='%d product' sprintf=$supplier.nb_products|intval}{else}{l s='%d products' sprintf=$supplier.nb_products|intval}{/if}
			                    	{if isset($supplier.nb_products) && $supplier.nb_products > 0}
			                        	</a>
			                    	{/if}
			                    </p>
			                    {if isset($supplier.nb_products) && $supplier.nb_products > 0}
			                        <a class="btn btn-default button exclusive-medium" href="{$link->getsupplierLink($supplier.id_supplier, $supplier.link_rewrite)|escape:'html':'UTF-8'}"><span>{l s='View products'} <i class="icon-chevron-right right"></i></span></a>
			                    {/if}
			                </div>
						</div><!-- .right-side -->
					</div>
		        </div>
			</li>
		{/foreach}
	</ul>
	<div class="content_sortPagiBar">
        <div class="bottom-pagination-content clearfix">
            {include file="$tpl_dir./pagination.tpl" no_follow=1 paginationId='bottom'}
        </div>
    </div>
{/if}
{/if}
