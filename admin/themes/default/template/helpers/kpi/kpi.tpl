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

<{if isset($href) && $href}a style="display:block" href="{$href|escape:'html':'UTF-8'}"{else}div{/if} id="{$id|escape:'html':'UTF-8'}" data-toggle="tooltip" class="box-stats label-tooltip {$color|escape}" data-original-title="{$tooltip|escape}" {if $target}target="_blank"{/if}>
	<div class="kpi-content">
		<div class="title-subtitle">
			<div class="title-container">
				<span class="title">
					{if isset($icon) && $icon}<i class="{$icon|escape}"></i>{/if}
					{$title|escape}
				</span>
			</div>
			{if $subtitle}
				<div class="subtitle-container">
					<span class="subtitle">
						{$subtitle|escape}
					</span>
				</div>
			{/if}
		</div>
		{if isset($chart) && $chart}
			<div class="boxchart-overlay">
				<div class="boxchart">
				</div>
			</div>
		{/if}
		<div class="value-container">
			{if isset($source) && $source}
				<span class="value skeleton-loading-wave loading-container-bar loading"></span>
			{elseif isset($value) && $value !== ''}
				<span class="value">{$value|escape:'html':'UTF-8'}</span>
			{/if}
		</div>

		{if isset($href) && $href}
			<span class="arrow"><i class="icon-angle-right"></i></span>
		{/if}
	</div>
</{if isset($href) && $href}a{else}div{/if}>

{if isset($source) && $source}
	<script>
		function refresh_{$id|replace:'-':'_'|addslashes}()
		{
			$.ajax({
				url: '{$source|addslashes}' + '&rand=' + new Date().getTime(),
				dataType: 'json',
				type: 'GET',
				cache: false,
				headers: { 'cache-control': 'no-cache' },
				beforeSend: function() {
					$('#{$id|addslashes}').find('.value').html('');
					$('#{$id|addslashes}').find('.value').addClass('skeleton-loading-wave loading-container-bar loading');
				},
				success: function(jsonData){
					if (!jsonData.has_errors)
					{
						if (jsonData.value != undefined)
							$('#{$id|addslashes} .value').html(jsonData.value);
						if (jsonData.data != undefined)
						{
							$("#{$id|addslashes} .boxchart svg").remove();
							set_d3_{$id|replace:'-':'_'|addslashes}(jsonData.data);
						}
					}
				},
				complete: function () {
					$('#{$id|addslashes}').find('.value').removeClass('skeleton-loading-wave loading-container-bar loading');
				},
			});
		}
	</script>
{/if}

{if $chart}
<script>
	function set_d3_{$id|replace:'-':'_'|addslashes}(jsonObject)
	{
		var data = new Array;
		$.each(jsonObject, function (index, value) {
			data.push(value);
		});
		var data_max = d3.max(data);

		var chart = d3.select("#{$id|addslashes} .boxchart").append("svg")
			.attr("class", "data_chart")
			.attr("width", data.length * 6)
			.attr("height", 45);

		var y = d3.scale.linear()
			.domain([0, data_max])
			.range([0, data_max * 45]);

		chart.selectAll("rect")
			.data(data)
			.enter().append("rect")
			.attr("y", function(d) { return 45 - d * 45 / data_max; })
			.attr("x", function(d, i) { return i * 6; })
			.attr("width", 4)
			.attr("height", y);
	}

	{if $data}
		set_d3_{$id|replace:'-':'_'|addslashes}($.parseJSON("{$data|addslashes}"));
	{/if}
</script>
{/if}
