{if isset($list)}
	{foreach from=$list key=data_k item=data_v}
		{foreach from=$data_v['date_diff'] key=rm_k item=rm_v}
			<tr>
				<td style="border:1px solid #D6D4D4;">
					<table class="table">
						<tr>
							<td width="10">&nbsp;</td>
							<td class="text-center">
								<font size="2" face="Open-sans, sans-serif" color="#555454">
									<img src="{$data_v['cover_img']}" class="img-responsive" />
								</font>
							</td>
							<td width="10">&nbsp;</td>
						</tr>
					</table>
				</td>
				<td style="border:1px solid #D6D4D4;">
					<table class="table">
						<tr>
							<td width="10">&nbsp;</td>
							<td  class="text-center">
								<font size="2" face="Open-sans, sans-serif" color="#555454">
									{$data_v['name']}
								</font>
							</td>
							<td width="10">&nbsp;</td>
						</tr>
					</table>
				</td>
				<td style="border:1px solid #D6D4D4;">
					<table class="table">
						<tr>
							<td width="10">&nbsp;</td>
							<td  class="text-center">
								<font size="2" face="Open-sans, sans-serif" color="#555454">
									<strong>
										{$data_v['adult']} {l s='Adults'}, {$data_v['children']} {l s='Children'}
									</strong>
								</font>
							</td>
							<td width="10">&nbsp;</td>
						</tr>
					</table>
				</td>
				<td style="border:1px solid #D6D4D4;">
					<table class="table">
						<tr>
							<td width="10">&nbsp;</td>
							<td align="right"  class="text-center">
								<font size="2" face="Open-sans, sans-serif" color="#555454">
									{convertPrice price=$data_v['unit_price']}
								</font>
							</td>
							<td width="10">&nbsp;</td>
						</tr>
					</table>
				</td>
				<td style="border:1px solid #D6D4D4;">
					<table class="table">
						<tr>
							<td width="10">&nbsp;</td>
							<td align="right"  class="text-center">
								<font size="2" face="Open-sans, sans-serif" color="#555454">
									{$rm_v['num_rm']}
								</font>
							</td>
							<td width="10">&nbsp;</td>
						</tr>
					</table>
				</td>
				<td style="border:1px solid #D6D4D4;">
					<table class="table">
						<tr>
							<td width="10">&nbsp;</td>
							<td align="right"  class="text-center">
								<font size="2" face="Open-sans, sans-serif" color="#555454">
									{$rm_v['data_form']|date_format:"%d-%b-%G"}
								</font>
							</td>
							<td width="10">&nbsp;</td>
						</tr>
					</table>
				</td>
				<td style="border:1px solid #D6D4D4;">
					<table class="table">
						<tr>
							<td width="10">&nbsp;</td>
							<td align="right"  class="text-center">
								<font size="2" face="Open-sans, sans-serif" color="#555454">
									{$rm_v['data_to']|date_format:"%d-%b-%G"}
								</font>
							</td>
							<td width="10">&nbsp;</td>
						</tr>
					</table>
				</td>
				<td style="border:1px solid #D6D4D4;">
					<table class="table">
						<tr>
							<td width="10">&nbsp;</td>
							<td align="right"  class="text-center">
								<font size="2" face="Open-sans, sans-serif" color="#555454">
									{convertPrice price=$rm_v['amount']}
								</font>
							</td>
							<td width="10">&nbsp;</td>
						</tr>
					</table>
				</td>
			</tr>
			{if isset($rm_v['extra_demands']) && $rm_v['extra_demands']}
				<tr>
					<td colspan="8" style="border:1px solid #D6D4D4;">
						<p bgcolor="#f8f8f8" style="color: #333;font-family: Arial;font-weight:bold;font-size: 13px;padding-top:5px;">&nbsp;&nbsp;{l s='Additional Facilities Details'}</p>
						<table class="demands-table" cellpadding="4" cellspacing="0" style="width:100%;">
							<thead>
								<tr>
									{assign var=roomCount value=1}
									{foreach $rm_v['extra_demands'] as $roomDemand}
										<th bgcolor="#f8f8f8" style="border:1px solid #D6D4D4;background-color: #fbfbfb;color: #333;font-family: Arial;font-size: 13px;padding: 10px;" align="left">{l s='Room'} - {$roomCount}<br></th>
										{assign var=roomCount value=$roomCount+1}
									{/foreach}
								</tr>
							</thead>
							<tbody>
								<tr>
									{foreach $rm_v['extra_demands'] as $roomDemands}
										<td style="border:1px solid #D6D4D4;">
											{foreach $roomDemands['extra_demands'] as $demand}
												<p>
													<span>
														<font size="2" face="Open-sans, sans-serif" color="#555454">
															{$demand['name']}
														</font>
													</span> &nbsp;&nbsp; - &nbsp;&nbsp;
													<span>
														<font size="2" face="Open-sans, sans-serif" color="#555454">
															{convertPrice price=$demand['price']}
														</font>
													</span>
												</p>
											{/foreach}
										</td>
									{/foreach}
								</tr>
							</tbody>
						</table>
					</td>
				</tr>
			{/if}
		{/foreach}
	{/foreach}
{/if}
<style>
	.demands-table td, .demands-table th {
		border: 1px solid #ccc;
	}
	.pull-right {
		float: right;
	}
</style>
