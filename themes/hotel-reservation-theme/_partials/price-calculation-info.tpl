{**
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

{* Required param: pcm — price_calculation_method integer value *}

<span class="price-calculation-info">
	<img src="{$img_dir}icon/icon-info.svg" />
</span>
<div class="price-calculation-info-container" style="display: none;">
	<div class="price-info-tooltip-cont">
		<p class="applied-on-label">{l s='Applied on'}</p>
		<ul class="applied-on-days">
			{if $pcm == 1 || $pcm == 3 || $pcm == 5 || $pcm == 7}
				<li>{l s='Check-in day'}</li>
			{/if}
			{if $pcm == 4 || $pcm == 5 || $pcm == 6 || $pcm == 7}
				<li>{l s='During-stay days'}</li>
			{/if}
			{if $pcm == 2 || $pcm == 3 || $pcm == 6 || $pcm == 7}
				<li>{l s='Check-out day'}</li>
			{/if}
		</ul>
	</div>
</div>
