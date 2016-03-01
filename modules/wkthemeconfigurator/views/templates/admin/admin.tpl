{*
* 2007-2014 PrestaShop
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
*  @copyright  2007-2014 PrestaShop SA
*  @license    http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*}

<div id="htmlcontent" class="panel">
    <div class="panel-heading">{$htmlcontent.info.name|escape:'htmlall':'UTF-8'} (v.{$htmlcontent.info.version|escape:'htmlall':'UTF-8'})</div>
    {if isset($error) && $error}
        {include file="{$htmlcontent.admin_tpl_path|escape:'htmlall':'UTF-8'}messages.tpl" id="main" text=$error class='error'}
    {/if}
    {if isset($confirmation) && $confirmation}
        {include file="{$htmlcontent.admin_tpl_path|escape:'htmlall':'UTF-8'}messages.tpl" id="main" text=$confirmation class='conf'}
    {/if}
    <!-- New -->
    {include file="{$htmlcontent.admin_tpl_path|escape:'htmlall':'UTF-8'}new.tpl"}
    <!-- Slides -->
    {include file="{$htmlcontent.admin_tpl_path|escape:'htmlall':'UTF-8'}items.tpl"}
</div>
