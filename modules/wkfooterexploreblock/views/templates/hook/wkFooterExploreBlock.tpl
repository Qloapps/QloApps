{*
* 2010-2018 Webkul.
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
*  @copyright 2010-2018 Webkul IN
*  @license   https://store.webkul.com/license.html
*}

{if isset($explore_links) && $explore_links}
	<div class="col-sm-3">
		<div class="row">
			<section class="col-xs-12 col-sm-12">
				<div class="row margin-lr-0 footer-section-heading">
					<p>{l s='Explore' mod='wkfooterexploreblock'}</p>
					<hr/>
				</div>
				<div class="row margin-lr-0">
					<ul class="footer-explore-section">
					{foreach $explore_links as $exploreLink}
						<li class="item">
							<a title="{$exploreLink['name']}" href="{$exploreLink['link']}">{$exploreLink['name']}</a>
						</li>
					{/foreach}
					</ul>
				</div>
			</section>
		</div>
	</div>
{/if}




