{*
* 2010-2019 Webkul.
*
* NOTICE OF LICENSE
*
* All right is reserved,
* Please go through this link for complete license : https://store.webkul.com/license.html
*
* DISCLAIMER
*
* Do not edit or add to this file if you wish to upgrade this module to newer
* versions in the future. If you wish to customize this module for your
* needs please refer to https://store.webkul.com/customisation-guidelines/ for more information.
*
*  @author    Webkul IN <support@webkul.com>
*  @copyright 2010-2019 Webkul IN
*  @license   https://store.webkul.com/license.html
*}

{if isset($navigation_links) && $navigation_links}
	<div class="col-sm-3">
		<div class="row">
			<section class="col-xs-12 col-sm-12">
				<div class="row margin-lr-0 footer-section-heading">
					<p>{l s='Explore' mod='blocknavigationmenu'}</p>
					<hr/>
				</div>
				<div class="row margin-lr-0">
					<ul class="footer-navigation-section">
					{foreach $navigation_links as $navigationLink}
						<li class="item">
							<a title="{$navigationLink['name']}" href="{$navigationLink['link']}">{$navigationLink['name']}</a>
						</li>
					{/foreach}
					{hook h="displayFooterExploreSectionHook"}
					</ul>
				</div>
			</section>
		</div>
	</div>
{/if}




