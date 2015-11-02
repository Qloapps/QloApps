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
<div class="panel">
    <h3>
        <i class="icon-picture"></i> {$add_new_theme_label} {if $context_mode == Context::MODE_HOST}{l s='(Advanced)'}{/if}
    </h3>
    <p>{l s='Duplicate an existing theme and edit it; or create a new theme from scratch! Recommended for advanced users only.'}</p>
    <a class="btn btn-default" href="{$add_new_theme_href}"><i class="icon-plus"></i> {$add_new_theme_label}</a>
</div>
