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

<!-- Block myaccount module -->
<div class="block myaccount-column">
	<p class="title_block">
		<a href="{$link->getPageLink('my-account', true)|escape:'html':'UTF-8'}" title="{l s='My account' mod='blockmyaccount'}">
			{l s='My account' mod='blockmyaccount'}
		</a>
	</p>
	<div class="block_content list-block">
		<ul>
			<li>
				<a href="{$link->getPageLink('history', true)|escape:'html':'UTF-8'}" title="{l s='My orders' mod='blockmyaccount'}">
					{l s='My orders' mod='blockmyaccount'}
				</a>
			</li>
			{if $returnAllowed}
				<li>
					<a href="{$link->getPageLink('order-follow', true)|escape:'html':'UTF-8'}" title="{l s='My merchandise returns' mod='blockmyaccount'}">
						{l s='My merchandise returns' mod='blockmyaccount'}
					</a>
				</li>
			{/if}
			<li>
				<a href="{$link->getPageLink('order-slip', true)|escape:'html':'UTF-8'}" title="{l s='My credit slips' mod='blockmyaccount'}">	{l s='My credit slips' mod='blockmyaccount'}
				</a>
			</li>
			<li>
				<a href="{$link->getPageLink('addresses', true)|escape:'html':'UTF-8'}" title="{l s='My addresses' mod='blockmyaccount'}">
					{l s='My addresses' mod='blockmyaccount'}
				</a>
			</li>
			<li>
				<a href="{$link->getPageLink('identity', true)|escape:'html':'UTF-8'}" title="{l s='My personal info' mod='blockmyaccount'}">
					{l s='My personal info' mod='blockmyaccount'}
				</a>
			</li>
			{if $voucherAllowed}
				<li>
					<a href="{$link->getPageLink('discount', true)|escape:'html':'UTF-8'}" title="{l s='My vouchers' mod='blockmyaccount'}">
						{l s='My vouchers' mod='blockmyaccount'}
					</a>
				</li>
			{/if}
			{$HOOK_BLOCK_MY_ACCOUNT}
		</ul>
		<div class="logout">
			<a 
			class="btn btn-default button button-small" 
			href="{$link->getPageLink('index', true, NULL, "mylogout")|escape:'html':'UTF-8'}" 
			title="{l s='Sign out' mod='blockmyaccount'}">
				<span>{l s='Sign out' mod='blockmyaccount'}<i class="icon-chevron-right right"></i></span>
			</a>
		</div>
	</div>
</div>
<!-- /Block myaccount module -->
