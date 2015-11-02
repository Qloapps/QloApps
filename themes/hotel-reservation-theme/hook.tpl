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
{if isset($htmlitems) && $htmlitems}
<div id="htmlcontent_{$hook|escape:'htmlall':'UTF-8'}"{if $hook == 'footer'} class="footer-block col-xs-12 col-sm-4"{/if}>
	<ul class="htmlcontent-home clearfix row">
		{foreach name=items from=$htmlitems item=hItem}
			{if $hook == 'left' || $hook == 'right'}
				<li class="htmlcontent-item-{$smarty.foreach.items.iteration|escape:'htmlall':'UTF-8'} col-xs-12">
			{else}
				<li class="htmlcontent-item-{$smarty.foreach.items.iteration|escape:'htmlall':'UTF-8'} col-xs-12">
			{/if}
					<!-- {if $hItem.url}
						<a href="{$hItem.url|escape:'htmlall':'UTF-8'}" class="item-link"{if $hItem.target == 1} onclick="return !window.open(this.href);"{/if} title="{$hItem.title|escape:'htmlall':'UTF-8'}">
					{/if} -->
						{if $hItem.image}
						<div class="outer-image-content-div col-sm-5" {if $hItem.description_side=='right'}style="right:0;"{/if}>
							<div class="des_head">{$hItem.url}</div>
							<div class="des_content">{$hItem.html}</div>
							<div><a target="blank" class="btn btn-default btn_view_details" href="{$link->getProductLink($hItem.url)}">{l s='View Details' mod='themeconfigurator'}</a></div>
						</div>
						<div class="inner-image-div">
							<img src="{$link->getMediaLink("`$module_dir`img/`$hItem.image`")}" class="item-img {if $hook == 'left' || $hook == 'right'}img-responsive{/if}" title="{$hItem.title|escape:'htmlall':'UTF-8'}" alt="{$hItem.title|escape:'htmlall':'UTF-8'}" width="{if $hItem.image_w}{$hItem.image_w|intval}{else}100%{/if}" height="{if $hItem.image_h}{$hItem.image_h|intval}{else}100%{/if}"/>
						</div>
						{/if}
						{if $hItem.title && $hItem.title_use == 1}
							<h3 class="item-title">{$hItem.title|escape:'htmlall':'UTF-8'}</h3>
						{/if}
<!-- 						{if $hItem.html}
							<div class="item-html">
								{$hItem.html} <i class="icon-double-angle-right"></i>
							</div>
						{/if} -->
					<!-- {if $hItem.url}
						</a>
					{/if} -->
				</li>
		{/foreach}
	</ul>
</div>
{/if}
