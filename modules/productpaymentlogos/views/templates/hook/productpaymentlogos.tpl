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
<!-- Productpaymentlogos module -->
<div id="product_payment_logos">
	<div class="box-security">
    <h5 class="product-heading-h5">{$banner_title|escape:'htmlall':'UTF-8'}</h5>
  	{if $banner_link != ''}<a href="{$banner_link|escape:'htmlall':'UTF-8'}" title="{$banner_title|escape:'htmlall':'UTF-8'}">{/if}
		<img src="{$module_dir}{$banner_img|escape:'htmlall':'UTF-8'}" alt="{$banner_title|escape:'htmlall':'UTF-8'}" class="img-responsive" />
	{if $banner_link != ''}</a>{/if}
    </div>
</div>
<!-- /Productpaymentlogos module -->
