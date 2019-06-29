{*
* 2007-2016 PrestaShop
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
*  @copyright  2007-2016 PrestaShop SA
*  @license    http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*}
<a href="{if $banner_link}{$banner_link|escape:'htmlall':'UTF-8'}{else}{if isset($force_ssl) && $force_ssl}{$base_dir_ssl}{else}{$base_dir}{/if}{/if}" title="{$banner_desc|escape:'htmlall':'UTF-8'}">
{if isset($banner_img)}
	<img class="img-responsive" src="{$banner_img|escape:'htmlall':'UTF-8'}" alt="{$banner_desc|escape:'htmlall':'UTF-8'}" title="{$banner_desc|escape:'htmlall':'UTF-8'}" width="1170" height="65" />
{else}
	{$banner_desc|escape:'htmlall':'UTF-8'}
{/if}
</a>
