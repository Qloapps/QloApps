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

{block name='cms'}
	{if isset($cms) && !isset($cms_category)}
		{if !$cms->active}
			<div class="container my-4">
				<div id="admin-action-cms" class="alert alert-warning">
					<p class="mb-2">
						<strong><i class="icon-warning"></i> {l s='This CMS page is not visible to your customers.'}</strong>
					</p>
					<input type="hidden" id="admin-action-cms-id" value="{$cms->id}" />
					<div class="d-flex gap-2">
						<input type="submit" value="{l s='Publish'}" name="publish_button" class="btn btn-primary btn-sm"/>
						<input type="submit" value="{l s='Back'}" name="lnk_view" class="btn btn-secondary btn-sm"/>
					</div>
					<p id="admin-action-result" class="mt-2 mb-0"></p>
				</div>
			</div>
		{/if}
		
		<div class="container-md my-4 my-lg-5">
			<div class="row justify-content-center">
				<div class="col-12 col-lg-10">
					<article class="cms-page-content card qlo-account-card p-4 p-lg-5">
						<div class="rte{if $content_only} content_only{/if}">
							{$cms->content}
						</div>
					</article>
				</div>
			</div>
		</div>
	{elseif isset($cms_category)}
		<div class="container-md my-4 my-lg-5">
			{* Category Header *}
			<div class="row mb-4 mb-lg-5">
				<div class="col-12">
					<nav aria-label="breadcrumb">
						<ol class="breadcrumb">
							<li class="breadcrumb-item"><a href="{$link->getPageLink('cms', true)|escape:'html':'UTF-8'}">{l s='Information'}</a></li>
							<li class="breadcrumb-item active" aria-current="page">{$cms_category->name|escape:'html':'UTF-8'}</li>
						</ol>
					</nav>
					<h1 class="heading h2 mb-3">{$cms_category->name|escape:'html':'UTF-8'}</h1>
					{if $cms_category->description}
						<p class="text-muted lead">{$cms_category->description|escape:'html':'UTF-8'}</p>
					{/if}
				</div>
			</div>
			
			<div class="row g-4">
				{* Sub Categories *}
				{if isset($sub_category) && !empty($sub_category)}
					<div class="col-12">
						<div class="card qlo-account-card p-4">
							<h2 class="heading h4 mb-4">{l s='Sub Categories'}</h2>
							<div class="row g-3">
								{foreach from=$sub_category item=subcategory}
									<div class="col-12 col-sm-6 col-md-4 col-lg-3">
										<a href="{$link->getCMSCategoryLink($subcategory.id_cms_category, $subcategory.link_rewrite)|escape:'html':'UTF-8'}" class="btn btn-outline-primary w-100">
											<i class="icon-folder me-2"></i>{$subcategory.name|escape:'html':'UTF-8'}
										</a>
									</div>
								{/foreach}
							</div>
						</div>
					</div>
				{/if}
				
				{* CMS Pages *}
				{if isset($cms_pages) && !empty($cms_pages)}
					<div class="col-12">
						<div class="card qlo-account-card p-4">
							<h2 class="heading h4 mb-4">{l s='Pages'}</h2>
							<div class="row g-3">
								{foreach from=$cms_pages item=cmspages}
									<div class="col-12 col-sm-6 col-md-6 col-lg-4">
										<a href="{$link->getCMSLink($cmspages.id_cms, $cmspages.link_rewrite)|escape:'html':'UTF-8'}" class="cms-page-link card h-100 border-0 shadow-sm text-decoration-none">
											<div class="card-body">
												<h3 class="h5 mb-2">{$cmspages.meta_title|escape:'html':'UTF-8'}</h3>
												{if $cmspages.meta_description}
													<p class="text-muted small mb-0">{$cmspages.meta_description|escape:'html':'UTF-8'}</p>
												{/if}
											</div>
											<div class="card-footer bg-transparent border-0">
												<span class="text-primary small">{l s='Read more'} <i class="icon-long-arrow-right"></i></span>
											</div>
										</a>
									</div>
								{/foreach}
							</div>
						</div>
					</div>
				{/if}
			</div>
		</div>
	{else}
		<div class="container-md my-4 my-lg-5">
			<div class="row justify-content-center">
				<div class="col-12 col-md-8 col-lg-6">
					<div class="alert alert-danger text-center" role="alert">
						<i class="icon-exclamation-triangle icon-2x mb-3 d-block"></i>
						<h4 class="alert-heading">{l s='Page Not Found'}</h4>
						<p class="mb-0">{l s='This page does not exist.'}</p>
						<hr>
						<a href="{$link->getPageLink('index', true)|escape:'html':'UTF-8'}" class="btn btn-primary">{l s='Go to Home'}</a>
					</div>
				</div>
			</div>
		</div>
	{/if}
	
	{block name='cms_js_vars'}
		{strip}
			{if isset($smarty.get.ad) && $smarty.get.ad}
				{addJsDefL name=ad}{$base_dir|cat:$smarty.get.ad|escape:'html':'UTF-8'}{/addJsDefL}
			{/if}
			{if isset($smarty.get.adtoken) && $smarty.get.adtoken}
				{addJsDefL name=adtoken}{$smarty.get.adtoken|escape:'html':'UTF-8'}{/addJsDefL}
			{/if}
		{/strip}
	{/block}
{/block}
