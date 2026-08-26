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
<article class="timeline-item{if isset($timeline_item.alt)} alt{/if}">
	<div class="timeline-caption">
		<div class="timeline-panel arrow arrow-{$timeline_item.arrow}">
			<span class="timeline-icon" style="background-color:{$timeline_item.background_color|escape:'html':'UTF-8'};">
				<i class="{$timeline_item.icon}"></i>
			</span>
			<span class="timeline-date"><i class="icon-calendar"></i> {dateFormat date=$timeline_item.date full=0} - <i class="icon-time"></i> {$timeline_item.date|substr:11:5}</span>
			{if isset($timeline_item.id_order)}<a class="badge" href="#">{l s="Order #"}{$timeline_item.id_order|intval}</a><br/>{/if}
			<span>{$timeline_item.content|truncate:220|nl2br}</span>
			{if isset($timeline_item.see_more_link)}
				<br/><br/><a href="{$timeline_item.see_more_link|escape:'html':'UTF-8'}" class="btn btn-default _blank">{l s="See more"}</a>
			{/if}
		</div>
	</div>
</article>
