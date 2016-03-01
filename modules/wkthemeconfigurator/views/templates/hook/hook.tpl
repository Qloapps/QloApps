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
				<li class="htmlcontent-item-{$smarty.foreach.items.iteration|escape:'htmlall':'UTF-8'} col-xs-12 margin-btm-20">
			{else}
				<li class="htmlcontent-item-{$smarty.foreach.items.iteration|escape:'htmlall':'UTF-8'} col-xs-12 margin-btm-20">
			{/if}
					<div class="row">
						<div class="col-sm-12 col-xs-12 item-img parent-rm-div {if $hook == 'left' || $hook == 'right'}img-responsive{/if}" style='background-image:url("{$link->getMediaLink("`$module_dir`img/`$hItem.image`")}")'>
							<div class="row">
								<div class="outer-image-content-div col-sm-7 col-md-6 col-lg-5 col-xs-12 {if $hItem.content_side=='right'}pull-right{/if}">
									<p class="des_head">{$hItem.product_name}</p>
									<div class="des_content">{$hItem.html}</div>
									<div>
										<a target="blank" class="btn btn-default btn_view_details" href="{$link->getProductLink($hItem.id_product)}">{l s='View Details' mod='themeconfigurator'}</a>
									</div>
								</div>
							</div>
						</div>
					</div>
				</li>
		{/foreach}
	</ul>
</div>
{/if}
