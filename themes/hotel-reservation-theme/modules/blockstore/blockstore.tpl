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

<!-- Block stores module -->
<div id="stores_block_left" class="block">
	<p class="title_block">
		<a href="{$link->getPageLink('stores')|escape:'html':'UTF-8'}" title="{l s='Our stores' mod='blockstore'}">
			{l s='Our stores' mod='blockstore'}
		</a>
	</p>
	<div class="block_content blockstore">
		<p class="store_image">
			<a href="{$link->getPageLink('stores')|escape:'html':'UTF-8'}" title="{l s='Our stores' mod='blockstore'}">
				<img class="img-responsive" src="{$link->getMediaLink("`$module_dir``$store_img|escape:'htmlall':'UTF-8'`")}" alt="{l s='Our stores' mod='blockstore'}" />
			</a>
		</p>
		{if !empty($store_text)}
        <p class="store-description">
        	{$store_text}
        </p>
        {/if}
		<div>
			<a 
			class="btn btn-default button button-small" 
			href="{$link->getPageLink('stores')|escape:'html':'UTF-8'}" 
			title="{l s='Our stores' mod='blockstore'}">
				<span>{l s='Discover our stores' mod='blockstore'}<i class="icon-chevron-right right"></i></span>
			</a>
		</div>
	</div>
</div>
<!-- /Block stores module -->
