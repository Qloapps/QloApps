		<script>var zones_nbr = {$zones|count +3} ; /*corresponds to the third input text (max, min and all)*/</script>
		<div id="zone_ranges" style="overflow:auto">
			<h4>{l s='Ranges'}</h4>
			<table id="zones_table" class="table" style="max-width:100%">
				<tbody>
					<tr class="range_inf">
						<td class="range_type"></td>
						<td class="border_left border_bottom range_sign">&gt;=</td>
						{foreach from=$ranges key=r item=range}
						<td class="border_bottom">
							<div class="input-group fixed-width-md">
								<span class="input-group-addon weight_unit">{$PS_WEIGHT_UNIT}</span>
								<span class="input-group-addon price_unit">{$currency_sign}</span>
								<input class="form-control" name="range_inf[{$range.id_range|intval}]" type="text" value="{$range.delimiter1|string_format:"%.6f"}" />
							</div>
						</td>
						{foreachelse}
						<td class="border_bottom">
							<div class="input-group fixed-width-md">
								<span class="input-group-addon weight_unit">{$PS_WEIGHT_UNIT}</span>
								<span class="input-group-addon price_unit">{$currency_sign}</span>
								<input name="form-control range_inf[{$range.id_range|intval}]" type="text" />
							</div>
						</td>
						{/foreach}
					</tr>
					<tr class="range_sup">
						<td class="range_type"></td>
						<td class="border_left range_sign">&lt;</td>
						{foreach from=$ranges key=r item=range}
						<td class="range_data">
							<div class="input-group fixed-width-md">
								<span class="input-group-addon weight_unit">{$PS_WEIGHT_UNIT}</span>
								<span class="input-group-addon price_unit">{$currency_sign}</span>
								<input class="form-control" name="range_sup[{$range.id_range|intval}]" type="text" {if isset($form_id) && !$form_id} value="" {else} value="{if isset($change_ranges) && $range.id_range == 0} {else}{$range.delimiter2|string_format:"%.6f"}{/if}" {/if} autocomplete="off"/>
							</div>
						</td>
						{foreachelse}
						<td class="range_data_new">
							<div class="input-group fixed-width-md">
								<span class="input-group-addon weight_unit">{$PS_WEIGHT_UNIT}</span>
								<span class="input-group-addon price_unit">{$currency_sign}</span>
								<input class="form-control" name="range_sup[{$range.id_range|intval}]" type="text" autocomplete="off" />
							</div>
						</td>
						{/foreach}
					</tr>
					<tr class="fees_all">
						<td class="border_top border_bottom border_bold">
							<span class="fees_all" {if $ranges|count == 0}style="display:none" {/if}>All</span>
						</td>
						<td style="">
							<input type="checkbox" onclick="checkAllZones(this);" class="form-control">
						</td>
						{foreach from=$ranges key=r item=range}
						<td class="border_top border_bottom {if $range.id_range != 0} validated {/if}"  >
							<div class="input-group fixed-width-md">
								<span class="input-group-addon currency_sign" {if $range.id_range == 0} style="display:none" {/if}>{$currency_sign}</span>
								<input class="form-control" type="text" {if isset($form_id) &&  !$form_id} disabled="disabled"{/if} {if $range.id_range == 0} style="display:none"{/if} autocomplete="off" />
							</div>
						</td>
						{foreachelse}
						<td class="border_top border_bottom">
							<div class="input-group fixed-width-md">
								<span class="input-group-addon currency_sign" style="display:none">{$currency_sign}</span>
								<input class="form-control" style="display:none" type="text" autocomplete="off" />
							</div>
						</td>
						{/foreach}
					</tr>
					{foreach from=$zones key=i item=zone}
					<tr class="fees" data-zoneid="{$zone.id_zone}">
						<td>
							<label for="zone_{$zone.id_zone}">{$zone.name}</label>
						</td>
						<td class="zone">
							<input class="form-control input_zone" id="zone_{$zone.id_zone}" name="zone_{$zone.id_zone}" value="1" type="checkbox" {if isset($fields_value['zones'][$zone.id_zone]) && $fields_value['zones'][$zone.id_zone]} checked="checked"{/if}/>
						</td>
						{foreach from=$ranges key=r item=range}
						<td>
							<div class="input-group fixed-width-md">
								<span class="input-group-addon">{$currency_sign}</span>
								<input class="form-control" name="fees[{$zone.id_zone|intval}][{$range.id_range|intval}]" type="text"
								{if !isset($fields_value['zones'][$zone.id_zone]) || (isset($fields_value['zones'][$zone.id_zone]) && !$fields_value['zones'][$zone.id_zone])} disabled="disabled"{/if}

								{if isset($price_by_range[$range.id_range][$zone.id_zone]) && $price_by_range[$range.id_range][$zone.id_zone] && isset($fields_value['zones'][$zone.id_zone]) && $fields_value['zones'][$zone.id_zone]} value="{$price_by_range[$range.id_range][$zone.id_zone]|string_format:'%.6f'}" {else} value="" {/if} />
							</div>
						</td>
						{/foreach}
					</tr>
					{/foreach}
					<tr class="delete_range">
						<td>&nbsp;</td>
						<td>&nbsp;</td>
						{foreach from=$ranges name=ranges key=r item=range}
							{if $smarty.foreach.ranges.first}
								<td>&nbsp;</td>
							{else}
								<td>
									<button class="btn btn-default">{l s='Delete'}</button>
								</td>
							{/if}
						{/foreach}
					</tr>
				</tbody>
			</table>
		</div>
