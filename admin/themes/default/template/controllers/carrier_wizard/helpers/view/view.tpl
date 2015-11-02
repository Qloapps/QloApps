{*
* 2007-2015 PrestaShop
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
*	@author PrestaShop SA <contact@prestashop.com>
*	@copyright	2007-2015 PrestaShop SA
*	@license		http://opensource.org/licenses/afl-3.0.php	Academic Free License (AFL 3.0)
*	International Registered Trademark & Property of PrestaShop SA
*}

{extends file="helpers/view/view.tpl"}
{block name="override_tpl"}
<script>
	var labelNext = '{$labels.next|addslashes}';
	var labelPrevious = '{$labels.previous|addslashes}';
	var	labelFinish = '{$labels.finish|addslashes}';
	var	labelDelete = '{l s='Delete' js=1}';
	var	labelValidate = '{l s='Validate' js=1}';
	var validate_url = '{$validate_url|addslashes}';
	var carrierlist_url = '{$carrierlist_url|addslashes}';
	var nbr_steps = {$wizard_steps.steps|count};
	var enableAllSteps = {if $enableAllSteps|intval == 1}true{else}false{/if};
	var need_to_validate = '{l s='Please validate the last range before create a new one.' js=1}';
	var delete_range_confirm = '{l s='Are you sure to delete this range ?' js=1}';
	var currency_sign = '{$currency_sign}';
	var PS_WEIGHT_UNIT = '{$PS_WEIGHT_UNIT}';
	var invalid_range = '{l s='This range is not valid' js=1}';
	var overlapping_range = '{l s='Ranges are overlapping' js=1}';
	var range_is_overlapping = '{l s='Ranges are overlapping' js=1}';
	var select_at_least_one_zone = '{l s='Please select at least one zone' js=1}';
	var multistore_enable = '{$multistore_enable}';
</script>

<div class="row">
	<div class="col-sm-2">
		{$logo_content}
	</div>
	<div class="col-sm-10">
		<div id="carrier_wizard" class="panel swMain">
			<ul class="steps nbr_steps_{$wizard_steps.steps|count}">
			{foreach from=$wizard_steps.steps key=step_nbr item=step}
				<li>
					<a href="#step-{$step_nbr + 1}">
						<span class="stepNumber">{$step_nbr + 1}</span>
						<span class="stepDesc">
							{$step.title}<br />
							{if isset($step.desc)}<small>{$step.desc}</small>{/if}
						</span>
						<span class="chevron"></span>
					</a>
				</li>
			{/foreach}
			</ul>
			{foreach from=$wizard_contents.contents key=step_nbr item=content}
				<div id="step-{$step_nbr + 1}" class="step_container"> 	
					{$content}
				</div>
			{/foreach}
		</div>
	</div>
</div>
{/block}
