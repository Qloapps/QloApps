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

{block name='footer_navigation'}
	{if isset($navigation_links) && $navigation_links}
		<div class="col-md-3">
			<div>
				<section>
					<div class="footer-section-heading footer-section-item ">
						<span>{l s='QUICK LINKS' mod='blocknavigationmenu'}</span>
					</div>
					<div>
						<ul class="footer-navigation-section list-style-none">
						{foreach $navigation_links as $navigationLink}
							<li class="footer-section-item">
								<a title="{$navigationLink['name']}" href="{$navigationLink['link']}">{$navigationLink['name']}</a>
							</li>
						{/foreach}
						{block name='displayFooterExploreSectionHook'}
							{hook h="displayFooterExploreSectionHook"}
						{/block}
						</ul>
					</div>
				</section>
			</div>
		</div>
	{/if}
{/block}
