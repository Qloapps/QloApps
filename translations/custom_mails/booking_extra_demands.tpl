{if isset($list)}
	<table class="table table-recap extra-demand-table">
		<thead>
			<tr>
				<th colspan="3">{l s='Extra Demands Details'}</th>
			</tr>
			<tr>
				<th>{l s='Room Type'}</th>
				<th>{l s='Extra demand name'}</th>
				<th>{l s='Total'} <br /> {l s='(Tax excl.)'}</th>
			</tr>
		</thead>
		<tbody>
			{if isset($list)}
				{foreach from=$list key=data_k item=data_v}
					{foreach from=$data_v['date_diff'] key=rm_k item=rm_v}
						{if isset($rm_v['extra_demands']) && $rm_v['extra_demands']}
							{assign var=roomCount value=1}
							{foreach $rm_v['extra_demands'] as $roomDemand}
								{foreach name=demandRow from=$roomDemand['extra_demands'] item=demand}
									<tr>
										{if $smarty.foreach.demandRow.first}
											<td class="text-center" rowspan="{$roomDemand['extra_demands']|count}">
												<font size="2" face="Open-sans, sans-serif" color="#555454">
													{$data_v['name']}<br>
													{$rm_v['data_form']|date_format:"%d-%m-%Y"} {l s='to'} {$rm_v['data_to']|date_format:"%d-%m-%Y"}<br>
													<strong>{l s='Room'} - {$roomCount}</strong>
												</font>
											</td>
										{/if}
										<td class="text-center">
											<font size="2" face="Open-sans, sans-serif" color="#555454">
												{$demand['name']}
											</font>
										</td>
										<td class="text-center">
											<font size="2" face="Open-sans, sans-serif" color="#555454">
												{convertPrice price=$demand['total_price_tax_excl']}
											</font>
										</td>
									</tr>
								{/foreach}
								{assign var=roomCount value=$roomCount+1}
							{/foreach}
						{/if}
					{/foreach}
				{/foreach}
			{/if}
		</tbody>
	</table>
	<style>
		.extra-demand-table {
		 	width:100%;
			border-collapse:collapse;
			padding:5px;
		}
		.extra-demand-table th {
			border:1px solid #D6D4D4;
			background-color: #fbfbfb;
			color: #333;
			font-family: Arial;
			font-size: 13px;
			padding: 7px 5px;
			text-align:left;
		}
		.extra-demand-table td {
			border:1px solid #D6D4D4;
			padding:5px;
		}
	</style>
{/if}
