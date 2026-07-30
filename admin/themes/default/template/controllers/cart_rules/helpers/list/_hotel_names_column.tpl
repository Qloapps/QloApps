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

{if $hotel_names_first}
	<span>{$hotel_names_first|escape:'html':'UTF-8'}</span>
	{if $hotel_names_remaining}
		{assign var='tooltipLines' value=''}
		{foreach from=$hotel_names_remaining item='hotel' key='index'}
			{assign var='tooltipLines' value="{$tooltipLines}{if $index > 0}<br>{/if}{$index+1}) {$hotel|escape:'html':'UTF-8'}"}
		{/foreach}
		<span class="badge label-tooltip"
			style="padding:2px 3px;"
			data-toggle="tooltip"
			data-placement="top"
			data-html="true"
			title="&lt;div style=&quot;text-align:left&quot;&gt;{$tooltipLines}&lt;/div&gt;">+{$hotel_names_remaining|@count}</span>
	{/if}
{else}
	--
{/if}
