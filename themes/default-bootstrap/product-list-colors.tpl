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
{if isset($colors_list)}
<ul class="color_to_pick_list clearfix">
	{foreach from=$colors_list item='color'}
		{if isset($col_img_dir)}
			{assign var='img_color_exists' value=file_exists($col_img_dir|cat:$color.id_attribute|cat:'.jpg')}
			<li>
				<a href="{$link->getProductLink($color.id_product, null, null, null, null, null, $color.id_product_attribute, Configuration::get('PS_REWRITING_SETTINGS'), false, true)|escape:'html':'UTF-8'}" id="color_{$color.id_product_attribute|intval}" class="color_pick"{if !$img_color_exists && isset($color.color) && $color.color} style="background:{$color.color};"{/if}>
					{if $img_color_exists}
						<img src="{$img_col_dir}{$color.id_attribute|intval}.jpg" alt="{$color.name|escape:'html':'UTF-8'}" title="{$color.name|escape:'html':'UTF-8'}" width="20" height="20" />
					{/if}
				</a>
			</li>
		{/if}
	{/foreach}
</ul>
{/if}
