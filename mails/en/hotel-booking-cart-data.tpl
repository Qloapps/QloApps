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
									{$data_v['hotel_name']}
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
										{$rm_v['adults']} {l s='Adults'}, {$rm_v['children']} {l s='Children'}
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
									{convertPrice price=$rm_v['avg_paid_unit_price_tax_incl']}
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
									{convertPrice price=$rm_v['amount_tax_incl']}
								</font>
							</td>
							<td width="10">&nbsp;</td>
						</tr>
					</table>
				</td>
			</tr>
		{/foreach}
	{/foreach}
{/if}
<style>
	.pull-right {
		float: right;
	}
</style>
