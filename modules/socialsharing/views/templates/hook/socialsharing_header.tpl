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
<meta property="og:type" content="product" /> 
<meta property="og:url" content="{$request}" /> 
<meta property="og:title" content="{$meta_title|escape:'html':'UTF-8'}" /> 
<meta property="og:site_name" content="{$shop_name}" />
<meta property="og:description" content="{$meta_description|escape:'html':'UTF-8'}" />
{if isset($link_rewrite) && isset($cover) && isset($cover.id_image)}
<meta property="og:image" content="{$link->getImageLink($link_rewrite, $cover.id_image, large_default)}" />
{/if}
<meta property="product:pretax_price:amount" content="{$pretax_price}" /> 
<meta property="product:pretax_price:currency" content="{$currency->iso_code}" /> 
<meta property="product:price:amount" content="{$price}" /> 
<meta property="product:price:currency" content="{$currency->iso_code}" /> 
{if isset($weight) && ($weight != 0)}
<meta property="product:weight:value" content="{$weight}" /> 
<meta property="product:weight:units" content="{$weight_unit}" /> 
{/if}
