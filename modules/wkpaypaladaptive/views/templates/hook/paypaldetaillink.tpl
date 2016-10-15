{**
* 2010-2016 Webkul.
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
*  @copyright 2010-2016 Webkul IN
*  @license   https://store.webkul.com/license.html
*}

{if $mpmenu==0}
	<li class="lnk_wishlist">
		<a href="{$link->getModuleLink('mppaypaladaptive', 'paypaldetail')|escape:'html':'UTF-8'}" title="Payment Detail">
			<i class="icon-money"></i>
			<span>{l s='Paypal Detail' mod='mppaypaladaptive'}</span>
		</a>
	</li>
{else}
	<li {if $logic=='mp_paypal'}class="menu_active"{/if}>
		<span>
			<a title="paypal detail" href="{$link->getModuleLink('mppaypaladaptive', 'paypaldetail')|escape:'html':'UTF-8'}">
				{l s='Paypal Detail' mod='mppaypaladaptive'}
			</a>
		</span>
	</li>
{/if}