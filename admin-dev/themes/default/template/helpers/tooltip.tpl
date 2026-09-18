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

<span class="tooltip-trigger{if isset($tooltip_class) && $tooltip_class} {$tooltip_class|escape:'html':'UTF-8'}{/if}">
	{if isset($tooltip_title) && $tooltip_title}
		{$tooltip_title nofilter}
	{elseif isset($tooltip_icon_class) && $tooltip_icon_class}
		<i class="{$tooltip_icon_class|escape:'html':'UTF-8'}"{if isset($tooltip_icon_style) && $tooltip_icon_style} style="{$tooltip_icon_style|escape:'html':'UTF-8'}"{/if}></i>
	{else}
		<img src="themes/default/img/icon-info.svg" alt="" />
	{/if}
</span>
<div class="tooltip-content" style="display: none;">
	{if isset($allow_html) && !$allow_html}
		{$tooltip_content|escape:'html':'UTF-8'}
	{else}
		{$tooltip_content nofilter}
	{/if}
</div>
