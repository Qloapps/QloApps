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
	<div class="header-top-item">
		<button type="button" class="nav_toggle">
			<span class="icon-bar"></span>
			<span class="icon-bar"></span>
			<span class="icon-bar"></span>
		</button>
	</div>

	<div id="menu_cont" class="menu_cont_right">
		<div class="row margin-lr-0">
			<div class="col-xs-12 col-sm-12">
				<div class="row margin-lr-0">
					<span class="pull-right close_navbar"><i class="icon-close"></i></span>
				</div>
				<div class="row">
					<ul class="nav nav-pills nav-stacked wk-nav-style">
						{if isset($navigation_links) && $navigation_links}
							{foreach $navigation_links as $navigationLink}
								<li>
									<a class="navigation-link" href="{$navigationLink['link']}">{$navigationLink['name']}</a>
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
			</div>
		</div>
	</div>
{/block}
