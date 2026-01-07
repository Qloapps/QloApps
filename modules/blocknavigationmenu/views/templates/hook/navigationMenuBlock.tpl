{*
* NOTICE OF LICENSE
*
* This source file is subject to the Open Software License version 3.0
* that is bundled with this package in the file LICENSE.md
* It is also available through the world-wide-web at this URL:
* https://opensource.org/license/osl-3-0-php
* If you did not receive a copy of the license and are unable to
* obtain it through the world-wide-web, please send an email
* to support@qloapps.com so we can send you a copy immediately.
*
* DISCLAIMER
*
* Do not edit or add to this file if you wish to upgrade this module to a newer
* versions in the future. If you wish to customize this module for your needs
* please refer to https://store.webkul.com/customisation-guidelines for more information.
*
* @author Webkul IN
* @copyright Since 2010 Webkul
* @license https://opensource.org/license/osl-3-0-php Open Software License version 3.0
*}

{block name='navigation_menu'}
	<div class="header-top-item p-0">
		<button class="btn nav_toggle">
			<i class="icon-solid icon-bars"></i>
		</button>
	</div>

	<div id="menu_cont" class="nav_cont_right">
		<div class="d-md-none row justify-content-end col-12 p-0 mt-2">
			<span class="close_navbar"><i class="icon-close"></i></span>
		</div>
		<ul class="nav main-nav">
			{if isset($navigation_links) && $navigation_links}
				{foreach $navigation_links as $navigationLink}
					<li>
						<a class="navigation-link" href="{$navigationLink['link']}">
							{$navigationLink['name']}
						</a>
					</li>
				{/foreach}
			{/if}

			{block name='displayDefaultNavigationHook'}
				{hook h="displayDefaultNavigationHook"}
			{/block}
		</ul>

		{block name='displayExternalNavigationHook'}
			{hook h="displayExternalNavigationHook"}
		{/block}
	</div>
{/block}
