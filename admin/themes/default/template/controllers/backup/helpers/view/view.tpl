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

{extends file="helpers/view/view.tpl"}

{block name="override_tpl"}
	{if !isset($errors) || $errors|count == 0}
	<div class="panel">
		<h3><i class="icon-download"></i> {l s='Download'}</h3>
		<div class="alert alert-success">{l s='Beginning the download ...'}</div>
		<p>{l s='Backup files should automatically start downloading.'}</p>
		<p>{l s='If not,'} <b><a href="{$url_backup}" class="btn btn-default"><i class="icon-download"></i> {l s='please click here!'}</a></b></p>
		<iframe  style="width:0px; height:0px; overflow:hidden; border:none;" src="{$url_backup}"></iframe>
	</div>
	{/if}
{/block}
